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
| $configs      | Array | Yes | Contains all variables of [$configs](./variables#configs)

### NeleBotX::loadUser

Return an array of current user automatically generated from the update or load from database if available.

### NeleBotX::loadGroup

Return an array of current group automatically generated from the update or load from database if available.

### NeleBotX::loadChannel

Return an array of current channel automatically generated from the update or load from database if available.
