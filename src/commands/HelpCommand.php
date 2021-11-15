<?php

namespace mmaurice\tgbot\commands;

use \mmaurice\tgbot\core\interfaces\Command;

class HelpCommand extends Command
{
    static public $description = 'Справка по командам бота';
    static public $order = 2;
    static public $alias = 'Помощь';
    static public $type = self::TYPE_SYSTEM;

    public function keyboard($keyboard = [])
    {
        return parent::keyboard($this->commandsAliases(self::TYPE_SYSTEM, false));
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