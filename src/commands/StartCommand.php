<?php

namespace mmaurice\tgbot\commands;

use \mmaurice\tgbot\core\interfaces\Command;

class StartCommand extends Command
{
    static public $description = 'Начало работы с ботом';
    static public $order = 0;
    static public $alias = 'Начать';
    static public $type = self::TYPE_HIDDEN;

    public function keyboard($keyboard = [])
    {
        return parent::keyboard($this->commandsAliases(self::TYPE_SYSTEM, false));
    }

    public function execute($message = '')
    {
        return $this->render('start', [
            'message' => $message,
        ]);
    }
}