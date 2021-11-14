<?php

namespace mmaurice\tgbot\helpers;

class ViewHelper
{
    static public function prepareMarkdown($string)
    {
        $replacements = [
            '_' => '\_',
            '*' => '\*',
            '[' => '\[',
        ];

        $string = str_replace(array_keys($replacements), array_values($replacements), $string);

        return $string;
    }

    static public function prepareHtml($string)
    {
        return $string;
    }
}
