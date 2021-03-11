# Setup

1. Get your API token from @BotFather: start the bot and read the message to create your own Bot.
2. Set your Webhook in **one** of these ways:
 - **Multi Bot method:** Call a curl request via bash like <code>curl https://api.telegram.org/bot\<TOKEN>/setWebhook?url=https://your-domain.com/NeleBotX/index.php?token=\<TOKEN></code> and go.
 - **Single Safe Bot method**: Create a script to [crypt](https://www.php.net/manual/en/function.crypt) your token with the key word '<code>NeleBotX</code>'. Then set this key word on token in your configs.php and call a curl request via bash like <code>curl https://api.telegram.org/bot\<TOKEN>/setWebhook?url=https://your-domain.com/NeleBotX/index.php?key=\<TOKEN></code>

Hint: _If the Bot doesn't start check your error_log file and be sure that php can write in it!_
