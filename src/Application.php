<?php

namespace mmaurice\tgbot;

use \DI\Container;
use \Exception;
use \mmaurice\tgbot\core\Config;
use \mmaurice\tgbot\core\Router;
use \mmaurice\tgbot\core\Store;
use \TelegramBot\Api\Client;
use \TelegramBot\Api\Types\ReplyKeyboardHide;

class Application
{
    const MODE_CLIENT = 'client';
    const MODE_SENDER = 'sender';

    static public $di;
    static protected $sources = [];

    protected $mode;

    public function __construct(array $config = [], $mode = self::MODE_CLIENT)
    {
        if (!defined('APP_ROOT')) {
            define('APP_ROOT', realpath(dirname($_SERVER['SCRIPT_FILENAME'])));
        }

        $this->mode = $mode;

        self::$di = new Container();

        self::$di->set('config', new Config($config));
        self::$di->set('client', new Client(TOKEN));
        self::$di->set('store', new Store(self::$di->get('config')->get('sleekdb.store', APP_ROOT . '/runtime/sleekdb/')));

        if ($mode === self::MODE_CLIENT) {
            if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post') {
                throw new Exception('Access denied!');
            }

            self::$di->set('router', new Router(self::$di->get('client')));
        }

        $this->importSource(__NAMESPACE__);
    }

    public static function __callStatic($name, $arguments)
    {
        if (self::$di->has($name)) {
            return self::$di->get($name);
        }

        throw new Exception("Dependency {$name} is not defined");
    }

    static public function sendMessage($id, $message, $parserMode = 'markdown', $keyboard = null)
    {
        $client = self::$di->get('client');

        if (is_null($keyboard)) {
            $keyboard = new ReplyKeyboardHide;
        }

        return $client->sendMessage($id, $message, $parserMode, false, null, $keyboard);
    }

    public function importSource($namespace)
    {
        array_unshift(self::$sources, $namespace);
    }

    static public function sources()
    {
        return self::$sources;
    }

    public function run()
    {
        if ($this->mode === self::MODE_CLIENT) {
            $router = self::$di->get('router');

            $router->run();
        } else {
            throw new Exception('Router running available only in CLIENT application mode');
        }
    }

    public function error(Exception $exception)
    {
        if ($this->mode === self::MODE_CLIENT) {
            $router = self::$di->get('router');

            $router->error($exception);
        } else if ($this->mode === self::MODE_SENDER) {
            echo $exception;
        }
    }

    static public function commands()
    {
        $results = [];
        $range = [];

        $namespaces = include realpath(APP_ROOT . '/vendor/composer/autoload_psr4.php');

        if (is_array($namespaces) and !empty($namespaces)) {
            foreach ($namespaces as $namespace => $path) {
                $namespace = trim($namespace, "/\\");

                if (in_array($namespace, self::sources())) {
                    $sourcePath = realpath(realpath($path[0]) . '/commands');

                    if ($sourcePath and ($sourcePathFiles = glob($sourcePath . '/*Command.php'))) {
                        foreach ($sourcePathFiles as $pathFile) {
                            $classNameSpace = $namespace . "\\commands\\" . pathinfo($pathFile, PATHINFO_FILENAME);

                            if (method_exists($classNameSpace, 'commandName')) {
                                $range[$classNameSpace::$order][$classNameSpace::commandName()] = $classNameSpace;
                            }
                        }
                    }
                }
            }
        }

        uksort($range, function ($left, $right) {
            return (($left <= $right) ? (($left < $right) ? -1 : 0) : 1);
        });

        if (is_array($range) and !empty($range)) {
            foreach ($range as $rangeItems) {
                $results = array_merge($results, $rangeItems);
            }
        }

        return $results;
    }
}