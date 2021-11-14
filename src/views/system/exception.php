<?php use \mmaurice\tgbot\helpers\ViewHelper; ?>

<b>EXCEPTION</b>

<i><?= ViewHelper::prepareHtml($exception->message); ?></i>

<b>CODE:</b> <?= ViewHelper::prepareHtml($exception->code); ?>

<b>FILE:</b> <?= ViewHelper::prepareHtml($exception->file); ?>

<b>LINE:</b> <?= ViewHelper::prepareHtml($exception->line); ?>


<b>TRACE:</b>
<?= ViewHelper::prepareHtml($exception->trace); ?>