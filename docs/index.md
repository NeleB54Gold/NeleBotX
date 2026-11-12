# NeleBot X Framework 2.0

NeleBot X is a PHP framework for building Telegram bots. It receives Telegram updates through a webhook, exposes the update as convenient PHP properties, provides helper methods for common Bot API operations, and optionally manages users, groups, channels, databases, Redis and anti-flood protection.

- **Framework version:** 2.0
- **Telegram Bot API target:** 10.4
- **Minimum PHP version:** 8.2
- **Recommended PHP version:** 8.5

> **Important — vibe coding and project maturity**
>
> NeleBot X 2.0 was developed with a *vibe coding* workflow: the maintainer used natural-language instructions and AI coding assistance to inspect, modify, document and test the framework. This explains how the release was produced; it is not a certification of code quality and does not mean that every generated change is automatically correct.
>
> The project has received syntax checks, smoke tests and selected HTTP integration tests using simulated Telegram API responses. These checks cover only the paths represented by the tests. They are not exhaustive, do not prove that every plugin or database configuration works, and are not a professional penetration test or independent security audit. Review the code, test with a separate bot, keep backups and validate your deployment before using it in production. Reported test success must not be interpreted as a guarantee of security or reliability.

## How the framework works

The normal webhook lifecycle is:

1. Telegram sends an HTTPS `POST` request to the configured webhook URL.
2. `index.php` loads the configuration and initializes logging. If logging cannot be initialized, the request is stopped.
3. The entry point checks the HTTP method and validates Telegram's `X-Telegram-Bot-Api-Secret-Token` header.
4. It decrypts the `key` query parameter with the installation-specific password in `configs.php`. If a fixed bot token is configured, the decrypted token must match it.
5. `NeleBotX` initializes the Telegram API client, reads the update, creates the `Variables` object, initializes database helpers and loads the relevant user, group or channel data.
6. Enabled plugins listed in `configs.php` are included. Plugins use the framework objects to inspect the update and perform bot actions.
7. `TelegramBot` sends requests to Telegram and returns decoded API responses. Use its generic API method when a dedicated convenience wrapper is not available.

The main objects exposed to plugins by the standard entry point are:

| Variable | Purpose |
|---|---|
| `$NeleBotX` | Main framework instance and shared configuration/state |
| `$bot` | Telegram API client and messaging/helper methods |
| `$v` | Parsed update variables and shortcuts for message, user, chat and update data |
| `$db` | Database helper, when configured |
| `$user` | Current user record, when applicable |
| `$group` | Current group/supergroup record, when applicable |
| `$channel` | Current channel record, when applicable |

The exact properties available depend on the incoming update type. A callback query, inline query, channel post and ordinary message do not contain the same fields. Check for optional values before using them. See [Variables](./variables) and [Functions](./functions).

## Main project files

- `index.php` — webhook entry point and framework bootstrap.
- `configs.php` — bot, webhook, logging, plugin and optional database settings.
- `NeleBotX.php` — main framework initialization and shared objects.
- `TelegramBot.php` — Telegram HTTP client, convenience wrappers and generic API calls.
- `Variables.php` — update parsing and normalized runtime variables.
- `Database.php` — optional database abstraction.
- `AntiFlood.php` — optional Redis-backed anti-flood helper.
- `WebhookKey.php` — authenticated encryption and decryption of webhook bot keys.
- `Log.php` — external log-file initialization and PHP error-log configuration.
- `tools/webhook-manager.php` — interactive CLI for webhook setup and maintenance.
- `plugins/` — enabled bot plugins and examples.
- `tests/` — local smoke and simulated HTTP tests.

## Telegram Bot API support

This release targets **Telegram Bot API 10.4**. The framework includes convenience wrappers for selected 10.4 methods and update parsing for `widget_query` and `shared_widget_query`. The generic `TelegramBot::callApi()` method (and its `call()` alias) can be used for Bot API methods that do not have a dedicated framework wrapper. Generic access does not automatically guarantee that a method's parameters or response are validated by NeleBot X; follow Telegram's official method documentation.

The Bot API is maintained independently of this framework. A newer Telegram release does not automatically become supported just because Telegram accepts it. Check the official [Bot API changelog](https://core.telegram.org/bots/api-changelog) before relying on newer fields or methods.

## Getting started

1. Read [Setup](./setup) and verify the PHP version and required extensions in the same runtime used by PHP-FPM/Apache and CLI.
2. Review `configs.php`; do not publish your bot token or webhook secrets.
3. Configure filesystem permissions for `/var/log/bots/NeleBotX.log` as described in the setup guide.
4. Run `php tools/webhook-manager.php` as the project/deployment owner and install the webhook.
5. Enable only the plugins you intend to run and test them with a separate bot.
6. Use the [examples and developer cookbook](../plugins/commands.php) as starting points, not as a substitute for checking Telegram's current API requirements.

## Documentation

- [Setup and deployment](./setup)
- [Framework functions](./functions)
- [Runtime and configuration variables](./variables)
- [Upgrade guide](../UPGRADE.md)
- [Changelog](../CHANGELOG.md)
- [Telegram Bot API changelog](https://core.telegram.org/bots/api-changelog)

## Examples

`plugins/commands.php` contains examples for commands, message replies, inline and reply keyboards, callbacks, message editing, media, locations, polls, dice, chat/member lookups, API error handling, generic API calls and selected Bot API 10.4 methods.
