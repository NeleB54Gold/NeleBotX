<?php

## Example Command Plugin
## Commands for Welcome Bot

// Simple Welcome only with text
if(isset($v->update) and $v->update['new_chat_member'] and in_array($v->chat_type, ['group', 'supergroup'])){
    $t = "Welcome " . $bot->tag($v->user_id, $v->name) . " in the Group";
    $bot->sendMessage($v->chat_id, $t);
}

// Simple Welcome with text and a Button
if(isset($v->update) and $v->update['new_chat_member'] and in_array($v->chat_type, ['group', 'supergroup'])){
    $t = "Welcome " . $bot->tag($v->user_id, $v->name) . " in the Group";
    $bot->sendMessage($v->chat_id, $t, $bot->createInlineButton('📣 Join Channel', '@NeleBotX'));
}