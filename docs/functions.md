# Classes and functions

- [NeleBotX](#NeleBotX)
- [AntiFlood](#AntiFlood)
- [Database](#Database)
- [Variables](#Variables)
- [TelegramBot](#TelegramBot)

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

# Variables

# TelegramBot
