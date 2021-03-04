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

# Variables

# TelegramBot
