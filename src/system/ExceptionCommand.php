<?php

namespace mmaurice\tgbot\system;

use \mmaurice\tgbot\core\interfaces\Command;
use \TelegramBot\Api\Client;

class ExceptionCommand extends Command
{
    static public $type = self::TYPE_HIDDEN;

    public function keyboard($keyboard = [])
    {
        return parent::keyboard($this->commandsAliases(self::TYPE_SYSTEM, false));
    }

    public function execute($message = '')
    {
        return $this->render('system/exception', [
            'exception' => json_decode($message),
            'message' => $message,
        ]);
    }
}