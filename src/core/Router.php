<?php

namespace mmaurice\tgbot\core;

use \Exception;
use \mmaurice\tgbot\Application;
use \mmaurice\tgbot\system\ErrorCommand;
use \mmaurice\tgbot\system\ExceptionCommand;
use \mmaurice\tgbot\system\MainCommand;
use \TelegramBot\Api\Client;
use \TelegramBot\Api\Types\Update;
use \TelegramBot\Api\Types\Message;

class Router
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function client()
    {
        return $this->client;
    }

    public function run()
    {
        $client = $this->client;

        $commands = Application::commands();

        $client->on(function (Update $update) use (&$client) {
            $message = $update->getMessage();

            $command = null;
            $text = !empty($message->getText()) ? trim($message->getText()) : null;

            if (preg_match('/^\/([^\s]+)(?:\s([^$]*)|.*)$/im', $text, $matches)) {
                $command = !empty($matches[1]) ? $matches[1] : null;
                $text = !empty($matches[2]) ? $matches[2] : null;
            }

            $commands = Application::commands();

            $commandClass = MainCommand::class;

            if (!is_null($command)) {
                foreach ($commands as $alias => $class) {
                    if ($command === $alias) {
                        $commandClass = $class;

                        break;
                    }

                    $commandClass = ErrorCommand::class;
                }
            } else {
                foreach ($commands as $alias => $class) {
                    if (is_string($class::$alias) and !empty($class::$alias) and preg_match('/^(' . $class::$alias . ')(?:\s([^$]*)|.*)$/imus', $text, $matches)) {
                        $command = !empty($matches[1]) ? $matches[1] : null;
                        $text = !empty($matches[2]) ? $matches[2] : null;

                        $commandClass = $class;

                        break;
                    }
                }
            }

            $commandObject = new $commandClass($client, $message);

            $commandObject->execute($text);
        }, function () {
            return true;
        });

        $client->run();
    }

    public function error(Exception $exception)
    {
        $client = $this->client;

        $client->on(function (Update $update) use (&$client, $exception) {
            $message = $update->getMessage();

            $commandObject = new ExceptionCommand($client, $message);

            $commandObject->execute(json_encode([
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]));
        }, function () {
            return true;
        });

        $client->run();
    }
}
