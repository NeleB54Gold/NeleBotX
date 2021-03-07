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
| $id | Numeric | Yes | Contains Telegram ID |

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
| [$offset](https://github.com/phpredis/phpredis#lrange-lgetrange) | Number | Optional | Start of the range |
| [$offset](https://github.com/phpredis/phpredis#lrange-lgetrange) | Number | Optional | End of the range |

### Database::rldel

Try to [remove](https://github.com/phpredis/phpredis#lrem-lremove) values from list by key and value from Redis.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$key](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Key |
| [$value](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Value |
| [$count](https://github.com/phpredis/phpredis#lrange-lgetrange) | Number | Optional | Count of values to remove |

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
| $return | Number | Optional | Return of contents can be 0 to return only true on success, 1 to [fetch](https://www.php.net/manual/en/pdostatement.fetch) with FETCH_ASSOC, or 2 to [fetchAll](https://www.php.net/manual/en/pdostatement.fetchall.php) |

### Database::limit

Create the LIMIT SQL statement. Return as string.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$limit](https://github.com/phpredis/phpredis#keys-and-strings) | ALL or Nubmer | Optional | Start of the range |
| [$offset](https://github.com/phpredis/phpredis#keys-and-strings) | Number | Optional | End of the range |

### Database::setup

Create default Framework tables. Return as array.

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
| $user_id | Number | Yes | Telegram User ID |
| $limit | Number | Optional | Limit for the query |

### Database::ban

Ban a chat from the Bot. Bot's admins excluded.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Number | Yes | Chat ID |

### Database::unban

Unban a chat from the Bot.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Number | Yes | Chat ID |

### Database::isBanned

Check if a chat has been banned. Return an array with ban for table.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Number | Yes | Chat ID |

### Database::getLanguage

Get the user language.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Number | Yes | User ID |

### Database::setStatus

Set user status on database.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $id | Number | Yes | User ID |
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
| $id | Number | Optional | User ID |

### Variables::getUser

Get the current user by the update variables. Return null if not found.

### Variables::getGroup

Get the current group by the update variables. Return null if not found.

### Variables::getChannel

Get the current channel by the update variables. Return null if not found.

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
| $post | Boolean or 'def' | Optional | True to post, false to get or 'def' by default from configs |

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
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format @channelusername) |
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
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format @channelusername) |
| $from_id | Integer or String | Yes | Unique identifier for the chat where the original message was sent (or channel username in the format @channelusername) |
| $id | Integer | Yes | Message identifier in the chat specified in from_chat_id |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::copyMessage

Use this method to copy messages of any kind. The method is analogous to the method [forwardMessages](https://core.telegram.org/bots/api#forwardmessage), but the copied message doesn't have a link to the original message. Returns the [MessageId](https://core.telegram.org/bots/api#messageid) of the sent message on success.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format @channelusername) |
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
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format @channelusername) |
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
| $chat_id | Integer or String | Yes | Unique identifier for the target chat or username of the target channel (in the format @channelusername) |
| $document | [InputFile](https://core.telegram.org/bots/api#inputfile) or String | Yes | Audio file to send. Pass a file_id as String to send an audio file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get an audio file from the Internet, or upload a new one using multipart/form-data |
| $caption | String | Optional | Audio caption, 0-1024 characters after entities parsing |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Array of keyboardButton or inlineKeyboardButton |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String or Array | Optional | String: Mode for parsing entities in the new caption. See formatting options for more details. Array: List of special entities that appear in the new caption, which can be specified instead of parse_mode |
| $reply | Integer | Optional | If the message is a reply, ID of the original message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Boolean | Optional | Get the response from the request |

### TelegramBot::sendDocument

# Send a Document. To complete

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $document | Number | Yes | Document ID |
| $caption | String | Yes | Caption of the message. Max 1024 characters |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String | Optional | Parse mode |
| $reply | Number | Optional | Reply to message |
| $thumb | File Input | Optional | File thumb |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendVideo

Send a Video.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $document | Number | Yes | Document ID |
| $caption | String | Yes | Caption of the message. Max 1024 characters |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String | Optional | Parse mode |
| $reply | Number | Optional | Reply to message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendAnimation

Send an Animation.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $document | Number | Yes | Document ID |
| $caption | String | Yes | Caption of the message. Max 1024 characters |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String | Optional | Parse mode |
| $reply | Number | Optional | Reply to message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendVoice

Send a Voice.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $document | Number | Yes | Document ID |
| $caption | String | Yes | Caption of the message. Max 1024 characters |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String | Optional | Parse mode |
| $reply | Number | Optional | Reply to message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendVideoNote

Send a Video Note.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $document | Number | Yes | Document ID |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| $reply | Number | Optional | Reply to message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendMediaGroup

Send a group of media.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| [$documents](https://core.telegram.org/bots/api#sendmediagroup) | Number | Yes | Array of documents |
| $reply | Number | Optional | Reply to message |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendLocation

Send a location with coordinates.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $lati | Number | Yes | Latitude |
| $long | Number | Yes | Longitude |
| [$live](https://telegram.org/blog/live-locations) | Number | Yes | Send as live location |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| $reply | Number | Optional | Reply to message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |


### TelegramBot::editLiveLocation

Edit a live location with coordinates.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $message_id | Number | Yes | Message ID |
| $lati | Number | Yes | Latitude |
| $long | Number | Yes | Longitude |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::stopLiveLocation

Stop a live location.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $message_id | Number | Yes | Message ID |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendVenue

Send venue with coordinates.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $lati | Number | Yes | Latitude |
| $long | Number | Yes | Longitude |
| $title | String | Yes | Title |
| $address | String | Yes | Address |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| $reply | Number | Optional | Reply to message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendContact

Send contact with phone number.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $number | Number | Yes | Phone number |
| $first_name | String | Yes | First name |
| $last_name | String | Optional | Last name |
| $vcard | String | Optional | [vCard](https://en.wikipedia.org/wiki/VCard) info |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| $reply | Number | Optional | Reply to message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendAction

Send an action, like "Typing...".

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $action | String | Optional | Action that can be typing, upload_photo, record_video, upload_video, record_voice, upload_voice , upload_document, find_location, record_video_note or upload_video_note. Default to typing |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::getUserPhotos

Return the user profile pic IDs.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $user_id | Number | Yes | USer ID |
| $offset | Number | Optional | Start of the range. |
| $limit | Number | Optional | End of the range. Max and default to 100. |

### TelegramBot::getFile

Return the file path. Note: if you have a Local Bot API you can get the file by others method instead of cURL.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$file_id](https://core.telegram.org/bots/api#getfile) | String | Yes | Document ID |

### TelegramBot::kickMember

Ban a chat member on groups or channels.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $user_id | Number | Yes | User ID |
| $until_date | Number | Optional | Time in seconds |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::unbanMember

Unban a chat member on groups or channels.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $user_id | Number | Yes | User ID |
| $only_banned | Bool | Optional | Unban only if the user has been banned. Default to true |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::restrictMember

Restrict a chat member on groups and channels.

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
