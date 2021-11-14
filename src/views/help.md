<?php use \mmaurice\tgbot\helpers\ViewHelper; ?>

*Cписок всех доступных комманд<?= ($setcommands ? ' (в формате setcommands)' : ''); ?>:*

<?php if (is_array($commands) and !empty($commands)) : ?>
<?php foreach ($commands as $command => $description) : ?>
<?php if ($setcommands) : ?>
<?= ViewHelper::prepareMarkdown($command); ?> - <?= ViewHelper::prepareMarkdown($description); ?>
<?php else: ?>
*/<?= ViewHelper::prepareMarkdown($command); ?>* -- _<?= ViewHelper::prepareMarkdown($description); ?>_
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>