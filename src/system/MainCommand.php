<?php

namespace mmaurice\tgbot\system;

use \mmaurice\tgbot\core\interfaces\Command;

class MainCommand extends Command
{
    static public $type = self::TYPE_HIDDEN;

    public function keyboard($keyboard = [])
    {
        return parent::keyboard($this->commandsAliases(self::TYPE_SYSTEM, false));
    }

    public function execute($message = '')
    {
        return $this->render('system/main', [
            'message' => $message,
        ]);
    }
}