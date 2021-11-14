<?php

namespace mmaurice\tgbot\system;

use \mmaurice\tgbot\commands\HelpCommand;
use \mmaurice\tgbot\commands\MenuCommand;
use \mmaurice\tgbot\core\interfaces\Command;
use \TelegramBot\Api\Client;

class ErrorCommand extends Command
{
    public function keyboard($keyboard = [])
    {
        return parent::keyboard([
            !is_null(HelpCommand::$alias) ? HelpCommand::$alias : HelpCommand::commandName(),
            !is_null(MenuCommand::$alias) ? MenuCommand::$alias : MenuCommand::commandName(),
        ]);
    }

    public function execute($message = '')
    {
        return $this->render('system/error', [
            'message' => $message,
        ]);
    }
}