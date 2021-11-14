<?php

namespace mmaurice\tgbot\commands;

use \mmaurice\tgbot\commands\HelpCommand;
use \mmaurice\tgbot\core\interfaces\Command;

class MenuCommand extends Command
{
    static public $description = 'Меню команд';
    static public $order = 2;
    static public $alias = 'Меню';

    public function keyboard($keyboard = [])
    {
        return parent::keyboard([
            !is_null(HelpCommand::$alias) ? HelpCommand::$alias : HelpCommand::commandName(),
        ]);
    }

    public function execute($message = '')
    {
        return $this->render('menu', [
            'message' => $message,
        ]);
    }
}