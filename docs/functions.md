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

Make cURL requests.
| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $url | String | Yes | Request url |
| $args | Array | Optional | Arguments for the request |
| $post | Bool or 'def' | Optional | True to post, false to get or 'def' by default from configs |

### TelegramBot::getUpdate

Get the current update.

### TelegramBot::api

Create the Telegram Bot API url for any method.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $method | String | Yes | Telegram Bot API [method](https://core.telegram.org/bots/api#available-methods) |

### TelegramBot::getMe

Return the Bot profile infos.

### TelegramBot::logOut

[Log out](https://core.telegram.org/bots/api#logout) from the current API.

### TelegramBot::close

[Close](https://core.telegram.org/bots/api#close) connection with API server.

### TelegramBot::sendMessage

Send text message.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $text | String | Yes | Text of the message. Max 4096 characters |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String | Optional | Parse mode |
| $preview | Bool or 'def' | Optional | Disable web content preview |
| $reply | Number | Optional | Reply to message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::forwardMessage

Forward a message. Only if the Bot can see it.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID where the message will be sent |
| $from_id | Number | Yes | Chat ID where the message is |
| $id | Number | Yes | Message ID |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::copyMessage

Send a copy of message.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $chat_id | Number | Yes | Chat ID |
| $from_id | Number | Yes | Chat ID where the message is |
| $id | Number | Yes | Message ID |
| $caption | String | Yes | Caption of the message. Max 1024 characters |
| [$buttons](https://core.telegram.org/bots/api#inlinekeyboardmarkup) | Array | Optional | Buttons. |
| [$parse](https://core.telegram.org/bots/api#formatting-options) | String | Optional | Parse mode |
| $preview | Bool or 'def' | Optional | Disable web content preview |
| $reply | Number | Optional | Reply to message |
| $buttonsType | String | Optional | Type of reply_markup. Can be reply, remove, hide or inline. Use inline by default |
| $response | Bool | Optional | Get the response from the request |

### TelegramBot::sendPhoto

Send a Photo.

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

### TelegramBot::sendAudio

Send an Audio.

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

### TelegramBot::sendDocument

Send a Document.

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
