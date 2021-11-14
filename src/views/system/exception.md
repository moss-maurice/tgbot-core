<?php use \mmaurice\tgbot\helpers\ViewHelper; ?>

*EXCEPTION*

_<?= ViewHelper::prepareMarkdown($exception->message); ?>_

*CODE:* <?= ViewHelper::prepareMarkdown($exception->code); ?>

*FILE:* <?= ViewHelper::prepareMarkdown($exception->file); ?>

*LINE:* <?= ViewHelper::prepareMarkdown($exception->line); ?>


*TRACE:*
<?= ViewHelper::prepareMarkdown($exception->trace); ?>