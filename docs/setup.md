# Secure Installation and Configuration

This guide walks you through everything from server preparation to the bot's first call. The commands are designed for Debian/Ubuntu; user and service names may vary on other distributions.

> **Version Note:** NeleBot X 2.0 was built with vibe coding support. Syntax checks, smoke tests, and simulated HTTP tests are included, but it has not undergone an independent security audit or exhaustive testing of every plugin and backend. Before production, review the code, use a test bot, and keep a backup.

## 1. Requirements

- PHP **8.2 or higher** (PHP 8.5 recommended).
- Mandatory PHP extensions: `curl` and `openssl`.
- Telegram bot created via [@BotFather](https://t.me/BotFather).
- Public webhook URL reachable by Telegram via **HTTPS** with a valid certificate.
- Outbound HTTPS access to `api.telegram.org`.
- PDO only if enabling SQLite, MySQL, or PostgreSQL; Redis extension only if enabling Redis.
- Shell access to run the Webhook Manager and tests.

Check the CLI version and extensions:

```bash
php -v
php -m | grep -Ei '^(curl|openssl|PDO|redis)$'
```

The CLI and PHP-FPM/Apache may use different versions or `php.ini` files. Also verify the web runtime with a temporary, protected diagnostic page, and remove it immediately afterward. Do not leave `phpinfo()` publicly accessible.

On Debian/Ubuntu, for example:

```bash
sudo apt update
sudo apt install php-cli php-curl php-openssl
```

Also install PHP-FPM or the Apache PHP module, depending on your server configuration. If you are using a specific PHP version, install the extensions for that exact version.

## 2. Placing the Framework

Extract or clone the project into a dedicated directory. Example:

```text
/var/www/nelebotx/
├── index.php
├── configs.php
├── tools/
├── plugins/
└── ...
```

Configure the HTTPS virtual host so that the domain points to the correct directory and forwards PHP files to PHP-FPM or the Apache PHP module. Do not place backups, logs, dumps, temporary files, or credentials in a publicly downloadable directory.

## 3. Users and Permissions: Recommended Model

**Do not assign the entire project to the web server user, and do not use `chmod 777`.** It is preferable that:

- a deploy user (e.g., `deploy`) owns the files and can update them;
- the PHP/web service group (often `www-data`) can only read the code;
- `configs.php` is readable by the PHP process, but not writable by the web server;
- the Webhook Manager is executed by the project owner user, not by `www-data` and not as `root`.

### Identifying the Effective PHP User

Do not assume Nginx or Apache run PHP directly. With PHP-FPM, the user configured in the FPM pool matters.

```bash
ps -eo user,group,comm | grep -E 'php-fpm|apache2|nginx'
```

For PHP-FPM, also check the pool configuration, often found in `/etc/php/<version>/fpm/pool.d/www.conf`, looking for `user` and `group`. With Nginx, the Nginx process may have a different user than PHP-FPM: the PHP-FPM user matters for reading `configs.php` and writing logs.

| System/Configuration | Frequent User or Group |
|---|---|
| Debian/Ubuntu, Nginx + PHP-FPM | `www-data` |
| Debian/Ubuntu, Apache + PHP-FPM | `www-data` |
| Debian/Ubuntu, Apache mod_php | `www-data` |
| RHEL / Rocky / AlmaLinux, Nginx | `nginx` |
| RHEL / Rocky / AlmaLinux, Apache | `apache` |

In the examples, replace `deploy` with your deployment user and `www-data` with the effective group of the PHP process. If the pool uses a dedicated group, use that.

### Setting Project Owner and Permissions

Adjust the path and then run:

```bash
sudo chown -R deploy:www-data /var/www/nelebotx
sudo find /var/www/nelebotx -type d -exec chmod 0750 {} \;
sudo find /var/www/nelebotx -type f -exec chmod 0640 {} \;
```

The `deploy` user can modify the project; the PHP group can traverse directories and read files. PHP files do not need the executable bit: they are interpreted by PHP.

**Important for the Webhook Manager:** the main directory must be writable by `deploy`, because the manager creates a temporary file and replaces `configs.php` using an atomic rename. With the permissions above, the owner already has this access. Do not run `sudo php tools/webhook-manager.php`: you might create files owned by root.

Also check parent directories (`/var`, `/var/www`): PHP must be able to traverse the path, but must not be able to modify code or configuration.

### Protecting `configs.php`

```bash
sudo chown deploy:www-data /var/www/nelebotx/configs.php
sudo chmod 0640 /var/www/nelebotx/configs.php
```

Backups like `configs.php.bak.*` contain sensitive configurations: do not publish them or make them downloadable. Keep the project directory non-writable by the PHP process. If you use a different group, replace `www-data`.

## 4. Preparing the Protected Log

The default path is `/var/log/bots/NeleBotX.log`, outside the document root. It must be writable by the PHP process.

Debian/Ubuntu with PHP-FPM or Apache running as `www-data`:

```bash
sudo install -d -o www-data -g www-data -m 0750 /var/log/bots
sudo install -o www-data -g www-data -m 0640 /dev/null /var/log/bots/NeleBotX.log
```

RHEL/Rocky/AlmaLinux with PHP running as `nginx`:

```bash
sudo install -d -o nginx -g nginx -m 0750 /var/log/bots
sudo install -o nginx -g nginx -m 0640 /dev/null /var/log/bots/NeleBotX.log
```

If PHP runs as `apache`, use `apache:apache`. For a dedicated PHP-FPM pool, use that pool's user and group: PHP writes the log, not necessarily the front-end web server.

Verify, replacing the user if necessary:

```bash
sudo -u www-data test -w /var/log/bots/NeleBotX.log && echo "Log is writable"
```

If PHP cannot configure the log, the webhook returns an HTTP 500 error. You can change the path using `log_file` in `configs.php`, but always keep it outside the document root.

## 5. Configuring `configs.php`

Check at least these items:

- `token`: for a single bot, you can enter the intended token. The manager verifies that the typed one matches. Leave it blank only if you intend to use multi-bot mode with distinct encrypted webhook keys.
- `telegram_bot_api`: normally leave as `https://api.telegram.org`.
- `admins`: enter the Telegram IDs of administrators authorized by the plugins.
- `plugins`: enable only the necessary plugins.
- `log_file`: leave as `/var/log/bots/NeleBotX.log`, unless using a different protected path.
- `redis` and `database`: enable them only after configuring the respective services.
- `webhook_password` and `webhook_secret_token`: for a new installation, leave them blank; the manager will generate them.

Do not put the Telegram token in URLs, shell commands, public scripts, or repositories.

## 6. Registering the Webhook with the CLI Manager

From the project directory, run the manager as the project owner user:

```bash
cd /var/www/nelebotx
php tools/webhook-manager.php
```

If you are logged in as another user, run it as the deployment user:

```bash
sudo -u deploy -H php /var/www/nelebotx/tools/webhook-manager.php
```

The manager must be able to read `configs.php` and write to the project directory. It requires PHP CLI with `curl` and `openssl` and outbound HTTPS access to Telegram.

Choose **Install / configure webhook**:

1. Enter the bot token: on Linux terminals, it is hidden while typing.
2. The manager verifies the token via `getMe`.
3. Enter the public HTTPS endpoint URL, for example `https://bot.example.com/index.php`. Do not manually append `key` or `token`.
4. The manager generates `webhook_password` and `webhook_secret_token`, creates a backup, and updates `configs.php`.
5. Registers the webhook on Telegram. The encrypted `key` parameter is appended to the URL and automatically encoded using RFC 3986; the `secret_token` is sent in the POST body, not in the query string.

The Telegram token is used in memory and is not written to `configs.php`. `index.php` accepts only POST requests, checks the `X-Telegram-Bot-Api-Secret-Token` header, and decrypts the `key` parameter. The old method using plain text tokens `?token=...` is not supported.

### Other Operations

- **Regenerate security keys**: generates new secrets and updates the webhook. Old keys will no longer be valid.
- **Update webhook URL**: changes the endpoint while keeping current secrets.
- **Remove webhook**: removes the webhook, with an option to clear pending updates.
- **Webhook status**: displays information returned by `getWebhookInfo`, including pending updates and any errors.

The manager creates dated backups of `configs.php`: keep them with restrictive permissions. If Telegram refuses installation or rotation, the manager restores the previous configuration.

## 7. Verifying the Installation

1. From the manager menu, select **Webhook status** and check the URL.
2. Send `/start` to the bot.
3. Try `/help` and then `/examples` to view demonstrations.
4. If you do not receive updates, check `getWebhookInfo` and `/var/log/bots/NeleBotX.log`.
5. Verify that the virtual host points to the correct project and that PHP can read the files.

Do not make logs public: they may contain operational details and update data.

## 8. Running Automated Tests

```bash
php tests/smoke.php
php tests/webhook-manager.php
bash tests/run-integration.sh
```

The integration suite uses a simulated Bot API server bound to `127.0.0.1`: it does not modify DNS or `/etc/hosts` and does not contact Telegram. However, native `curl` and `openssl` extensions are still required for real usage.

## 9. Quick Troubleshooting

| Symptom | Check |
|---|---|
| Manager fails to start | Check `php -v` and ensure CLI has `curl` and `openssl`. |
| `configs.php` is not writable | Run the manager as the project owner; check file and directory permissions. Do not use `chmod 777`. |
| Webhook HTTP 403 | Secret header is missing or mismatching: use the manager to update or regenerate the webhook. |
| Webhook HTTP 401 | Encrypted `key` parameter is missing or invalid: use the URL generated by the manager. |
| Webhook HTTP 500 on startup | Check the protected log and permissions of `/var/log/bots`. |
| Telegram cannot reach the webhook | Check public DNS, HTTPS certificate, firewall, and endpoint reachability. |
| `getWebhookInfo` shows an error | Read the message reported by the manager and check PHP and virtual host logs. |

## 10. Framework Configuration Notes

`index.php` and `crontab.php` load `configs.php` only once via `require_once` and pass the array to the framework. `TelegramBot::getConfigs()` returns the already loaded configuration and does not include the file again. If you create a custom entry point, load the configuration once and pass the array to the constructor.