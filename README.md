<p align="center">
    <img width="1376" height="768" alt="1794485519036" src="https://github.com/user-attachments/assets/76a87587-da8e-4d5b-8b50-48e91a489ff3" />
</p>

# NeleBot X Framework 2.0

Develop Telegram bots in PHP with NeleBot X.

> **Important — use with care:** this version was produced using vibe coding (AI-assisted code generation). It has undergone limited syntax, smoke, and simulated HTTP testing, but has not received a complete independent security audit or exhaustive testing of every plugin and database backend. Review the code, test with a separate Telegram bot, back up your data, and do not deploy to critical production workloads without your own audit and validation.

Targets Telegram Bot API 10.4 and PHP 8.5. PHP 8.2+ is declared as the minimum runtime; verify the version used by your host.

## Requirements

- PHP 8.2 or newer; PHP 8.5 recommended.
- PHP extensions: `curl` and `openssl` required.
- PDO only if using SQLite, MySQL, or PostgreSQL.
- Redis extension only if Redis is enabled.
- A public HTTPS endpoint for Telegram webhooks, or a separately configured local Telegram Bot API server.

## Installation and setup

1. Download or clone this repository into your PHP web root.
2. Install the required PHP extensions.
3. Review the settings in `configs.php`.
4. Run `php tools/webhook-manager.php` and choose **Install / configure webhook**. The manager validates the bot token with `getMe`, generates both security keys, writes them to `configs.php` with a backup, creates the URL-encoded encrypted key, and calls Telegram `setWebhook` over HTTPS.
5. The manager registers the webhook automatically. Follow [the setup guide](docs/setup.md) to prepare the server permissions and verify the deployment.

The manager generates independent 256-bit secrets. It never places the Telegram token in `configs.php`, command-line arguments, or shell history. Keep `configs.php` and its timestamped backups private.

The `index.php` entry point requires the configured Telegram `secret_token` header and accepts encrypted `key` values only; the former plaintext `?token=...` webhook mode has been removed. When `configs.php` contains a non-empty `token`, the decrypted token must also match it exactly. Leave `token` empty only when intentionally using the encrypted key to select among multiple bots.

The supplied `index.php` and `crontab.php` load `configs.php` once with `require_once`. `TelegramBot::getConfigs()` returns the configuration injected into the object and does not load `configs.php` again.

## Calling newer Bot API methods

Use `callApi()` for Bot API methods without a dedicated wrapper:

```php
$result = $bot->callApi('sendMessage', [
    'chat_id' => $chatId,
    'text' => 'Hello from NeleBot X',
]);
```

## Development checks

```bash
php tests/smoke.php
tests/run-integration.sh
```

The integration suite uses a fake Bot API server bound only to `127.0.0.1`. It does not change system DNS or `/etc/hosts` and does not contact Telegram. CI is configured for PHP 8.2–8.5; the local development environment used PHP 8.4.24 without native cURL, so PHP 8.5 and native-cURL testing still need confirmation in CI.

## About NeleBot X

[Telegram Channel](https://t.me/NeleBotX) · [Telegram Group](https://t.me/NeleBotXSupport)


### Logging

PHP/framework logs are written to `/var/log/bots/NeleBotX.log`, outside the web root. Create the directory and grant write access to the PHP-FPM/web-server account before enabling the webhook:

```bash
sudo install -d -o www-data -g www-data -m 0750 /var/log/bots
sudo install -o www-data -g www-data -m 0640 /dev/null /var/log/bots/NeleBotX.log
```

Replace `www-data` with the account used by your PHP service. The configured path can be changed with `log_file` in `configs.php`. Error display is disabled; PHP errors are sent to the protected log file.

### Telegram Bot API 10.4

The framework exposes convenience methods for `sendWidget`, `sendSharedWidget`, `answerWidgetQuery`, `editMessageWidgetData`, `editSharedWidgetData`, `getChatShareThemeBackdrops`, `getUniqueGift`, and `deleteBusinessAccountGift`. All other API methods and parameters remain accessible through `callApi()` / `call()`. New update types `widget_query` and `shared_widget_query` are surfaced through `Variables::$update_type` and `Variables::$update_data`.


## Command examples

The bundled `/help` command now lists the ready-to-test demo commands, inline-mode instructions, and admin-only diagnostics. Run `/examples` (or use the button in `/help`) to launch the interactive demonstration. Replace `@YourBotUsername` with your bot's actual username when testing inline mode.

The bundled `plugins/commands.php` includes a developer cookbook with copy-ready examples for commands, replies, inline and reply keyboards, callbacks, editing messages, media, locations, polls, dice, chat/member lookups, API error handling, generic Bot API calls, and the Bot API 10.4 widget methods. The examples are disabled templates inside a comment: copy only the snippets you need into your command handler and adapt their parameters.
