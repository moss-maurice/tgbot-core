<?php use \mmaurice\tgbot\helpers\ViewHelper; ?>

<b>Cписок всех доступных комманд<?= ($setcommands ? ' (в формате setcommands)' : ''); ?>:</b>

<?php if (is_array($commands) and !empty($commands)) : ?>
<?php foreach ($commands as $command => $description) : ?>
<?php if ($setcommands) : ?>
<?= ViewHelper::prepareHtml($command); ?> - <?= ViewHelper::prepareHtml($description); ?>
<?php else: ?>
<b>/<?= ViewHelper::prepareHtml($command); ?></b> -- <i><?= ViewHelper::prepareHtml($description); ?></i>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>