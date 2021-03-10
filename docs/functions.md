# Classes and functions

- [NeleBotX](#nelebotx)
- [AntiFlood](#antiflood)
- [Database](#database)
- [Variables](#variables)
- [TelegramBot](#telegrambot)

# NeleBotX

This is the class that start the Framework.

### NeleBotX::__construct

Start the NeleBotX class: virify the token, start other classes and load user and group or channel.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$configs](./variables.md#configs) | Array | Yes | Contains all variables for the Framework |

### NeleBotX::loadUser

Return an array of current user automatically generated from the update or load from database if available.

### NeleBotX::loadGroup

Return an array of current group automatically generated from the update or load from database if available.

### NeleBotX::loadChannel

Return an array of current channel automatically generated from the update or load from database if available.

# AntiFlood

### AntiFlood::__construct

Calculate the flood via Redis. 

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$database](#Database) | Class | Yes | Contains Database class |
| $id | Integer | Yes | Contains Telegram ID |

# Database

### Database::__construct

Start connection to SQLite/MySQL/PostrgeSQL or/and Redis.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$configs](./variables.md#configs) | Array | Yes | Contains all variables for the Framework |

### Database::redisConnect

Try to connect to the Redis server.

### Database::rget

Try to [get](https://github.com/phpredis/phpredis#get) value of a key from Redis.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$key](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Key |

### Database::rset

Try to [set](https://github.com/phpredis/phpredis#set) the string value as value of key.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$key](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Key |
| [$value](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Value |
| [$time](https://github.com/phpredis/phpredis#set) | Number or Array | Optional | Timeout or Options Array |

### Database::rdel

Try to [delete](https://github.com/phpredis/phpredis#del-delete-unlink) a key from Redis.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$key](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Key |

### Database::rkeys

Try to get [keys](https://github.com/phpredis/phpredis#keys-getkeys) that match a certain pattern from Redis.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$key](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Key |

### Database::rladd

Try to [lpush](https://github.com/phpredis/phpredis#lpush): adds one or more values to the head of a LIST from Redis.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$key](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Key |
| [$value](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Value |

### Database::rlget

Try to get the [list](https://github.com/phpredis/phpredis#lrange-lgetrange) by key from Redis.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$key](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Key |
| [$offset](https://github.com/phpredis/phpredis#lrange-lgetrange) | Integer | Optional | Start of the range |
| [$offset](https://github.com/phpredis/phpredis#lrange-lgetrange) | Integer | Optional | End of the range |

### Database::rldel

Try to [remove](https://github.com/phpredis/phpredis#lrem-lremove) values from list by key and value from Redis.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$key](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Key |
| [$value](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Value |
| [$count](https://github.com/phpredis/phpredis#lrange-lgetrange) | Integer | Optional | Count of values to remove |

### Database::dbConnect

Start SQLite/MySQL/PostgreSQL database connection by configs. Require [PDO class](https://www.php.net/manual/en/book.pdo.php).

### Database::sqliteConnect

Start SQLite database connection. Require [PDO_SQLITE](https://www.php.net/manual/en/ref.pdo-sqlite.php) driver.

### Database::mysqlConnect

Start MySQL database connection. Require [PDO_MYSQL](https://www.php.net/manual/en/ref.pdo-mysql.php) driver.

### Database::mysqlConnect

Start PostgreSQL database connection. Require [PDO_PGSQL](https://www.php.net/manual/en/ref.pdo-pgsql.php) driver.

### Database::query

Custom general unique query with prepare on PDO connection.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$query](https://www.php.net/manual/en/class.pdostatement.php) | String | Yes | SQL statement |
| [$args](https://www.php.net/manual/en/pdostatement.execute) | Array | Optional | An array of values with as many elements as there are bound parameters in the SQL statement being executed. |
| $return | Integer | Optional | Return of contents can be 0 to return only true on success, 1 to [fetch](https://www.php.net/manual/en/pdostatement.fetch) with FETCH_ASSOC, or 2 to [fetchAll](https://www.php.net/manual/en/pdostatement.fetchall.php) |

### Database::limit

Create the LIMIT SQL statement. Return as String that contain the limit-offset SQL statement.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$limit](https://github.com/phpredis/phpredis#keys-and-strings) | <code>ALL</code> or Integer | Optional | Start of the range |
| [$offset](https://github.com/phpredis/phpredis#keys-and-strings) | Integer | Optional | End of the range |

### Database::setup

Create default Framework tables. Return an Array of query results.

### Database::createTemplateTable

Get the SQL Statement tamplate of a Framework table.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$table](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Table name |

### Database::getUser

Get user variables and automatically insert it in the database if not exists.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$user](https://core.telegram.org/bots/api#user) | Array | Yes | Telegram User informations |

### Database::getGroup

Get group variables and automatically insert it in the database if not exists.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$chat](https://core.telegram.org/bots/api#chat) | Array | Yes | Telegram Chat informations |

### Database::getChannel

Get channel variables and automatically insert it in the database if not exists.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$chat](https://core.telegram.org/bots/api#chat) | Array | Yes | Telegram Chat informations |

### Database::getChatsByAdmin

Get groups and channels variables by a Telegram User ID that is an administrator in it.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $user_id | Integer | Yes | Telegram User ID |
| $limit | Integer | Optional | Limit for the query |

### Database::ban

Ban a chat from the Bot. Bot's admins excluded.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Integer | Yes | Chat ID |

### Database::unban

Unban a chat from the Bot.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Integer | Yes | Chat ID |

### Database::isBanned

Check if a chat has been banned. Return an array with ban for table.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Integer | Yes | Chat ID |

### Database::getLanguage

Get the user language.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Integer | Yes | User ID |

### Database::setStatus

Set user status on database.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Integer | Yes | User ID |
| $status | String | Optional | Status of a user on your Bot |

# Variables

For all var functions see the Telegram [Available types](https://core.telegram.org/bots/api#available-types).

### Variables::__construct

Create all variables that can exists in the update.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$configs](./variables.md#configs) | Array | Yes | Contains all variables for the Framework |
| [$update](https://core.telegram.org/bots/api#update) | Array | Yes | Contains all variables from the Telegram update |

### Variables::isAdmin

Check if the user is an administrator of the Bot by configs.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Integer | Optional | User ID |

### Variables::getUser

Get the current user by the update variables. Return *null* if not found.

### Variables::getGroup

Get the current group by the update variables. Return *null* if not found.

### Variables::getChannel

Get the current channel by the update variables. Return *null* if not found.

### Variables::getGroupsPerms

Get the default Telegram [permissions](https://core.telegram.org/bots/api#chatpermissions) for only groups.

### Variables::getChannelsPerms

Get the default [Telegram permissions](https://core.telegram.org/bots/api#chatpermissions) for only channels.

# TelegramBot

To create new functions you can check the Telegram [methods](https://core.telegram.org/bots/api#available-methods)!

### TelegramBot::__construct

Start the class to make requests.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$configs](./variables.md#configs) | Array | Yes | Contains all variables for the Framework |

### TelegramBot::editConfigs

It only changes the script configurations, not the file. 

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $key | Array | Yes | [$configs](./variables#configs) key |
| $value | All | Yes | Value |

### TelegramBot::getConfigs

Get the current script configurations. Return [$configs](./variables#configs).

### TelegramBot::sendLog

Send logs to the logs chat.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $message | String | Yes | Error message |

### TelegramBot::request

Make cURL requests. See how can you make [requests](https://core.telegram.org/bots/faq#how-can-i-make-requests-in-response-to-updates) in response to Telegram updates.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $url | String | Yes | Request url |
| $args | Array | Optional | Arguments for the request |
| $post | Boolean or <code>def</code> | Optional | *True* to post, *false* to get or <code>def</code> by default from configs |

### TelegramBot::getUpdate

Return the current [update](https://core.telegram.org/bots/api#update).

### TelegramBot::api

Create the Telegram Bot API url for any method. 
See the [Local API Server](https://core.telegram.org/bots/api#do-i-need-a-local-bot-api-server) to have [features](https://core.telegram.org/bots/api#using-a-local-bot-api-server).

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $method | String | Yes | Telegram Bot API [method](https://core.telegram.org/bots/api#available-methods) |

### TelegramBot::getMe

A simple method for testing your bot's auth token.
Returns basic information about the bot in form of a [User](https://core.telegram.org/bots/api#user) object.

### TelegramBot::logOut
Use this method to log out from the cloud Bot API server before launching the bot locally.
After a successful call, you can immediately log in on a local server, but will not be able to log in back to the cloud Bot API server for 10 minutes.
See the Bot [Log out](https://core.telegram.org/bots/api#logout).

### TelegramBot::close

Use this method to close the bot instance before moving it from one local server to another. 
See [Close](https://core.telegram.org/bots/api#close) docs.

### TelegramBot::sendMessage

Use this method to send text messages. On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $text | String | Yes | Text of the message to be sent, 1-4096 characters after entities parsing |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String or Array | Optional | String: Mode for parsing entities in the message text. See [formatting options](https://core.telegram.org/bots/api#formatting-options) for more details. Array: List of special entities that appear in message text, which can be specified instead of parse_mode |
| $preview | Boolean or 'def' | Optional | Disables link previews for links in this message |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::forwardMessage

Use this method to forward messages of any kind. On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $from_id | Integer or String | Yes | Unique identifier for the chat where the original message was sent (or channel username in the format @channelusername) |
| $id | Integer | Yes | Message identifier in the chat specified in from_chat_id |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::copyMessage

Use this method to copy messages of any kind. The method is analogous to the method [forwardMessages](https://core.telegram.org/bots/api#forwardmessage), but the copied message doesn't have a link to the original message. Returns the [MessageId](https://core.telegram.org/bots/api#messageid) of the sent message on success.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $from_id | Integer or String | Yes | Unique identifier for the chat where the original message was sent (or channel username in the format @channelusername) |
| $id | Integer | Yes | Message identifier in the chat specified in from_chat_id |
| $caption | String | Optional | String: New caption for media, 0-1024 characters after entities parsing. If not specified, the original caption is kept. |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String or Array | Optional | String: Mode for parsing entities in the new caption. See formatting options for more details. Array: List of special entities that appear in the new caption, which can be specified instead of parse_mode |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendPhoto

Use this method to send photos. On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $document | [InputFile](https://core.telegram.org/bots/api#inputfile) or String | Yes | Photo to send. Pass a file_id as String to send a photo that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a photo from the Internet, or upload a new photo using multipart/form-data. |
| $caption | String | Optional | Photo caption (may also be used when resending photos by file_id), 0-1024 characters after entities parsing |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String or Array | Optional | String: Mode for parsing entities in the new caption. See formatting options for more details. Array: List of special entities that appear in the new caption, which can be specified instead of parse_mode |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendAudio

Use this method to send audio files, if you want Telegram clients to display them in the music player. Your audio must be in the .MP3 or .M4A format. On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned. Bots can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
For sending voice messages, use the sendVoice method instead.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $document | [InputFile](https://core.telegram.org/bots/api#inputfile) or String | Yes | Audio file to send. Pass a file_id as String to send an audio file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get an audio file from the Internet, or upload a new one using multipart/form-data |
| $caption | String | Optional | Audio caption, 0-1024 characters after entities parsing |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String or Array | Optional | String: Mode for parsing entities in the new caption. See formatting options for more details. Array: List of special entities that appear in the new caption, which can be specified instead of parse_mode |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendDocument

Use this method to send general files. On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $document | [InputFile](https://core.telegram.org/bots/api#inputfile) or String | Yes | File to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data |
| $caption | String | Optional | Audio caption, 0-1024 characters after entities parsing |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String or Array | Optional | String: Mode for parsing entities in the new caption. See formatting options for more details. Array: List of special entities that appear in the new caption, which can be specified instead of parse_mode |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $thumb | [InputFile](https://core.telegram.org/bots/api#inputfile) or String | Optional | Thumbnail of the file sent; can be ignored if thumbnail generation for the file is supported server-side. The thumbnail should be in JPEG format and less than 200 kB in size. A thumbnail's width and height should not exceed 320. Ignored if the file is not uploaded using multipart/form-data. Thumbnails can't be reused and can be only uploaded as a new file, so you can pass “attach://<file_attach_name>” if the thumbnail was uploaded using multipart/form-data under <file_attach_name>. |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendVideo

Use this method to send video files, Telegram clients support mp4 videos (other formats may be sent as [Document](https://core.telegram.org/bots/api#document)). On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $document | [InputFile](https://core.telegram.org/bots/api#inputfile) or String | Yes | Video to send. Pass a file_id as String to send a video that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a video from the Internet, or upload a new video using multipart/form-data |
| $caption | String | Optional | Audio caption, 0-1024 characters after entities parsing |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String or Array | Optional | String: Mode for parsing entities in the new caption. See formatting options for more details. Array: List of special entities that appear in the new caption, which can be specified instead of parse_mode |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendAnimation

Use this method to send animation files (GIF or H.264/MPEG-4 AVC video without sound). On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $document | [InputFile](https://core.telegram.org/bots/api#inputfile) or String | Yes | Animation to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data |
| $caption | String | Optional | Audio caption, 0-1024 characters after entities parsing |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String or Array | Optional | String: Mode for parsing entities in the new caption. See formatting options for more details. Array: List of special entities that appear in the new caption, which can be specified instead of parse_mode |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendVoice

Use this method to send audio files, if you want Telegram clients to display the file as a playable voice message. For this to work, your audio must be in an .OGG file encoded with OPUS (other formats may be sent as [Audio](https://core.telegram.org/bots/api#audio) or [Document](https://core.telegram.org/bots/api#document)). On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned. 

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $document | [InputFile](https://core.telegram.org/bots/api#inputfile) or String | Yes | Voice to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data |
| $caption | String | Optional | Audio caption, 0-1024 characters after entities parsing |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String or Array | Optional | String: Mode for parsing entities in the new caption. See formatting options for more details. Array: List of special entities that appear in the new caption, which can be specified instead of parse_mode |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendVideoNote

Use this method to send video messages. On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $document | [InputFile](https://core.telegram.org/bots/api#inputfile) or String | Yes | Video note to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data |
| $caption | String | Optional | Audio caption, 0-1024 characters after entities parsing |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String or Array | Optional | String: Mode for parsing entities in the new caption. See formatting options for more details. Array: List of special entities that appear in the new caption, which can be specified instead of parse_mode |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendMediaGroup

Use this method to send a group of photos, videos, documents or audios as an album. Documents and audio files can be only grouped in an album with messages of the same type. On success, an array of [Message](https://core.telegram.org/bots/api#message) that were sent is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| [$documents](https://core.telegram.org/bots/api#sendmediagroup) | Array of InputMediaAudio, InputMediaDocument, InputMediaPhoto and InputMediaVideo | Yes | A JSON-serialized array describing messages to be sent, must include 2-10 items |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendLocation

Use this method to send point on the map. On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $lati | Float number | Yes | Latitude of the location |
| $long | Float number | Yes | Longitude of the location |
| [$live](https://telegram.org/blog/live-locations) | Integer | Optional | Period in seconds for which the location will be updated (see [Live Locations](https://telegram.org/blog/live-locations)), should be between 60 and 86400. |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::editLiveLocation

Use this method to edit live location messages. A location can be edited until its live_period expires or editing is explicitly disabled by a call to [stopMessageLiveLocation](https://core.telegram.org/bots/api#stopmessagelivelocation). On success, if the edited message is not an inline message, the edited [Message](https://core.telegram.org/bots/api#message) is returned, otherwise True is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $message_id | Integer or String | Yes | Integer: Required if inline_message_id is not specified. Identifier of the message to edit. String: Required if chat_id and message_id are not specified. Identifier of the inline message |
| $lati | Float number | Yes | Latitude of the location |
| $long | Float number | Yes | Longitude of the location |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::stopLiveLocation

Use this method to stop updating a live location message before live_period expires. On success, if the message was sent by the bot, the sent [Message](https://core.telegram.org/bots/api#message) is returned, otherwise True is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $message_id | Integer or String | Yes | Integer: Required if inline_message_id is not specified. Identifier of the message to edit. String: Required if chat_id and message_id are not specified. Identifier of the inline message |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendVenue

Use this method to send information about a venue. On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $lati | Float number | Yes | Latitude of the venue |
| $long | Float number | Yes | Longitude of the venue |
| $title | String | Yes | Name of the venue |
| $address | String | Yes | Address of the venue |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendContact

Use this method to send phone contacts. On success, the sent [Message](https://core.telegram.org/bots/api#message) is returned.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $number | String | Yes | Contact's phone number |
| $first_name | String | Yes | Contact's first name |
| $last_name | String | Optional | Contact's last name |
| $vcard | String | Optional | Additional data about the contact in the form of a [vCard](https://en.wikipedia.org/wiki/VCard), 0-2048 bytes |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendAction

Use this method when you need to tell the user that something is happening on the bot's side. The status is set for 5 seconds or less (when a message arrives from your bot, Telegram clients clear its typing status). Returns *True* on success.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format <code>@channelusername</code>) |
| $action | String | Optional | Type of action to broadcast. Choose one, depending on what the user is about to receive: *typing* for text messages, *upload_photo* for photos, *record_video* or *upload_video* for videos, *record_voice* or *upload_voice* for voice notes, *upload_document* for general files, *find_location* for location data, *record_video_note* or *upload_video_note* for video notes. Default to *typing* |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::getUserPhotos

Use this method to get a list of profile pictures for a user. Returns a [UserProfilePhotos](https://core.telegram.org/bots/api#userprofilephotos) object.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $user_id | Integer | Yes | Unique identifier of the target user |
| $offset | Integer | Optional | Sequential number of the first photo to be returned. By default, all photos are returned. |
| $limit | Integer | Optional | Limits the number of photos to be retrieved. Values between 1-100 are accepted. Defaults to 100. |

### TelegramBot::getFile

Use this method to get basic info about a file and prepare it for downloading. For the moment, bots can download files of up to 20MB in size. On success, a File object is returned. The file can then be downloaded via the link *https://api.telegram.org/file/bot\<token>/<file_path>*, where *<file_path>* is taken from the response. It is guaranteed that the link will be valid for at least 1 hour. When the link expires, a new one can be requested by calling getFile again.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$file_id](https://core.telegram.org/bots/api#getfile) | String | Yes | File identifier to get info about |

### TelegramBot::kickMember

Use this method to kick a user from a group, a supergroup or a channel. In the case of supergroups and channels, the user will not be able to return to the chat on their own using invite links, etc., unless unbanned first. The bot must be an administrator in the chat for this to work and must have the appropriate admin rights. Returns *True* on success.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target group or username of the target supergroup or channel (in the format <code>@channelusername</code>) |
| $user_id | Integer | Yes | Unique identifier of the target user |
| $delete_all_from | Boolean | Optional | Pass True to delete all messages from the chat for the user that is being removed. If False, the user will be able to see messages in the group that were sent before the user was removed. Always True for supergroups and channels. |
| $until_date | Integer | Optional | Date when the user will be unbanned, unix time. If user is banned for more than 366 days or less than 30 seconds from the current time they are considered to be banned forever. Applied for supergroups and channels only. |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::unbanMember

Use this method to unban a previously kicked user in a supergroup or channel. The user will not return to the group or channel automatically, but will be able to join via link, etc. The bot must be an administrator for this to work. By default, this method guarantees that after the call the user is not a member of the chat, but will be able to join it. So if the user is a member of the chat they will also be **removed** from the chat. If you don't want this, use the parameter *only_if_banned*. Returns True on success.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target group or username of the target supergroup or channel (in the format <code>@username</code>) |
| $user_id | Integer | Yes | Unique identifier of the target user |
| $only_banned | Boolean | Optional | Do nothing if the user is not banned. Default to true |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::restrictMember

### Restrict a chat member on groups and channels. ----TO

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $user_id | Number | Yes | User ID |
| $permissions | Array | Yes | User permissions. |
| $until_date | Number | Optional | Time in seconds |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::promoteMember

Promote a chat member on groups and channels.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $user_id | Number | Yes | User ID |
| $permissions | Array | Yes | User permissions. See [Variables::getGroupsPerms](#variables) for groups and [Variables::getChannelsPerms](#variables) for channels |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::setCustomTitle

Set chat administrator custom title.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $user_id | Number | Yes | User ID |
| $custom_title | String | Yes | Administrator title for groups |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::setPerms

Set global chat permissions.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $permissions | Array | Yes | Global group permissions |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::getLink

Export an invite link from groups and channels.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |

### TelegramBot::setPhoto

Set chat photo on groups and channels.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $photo | String | Yes | Photo |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::delPhoto

Remove chat photo on groups and channels.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::setTitle

Change chat title on groups and channel.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $title | String | Yes | Title |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::setDescription

Change chat title on groups and channel.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $description | String | Optional | Description |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::pinMessage

Pin message on groups and channel.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $message_id | Number | Yes | Message ID |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::unpinMessage

Unpin message on groups and channel.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $message_id | Number | Optional | Message ID. By default unpin the last pinned message |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::unpinAll

Unpin all pinned message on groups and channel.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::leave

Leave groups and channel.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::getChat

Returns chat infos.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |

### TelegramBot::getAdministrators

Return an array of Users.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |

### TelegramBot::getMembersCount

Return the number of members from the chat.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |

### TelegramBot::getMember

Return User infos for groups and channels.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $user_id | Number | Yes | User ID |

### TelegramBot::setStickerSet

Set the group sticker set.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $sticker_set | String | Yes | Set name |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::delStickerSet

Remove the group sticker set.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::answerCBQ

Answer to a [Callback](https://core.telegram.org/bots/api#callbackquery) query.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $cbq_id | Number | Yes | Callback Query ID |
| $text | String | Optional | Text |
| $alert | Bool | Optional | True to see the alert, falso to not see the alert. |
| $url | String | Optional | URL, instead of $text |
| $cache_time | Number | Optional | Callback cache time |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::setCommands

Set the command list on the Bot.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $commands | Array | Yes | Array of commands and description |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::getCommands

Get the command list on the Bot.

### TelegramBot::editText

Edit message text.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Optional | Chat ID, 0 for inline message |
| $message_id | Number | Yes | Message ID or Inline Message ID |
| $text | String | Yes | Text of the message. Max 4096 characters |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String | Optional | Parse mode |
| $preview | Bool or 'def' | Optional | Disable web content preview |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::editCaption

Edit media caption.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID, 0 for inline message |
| $message_id | Number | Yes | Message ID or Inline Message ID |
| $caption | String | Optional | Caption of the media message. Max 1024 characters |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String | Optional | Parse mode |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::editMedia

Edit media message.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID, 0 for inline message |
| $message_id | Number | Yes | Message ID or Inline Message ID |
| $media | Array | Yes | Array of media |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::editReplyMarkup

Edit message buttons.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID, 0 for inline message |
| $message_id | Number | Yes | Message ID or Inline Message ID |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::deleteMessage

Delete one message.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $message_id | Number | Yes | Message ID |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendSticker

Send a Sticker.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $document | Number | Yes | Document ID |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| $reply | Number | Optional | Reply to message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::getStickers

Get sticker set.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $set | String | Yes | Sticker set name |

### TelegramBot::uploadSticker

Upload sticker.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $user_id | Number | Yes | User ID |
| $document | Input File | Yes | Document ID |

### TelegramBot::createStickers

Create a new sticker set.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $user_id | Number | Yes | User ID |
| $set | String | Yes | Set name |
| $title | String | Yes | Set title |
| $sticker | Input File | Yes | Document ID |
| $is_animated | Bool | Optional | Default to false |
| $contains_masks | Bool | Optional | Default to false |
| $mask_position | Bool | Optional | Default to false |
| $response | Bool | Optional | Get the response from the request |
