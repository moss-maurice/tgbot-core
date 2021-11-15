<?php

namespace mmaurice\tgbot\core;

use \Exception;
use \SleekDB\Store as SleekDbStore;

class Store
{
    protected $storage;

    public function __construct($path)
    {
        if (!realpath($path)) {
            mkdir($path, 0777, true);
        }

        if (realpath($path)) {
            $this->storage = realpath($path);
        }

        if (!is_null($this->storage)) {
            return true;
        }

        throw new Exception("Store \"$path\" is not exists");
    }

    public function init($name)
    {
        return new SleekDbStore($name, $this->storage);
    }
}
