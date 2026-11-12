# Variables

This reference describes the runtime properties exposed by the `Variables` class and the configuration keys consumed by NeleBot X 2.0. Property names are preserved exactly as implemented, including legacy spellings and known inconsistencies.

## Important: PHP types and property availability

`Variables` declares only a small set of properties explicitly and uses `#[\AllowDynamicProperties]` for the properties populated while parsing updates. The types below are therefore **documented/inferred types**, not native PHP property declarations or runtime validation rules. A property may not exist at all when the corresponding optional Telegram field is absent from an update.

Most values originate from Telegram's JSON objects and are decoded into PHP arrays/scalars. Telegram marks many fields as optional; check with `isset()` or `array_key_exists()` before relying on them.

**Type notation:** `|null` means the property may be absent or not populated for a particular update. Telegram identifiers are documented as `int|string|null` to account for API values and PHP decoding; timestamps and numeric counters are `int|null`; JSON objects are represented as associative `array|null`. These are inferred from assignments and API schemas, not enforced declarations.

**PHP references:** [Properties](https://www.php.net/manual/en/language.oop5.properties.php), [Dynamic properties](https://www.php.net/manual/en/class.allowdynamicproperties.php), [Type declarations](https://www.php.net/manual/en/language.types.declarations.php), [Arrays](https://www.php.net/manual/en/language.types.array.php), [isset()](https://www.php.net/manual/en/function.isset.php).

**Telegram references:** [Update](https://core.telegram.org/bots/api#update), [Message](https://core.telegram.org/bots/api#message), [User](https://core.telegram.org/bots/api#user), [Chat](https://core.telegram.org/bots/api#chat), [Telegram Bot API](https://core.telegram.org/bots/api).

## Runtime properties

### Core update state

These values are initialized or populated by the update constructor. 

#### `$configs`

**Documented type:** `array` (configuration array supplied to the constructor).

Configuration array supplied to the constructor; normally loaded from `configs.php`.

#### `$response`

**Documented type:** `array` (framework initialization result with an `ok` flag and optional error details).

Initialization status array. `ok` is `1` on success and `0` on an invalid/missing update.

#### `$update`

**Documented type:** `array` (raw associative Telegram Update payload).

Raw associative array containing the incoming Telegram `Update` object.

#### `$update_type`

**Documented type:** `string|null` (detected Telegram update key; unknown update types are preserved).

Name of the detected update field (for example `message`, `callback_query` or `inline_query`).

#### `$update_data`

**Documented type:** `array|null` (payload for an update type not parsed into dedicated properties).

Raw payload belonging to `update_type`; the fallback parser preserves unknown update types here.


### Current user

These values are extracted by [`Variables::varUser()`](functions.md#variablesvaruser). 

#### `$user_id`

**Documented type:** `int|string|null`.

Telegram user identifier from the current `User` object.

#### `$user_is_bot`

**Documented type:** `bool|null`.

Whether the current Telegram user is a bot.

#### `$user_first_name`

**Documented type:** `string|null`.

First name of the current Telegram user.

#### `$user_last_name`

**Documented type:** `string|null`.

Optional last name of the current Telegram user.

#### `$user_username`

**Documented type:** `string|null`.

Optional public username of the current Telegram user, without `@`.

#### `$user_language_code`

**Documented type:** `string|null`.

Optional IETF language tag reported by the current user.

#### `$user_is_premium`

**Documented type:** `bool|null`.

Whether Telegram reports the current user as a Premium user.


### Forwarded user

These values are extracted by [`Variables::varForwardUser()`](functions.md#variablesvarforwarduser). 

#### `$f_user_id`

**Documented type:** `int|string|null`.

Identifier of the forwarded user (`forward_from`).

#### `$f_user_is_bot`

**Documented type:** `bool|null`.

Whether the forwarded user is a bot.

#### `$f_user_first_name`

**Documented type:** `string|null`.

First name of the forwarded user.

#### `$f_user_last_name`

**Documented type:** `string|null`.

Optional last name of the forwarded user.

#### `$f_user_username`

**Documented type:** `string|null`.

Optional username of the forwarded user.

#### `$f_user_language_code`

**Documented type:** `string|null`.

Optional language code of the forwarded user.


### Current chat

These values are extracted by [`Variables::varChat()`](functions.md#variablesvarchat). 

#### `$chat_id`

**Documented type:** `int|string|null`.

Unique identifier of the chat associated with the current message/update.

#### `$chat_type`

**Documented type:** `string|null`.

Chat type, normally `private`, `group`, `supergroup` or `channel`.

#### `$chat_title`

**Documented type:** `string|null`.

Title of a group, supergroup or channel.

#### `$chat_username`

**Documented type:** `string|null`.

Optional public username of the chat.

#### `$chat_is_forum`

**Documented type:** `bool|null`.

Whether the chat is a forum supergroup.

#### `$chat_first_name`

**Documented type:** `string|null`.

First name for a private chat.

#### `$chat_last_name`

**Documented type:** `string|null`.

Optional last name for a private chat.


### Forwarded chat

These values are extracted by [`Variables::varForwardChat()`](functions.md#variablesvarforwardchat). 

#### `$f_chat_id`

**Documented type:** `int|string|null`.

Identifier of the forwarded chat.

#### `$f_chat_type`

**Documented type:** `string|null`.

Type of the forwarded chat.

#### `$f_chat_title`

**Documented type:** `string|null`.

Title of the forwarded chat.

#### `$f_chat_username`

**Documented type:** `string|null`.

Optional username of the forwarded chat.

#### `$f_chat_first_name`

**Documented type:** `string|null`.

First name associated with the forwarded private chat.

#### `$f_chat_last_name`

**Documented type:** `string|null`.

Last name associated with the forwarded private chat.


### Message

These values are extracted by [`Variables::varMessage()`](functions.md#variablesvarmessage). 

#### `$raw_message`

**Documented type:** `array|null`.

Original `Message` payload before the framework extracts convenience properties.

#### `$widget`

**Documented type:** `array|null`.

Widget object attached to the message, where provided by the Bot API version in use.

#### `$ton_wallet_transfer`

**Documented type:** `array|null`.

TON wallet transfer data attached to a message, when present.

#### `$share_theme`

**Documented type:** `array|null`.

Chat-share theme data attached to a message, when present.

#### `$rich_texts`

**Documented type:** `array|null`.

Rich text content attached to a message, when present.

#### `$message_id`

**Documented type:** `int|string|null`.

Unique message identifier within its chat.

#### `$message_thread_id`

**Documented type:** `int|string|null`.

Identifier of the forum topic/thread containing the message. See implementation warning below: the current parser reads a misspelled array key.

#### `$sender_chat`

**Documented type:** `array|null`.

Sender chat when a message is sent on behalf of a chat.

#### `$date`

**Documented type:** `int|null`.

Message or membership-update date as a Unix timestamp.

#### `$forward_signature`

**Documented type:** `string|null`.

Optional author signature of a forwarded channel post.

#### `$forward_sender_chat`

**Documented type:** `array|null`.

Chat from which the message was forwarded, when available.

#### `$is_automatic_forward`

**Documented type:** `bool|null`.

Whether the message is an automatic forward from a linked channel.

#### `$forward_date`

**Documented type:** `int|null`.

Date of the original message, as a Unix timestamp.

#### `$is_topic_message`

**Documented type:** `bool|null`.

Whether the message belongs to a forum topic.

#### `$reply_to_message`

**Documented type:** `array|null`.

Message being replied to, represented as a nested Telegram `Message` object.

#### `$via_bot`

**Documented type:** `array|null`.

Bot through which the message was sent, when applicable.

#### `$edit_date`

**Documented type:** `int|null`.

Last edit date as a Unix timestamp.

#### `$has_protected_content`

**Documented type:** `bool|null`.

Whether the message is protected from forwarding and saving.

#### `$media_group_id`

**Documented type:** `int|string|null`.

Identifier shared by messages that belong to the same media album.

#### `$author_signature`

**Documented type:** `string|null`.

Optional signature of the post author in a channel.

#### `$text`

**Documented type:** `string|null`.

Text content of the message, when the update contains text.

#### `$command`

**Documented type:** `string|null`.

Command text extracted from `text` when it starts with one of the configured `commands_alias` characters. The parser keeps arguments after a space.

#### `$entities`

**Documented type:** `array|null`.

Array of Telegram `MessageEntity` objects associated with message text.

#### `$caption`

**Documented type:** `string|null`.

Caption attached to supported media messages.

#### `$caption_entities`

**Documented type:** `array|null`.

Array of `MessageEntity` objects found in the caption.

#### `$has_media_spoiler`

**Documented type:** `bool|null`.

Whether the media is covered by a spoiler animation.

#### `$new_chat_title`

**Documented type:** `string|null`.

New title in a service message reporting a chat title change.

#### `$new_chat_photo`

**Documented type:** `array|null`.

New chat photo sizes in a service message.

#### `$delete_chat_photo`

**Documented type:** `bool|null`.

True when the chat photo was deleted.

#### `$group_chat_created`

**Documented type:** `bool|null`.

True when a basic group was created.

#### `$supergroup_chat_created`

**Documented type:** `bool|null`.

True when a supergroup was created.

#### `$channel_chat_created`

**Documented type:** `bool|null`.

True when a channel was created.

#### `$migrate_to_chat_id`

**Documented type:** `int|string|null`.

New supergroup identifier when a group migrates to a supergroup.

#### `$migrate_from_chat_id`

**Documented type:** `int|string|null`.

Previous group identifier when a supergroup was migrated from a group.

#### `$pinned_message`

**Documented type:** `array|null`.

Pinned `Message` object included in a service message.

#### `$invoice`

**Documented type:** `array|null`.

Invoice details attached to a message.

#### `$successful_payment`

**Documented type:** `array|null`.

Successful payment details attached to a message.

#### `$connected_website`

**Documented type:** `string|null`.

Domain of the website connected to the user's Telegram account, when provided.

#### `$passport_data`

**Documented type:** `array|null`.

Telegram Passport data attached to a message.

#### `$proximity_alert_triggered`

**Documented type:** `array|null`.

Proximity alert event object attached to a message.

#### `$video_chat_ended`

**Documented type:** `bool|null`.

Duration of a video chat that ended.

#### `$video_chat_participants_invited`

**Documented type:** `bool|null`.

User list from a video-chat participants-invited service message.


### Former / departing member

These values are extracted by [`Variables::varLeftMember()`](functions.md#variablesvarleftmember). 

#### `$left_user_id`

**Documented type:** `int|string|null`.

Identifier of a user who left the chat. The current message parser does not invoke `varLeftMember()` automatically.

#### `$left_is_bot`

**Documented type:** `bool|null`.

Whether the departing user is a bot; not populated automatically by the current message parser.

#### `$left_first_name`

**Documented type:** `string|null`.

First name of the departing user; not populated automatically by the current message parser.

#### `$left_last_name`

**Documented type:** `string|null`.

Optional last name of the departing user; not populated automatically by the current message parser.

#### `$left_username`

**Documented type:** `string|null`.

Optional username of the departing user; not populated automatically by the current message parser.

#### `$left_language`

**Documented type:** `string|null`.

Language code of the departing user; not populated automatically by the current message parser.


### Animation

These values are extracted by [`Variables::varAnimation()`](functions.md#variablesvaranimation). 

#### `$animation_id`

**Documented type:** `int|string|null`.

Telegram `file_id` for the animation; reusable to send or download the file.

#### `$animation_uid`

**Documented type:** `array|null`.

Stable `file_unique_id` for the animation; not usable to download or resend the file.

#### `$animation_width`

**Documented type:** `int|null`.

Animation width in pixels.

#### `$animation_height`

**Documented type:** `int|null`.

Animation height in pixels.

#### `$animation_duration`

**Documented type:** `int|null`.

Animation duration in seconds.

#### `$animation_thumb`

**Documented type:** `array|null`.

Optional animation thumbnail object (Telegram currently uses `thumbnail`; this parser reads the legacy `thumb` key).

#### `$animation_name`

**Documented type:** `array|null`.

Optional original file name.

#### `$animation_mime`

**Documented type:** `array|null`.

Optional MIME type reported for the animation.

#### `$animation_size`

**Documented type:** `int|null`.

Optional file size in bytes.


### Audio

These values are extracted by [`Variables::varAudio()`](functions.md#variablesvaraudio). 

#### `$audio_id`

**Documented type:** `int|string|null`.

Telegram `file_id` for the audio file.

#### `$audio_uid`

**Documented type:** `array|null`.

Stable `file_unique_id` for the audio file.

#### `$audio_duration`

**Documented type:** `int|null`.

Audio duration in seconds.

#### `$audio_performer`

**Documented type:** `array|null`.

Optional performer name.

#### `$audio_title`

**Documented type:** `array|null`.

Optional audio track title.

#### `$audio_name`

**Documented type:** `array|null`.

Optional original file name.

#### `$audio_mime`

**Documented type:** `array|null`.

Optional MIME type.

#### `$audio_size`

**Documented type:** `int|null`.

Optional file size in bytes.

#### `$audio_thumb`

**Documented type:** `array|null`.

Optional audio thumbnail object (the parser currently reads `thumb`).


### Document

These values are extracted by [`Variables::varDocument()`](functions.md#variablesvardocument). 

#### `$document_id`

**Documented type:** `int|string|null`.

Telegram `file_id` for the document.

#### `$document_uid`

**Documented type:** `array|null`.

Stable `file_unique_id` for the document.

#### `$document_thumb`

**Documented type:** `array|null`.

Optional document thumbnail object (the parser currently reads `thumb`).

#### `$document_name`

**Documented type:** `array|null`.

Optional original file name.

#### `$document_mime`

**Documented type:** `array|null`.

Optional MIME type.

#### `$document_size`

**Documented type:** `int|null`.

Optional file size in bytes.


### Video

These values are extracted by [`Variables::varVideo()`](functions.md#variablesvarvideo). 

#### `$video_id`

**Documented type:** `int|string|null`.

Telegram `file_id` for the video.

#### `$video_uid`

**Documented type:** `array|null`.

Stable `file_unique_id` for the video.

#### `$video_width`

**Documented type:** `int|null`.

Video width in pixels.

#### `$video_height`

**Documented type:** `int|null`.

Video height in pixels.

#### `$video_duration`

**Documented type:** `int|null`.

Video duration in seconds.

#### `$video_thumb`

**Documented type:** `array|null`.

Optional video thumbnail object (the parser currently reads `thumb`).

#### `$video_name`

**Documented type:** `array|null`.

Optional original file name.

#### `$video_mime`

**Documented type:** `array|null`.

Optional MIME type.

#### `$video_size`

**Documented type:** `int|null`.

Optional file size in bytes.


### Video note

These values are extracted by [`Variables::varVideoNote()`](functions.md#variablesvarvideonote). 

#### `$video_note_id`

**Documented type:** `int|string|null`.

Expected Telegram `file_id` for a video note. The current `varVideoNote()` implementation incorrectly reads from `$Video`, so this property is not reliable.

#### `$video_note_uid`

**Documented type:** `array|null`.

Expected `file_unique_id` for a video note; currently affected by the `$Video` variable bug in `varVideoNote()`.

#### `$video_note_length`

**Documented type:** `int|null`.

Video-note diameter/length in pixels; currently affected by the parser bug.

#### `$video_note_duration`

**Documented type:** `int|null`.

Video-note duration in seconds; currently affected by the parser bug.

#### `$video_note_thumb`

**Documented type:** `array|null`.

Optional video-note thumbnail; currently affected by the parser bug.

#### `$video_note_size`

**Documented type:** `int|null`.

Optional video-note file size in bytes; currently affected by the parser bug.


### Voice

These values are extracted by [`Variables::varVoice()`](functions.md#variablesvarvoice). 

#### `$voice_id`

**Documented type:** `int|string|null`.

Telegram `file_id` for the voice message.

#### `$voice_uid`

**Documented type:** `array|null`.

Stable `file_unique_id` for the voice message.

#### `$voice_duration`

**Documented type:** `int|null`.

Voice message duration in seconds.

#### `$voice_mime`

**Documented type:** `array|null`.

Optional MIME type.

#### `$voice_size`

**Documented type:** `int|null`.

Optional file size in bytes.


### Contact

These values are extracted by [`Variables::varContact()`](functions.md#variablesvarcontact). 

#### `$c_number`

**Documented type:** `int|null`.

Phone number from a shared `Contact`.

#### `$c_first_name`

**Documented type:** `string|null`.

First name from a shared contact.

#### `$c_last_name`

**Documented type:** `string|null`.

Optional last name from a shared contact.

#### `$c_user_id`

**Documented type:** `int|string|null`.

Optional Telegram user identifier associated with the contact.

#### `$c_vcard`

**Documented type:** `string|null`.

Optional vCard data for the contact.


### Dice

These values are extracted by [`Variables::varDice()`](functions.md#variablesvardice). 

#### `$dice_emoji`

**Documented type:** `array|null`.

Emoji used by the Telegram dice message.

#### `$dice_value`

**Documented type:** `array|null`.

Numeric result of the dice animation.


### Poll

These values are extracted by [`Variables::varPoll()`](functions.md#variablesvarpoll). 

#### `$option_name`

**Documented type:** `string|null`.

Text of a poll option (when processed by `varPollOption()`).

#### `$option_count`

**Documented type:** `int|null`.

Number of voters for the poll option (when processed by `varPollOption()`).

#### `$poll_id`

**Documented type:** `int|string|null`.

Poll identifier. The current `varPollAnswer()` mistakenly assigns the nonexistent `text` field to this property; for a `Poll` object it uses the poll's `id`.

#### `$poll_options`

**Documented type:** `array|null`.

Poll option array or selected option ID array, depending on whether the update contains a `Poll` or `PollAnswer`.

#### `$poll_question`

**Documented type:** `string|null`.

Poll question text.

#### `$poll_counter`

**Documented type:** `array|null`.

Total number of voters in the poll.

#### `$poll_closed`

**Documented type:** `array|null`.

Whether the poll is closed.

#### `$poll_anonymous`

**Documented type:** `array|null`.

Whether the poll is anonymous.

#### `$poll_type`

**Documented type:** `string|null`.

Poll type, normally `regular` or `quiz`.

#### `$poll_multi`

**Documented type:** `array|null`.

Whether multiple answers are allowed.

#### `$poll_correct`

**Documented type:** `array|null`.

Correct option index for a quiz poll, when present.

#### `$poll_explanation`

**Documented type:** `string|null`.

Optional explanation for a quiz poll.

#### `$poll_explanation_entities`

**Documented type:** `array|null`.

Optional formatting entities in the quiz explanation.

#### `$poll_open`

**Documented type:** `array|null`.

Optional poll open period in seconds.

#### `$poll_close_date`

**Documented type:** `int|null`.

Optional poll closing date as a Unix timestamp.


### Location and venue

These values are extracted by [`Variables::varLocation()`](functions.md#variablesvarlocation). 

#### `$loc_longitude`

**Documented type:** `float|null`.

Longitude of a Telegram `Location`, in degrees.

#### `$loc_latitude`

**Documented type:** `float|null`.

Latitude of a Telegram `Location`, in degrees.

#### `$loc_live_period`

**Documented type:** `int|null`.

Optional live-location period in seconds.

#### `$heading`

**Documented type:** `int|null`.

Direction in degrees for a live location, when provided.

#### `$proximity_alert_radius`

**Documented type:** `array|null`.

Optional proximity alert radius in meters.

#### `$venue_title`

**Documented type:** `array|null`.

Name/title of a shared venue.

#### `$venue_address`

**Documented type:** `array|null`.

Human-readable address of the venue.

#### `$foursquare_id`

**Documented type:** `int|string|null`.

Optional Foursquare venue identifier.

#### `$foursquare_type`

**Documented type:** `string|null`.

Optional Foursquare venue category/type.

#### `$google_place_id`

**Documented type:** `int|string|null`.

Optional Google Places identifier.

#### `$google_place_type`

**Documented type:** `string|null`.

Optional Google Places type.


### Web App and proximity

These values are extracted by [`Variables::varWebAppData()`](functions.md#variablesvarwebappdata). 

#### `$webapp_data`

**Documented type:** `string|null`.

Data string sent by a Telegram Web App through a service message.

#### `$webapp_text`

**Documented type:** `string|null`.

Text of the Web App button that was pressed.

#### `$proximity_traveler`

**Documented type:** `array|null`.

Traveler `User` object in a proximity alert.

#### `$proximity_watcher`

**Documented type:** `array|null`.

Watcher `User` object in a proximity alert.

#### `$proximity_distance`

**Documented type:** `array|null`.

Distance in meters reported by a proximity alert.


### Forum and service messages

These values are extracted by [`Variables::varMessageAutoDeleteTimerChanged()`](functions.md#variablesvarmessageautodeletetimerchanged). 

#### `$message_auto_delete_time`

**Documented type:** `int|null`.

New automatic message deletion time in seconds.

#### `$forum_topic_created`

**Documented type:** `array|null`.

Boolean marker set when a forum topic creation event is parsed.

#### `$forum_topic_name`

**Documented type:** `array|null`.

Forum topic name from a create/edit service event.

#### `$forum_topic_icon`

**Documented type:** `array|null`.

RGB icon color of a newly created forum topic.

#### `$forum_topic_emoji_id`

**Documented type:** `int|string|null`.

Custom emoji identifier used as the forum topic icon.

#### `$forum_topic_closed`

**Documented type:** `bool|null`.

Boolean marker intended for a topic-closed service event; current parser condition is inverted and does not set it for a normal non-null payload.

#### `$forum_topic_edited`

**Documented type:** `array|null`.

Boolean marker set when a topic edit event is parsed.

#### `$forum_topic_reopened`

**Documented type:** `bool|null`.

Boolean marker intended for a topic-reopened event; current parser condition is inverted.

#### `$forum_topic_hidden`

**Documented type:** `array|null`.

Boolean marker intended for a general topic hidden event; current parser condition is inverted.

#### `$forum_topic_unhidden`

**Documented type:** `array|null`.

Boolean marker intended for a general topic unhidden event; current parser condition is inverted.

#### `$ushared_request_id`

**Documented type:** `int|string|null`.

Request identifier from a user-sharing service message; current parser has an inverted null check.

#### `$shared_user_id`

**Documented type:** `int|string|null`.

User ID shared through a request button; current parser has an inverted null check.

#### `$cshared_request_id`

**Documented type:** `int|string|null`.

Request identifier from a chat-sharing service message; current parser has an inverted null check.

#### `$shared_chat_id`

**Documented type:** `int|string|null`.

Chat ID shared through a request button; current parser has an inverted null check.

#### `$write_access_allowed`

**Documented type:** `array|null`.

Web App write-access event data. The current parser references an undefined variable and does not accept the payload parameter.

#### `$video_chat_sceduled`

**Documented type:** `array|null`.

Start timestamp for a scheduled video chat. Property name is misspelled in the framework (`sceduled`).

#### `$video_chat_started`

**Documented type:** `bool|null`.

Boolean marker intended for a video-chat-started service event; current parser condition is inverted.

#### `$voice_chat_invited`

**Documented type:** `array|null`.

Array of users invited to a video chat.


### User profile photos and file

These values are extracted by [`Variables::varFile()`](functions.md#variablesvarfile). 

#### `$user_photos_count`

**Documented type:** `int|null`.

Total number of profile photos returned by `getUserProfilePhotos`.

#### `$user_photos`

**Documented type:** `array|null`.

Nested array of profile photo sizes returned by `getUserProfilePhotos`.

#### `$file_id`

**Documented type:** `string|null`.

Telegram `file_id` from a `File` object.

#### `$file_uid`

**Documented type:** `string|null`.

Stable `file_unique_id` from a `File` object.

#### `$file_mime`

**Documented type:** `string|null`.

Optional MIME type of the file.

#### `$file_size`

**Documented type:** `int|null`.

Optional file size in bytes.

#### `$file_path`

**Documented type:** `string|null`.

File path returned by Telegram `getFile`; use it with the configured Bot API base URL to download the file.


### Reply keyboard

These values are extracted by [`Variables::varReplyKeyboardMarkup()`](functions.md#variablesvarreplykeyboardmarkup). 

#### `$keyboard`

**Documented type:** `array|null`.

Rows of buttons in a `ReplyKeyboardMarkup`.

#### `$resize_keyboard`

**Documented type:** `array|null`.

Whether Telegram clients should resize the custom keyboard.

#### `$one_time_keyboard`

**Documented type:** `array|null`.

Whether the custom keyboard should be hidden after one use.

#### `$selective_keyboard`

**Documented type:** `array|null`.

Whether the reply keyboard is shown only to selected users.

#### `$keyboard_poll_type`

**Documented type:** `array|null`.

Optional poll type restriction for a keyboard button requesting a poll.

#### `$remove_keyboard`

**Documented type:** `array|null`.

Boolean flag from `ReplyKeyboardRemove`.


### Inline keyboard and login

These values are extracted by [`Variables::varInlineKeyboardMarkup()`](functions.md#variablesvarinlinekeyboardmarkup). 

#### `$inline_keyboard`

**Documented type:** `array|null`.

Rows of inline keyboard buttons from `InlineKeyboardMarkup`.

#### `$login_url`

**Documented type:** `string|null`.

URL used by a LoginUrl button.

#### `$login_forward_text`

**Documented type:** `string|null`.

Optional text shown in the forwarded message for a login URL button.

#### `$login_bot_username`

**Documented type:** `string|null`.

Optional bot username associated with the login URL.

#### `$login_request_write`

**Documented type:** `bool|null`.

Whether the login flow requests permission to write to the user.


### Callback query

These values are extracted by [`Variables::varCallbackQuery()`](functions.md#variablesvarcallbackquery). 

#### `$query_id`

**Documented type:** `int|string|null`.

Identifier of an inline query or callback query, depending on the update parser.

#### `$inline_message_id`

**Documented type:** `string|null`.

Identifier of the inline message associated with a callback/chosen inline result.

#### `$chat_instance`

**Documented type:** `string|null`.

Global identifier of the chat where the callback originated.

#### `$query_data`

**Documented type:** `string|null`.

Callback data supplied by the pressed inline keyboard button.

#### `$game_short_name`

**Documented type:** `array|null`.

Short name of the game associated with a callback query.

#### `$force_reply`

**Documented type:** `array|null`.

Boolean flag from `ForceReply`.


### Chat photo and membership

These values are extracted by [`Variables::varChatMemberUpdated()`](functions.md#variablesvarchatmemberupdated). 

#### `$small_photo_id`

**Documented type:** `int|string|null`.

Telegram `file_id` of the small chat profile photo.

#### `$small_photo_uid`

**Documented type:** `array|null`.

Stable `file_unique_id` of the small chat profile photo.

#### `$photo_id`

**Documented type:** `int|string|null`.

Telegram `file_id` of the large chat profile photo.

#### `$photo_uid`

**Documented type:** `array|null`.

Stable `file_unique_id` of the large chat profile photo.

#### `$invite_link`

**Documented type:** `string|null`.

Chat invite link object associated with a membership update or join request.

#### `$administrator_rights`

**Documented type:** `array|null`.

`ChatAdministratorRights` object supplied to the parser.

#### `$chat_member`

**Documented type:** `array|null`.

New/current `ChatMember` object in a membership update.

#### `$via_chat_folder_invite_link`

**Documented type:** `string|null`.

Whether the user joined through a chat-folder invite link.

#### `$user_chat_id`

**Documented type:** `int|string|null`.

Private chat identifier usable to contact the user who submitted a join request, subject to Telegram's limitations.

#### `$user_bio`

**Documented type:** `string|null`.

Optional bio supplied with a chat join request.


### Sticker

These values are extracted by [`Variables::varSticker()`](functions.md#variablesvarsticker). 

#### `$sticker_id`

**Documented type:** `int|string|null`.

Telegram `file_id` for the sticker.

#### `$sticker_uid`

**Documented type:** `array|null`.

Stable `file_unique_id` for the sticker.

#### `$sticker_type`

**Documented type:** `array|null`.

Sticker format/type, such as regular, mask or custom emoji.

#### `$sticker_witdh`

**Documented type:** `array|null`.

Sticker width in pixels. The framework property is misspelled (`witdh`); no `$sticker_width` property is created.

#### `$sticker_height`

**Documented type:** `int|null`.

Sticker height in pixels.

#### `$sticker_animated`

**Documented type:** `array|null`.

Whether the sticker is animated.

#### `$sticker_video`

**Documented type:** `array|null`.

Whether the sticker is a video sticker.

#### `$sticker_thumb`

**Documented type:** `array|null`.

Optional sticker thumbnail object (the parser currently reads `thumb`).

#### `$sticker_emoji`

**Documented type:** `array|null`.

Associated emoji, when present.

#### `$sticker_set`

**Documented type:** `array|null`.

Name of the sticker set containing the sticker.

#### `$sticker_premium`

**Documented type:** `array|null`.

Premium animation metadata associated with the sticker, when provided.

#### `$sticker_mask`

**Documented type:** `array|null`.

Optional mask-position object.

#### `$sticker_custom_emoji_id`

**Documented type:** `int|string|null`.

Custom emoji identifier for a custom-emoji sticker.

#### `$sticker_needs_repainting`

**Documented type:** `array|null`.

Whether the sticker needs repainting.

#### `$sticker_size`

**Documented type:** `int|null`.

Optional sticker file size in bytes.


### Sticker set

These values are extracted by [`Variables::varStickerSet()`](functions.md#variablesvarstickerset). 

#### `$stickers`

**Documented type:** `array|null`.

Sticker-set name.

#### `$stickers_name`

**Documented type:** `array|null`.

Human-readable sticker-set title.

#### `$stickers_type`

**Documented type:** `array|null`.

Sticker format/type of the set.

#### `$stickers_animated`

**Documented type:** `array|null`.

Whether the set contains animated stickers.

#### `$stickers_video`

**Documented type:** `array|null`.

Whether the set contains video stickers.

#### `$stickers_thumb`

**Documented type:** `array|null`.

Optional sticker-set thumbnail object.


### Inline query and game

These values are extracted by [`Variables::varInlineQuery()`](functions.md#variablesvarinlinequery). 

#### `$id`

**Documented type:** `int|string|null`.

Identifier set by the inline-query parser to the inline query ID, or by chosen-inline-result parser to the result ID.

#### `$location`

**Documented type:** `array|null`.

Optional `Location` object associated with an inline query or chosen inline result.

#### `$query`

**Documented type:** `string|null`.

Search query text from an inline query or chosen inline result.

#### `$offset`

**Documented type:** `string|null`.

Pagination offset supplied by an inline query.

#### `$game_name`

**Documented type:** `string|null`.

Game title from a Telegram `Game` object.

#### `$game_description`

**Documented type:** `string|null`.

Game description from a Telegram `Game` object.

#### `$animation`

**Documented type:** `array|null`.

Animation object associated with a game. Current `varGame()` calls `varAnimation()` as a global function rather than `$this->varAnimation()`, which can fail when this field is present.


## Configuration

The configuration array is loaded from `configs.php` and passed to `NeleBotX` / `Variables`. Configuration values are ordinary PHP array entries, not `Variables` runtime properties. The types below describe the expected configuration shape; values are not validated uniformly by a schema.

### Telegram API and message defaults

#### `configs.token`

**Expected type:** `string`. **Default:** `''`.

Bot API token. Leave empty only when the application deliberately supplies a token dynamically (multi-bot mode). Treat as a secret.

#### `configs.webhook_password`

**Expected type:** `string`. **Default:** `''`.

Password used by the framework's webhook-key encryption workflow. Keep private and use a strong random value.

#### `configs.webhook_secret_token`

**Expected type:** `string`. **Default:** `''`.

Secret expected in Telegram's `X-Telegram-Bot-Api-Secret-Token` request header. The HTTP entry point must validate it; configuring it alone is not validation.

#### `configs.telegram_bot_api`

**Expected type:** `string (URL)`. **Default:** `https://api.telegram.org`.

Base URL of Telegram's cloud Bot API or a compatible local Bot API server.

#### `configs.parse_mode`

**Expected type:** `string`. **Default:** `html`.

Default parse mode used by message wrappers. Telegram supports `HTML`, `Markdown` and `MarkdownV2`; prefer the exact supported spelling and escape content correctly.

#### `configs.disable_web_page_preview`

**Expected type:** `bool`. **Default:** `true`.

Default link-preview setting used by the message wrapper. The Telegram API has evolved toward `link_preview_options`; this framework setting is a legacy boolean.

#### `configs.allow_sending_w/out_reply`

**Expected type:** `bool`. **Default:** `true`.

Legacy framework setting intended to control sending without a reply. Its key contains `/` and must be accessed as `$configs['allow_sending_w/out_reply']`; verify actual consumers before changing it.

#### `configs.disable_notification`

**Expected type:** `bool`. **Default:** `false`.

Default silent-delivery option for supported send methods.

#### `configs.protect_content`

**Expected type:** `bool`. **Default:** `false`.

Default protection setting for supported send methods; asks Telegram to restrict forwarding and saving.

#### `configs.commands_alias`

**Expected type:** `array<string>`. **Default:** `['/', '!']`.

One-character prefixes recognized by `Variables::varMessage()` when extracting `$command`.

### Logging

#### `configs.log_file`

**Expected type:** `string (filesystem path)`. **Default:** `/var/log/bots/NeleBotX.log`.

Absolute path to the framework log file. The PHP-FPM/Apache runtime user must be able to write to the file and traverse its parent directories.

#### `configs.log_chat`

**Expected type:** `int|string`. **Default:** `244432022`.

Optional Telegram chat ID used by `TelegramBot::sendLog()` to forward log messages. Set to an empty value/false when unused.

#### `configs.log_types`

**Expected type:** `array<int,bool>`. **Default:** see `configs.php`.

Map of PHP error constants to enabled/disabled logging flags. Error constants are documented in the PHP error handling reference.

[PHP reference: Error constants](https://www.php.net/manual/en/errorfunc.constants.php).

### Redis

#### `configs.redis.status`

**Expected type:** `bool`. **Default:** `false`.

Enables Redis integration. AntiFlood requires Redis to be enabled and reachable.

#### `configs.redis.database`

**Expected type:** `int`. **Default:** `1`.

Redis logical database index selected after connecting.

#### `configs.redis.host`

**Expected type:** `string`. **Default:** `localhost`.

Redis server hostname or IP address.

#### `configs.redis.port`

**Expected type:** `int`. **Default:** `6379`.

Redis TCP port.

#### `configs.redis.password`

**Expected type:** `string|false`. **Default:** `false`.

Redis authentication password, if required. Keep it secret.

#### `configs.redis.antiflood.status`

**Expected type:** `bool`. **Default:** `true`.

Enables the AntiFlood policy when Redis integration is active.

#### `configs.redis.antiflood.messages`

**Expected type:** `int`. **Default:** `5`.

Number of updates/messages within the configured time window that triggers the threshold.

#### `configs.redis.antiflood.seconds`

**Expected type:** `int`. **Default:** `2`.

Time window, in seconds, used by AntiFlood.

#### `configs.redis.antiflood.ban_time`

**Expected type:** `int`. **Default:** `60`.

Temporary ban duration, in seconds.

#### `configs.redis.antiflood.notice`

**Expected type:** `string`. **Default:** `'‼️ You have been banned due to message flood for 1 minute!'`.

Notice text used when the AntiFlood threshold is reached.

[Redis command reference](https://redis.io/docs/latest/commands/).

### SQL database

#### `configs.database.status`

**Expected type:** `bool`. **Default:** `false`.

Enables PDO database integration.

#### `configs.database.type`

**Expected type:** `string`. **Default:** `''`.

Driver selector. Supported values in this implementation are `sqlite`, `mysql` and `pgsql`.

#### `configs.database.database_name`

**Expected type:** `string`. **Default:** `''`.

Database name for MySQL/PostgreSQL or filesystem path for SQLite.

#### `configs.database.user`

**Expected type:** `string`. **Default:** `''`.

Database username for MySQL/PostgreSQL; not normally required for SQLite.

#### `configs.database.password`

**Expected type:** `string`. **Default:** `''`.

Database password for MySQL/PostgreSQL. Keep it secret.

#### `configs.database.host`

**Expected type:** `string`. **Default:** `localhost`.

Database server host for MySQL/PostgreSQL.

[PHP references: PDO](https://www.php.net/manual/en/book.pdo.php), [PDO drivers](https://www.php.net/manual/en/pdo.drivers.php).

### Framework behavior

#### `configs.admins`

**Expected type:** `array<int|string>`. **Default:** `[244432022]`.

Telegram user IDs treated as framework administrators by `Variables::isAdmin()` and excluded from AntiFlood checks.

#### `configs.plugins`

**Expected type:** `array<string,bool>`. **Default:** `management.php => true`, `commands.php => true`.

Map of plugin filenames to enabled/disabled flags.

#### `configs.response`

**Expected type:** `bool`. **Default:** `false`.

Default response mode passed to the low-level Telegram request handler. When false, the handler may echo the raw HTTP response rather than returning decoded JSON.

#### `configs.post`

**Expected type:** `bool`. **Default:** `true`.

Default HTTP request method: true for POST, false for GET.

#### `configs.timeout`

**Expected type:** `int`. **Default:** `2`.

Default Telegram API request timeout in seconds.

#### `configs.version`

**Expected type:** `string`. **Default:** `'2.0'`.

Framework version identifier.

## Known implementation limitations to account for

The following are documented deliberately so the reference does not imply that affected properties work correctly in every update path. These are observations from the current `Variables.php`; this documentation does not silently change runtime behavior.

1. **`$message_thread_id`:** `varMessage()` reads the array key `'message_thread_id '` with a trailing space. Telegram sends `message_thread_id` without that space, so the property may not be populated.
2. **Video notes:** `varVideoNote($VideoNote)` reads from `$Video` instead of `$VideoNote`. The six `$video_note_*` properties are therefore unreliable and may raise notices.
3. **Shared user/chat and write access:** `varUserShared()`, `varChatShared()` and several event parsers use inverted `is_null()` checks. `varWriteAccessAllowed()` also has no parameter but references `$WriteAccessAllowed`. The related properties may not be populated.
4. **Forum/video-chat service flags:** `varForumTopicClosed()`, `varForumTopicReopened()`, `varGeneralForumTopicHidden()`, `varGeneralForumTopicUnhidden()` and `varVideoChatStarted()` use inverted null checks.
5. **Poll answers:** `varPollAnswer()` assigns `$PollAnswer['text']` to `$poll_id`; the Telegram `PollAnswer` object uses `poll_id`, so `$poll_id` is not correctly populated on that path.
6. **Sticker width:** the framework property is named `$sticker_witdh` (typo). No `$sticker_width` property is created.
7. **Scheduled video chat:** the property is named `$video_chat_sceduled` (typo). The spelling is retained for compatibility with the current source.
8. **Game animation:** `varGame()` calls `varAnimation()` as a global function instead of `$this->varAnimation()`, which can fail when the game includes an animation.
9. **Story:** `varStory()` currently has an empty body; the `$story` payload is not expanded into dedicated convenience properties.
10. **Departing/new members:** calls to `varNewMembers()` and `varLeftMember()` inside `varMessage()` are commented out. The related convenience properties are not populated automatically from ordinary message service updates.

Do not build new plugin logic around these affected properties until the corresponding source defects are fixed and regression-tested. For optional fields, always test their presence before use.

## Related framework methods

- [`Variables::__construct()`](functions.md#variablesconstruct) — receives the configuration and raw update.
- [`Variables::varMessage()`](functions.md#variablesvarmessage) — extracts message properties.
- [`Variables::varUser()`](functions.md#variablesvaruser) — extracts the current user.
- [`Variables::varChat()`](functions.md#variablesvarchat) — extracts the current chat.
- [`Variables::isAdmin()`](functions.md#variablesisadmin), [`Variables::isOwner()`](functions.md#variablesisowner), [`Variables::isStaff()`](functions.md#variablesisstaff) — permission checks.
- [`NeleBotX::loadUser()`](functions.md#nelebotxloaduser), [`NeleBotX::loadGroup()`](functions.md#nelebotxloadgroup), [`NeleBotX::loadChannel()`](functions.md#nelebotxloadchannel) — database-backed context loading.

## Official references

- [Telegram Bot API — Update](https://core.telegram.org/bots/api#update)
- [Telegram Bot API — Message](https://core.telegram.org/bots/api#message)
- [Telegram Bot API — User](https://core.telegram.org/bots/api#user)
- [Telegram Bot API — Chat](https://core.telegram.org/bots/api#chat)
- [Telegram Bot API — MessageEntity](https://core.telegram.org/bots/api#messageentity)
- [Telegram Bot API — File](https://core.telegram.org/bots/api#file)
- [PHP — Properties](https://www.php.net/manual/en/language.oop5.properties.php)
- [PHP — Dynamic properties](https://www.php.net/manual/en/class.allowdynamicproperties.php)
- [PHP — Type declarations](https://www.php.net/manual/en/language.types.declarations.php)
- [PHP — Error constants](https://www.php.net/manual/en/errorfunc.constants.php)
