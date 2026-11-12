# Changelog

All notable changes to NeleBot X are recorded here. Version 2.0 is a major release: review the [upgrade guide](UPGRADE.md) before replacing an existing installation.

## [2.0] - 2026-11-12

### Breaking changes

- **Webhook authentication redesigned:** removed the fixed `crypt(..., 'NeleBotX')` mechanism and plaintext `?token=...` webhook mode. Webhook URLs now carry an authenticated encrypted `key` generated with AES-256-GCM and an installation-specific `webhook_password`.
- **Telegram webhook secret is mandatory:** requests must include the matching `X-Telegram-Bot-Api-Secret-Token` header, validated with `hash_equals()`.
- **POST-only endpoint:** `index.php` rejects non-POST requests and no longer accepts a plaintext token in the query string.
- **Logging destination changed:** PHP logs now default to `/var/log/bots/NeleBotX.log`, outside the project/web root. The PHP service account must have appropriate write permissions; bootstrap stops if logging cannot be initialized.
- **PHP runtime baseline:** PHP 8.2+ is required by the declared project metadata; PHP 8.5 is recommended. cURL and OpenSSL extensions are required.
- **Webhook setup utility changed:** the previous standalone key-generation utility is retired in favor of `tools/webhook-manager.php`.

### Security and webhook management

- Added `WebhookKey.php` with versioned AES-256-GCM encryption, random salt and nonce, HKDF-SHA-256 key derivation and authenticated additional data.
- Added an interactive CLI webhook manager for token validation (`getMe`), initial setup, key rotation, URL update, webhook removal and status inspection.
- The manager generates independent random secrets, avoids passing the bot token as a command-line argument, URL-encodes the encrypted key, sends Telegram's `secret_token` when registering the webhook, creates timestamped configuration backups and updates `configs.php` atomically.
- Added rollback of the saved configuration when webhook installation or key rotation is rejected by Telegram.
- Added `Log.php` to configure an external log file with restrictive directory/file permissions and disable PHP error display in the webhook runtime.
- Added explicit HTTP status handling for unsupported methods, missing/invalid webhook credentials and bootstrap/log initialization failures.

### Telegram Bot API 10.4

- Updated the framework's documented target to **Telegram Bot API 10.4**, released on 2026-11-09.
- Added convenience wrappers for `sendWidget`, `sendSharedWidget`, `answerWidgetQuery`, `editMessageWidgetData`, `editSharedWidgetData`, `getChatShareThemeBackdrops`, `getUniqueGift` and `deleteBusinessAccountGift`.
- Added update parsing exposure for `widget_query` and `shared_widget_query`.
- Added the new widget update types while retaining generic update data for forward compatibility.
- Documented the Bot API 10.4 widget model and the relevant restrictions: widgets sent in production are currently limited to private chats, and widget messages cannot be copied using `copyMessage`/`copyMessages`.
- Generic `TelegramBot::callApi()` remains available for Bot API methods without a dedicated convenience wrapper; `call()` is its alias.

### Framework and HTTP client

- `TelegramBot::getConfigs()` returns the configuration held by the current instance instead of reloading `configs.php` from disk.
- Added/retained the generic `callApi(string $method, array $parameters = [], bool $post = true)` and `call()` API path for methods without dedicated wrappers.
- Improved HTTP request handling with cURL error reporting, HTTP status handling, JSON decoding checks, connection timeout configuration and GET query-string parameter handling.
- Updated webhook registration/removal handling to use `drop_pending_updates` correctly.
- Preserved unrecognized Telegram update payloads so plugins can handle update types not explicitly normalized by the framework.

### Documentation and developer experience

- Reworked the setup guide with PHP CLI versus PHP-FPM/Apache checks, extension installation, deployment permissions, external log setup, webhook-manager workflow and troubleshooting.
- Expanded the functions reference and added a comprehensive runtime/configuration variables reference.
- Expanded `docs/index.md` with the framework lifecycle, main objects, file map, Bot API support notes and a plain-language explanation of vibe coding and its limitations.
- Rewrote `UPGRADE.md` with migration steps for old webhook URLs, configuration, logging, PHP requirements, plugins, database/Redis use and rollback.
- Expanded `plugins/commands.php` into a developer cookbook with examples for commands, replies, keyboards, callbacks, edits, media, locations, polls, dice, chat/member lookups, error handling, generic API calls and selected Bot API 10.4 methods.
- Expanded `/help`, added `/examples` as a direct command and retained the interactive example flow for command and callback entry points.

### Tests and validation

- Added/updated smoke tests, webhook-manager tests and a local HTTP integration harness using simulated Bot API responses.
- Added a PHP 8.2–8.5 CI matrix.
- Tests exercise selected paths only. They are not exhaustive plugin/database regression tests, do not constitute a full security audit and do not guarantee production safety.
- The recorded local runtime was PHP 8.4.24 without native cURL. PHP 8.5 and native-cURL execution require verification in a matching runtime/CI environment.

### Security and configuration

- Replaced the fixed webhook `crypt(..., 'NeleBotX')` check with authenticated AES-256-GCM encryption using an installation-specific `webhook_password`.
- Added encrypted webhook-key generation and validation; invalid, tampered or incorrectly encrypted keys are rejected.
- Removed plaintext `?token=...` webhook mode and required Telegram's `secret_token` header.
- Added fixed-token validation when `configs.php` contains a token, while allowing encrypted-key token selection for intentional multi-bot deployments when the configured token is empty.
- Added OpenSSL as a requirement for webhook-key encryption/decryption.
- Documented the AI-assisted/vibe-coding development process and the need for independent review.

## Earlier modernization work

The following work preceded the 1.8 release series and is retained in the 2.0 codebase:

- `TelegramBot::getConfigs()` returns the configuration injected into the instance rather than reloading the configuration file.
- Updated the Telegram Bot API target to 10.3 before the 10.4 update in 2.0.
- Added generic Bot API access through `TelegramBot::callApi()` and the `call()` alias.
- Improved cURL error handling, JSON decoding, HTTP error reporting, connection timeout and GET query parameter handling.
- Updated legacy endpoint wrappers and preserved unknown Telegram update payloads for forward compatibility.
- Added local smoke tests, a fake Bot API HTTP integration setup and a PHP 8.2–8.5 CI matrix.

## Versioning notes

- **2.0** changes the webhook request contract and default logging destination. Existing deployments must follow `UPGRADE.md`.
- Telegram Bot API support refers to the target version and the framework's exposed parsing/wrappers. It does not mean every Bot API method has a dedicated NeleBot X wrapper; use `callApi()` for other methods and consult Telegram's official documentation.
- The official Bot API changelog is maintained by Telegram: https://core.telegram.org/bots/api-changelog
