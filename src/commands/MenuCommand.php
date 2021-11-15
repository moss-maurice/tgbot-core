<?php

namespace mmaurice\tgbot\commands;

use \mmaurice\tgbot\core\interfaces\Command;

class MenuCommand extends Command
{
    static public $description = 'Меню команд';
    static public $order = 2;
    static public $alias = 'Меню';
    static public $type = self::TYPE_SYSTEM;

    public function keyboard($keyboard = [])
    {
        return parent::keyboard(array_merge($this->commandsAliases(self::TYPE_PUBLIC), $this->commandsAliases(self::TYPE_SYSTEM)));
    }

    public function execute($message = '')
    {
        return $this->render('menu', [
            'message' => $message,
        ]);
    }
}