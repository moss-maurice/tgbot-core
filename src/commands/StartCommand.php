<?php

namespace mmaurice\tgbot\commands;

use \mmaurice\tgbot\commands\HelpCommand;
use \mmaurice\tgbot\commands\MenuCommand;
use \mmaurice\tgbot\core\interfaces\Command;

class StartCommand extends Command
{
    static public $description = 'Начало работы с ботом';
    static public $order = 0;
    static public $alias = 'Начать';

    public function keyboard($keyboard = [])
    {
        return parent::keyboard([
            !is_null(HelpCommand::$alias) ? HelpCommand::$alias : HelpCommand::commandName(),
            !is_null(MenuCommand::$alias) ? MenuCommand::$alias : MenuCommand::commandName(),
        ]);
    }

    public function execute($message = '')
    {
        return $this->render('start', [
            'message' => $message,
        ]);
    }
}