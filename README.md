# КРАТКАЯ ИНСТРУКЦИЯ
_позаимствована из проекта - ![https://github.com/php-telegram-bot/core](https://github.com/php-telegram-bot/core)_

1) Зарегистрировать бота можно, перейдя к ТГ-боту ![BotFather](https://telegram.me/BotFather) и запустив команду:
    /newbot
2) После регистрации бота будет выдан [token]
3) Необходимо развернуть приложение бота на web-сервере с SSL-сертификатом
4) Зарегистрировать webhook для приложения бота, перейдя по ссылке:
    https://api.telegram.org/bot[token]/setWebhook?url=[handler-url]
где,
    [token]         - выданный боту токен
    [handler-url]   - путь к handler приложения для вебхука (в формате: https://site.com/path/to/handler.php)
5) Готово