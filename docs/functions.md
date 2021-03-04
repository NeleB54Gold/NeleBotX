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
| $return | Number | Optional | Return of contents can be 0 to return only true on succes, 1 to [fetch](https://www.php.net/manual/en/pdostatement.fetch) with FETCH_ASSOC, or 2 to [fetchAll](https://www.php.net/manual/en/pdostatement.fetchall.php) |

### Database::limit

Create the LIMIT SQL statement. Return as string.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$limit](https://github.com/phpredis/phpredis#keys-and-strings) | ALL or Nubmer | Optional | Start of the range |
| [$args](https://github.com/phpredis/phpredis#keys-and-strings) | Number | Optional | End of the range |

### Database::setup

Create default Framework tables. Return as array.

### createTemplateTable

Get the SQL Statement tamplate of a Framework table.

| Parameters    | Type          | Required  | Description    |
|:-------------:|:-------------:|:---------:|:--------------:|
| [$table](https://github.com/phpredis/phpredis#keys-and-strings) | String | Yes | Table name |

# Variables

# TelegramBot
