# Upgrade guide — NeleBot X 2.0

This guide describes the migration from the older NeleBot X codebase to 2.0. Version 2.0 is not a drop-in replacement for an old deployment: the webhook authentication contract, configuration secrets, logging destination and runtime requirements have changed. Plan a controlled migration and test each bot before switching production traffic.

> **Vibe coding notice:** this release was developed with AI-assisted, natural-language-driven coding. The available syntax, smoke and simulated HTTP tests cover selected paths only. They do not replace a code review, an independent security audit or application-specific regression tests.

## 1. Before upgrading

1. Put the bot into a maintenance window or prepare a second endpoint so that you can roll back.
2. Back up the complete old project, `configs.php`, database files/dumps, Redis data if relevant, plugin files and web-server configuration.
3. Record the current webhook URL, allowed update types, plugin list, PHP version and enabled PHP extensions.
4. Make a list of custom changes made directly to framework core files. Reapply those changes deliberately; do not overwrite them blindly.
5. Prepare PHP 8.2 or newer. PHP 8.5 is recommended. Both the web runtime and CLI runtime must have `curl` and `openssl` enabled.
6. Test the migration with a separate Telegram bot first.

Do not copy the old `configs.php` over the new one without reviewing it. Do not copy old framework core files over the 2.0 release.

## 2. Breaking changes

### 2.1 Webhook URL and authentication

The old fixed `crypt(..., 'NeleBotX')` mechanism and plaintext `?token=...` webhook URLs are no longer supported. Version 2.0 expects an encrypted `key` query parameter and two installation-specific secrets:

- `webhook_password` — password used to derive the AES-256-GCM encryption key for the encrypted bot token.
- `webhook_secret_token` — secret that Telegram must send in the `X-Telegram-Bot-Api-Secret-Token` HTTP header.

The two values must be different, random and kept private. The webhook endpoint rejects requests without the secret header, with an invalid header, with an invalid encrypted key, or with a token that does not match the configured fixed token.

**Migration:** do not try to preserve old encrypted URLs. Run the new manager and register a new webhook:

```bash
php tools/webhook-manager.php
```

Choose **Install / configure webhook** and follow the prompts. The manager validates the token with Telegram `getMe`, creates the secrets, backs up `configs.php`, saves the settings, generates the encrypted URL key and calls `setWebhook`. If Telegram rejects the registration, the manager attempts to restore the previous configuration. Review the resulting status before switching traffic.

If you operate one bot, put its token in `configs.php` under `token` (or let the manager configure it). If you intentionally use one codebase for multiple bots, leave `token` empty and create a separate encrypted key for each bot. In multi-bot mode, protect each URL and ensure that only trusted systems can invoke the endpoint.

### 2.2 POST-only webhook endpoint

The entry point now accepts `POST` requests only. A browser visit or a `GET` request to the webhook endpoint is expected to receive HTTP 405. Telegram's secret-token header is mandatory; requests without it are rejected. The old practice of placing a plaintext token in the URL must be removed from monitoring tools, cron jobs, reverse proxies and deployment scripts.

### 2.3 Logging moved outside the project directory

The default log path is now:

```text
/var/log/bots/NeleBotX.log
```

The PHP service account must be able to write to the log directory/file. Create the directory and set ownership for the actual PHP-FPM/Apache user as described in `docs/setup.md`. Do not solve permission problems with `chmod 777` and do not make the whole project writable by the web server.

The entry point stops with HTTP 500 if logging cannot be initialized. PHP error display is disabled for the webhook runtime; inspect the configured log file and the web-server error log when troubleshooting.

### 2.4 Runtime and extensions

- PHP 8.2 is the minimum declared version; PHP 8.5 is recommended.
- `ext-curl` is required for Telegram HTTP requests and the webhook manager.
- `ext-openssl` is required for encrypted webhook keys.
- PDO drivers are needed only for the database backend you enable.
- The Redis extension/server are needed only if Redis features are enabled.

Check the PHP version and extensions separately for CLI and PHP-FPM/Apache. A successful `php -m` in a shell does not prove that the web runtime has the same modules.

## 3. Configuration migration

Start from the new 2.0 `configs.php` and merge your old values into it manually. Preserve the new keys and review each setting rather than copying the old array wholesale.

| Setting | Migration action |
|---|---|
| `token` | Set the expected bot token for a single-bot installation. Leave empty only for intentional multi-bot mode. Never put it in the webhook URL. |
| `webhook_password` | Generate/configure through `tools/webhook-manager.php`; do not reuse a public or old hard-coded value. |
| `webhook_secret_token` | Generate/configure through the manager and ensure Telegram's webhook registration uses the same value. |
| `telegram_bot_api` | Keep the official API URL unless you intentionally use a trusted local Bot API server. |
| `log_file` | Defaults to `/var/log/bots/NeleBotX.log`; create the directory and grant the PHP service account write access. |
| `version` | Set to `2.0`. |
| `plugins`, `admins`, `parse_mode`, `post`, `timeout`, `response` | Merge your existing values after checking their meaning and the updated examples. |
| `redis`, `database` | Preserve only the backend settings you actually use and test the connection/schema with a backup. |

The existing configuration contains the legacy key `allow_sending_w/out_reply`; the Telegram client also checks the correctly spelled `allow_sending_without_reply` key first and falls back to the legacy spelling. Avoid introducing a new spelling into custom code without checking which setting your deployment uses.

## 4. Framework and plugin code to review

### 4.1 Custom webhook/bootstrap code

If you had a custom `index.php`, webhook router or bootstrap script, compare it with the new entry point. It must load the new logging and webhook-key components, enforce POST and secret-header validation, decrypt `key`, and initialize `NeleBotX` with the resulting configuration. Do not retain an old token query-string fallback.

### 4.2 Direct construction of framework classes

`NeleBotX` accepts the configuration array and can optionally skip reading the incoming update using its second constructor argument. `TelegramBot` receives the configuration array; `getConfigs()` returns the configuration held by that instance rather than reloading `configs.php`. If your plugin or external script expected `getConfigs()` to reread a file after it had been changed on disk, update that code to call `editConfigs()` or create a new instance with the desired configuration.

### 4.3 Telegram API calls

`TelegramBot::callApi(string $method, array $parameters = [], bool $post = true)` is the generic route for Bot API methods without a dedicated convenience wrapper. `call()` is an alias. Existing helper methods remain available, but custom code should be checked against their current parameter order and Telegram's current method requirements. Do not assume that the presence of a generic API call means the framework validates every method-specific parameter.

The target is now Bot API 10.4. The new convenience wrappers include:

- `sendWidget()`
- `sendSharedWidget()`
- `answerWidgetQuery()`
- `editMessageWidgetData()`
- `editSharedWidgetData()`
- `getChatShareThemeBackdrops()`
- `getUniqueGift()`
- `deleteBusinessAccountGift()`

Update parsing exposes `widget_query` and `shared_widget_query`. If your plugin switches on update types, add handling for these types where relevant and retain a safe default branch for unknown/future update types.

### 4.4 Runtime variables and update parsing

Review plugins that read properties from `Variables` directly. Telegram update objects have different shapes and many fields are optional. Add `isset()`/`??` checks before accessing optional values, and test message, edited message, callback query, inline query, channel post and service-message paths that your bot uses. Consult `docs/variables.md` for the current documented properties and known parsing limitations; documentation does not imply that every legacy parser edge case has been corrected.

### 4.5 Database and Redis

Do not assume that a core upgrade automatically migrates your database schema or Redis contents. Back up the data, compare the table definitions and the code paths used by your bot, then test reads and writes with a copy of production data. Enable only the backend you use. Verify Redis connectivity and anti-flood behavior separately.

### 4.6 Plugins and relative paths

Keep custom plugins under `plugins/` or update the configured paths deliberately. Check plugins for assumptions about the working directory, old webhook query parameters, removed utilities, old configuration keys, and direct edits to core classes. Run every enabled plugin in a staging bot; the framework's bundled examples are not a regression suite for custom plugins.

## 5. New webhook manager and retired utility

Use `tools/webhook-manager.php` for installation/configuration, key rotation, URL updates, webhook removal and status checks. Run it as the deployment/project owner, not as root by default. It requires writable access to the configuration file and the required PHP extensions. It creates timestamped backups before changing the configuration.

The standalone `tools/generate-webhook-key.php` utility from earlier versions is retired. Replace scripts or documentation that call it with the manager workflow. Do not pass bot tokens as command-line arguments or store them in shell history.

## 6. Recommended migration sequence

1. Extract 2.0 into a new directory; keep the old release intact for rollback.
2. Install/check PHP 8.2+ and `curl`/`openssl` in CLI and web runtimes.
3. Merge configuration values into the new `configs.php` without copying old webhook credentials.
4. Configure `/var/log/bots/NeleBotX.log` ownership and permissions for the PHP service account.
5. Copy custom plugins and assets; review each plugin for the breaking changes above.
6. Run syntax and local tests:

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
php tests/smoke.php
php tests/webhook-manager.php
bash tests/run-integration.sh
```

7. Use a test bot to run `php tools/webhook-manager.php` and register the new webhook.
8. Test messages, callbacks, inline queries, media, database operations and any bot-specific workflows.
9. Inspect PHP and web-server logs; verify that invalid methods, missing secret headers and invalid keys are rejected.
10. Switch production traffic only after the staging checks pass. Keep the old deployment and backups until the new version has been observed working reliably.

## 7. Rollback

Keep the old project, old configuration and database backup untouched until migration is accepted. To roll back, restore the previous code and its matching configuration together, then re-register the old webhook URL using the old release's procedure. Do not combine 2.0's `key` URL with an old entry point or an old plaintext-token URL with the 2.0 entry point. If the bot token or webhook secrets were exposed during migration, rotate them before returning to service.

## 8. Validation limits

The release includes syntax checks, smoke tests, webhook-manager tests and a simulated HTTP integration harness. The local development runtime used for the recorded test run was PHP 8.4.24 without native cURL; PHP 8.5 and native-cURL execution must be verified in a matching environment/CI. No live Telegram API calls are part of the local fake-server integration tests, and no complete independent security audit has been performed.
