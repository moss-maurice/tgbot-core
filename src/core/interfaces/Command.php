<?php

namespace mmaurice\tgbot\core\interfaces;

use \Exception;
use \mmaurice\tgbot\Application;
use \ReflectionClass;
use \TelegramBot\Api\Client;
use \TelegramBot\Api\Types\Message;
use \TelegramBot\Api\Types\ReplyKeyboardMarkup;
use \TelegramBot\Api\Types\ReplyKeyboardHide;
use \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

abstract class Command
{
    const KEYBOARD_TYPE_NONE = 'none';
    const KEYBOARD_TYPE_INLINE = 'inline';
    const KEYBOARD_TYPE_BUTTONS = 'buttons';

    const KEYBOARD_COLUMNS = 4;

    const TYPE_PUBLIC = 'public';
    const TYPE_SYSTEM = 'system';
    const TYPE_HIDDEN = 'hidden';

    static public $description = '';
    static public $order = 1000;
    static public $alias;
    static public $type = self::TYPE_PUBLIC;

    protected $message;
    protected $client;
    protected $parserMode;

    protected $extension = [
        'markdown' => '.md',
        'html' => '.php',
    ];

    public function __construct(Client &$client, Message &$message)
    {
        $config = Application::$di->get('config');

        $this->message = $message;
        $this->client = $client;
        $this->parserMode = $config->get('parserMode', 'markdown');
    }

    abstract public function execute($message = '');

    public function keyboard(array $keyboard = [])
    {
        $results = [];

        while (!empty($keyboard)) {
            $row = [];

            $keys = count($keyboard);

            for ($i = 0; $i < (min(static::KEYBOARD_COLUMNS, $keys)); $i++) {
                $row[] = array_shift($keyboard);
            }

            $results[] = $row;
        };

        return $results;
    }

    public function keyboardType()
    {
        return self::KEYBOARD_TYPE_BUTTONS;
    }

    static public function commandName()
    {
        if (preg_match('/([\w]+)Command$/im', get_called_class(), $matches)) {
            return strtolower($matches[1]);
        }

        throw new Exception("Command name is not matched!");
    }

    protected function render($view, $properties = [])
    {
        $view = $this->renderView($view, $properties);

        $keyboardObject = new ReplyKeyboardHide;

        if (is_array($this->keyboard()) and in_array($this->keyboardType(), [self::KEYBOARD_TYPE_INLINE, self::KEYBOARD_TYPE_BUTTONS])) {
            $keyboardObject = new ReplyKeyboardMarkup($this->keyboard(), true, true);

            if ($this->keyboardType() === self::KEYBOARD_TYPE_INLINE) {
                $keyboardObject = new InlineKeyboardMarkup($this->keyboard());
            }
        }

        Application::sendMessage($this->message->getChat()->getId(), $view, $this->parserMode, $keyboardObject);
    }

    protected function renderView($view, $properties = [])
    {
        $reflector = new \ReflectionClass(get_called_class());

        $extension = (array_key_exists($this->parserMode, $this->extension) ? $this->extension[$this->parserMode] : '.php');

        $viewFilePath = realpath(dirname($reflector->getFileName()) . "/../views/{$view}{$extension}");

        if (!file_exists($viewFilePath) or !is_file($viewFilePath)) {
            throw new Exception("Template file \"{$viewFilePath}\" is not found!");
        }

        ob_start();

        extract($properties, EXTR_SKIP);

        ob_implicit_flush(false);

        include $viewFilePath;

        return trim(ob_get_clean());
    }

    protected function commandsAliases($type = null, $selfInclude = true)
    {
        $results = [];

        $commands = Application::commands();

        if (is_array($commands) and !empty($commands)) {
            foreach ($commands as $command => $class) {
                if (($selfInclude and ($class === get_called_class())) or ($class !== get_called_class())) {
                    if (is_null($type) or ($class::$type === $type)) {
                        $results[] = !is_null($class::$alias) ? $class::$alias : $class::commandName();
                    }
                }
            }
        }

        return array_values(array_filter($results));
    }

    protected function commandsDescriptions($type = null)
    {
        $results = [];

        $commands = Application::commands();

        if (is_array($commands) and !empty($commands)) {
            foreach ($commands as $command => $class) {
                if (is_null($type) or ($class::$type === $type)) {
                    $results[$command] = $class::$description;
                }
            }
        }

        return $results;
    }
}
