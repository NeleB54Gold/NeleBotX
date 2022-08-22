<?php

## Example Command Plugin
## Commands for Personal Commands

// Creation of the Table if not exists
$db->query("CREATE TABLE IF NOT EXISTS `personal_commands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `command` varchar(255) NOT NULL,
  `tect` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

if(in_array($v->chat_type, ['group', 'supergroup'])){
    if($v->isStaff()){
        if(strpos($v->command, 'personal')===0){
            $e = explode(' ', $v->command, 3);
            $command = $e[1];
            $text = $e[2];
            $check = $db->query("SELECT * FROM personal_commands WHERE chat_id = ' . $v->chat_id . ' AND command = ' . $command . '", false, 1);
            if(!$check){
                $db->query("INSERT INTO personal_commands (chat_id, user_id, command, text) VALUES (' . $v->chat_id . ', ' . $v->user_id . ', ' . $command . ', ' . $text . ')");
                $bot->sendMessage($v->chat_id, 'Command successfully created!');
            } else {
                $bot->sendMessage($v->chat_id, 'Command already exists!');
            }
            die;
        }
    }
}