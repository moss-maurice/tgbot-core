<?php

namespace mmaurice\tgbot\commands;

use \mmaurice\tgbot\commands\MenuCommand;
use \mmaurice\tgbot\core\interfaces\Command;

class HelpCommand extends Command
{
    static public $description = 'Справка по командам бота';
    static public $order = 1;
    static public $alias = 'Помощь';

    public function keyboard($keyboard = [])
    {
        return parent::keyboard([
            !is_null(MenuCommand::$alias) ? MenuCommand::$alias : MenuCommand::commandName(),
        ]);
    }

    public function execute($message = '')
    {
        return $this->render('help', [
            'commands' => $this->commandsDescriptions(),
            'setcommands' => (($message === 'setcommands') ? true : false),
            'message' => $message,
        ]);
    }
}