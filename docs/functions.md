# Classes and functions

- [NeleBotX](#NeleBotX)
- [AntiFlood](#AntiFlood)
- [Database](#Database)
- [Variables](#Variables)
- [TelegramBot](#TelegramBot)

## NeleBotX Class

This is the class that start the Framework.

# NeleBotX::__construct

Start the NeleBotX class: virify the token, start other classes and load user and group or channel.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| $configs      | Array | Yes | Contains all variables of [$configs](./variables#configs)

# NeleBotX::loadUser

Create an array of current user automatically from the update and load from database if available.

# NeleBotX::loadGroup

Create an array of current group automatically from the update and load from database if available.

# NeleBotX::loadChannel

Create an array of current channel automatically from the update and load from database if available.
