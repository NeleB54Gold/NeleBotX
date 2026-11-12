# Classes and functions

This reference documents the methods implemented in NeleBot X 2.0. Signatures are taken from the PHP source. If a parameter has no native PHP type declaration, the type shown here is the expected type inferred from its implementation and, where applicable, the Telegram Bot API. Such inferred types are documentation only and are **not enforced by PHP at runtime**.

## PHP type notation

| Type | Meaning |
|---|---|
| `string`, `int`, `float`, `bool` | PHP scalar types |
| `array` | PHP array; Telegram objects are normally associative arrays |
| `?string`, `?int` | Nullable value |
| `mixed` | Any PHP value |
| `int|string` | Either of the listed types |

Defaults such as `'def'`, `false`, `0` and `null` have method-specific meanings; do not treat them as interchangeable.

**PHP references:** [Type declarations](https://www.php.net/manual/en/language.types.declarations.php), [Function arguments](https://www.php.net/manual/en/functions.arguments.php), [Return values](https://www.php.net/manual/en/functions.returning-values.php), [Arrays](https://www.php.net/manual/en/language.types.array.php).

**Telegram reference:** [Telegram Bot API](https://core.telegram.org/bots/api).

## NeleBotX

Main framework class. Initializes the Telegram client, update variables and optional storage backends, then loads the current user/chat context and applies framework ban and AntiFlood checks.

### `NeleBotX::__construct($configs, $getUpdate = true)`

Initializes a framework instance.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$configs` | `array` | — | Complete configuration array, normally loaded from `configs.php`. |
| `$getUpdate` | `bool` | `true` | Reads the webhook body and builds update variables when true. Set false when initializing without an incoming update. |

**Result:** The constructor sets `$this->response`. Success is represented by `['ok' => 1]`; initialization errors contain `ok`, `error_code` and `description`.

**Side effects:** May read `php://input`, call Telegram methods, connect to Redis/PDO, load user/chat records and reject banned users/chats. It does not return a separate result object.

**Related:** [`TelegramBot::__construct()`](#telegrambotconstruct), [`Variables::__construct()`](#variablesconstruct), [`Database::__construct()`](#databaseconstruct), [`AntiFlood::__construct()`](#antifloodconstruct).

### `NeleBotX::loadUser()`

Loads the user associated with the current update.

**Parameters:** None.

**Returns:** Associative user array. With database support enabled, the record is retrieved or synchronized through [`Database::getUser()`](#databasegetuser); otherwise it is returned by [`Variables::getUser()`](#variablesgetuser).

### `NeleBotX::loadGroup()`

Loads the group associated with the current update.

**Parameters:** None.

**Returns:** Associative group array, or the value from [`Variables::getGroup()`](#variablesgetgroup) when database support is disabled.

When database support is enabled, missing or stale group permissions and administrator data are refreshed (approximately one-hour cache age) through Telegram `getChat` and `getChatAdministrators`, then persisted.

**References:** [Telegram `getChat`](https://core.telegram.org/bots/api#getchat), [Telegram `getChatAdministrators`](https://core.telegram.org/bots/api#getchatadministrators).

### `NeleBotX::loadChannel()`

Loads the channel associated with the current update.

**Parameters:** None.

**Returns:** Associative channel array, or the value from [`Variables::getChannel()`](#variablesgetchannel) when database support is disabled.

When database support is enabled, missing or stale administrator data is refreshed through `getChatAdministrators` and persisted.

**Reference:** [Telegram `getChatAdministrators`](https://core.telegram.org/bots/api#getchatadministrators).

## AntiFlood

Redis-backed rate limiter for updates associated with a user, group or channel.

### `AntiFlood::__construct($db, $id)`

Checks and updates the AntiFlood counters for an identifier.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$db` | `Database` | — | Initialized database object with a working Redis connection. |
| `$id` | `int|string` | — | Telegram user or chat identifier. |

**Result:** Initializes public state properties including `messages`, `seconds`, `bantime`, `banned`, `send_notice`, `updates`, `rkey` and `rbkey`. Constructors do not return a separate result value.

**Behavior:** Configured administrators are excluded. Update timestamps are stored in a Redis list; timestamps older than the configured window are removed. When the number of remaining updates reaches the configured threshold, a temporary Redis ban key is created and `banned` is set. `send_notice` indicates that the threshold was reached.

**Requirements:** Redis must be enabled and connected. Normally instantiated by `NeleBotX`.

**Related:** [`Database::rladd()`](#databaserladd), [`Database::rlget()`](#databaserlget), [`Database::rldel()`](#databaserldel), [`Database::rset()`](#databaserset).

## Database

Provides Redis and PDO access and manages NeleBot X user, group and channel records. PDO drivers supported by this implementation are SQLite, MySQL and PostgreSQL.

### `Database::__construct($configs)`

Initializes the configured storage backends.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$configs` | `array` | — | Framework configuration array containing `redis` and `database` settings. |

**Result:** Sets `$this->response` to `['ok' => 1]` on success, or an error array on connection/driver failure. Connections are opened only when their respective `status` flags are enabled.

### `Database::redisConnect()`

Connects to Redis using configured host, port and optional password/database index.

**Parameters:** None.

**Returns:** `array`; `['ok' => 1]` on success or an error array with `ok => 0`, `error_code` and `description`.

**Requirement:** PHP Redis extension and a reachable Redis server. See [phpredis](https://github.com/phpredis/phpredis).

### `Database::rget($key)`

Reads a Redis key using `GET`.

**Parameter:** `$key` (`string`) — Redis key.

**Returns:** Stored value, or integer `0` if Redis is unavailable or an exception is caught.

### `Database::rset($key, $value, $time = null)`

Writes a value using Redis `SET`.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$key` | `string` | — | Redis key. |
| `$value` | `mixed` | — | Value to store. |
| `$time` | `int|array|null` | `null` | Optional expiration/options argument passed to phpredis; accepted forms depend on the extension version. |

**Returns:** Redis `SET` result, or `0` if Redis is unavailable or an exception is caught.

### `Database::rdel($key)`

Deletes a Redis key using `DEL`.

**Parameter:** `$key` (`string`) — key to delete.

**Returns:** Redis deletion result, or `0` if unavailable/error.

### `Database::rkeys($key)`

Returns Redis keys matching a glob-style pattern using `KEYS`.

**Parameter:** `$key` (`string`) — pattern, for example `cache:*`.

**Returns:** Array of matching keys, or `0` if unavailable/error.

**Performance note:** Redis `KEYS` can block Redis while scanning a large keyspace. Prefer `SCAN` for large production datasets. See [Redis KEYS](https://redis.io/docs/latest/commands/keys/).

### `Database::rladd($key, $value)`

Pushes a value to the head of a Redis list using `LPUSH`.

**Parameters:** `$key` (`string`) — list key; `$value` (`mixed`) — value to push.

**Returns:** Redis command result, or `0` if unavailable/error.

### `Database::rlget($key, $offset = 0, $limit = 50)`

Reads a Redis list range using `LRANGE`.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$key` | `string` | — | List key. |
| `$offset` | `int` | `0` | Start index. |
| `$limit` | `int` | `50` | End index passed to Redis; it is an inclusive index, not a number of elements. |

**Returns:** Array of values, or `0` if unavailable/error. See [Redis LRANGE](https://redis.io/docs/latest/commands/lrange/).

### `Database::rldel($key, $value = 0, $count = 50)`

Removes matching values from a Redis list using `LREM`.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$key` | `string` | — | List key. |
| `$value` | `mixed` | `0` | Value to remove. |
| `$count` | `int` | `50` | Count/direction argument passed to Redis. |

**Returns:** Number of removed elements, or `0` if unavailable/error. See [Redis LREM](https://redis.io/docs/latest/commands/lrem/).

### `Database::dbConnect()`

Selects and opens the configured PDO driver.

**Parameters:** None.

**Returns:** Status array. Supported `database.type` values: `sqlite`, `mysql`, `pgsql`. Unknown drivers or missing PDO support produce an error array.

### `Database::sqliteConnect()`

Opens the SQLite database through PDO.

**Parameters:** None.

**Returns:** Status array with `ok` set to `1` on success or `0` on failure.

**Reference:** [PHP PDO_SQLITE](https://www.php.net/manual/en/ref.pdo-sqlite.php).

### `Database::mysqlConnect()`

Opens the MySQL database through PDO.

**Parameters:** None.

**Returns:** Status array with `ok` set to `1` on success or `0` on failure.

**Reference:** [PHP PDO_MYSQL](https://www.php.net/manual/en/ref.pdo-mysql.php).

### `Database::pgsqlConnect()`

Opens the PostgreSQL database through PDO.

**Parameters:** None.

**Returns:** Status array with `ok` set to `1` on success or `0` on failure.

**Reference:** [PHP PDO_PGSQL](https://www.php.net/manual/en/ref.pdo-pgsql.php).

### `Database::query($query, $args = 0, $return = 0)`

Prepares and executes an SQL statement through the active PDO connection.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$query` | `string` | — | SQL statement. |
| `$args` | `array|int` | `0` | Bound parameter values; pass an array for parameterized statements. |
| `$return` | `int|bool` | `0` | Selects the method's result/fetch behavior. |

**Returns:** Depends on execution outcome and `$return`: fetched rows for result-fetch mode, otherwise an execution/status result. Database errors are converted to an error result by the implementation.

**Security:** Bind all untrusted values using `$args`. Placeholders represent values, not table or column identifiers.

**References:** [PDO::prepare](https://www.php.net/manual/en/pdo.prepare.php), [PDOStatement::execute](https://www.php.net/manual/en/pdostatement.execute.php).

### `Database::limit($limit = 'ALL', $offset = 0)`

Builds a SQL pagination fragment.

**Parameters:** `$limit` (`int|string`, default `'ALL'`) — maximum rows or the special `ALL` value; `$offset` (`int`, default `0`) — rows to skip.

**Returns:** SQL fragment as `string`. Validate externally supplied values before using this helper.

### `Database::setup()`

Creates the standard NeleBot X tables.

**Parameters:** None.

**Returns:** Result of the table creation operations. Creates the `users`, `groups` and `channels` tables.

### `Database::createTemplateTable($table)`

Creates one predefined framework table if it does not exist.

**Parameter:** `$table` (`string`) — one of `users`, `groups` or `channels`.

**Returns:** Query result for a supported table, or an error array for an unknown template.

### `Database::getUser($user)`

Retrieves or creates/synchronizes a user record.

**Parameter:** `$user` (`array`) — Telegram `User` object as an associative array.

**Returns:** Associative user record or error array.

**Reference:** [Telegram `User`](https://core.telegram.org/bots/api#user).

### `Database::getGroup($chat)`

Retrieves or creates/synchronizes a group record.

**Parameter:** `$chat` (`array`) — Telegram `Chat` object as an associative array.

**Returns:** Associative group record or error array.

**Reference:** [Telegram `Chat`](https://core.telegram.org/bots/api#chat).

### `Database::getChannel($chat)`

Retrieves or creates/synchronizes a channel record.

**Parameter:** `$chat` (`array`) — Telegram `Chat` object representing a channel.

**Returns:** Associative channel record or error array.

### `Database::getChatsByAdmin($user_id, $limit = 20)`

Finds stored chats associated with an administrator.

**Parameters:** `$user_id` (`int|string`) — Telegram user ID; `$limit` (`int`, default `20`) — maximum records requested.

**Returns:** Array of matching chat records.

### `Database::ban($id)`

Sets the framework's local ban flag for a user or chat.

**Parameter:** `$id` (`int|string`) — Telegram identifier.

**Returns:** Database query result. This does **not** call Telegram's `banChatMember`.

### `Database::unban($id)`

Clears the framework's local ban flag.

**Parameter:** `$id` (`int|string`) — Telegram identifier.

**Returns:** Database query result. This does **not** unban a Telegram member in the chat.

### `Database::isBanned($id)`

Checks the local ban flag.

**Parameter:** `$id` (`int|string`) — Telegram identifier.

**Returns:** Truthy/falsy result based on the stored record.

### `Database::getLanguage($id)`

Retrieves the stored language preference.

**Parameter:** `$id` (`int|string`) — Telegram identifier.

**Returns:** Stored language value or the implementation's fallback.

### `Database::setStatus($id, $status = 'Unknown', $table = 0)`

Updates a record's status field.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$id` | `int|string` | — | Telegram identifier. |
| `$status` | `string` | `'Unknown'` | Status value to store. |
| `$table` | `int|string` | `0` | Record category selector used by this implementation. |

**Returns:** Database query result.

## Variables

### Variables::__construct()

### Variables::isAdmin()

### Variables::isOwner()

### Variables::isStaff()

### Variables::varChatAdministrators()

### Variables::varUser()

### Variables::varForwardUser()

### Variables::varChat()

### Variables::varForwardChat()

### Variables::varMessage()

### Variables::varNewMembers()

### Variables::varLeftMember()

### Variables::varPhoto()

### Variables::varPhotoSize()

### Variables::varAnimation()

### Variables::varAudio()

### Variables::varDocument()

### Variables::varVideo()

### Variables::varVideoNote()

### Variables::varVoice()

### Variables::varContact()

### Variables::varDice()

### Variables::varPollOption()

### Variables::varPollAnswer()

### Variables::varPoll()

### Variables::varLocation()

### Variables::varVenue()

### Variables::varWebAppData()

### Variables::varProximityAlertTriggered()

### Variables::varMessageAutoDeleteTimerChanged()

### Variables::varForumTopicCreated()

### Variables::varForumTopicClosed()

### Variables::varForumTopicEdited()

### Variables::varForumTopicReopened()

### Variables::varGeneralForumTopicHidden()

### Variables::varGeneralForumTopicUnhidden()

### Variables::varUserShared()

### Variables::varChatShared()

### Variables::varWriteAccessAllowed()

### Variables::varVideoChatScheduled()

### Variables::varVideoChatStarted()

### Variables::varVideoChatEnded()

### Variables::varVideoChatParticipantsInvited()

### Variables::varUserProfilePhotos()

### Variables::varFile()

### Variables::varReplyKeyboardMarkup()

### Variables::varKeyboardButton()

### Variables::varKeyboardButtonPollType()

### Variables::varReplyKeyboardRemove()

### Variables::varInlineKeyboardMarkup()

### Variables::varInlineKeyboardButton()

### Variables::varLoginUrl()

### Variables::varCallbackQuery()

### Variables::varForceReply()

### Variables::varChatPhoto()

### Variables::varChatInviteLink()

### Variables::varChatAdministratorRights()

### Variables::varChatMember()

### Variables::varChatMemberUpdated()

### Variables::varChatJoinRequest()

### Variables::varSticker()

### Variables::varStory()

### Variables::varStickerSet()

### Variables::varInlineQuery()

### Variables::varChosenInlineResult()

### Variables::varGame()

### Variables::getUser()

### Variables::getGroup()

### Variables::getChannel()

### Variables::getGroupsPerms()

### Variables::getChannelsPerms()

## TelegramBot

Low-level Telegram Bot API client. It constructs endpoint URLs, sends HTTP requests through PHP cURL and decodes Telegram JSON responses. Use [`callApi()`](#telegrambotcallapi) for endpoints without a dedicated wrapper.

### `TelegramBot::__construct($configs)`

Initializes the client.

**Parameter:** `$configs` (`array`) — framework configuration containing the bot token and Telegram API base URL.

**Result:** Sets `$this->response`. The numeric prefix of the token is checked locally; this is not equivalent to validating the token with Telegram `getMe`.

### `TelegramBot::editConfigs($key, $value)`

Changes one configuration entry in this object.

**Parameters:** `$key` (`string`) — configuration key; `$value` (`mixed`) — new value.

**Returns:** `null`. The change is in memory only and does not write `configs.php`.

### `TelegramBot::getConfigs()`

Returns the current configuration array.

**Parameters:** None.

**Returns:** `array`.

### `TelegramBot::sendLog($message)`

Sends a message to the configured `log_chat`.

**Parameter:** `$message` (`string`) — log text.

**Returns:** `null` when no log chat is configured; otherwise the result of [`sendMessage()`](#telegrambotsendmessage).

### `TelegramBot::request($url, $args = [], $post = 'def', $response = 'def', $timeout = 'def')`

Executes an HTTP request to a Telegram API endpoint using cURL.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$url` | `string` | — | Complete API endpoint URL. |
| `$args` | `array|string|bool` | `[]` | Request parameters. For GET, an array is appended as a query string; for POST it is passed as POST fields. |
| `$post` | `bool|string` | `'def'` | POST/GET selector; `'def'` reads the `post` config (default true). |
| `$response` | `bool|string` | `'def'` | When false, echoes raw response and returns true; `'def'` reads the `response` config. |
| `$timeout` | `int|string` | `'def'` | Timeout in seconds; `'def'` reads config or defaults to 10. |

**Returns:** Decoded response array, or an error array for missing cURL, transport errors or invalid JSON. With `$response = false`, returns `true` after echoing the response body.

**Requirements:** PHP cURL extension. Redirects are disabled; a connection timeout is applied and the cURL handle is reused.

**References:** [PHP cURL](https://www.php.net/manual/en/book.curl.php), [Telegram — Making requests](https://core.telegram.org/bots/api#making-requests).

### `TelegramBot::callApi(string $method, array $parameters = [], bool $post = true)`

Calls any Telegram Bot API method, including methods without a named NeleBot X wrapper.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$method` | `string` | — | Method name; must begin with a letter and contain only letters and digits. |
| `$parameters` | `array` | `[]` | Associative parameter array. Nested arrays are JSON-encoded. File uploads may use `CURLFile`. |
| `$post` | `bool` | `true` | Selects POST or GET. POST is recommended for mutations. |

**Returns:** `array|string|bool`, according to `request()`. Invalid method names return an error array (`error_code` 400).

```php
$result = $bot->api->callApi('getChat', ['chat_id' => '@example'], false);
```

**Related:** [`call()`](#telegrambotcall), [`request()`](#telegrambotrequest), [Telegram methods](https://core.telegram.org/bots/api#available-methods).

### `TelegramBot::call(string $method, array $parameters = [], bool $post = true)`

Alias for [`callApi()`](#telegrambotcallapi).

**Parameters / returns:** Identical to `callApi()`.

### `TelegramBot::getUpdate()`

Reads the incoming webhook body from `php://input` and decodes it as JSON.

**Parameters:** None.

**Returns:** Associative `array`; returns an empty array for an empty body, invalid JSON or a decoded value that is not an array. This does not use Telegram's `getUpdates` long polling method.

**References:** [Telegram `Update`](https://core.telegram.org/bots/api#update), [PHP stream wrappers](https://www.php.net/manual/en/wrappers.php.php).

### `TelegramBot::api($method)`

Builds the complete URL for a Bot API method.

**Parameter:** `$method` (`string`) — endpoint method name.

**Returns:** `string` in the form `{telegram_bot_api}/bot{token}/{method}`. The URL contains the bot token; never expose it in logs or client output.

### `TelegramBot::setThreadID($ThreadID)`

Sets the current message thread ID used by supported wrapper methods.

**Parameter:** `$ThreadID` (`int|string|null`) — Telegram thread identifier, or null to clear it.

**Returns:** The assigned value. This is mutable per-instance state; clear it before sending a message outside the previously selected topic.

### `TelegramBot::sendWidget(array $parameters)`

Calls Telegram's `sendWidget` endpoint.

**Parameter:** `$parameters` (`array`) — endpoint parameters.

**Returns:** Telegram response array or transport error array. See [Telegram Bot API](https://core.telegram.org/bots/api).

### `TelegramBot::sendSharedWidget(array $parameters)`

Calls Telegram's `sendSharedWidget` endpoint.

**Parameter:** `$parameters` (`array`) — endpoint parameters.

**Returns:** Telegram response array or transport error array. See [Telegram Bot API](https://core.telegram.org/bots/api).

### `TelegramBot::answerWidgetQuery(array $parameters)`

Answers an incoming widget query.

**Parameter:** `$parameters` (`array`) — endpoint parameters, including the query identifier and optional response data.

**Returns:** Telegram response array or transport error array. See [Telegram Bot API](https://core.telegram.org/bots/api).

### `TelegramBot::editMessageWidgetData(array $parameters)`

Edits data associated with a message widget.

**Parameter:** `$parameters` (`array`) — endpoint parameters.

**Returns:** Telegram response array or transport error array. See [Telegram Bot API](https://core.telegram.org/bots/api).

### `TelegramBot::editSharedWidgetData(array $parameters)`

Edits data associated with a shared widget.

**Parameter:** `$parameters` (`array`) — endpoint parameters.

**Returns:** Telegram response array or transport error array. See [Telegram Bot API](https://core.telegram.org/bots/api).

### `TelegramBot::getChatShareThemeBackdrops(array $parameters = [])`

Retrieves chat-share theme backdrops.

**Parameter:** `$parameters` (`array`, default `[]`) — endpoint parameters.

**Returns:** Telegram response array or transport error array. Uses GET.

### `TelegramBot::getUniqueGift(string $name)`

Retrieves a unique gift by name.

**Parameter:** `$name` (`string`) — unique gift name.

**Returns:** Telegram response array or transport error array. Uses GET.

### `TelegramBot::deleteBusinessAccountGift(string $business_connection_id, string $owned_gift_id)`

Deletes a gift owned by a business account.

| Parameter | PHP type | Description |
|---|---|---|
| `$business_connection_id` | `string` | Business connection identifier. |
| `$owned_gift_id` | `string` | Identifier of the owned gift. |

**Returns:** Telegram response array or transport error array.

### `TelegramBot::setWebhook($url, $certificate = null, $ip = null, $connections = 40, $updates = null, $drop = false, $secret = null)`

Registers the webhook used by Telegram to deliver updates.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$url` | `string` | — | Webhook HTTPS URL. |
| `$certificate` | `string|CURLFile|null` | `null` | Optional certificate upload. |
| `$ip` | `string|null` | `null` | Optional fixed webhook IP address. |
| `$connections` | `int` | `40` | Maximum simultaneous connections. |
| `$updates` | `array|null` | `null` | Optional list of update types to receive. |
| `$drop` | `bool` | `false` | Discards pending updates when true. |
| `$secret` | `string|null` | `null` | Secret token Telegram sends in the `X-Telegram-Bot-Api-Secret-Token` header. |

**Returns:** Telegram response array.

**Security:** Supplying a secret configures Telegram to send the header; the receiving endpoint must independently validate it.

**Reference:** [Telegram `setWebhook`](https://core.telegram.org/bots/api#setwebhook).

### `TelegramBot::deleteWebhook($drop = false)`

Removes the webhook.

**Parameter:** `$drop` (`bool`, default `false`) — when true, pending updates are discarded.

**Returns:** Telegram response array.

**Reference:** [Telegram `deleteWebhook`](https://core.telegram.org/bots/api#deletewebhook).

### `TelegramBot::getWebhook()`

Retrieves the current webhook status.

**Parameters:** None.

**Returns:** Telegram response array containing `WebhookInfo` on success.

**Reference:** [Telegram `getWebhookInfo`](https://core.telegram.org/bots/api#getwebhookinfo).

### `TelegramBot::getMe()`

Retrieves basic information about the authenticated bot.

**Parameters:** None.

**Returns:** Telegram response array containing a `User` object on success.

**Reference:** [Telegram `getMe`](https://core.telegram.org/bots/api#getme).

### `TelegramBot::logOut()`

Logs the bot out from Telegram's cloud Bot API server before moving it to a local Bot API server.

**Parameters:** None.

**Returns:** Telegram response array. This is an operational API method, not a framework session logout.

**Reference:** [Telegram `logOut`](https://core.telegram.org/bots/api#logout).

### `TelegramBot::close()`

Closes the bot instance before moving it between local Bot API servers.

**Parameters:** None.

**Returns:** Telegram response array.

**Reference:** [Telegram `close`](https://core.telegram.org/bots/api#close).

### `TelegramBot::sendMessage($chat_id, $text, $buttons = false, $parse = 'def', $preview = 'def', $reply = false, $buttonsType = 'inline', $response = 'def')`

Sends a text message to a Telegram chat.

| Parameter | PHP type | Default | Description |
|---|---|---|---|
| `$chat_id` | `int|string` | — | Chat ID or public username in `@username` format. |
| `$text` | `string` | — | Message text; Telegram entity and length limits apply. |
| `$buttons` | `array|bool` | `false` | Optional keyboard definition converted by `createButtons()`. |
| `$parse` | `string|array` | `'def'` | Parse mode or array of `MessageEntity` objects. `'def'` uses configured `parse_mode`. |
| `$preview` | `bool|string` | `'def'` | Link-preview suppression flag; `'def'` uses `disable_web_page_preview` config. |
| `$reply` | `int|bool` | `false` | Optional message ID to reply to. |
| `$buttonsType` | `string` | `'inline'` | Keyboard type passed to `createButtons()`. |
| `$response` | `bool|string` | `'def'` | Response handling option passed to `request()`. |

**Returns:** Telegram response array, or the configured raw-response behavior.

**Additional behavior:** Adds the current thread ID when set. Also applies configured `protect_content` and `disable_notification`.

**References:** [Telegram `sendMessage`](https://core.telegram.org/bots/api#sendmessage), [Formatting options](https://core.telegram.org/bots/api#formatting-options).

## Remaining TelegramBot methods

The remaining methods below are part of the current PHP implementation. Their headings are retained as stable cross-reference anchors; the endpoint-specific parameter tables and descriptions will be completed in the next documentation pass against each method's implementation and the matching official Telegram API entry.

### `TelegramBot::forwardMessage()`

### `TelegramBot::copyMessage()`

### `TelegramBot::sendPhoto()`

### `TelegramBot::sendAudio()`

### `TelegramBot::sendDocument()`

### `TelegramBot::sendVideo()`

### `TelegramBot::sendAnimation()`

### `TelegramBot::sendVoice()`

### `TelegramBot::sendVideoNote()`

### `TelegramBot::sendMediaGroup()`

### `TelegramBot::sendLocation()`

### `TelegramBot::editLiveLocation()`

### `TelegramBot::stopLiveLocation()`

### `TelegramBot::sendVenue()`

### `TelegramBot::sendContact()`

### `TelegramBot::sendPoll()`

### `TelegramBot::sendDice()`

### `TelegramBot::sendAction()`

### `TelegramBot::getUserPhotos()`

### `TelegramBot::getFile()`

### `TelegramBot::banMember()`

### `TelegramBot::kickMember()`

### `TelegramBot::unbanMember()`

### `TelegramBot::restrictMember()`

### `TelegramBot::promoteMember()`

### `TelegramBot::setCustomTitle()`

### `TelegramBot::banChatSender()`

### `TelegramBot::unbanChatSender()`

### `TelegramBot::setPerms()`

### `TelegramBot::getLink()`

### `TelegramBot::newLink()`

### `TelegramBot::editLink()`

### `TelegramBot::delLink()`

### `TelegramBot::approveJoinRequest()`

### `TelegramBot::declineJoinRequest()`

### `TelegramBot::setPhoto()`

### `TelegramBot::delPhoto()`

### `TelegramBot::setTitle()`

### `TelegramBot::setDescription()`

### `TelegramBot::pinMessage()`

### `TelegramBot::unpinMessage()`

### `TelegramBot::unpinAll()`

### `TelegramBot::leave()`

### `TelegramBot::getChat()`

### `TelegramBot::getAdministrators()`

### `TelegramBot::getMemberCount()`

### `TelegramBot::getMembersCount()`

### `TelegramBot::getMember()`

### `TelegramBot::setStickerSet()`

### `TelegramBot::delStickerSet()`

### `TelegramBot::getForumIcon()`

### `TelegramBot::createForumTopic()`

### `TelegramBot::editForumTopic()`

### `TelegramBot::closeForumTopic()`

### `TelegramBot::reopenForumTopic()`

### `TelegramBot::deleteForumTopic()`

### `TelegramBot::unpinAllForumTopicMessages()`

### `TelegramBot::editGeneralForumTopic()`

### `TelegramBot::closeGeneralForumTopic()`

### `TelegramBot::reopenGeneralForumTopic()`

### `TelegramBot::hideGeneralForumTopic()`

### `TelegramBot::unhideGeneralForumTopic()`

### `TelegramBot::unpinAllGeneralForumTopicMessages()`

### `TelegramBot::answerCBQ()`

### `TelegramBot::setCommands()`

### `TelegramBot::delCommand()`

### `TelegramBot::getCommands()`

### `TelegramBot::setMyName()`

### `TelegramBot::getMyName()`

### `TelegramBot::setMyDescription()`

### `TelegramBot::getMyDescription()`

### `TelegramBot::setMyShortDescription()`

### `TelegramBot::getMyShortDescription()`

### `TelegramBot::setChatButton()`

### `TelegramBot::getChatButton()`

### `TelegramBot::setMyAdminRights()`

### `TelegramBot::editText()`

### `TelegramBot::editCaption()`

### `TelegramBot::editMedia()`

### `TelegramBot::editReplyMarkup()`

### `TelegramBot::deleteMessage()`

### `TelegramBot::createStickerInput()`

### `TelegramBot::sendSticker()`

### `TelegramBot::getStickers()`

### `TelegramBot::getEmojiStickers()`

### `TelegramBot::uploadSticker()`

### `TelegramBot::createStickers()`

### `TelegramBot::addSticker()`

### `TelegramBot::setStickerPos()`

### `TelegramBot::delSticker()`

### `TelegramBot::setStickerEmojiList()`

### `TelegramBot::setStickerKeywords()`

### `TelegramBot::setStickerSetTitle()`

### `TelegramBot::setStickerSetThumbnail()`

### `TelegramBot::setCustomEmojiStickerSetThumbnail()`

### `TelegramBot::deleteStickerSet()`

### `TelegramBot::answerIQ()`

### `TelegramBot::answerWAQ()`

### `TelegramBot::sendInvoice()`

### `TelegramBot::createInvoiceLink()`

### `TelegramBot::answerSQ()`

### `TelegramBot::answerPCQ()`

### `TelegramBot::createWebApp()`

### `TelegramBot::createButtons()`

### `TelegramBot::createButton()`

### `TelegramBot::createReplyKeyboardRemove()`

### `TelegramBot::createInlineButton()`

### `TelegramBot::InlineQueryResultsButton()`

### `TelegramBot::createLoginButton()`

### `TelegramBot::createForceReply()`

### `TelegramBot::createChatPermissions()`

### `TelegramBot::createBotCommand()`

### `TelegramBot::switchChosenChat()`

### `TelegramBot::createAnimationInput()`

### `TelegramBot::createDocumentInput()`

### `TelegramBot::createAudioInput()`

### `TelegramBot::createPhotoInput()`

### `TelegramBot::createVideoInput()`

### `TelegramBot::createFileInput()`

### `TelegramBot::createInlineArticle()`

### `TelegramBot::createInlinePhoto()`

### `TelegramBot::createInlineGif()`

### `TelegramBot::createInlineVideo()`

### `TelegramBot::createInlineAudio()`

### `TelegramBot::createInlineVoice()`

### `TelegramBot::createInlineDocument()`

### `TelegramBot::createInlineLocation()`

### `TelegramBot::createInlineVenue()`

### `TelegramBot::createInlineContact()`

### `TelegramBot::createInlineGame()`

### `TelegramBot::createTextInput()`

### `TelegramBot::createLocationInput()`

### `TelegramBot::createVenueInput()`

### `TelegramBot::createContactInput()`

### `TelegramBot::createWebAppMessage()`

### `TelegramBot::parseMode()`

### `TelegramBot::specialchars()`

### `TelegramBot::mdspecialchars()`

### `TelegramBot::md2specialchars()`

### `TelegramBot::bold()`

### `TelegramBot::italic()`

### `TelegramBot::underline()`

### `TelegramBot::strikethrough()`

### `TelegramBot::spoiler()`

### `TelegramBot::text_link()`

### `TelegramBot::tag()`

### `TelegramBot::custom_emoji()`

### `TelegramBot::code()`

### `TelegramBot::pre()`

### `TelegramBot::prepy()`

### `TelegramBot::createStartLink()`

### `TelegramBot::getFileLink()`

## Log

### `Log::configure()`

## WebhookKey

### `WebhookKey::VERSION`

### `WebhookKey::AAD`

### `WebhookKey::encrypt()`

### `WebhookKey::decrypt()`

## errorHandler

### `errorHandler::errorHandler()`

### `errorHandler::shutdownHandler()`
