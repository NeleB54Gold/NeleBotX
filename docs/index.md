# NeleBot X Framework

Develop your own Telegram Bot in PHP with NeleBot X!

### Requirements

- API token from [@BotFather](t.me/BotFather) **[Required]**
- Local Telegram Bot API or a webhost with SSL. **[Required]**
- PHP 7 recommented!
- PHP Extension: [cURL](https://www.php.net/manual/en/book.curl.php) **[Required]**
- PHP Extension: [PDO](https://www.php.net/manual/en/book.pdo.php) *[Required only for SQLite/MySQL/PostgreSQL database]*
- PHP Extension: [Redis](https://github.com/phpredis/phpredis) *[Required only for Redis database]*

### Setup

# Get the API Token 

Go to @BotFather on Telegram, send /newbot and follow the steps to create your bot, in the end it will give you an API token to access the Telegram Bot API.

# Edit your configurations

Go to NeleBot X sources and edit configs.php according to the [configs](./variables#configs) variables!

# Set Webhook

For greater security it's recommended to manually set the webhook via URL. 
See [setWebhook](https://core.telegram.org/bots/api#setwebhook) for more info.

### How to use

See all methods and functions [here](./docs/functions.md) and variables [here](./docs/variables.md).

## About NeleBot X

[Telegram Channel](https://t.me/NeleBotX)

[Telegram Chat](https://t.me/NeleBotXSupport)
