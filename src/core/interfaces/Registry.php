<?php

namespace mmaurice\tgbot\core\interfaces;

abstract class Registry
{
    const FIRST_KEY = 0;
    const LAST_KEYS = 1;
    const AS_ARRAY = 2;

    protected $registry;

    public function __construct(array $data = [])
    {
        $this->wipe($data);
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->find($this->registry, $key);
        }

        return $default;
    }

    public function set($key, $value)
    {
        $this->update($this->registry, $key, $value);

        return $this;
    }

    public function delete($key)
    {
        if ($this->has($key)) {
            return $this->set($key, null);
        }
    }

    public function has($key)
    {
        $isHas = false;

        $this->find($this->registry, $key, $isHas);

        return $isHas;
    }

    public function wipe($data = [])
    {
        $this->registry = $data;
    }

    protected function find($data, $key, &$isHas = false)
    {
        if (!is_null($key)) {
            $subKey = $this->extractKeys($key, self::LAST_KEYS);
            $key = $this->extractKeys($key, self::FIRST_KEY);

            if (is_array($data) and array_key_exists($key, $data)) {
                if (!is_null($subKey)) {
                    return $this->find($data[$key], $subKey, $isHas);
                }

                $isHas = true;

                return $data[$key];
            }
        }

        return null;
    }

    protected function update(&$data, $key, $value)
    {
        if (!is_array($data)) {
            $data = is_null($data) ? [] : [$data];
        }

        if (!is_null($key)) {
            $subKey = $this->extractKeys($key, self::LAST_KEYS);
            $key = $this->extractKeys($key, self::FIRST_KEY);

            if (!array_key_exists($key, $data)) {
                $data[$key] = null;
            }

            if (!is_null($subKey)) {
                return $this->find($data[$key], $subKey, $value);
            }

            $data[$key] = $value;

            return true;
        }

        return false;
    }

    protected function extractKeys($key, $result = self::FIRST_KEY)
    {
        if (in_array($result, [self::FIRST_KEY, self::LAST_KEYS])) {
            if (preg_match('/^([^\.]+)([^\$]*)$/im', $key, $matches)) {
                if ($result === self::FIRST_KEY) {
                    if (array_key_exists(1, $matches)) {
                        return (!empty($matches[1]) ? $matches[1] : null);
                    }
                } else if ($result === self::LAST_KEYS) {
                    if (array_key_exists(2, $matches)) {
                        return (!empty(trim($matches[2], '.')) ? trim($matches[2], '.') : null);
                    }
                }
            }
        } else if (in_array($result, [self::AS_ARRAY])) {
            return explode('.', $key);
        }

        return null;
    }
}