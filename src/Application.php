<?php

namespace mmaurice\tgbot;

use \DI\Container;
use \Exception;
use \mmaurice\tgbot\core\Config;
use \mmaurice\tgbot\core\Router;
use \TelegramBot\Api\Client;

class Application
{
    static public $di;
    static protected $sources = [];

    public function __construct(array $config = [])
    {
        if (!defined('APP_ROOT')) {
            define('APP_ROOT', realpath(dirname($_SERVER['SCRIPT_FILENAME'])));
        }

        self::$di = new Container();

        self::$di->set('config', new Config($config));
        self::$di->set('client', new Client(TOKEN));
        self::$di->set('router', new Router(self::$di->get('client')));

        $this->importSource(__NAMESPACE__);
    }

    public static function __callStatic($name, $arguments)
    {
        if (self::$di->has($name)) {
            return self::$di->get($name);
        }

        throw new Exception("Dependency {$name} is not defined");
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
        $router = self::$di->get('router');

        $router->run();
    }

    public function error(Exception $exception)
    {
        $router = self::$di->get('router');

        $router->error($exception);
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