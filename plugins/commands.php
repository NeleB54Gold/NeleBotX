<?php

## Default Commands plugin
## Here you can write all the code that will be executed.
## Take these commands as an example to write your Bot!

# Ignore inline messages (via @)
if ($v->via_bot) die;

# Print the result from Database
if ($v->isAdmin()) {
	if ($v->command == 'data') {
		$juser = $bot->code(substr(json_encode($user, JSON_PRETTY_PRINT), 0, 1024), 1);
		$jgroup = $bot->code(substr(json_encode($group, JSON_PRETTY_PRINT), 0, 1024), 1);
		$jchannel = $bot->code(substr(json_encode($channel, JSON_PRETTY_PRINT), 0, 1024), 1);
		$bot->sendMessage($v->chat_id, $juser . PHP_EOL . $jgroup . PHP_EOL . $jchannel);
		die;
	}
	# Dump the update
	elseif ($v->command == 'dump') {
		$bot->sendMessage($v->chat_id, $bot->code(json_encode($v->update, JSON_PRETTY_PRINT), 1));
		die;
	}
	# Dump NeleBot X Variables
	elseif ($v->command == 'neledump') {
		$bot->sendMessage($v->chat_id, $bot->code(substr(json_encode($v, JSON_PRETTY_PRINT), 0, 4096), 1));
		die;
	}
}

# Private chat with Bot
if ($v->chat_type == 'private') {
	# Register that the user has started the Bot in one string
	if ($bot->configs['database']['status'] && $user['status'] !== 'started') $db->setStatus($v->user_id, 'started');
	# Check the exec time of all script to here (Bot-Admin only)
	if ($v->isAdmin() && $v->command == 'performance') {
		$end_time = microtime(true);
		$exec_time = ($end_time - $start_time) * 1000; // Microseconds to milliseconds
		$bot->sendMessage($v->chat_id, $bot->bold('Ziumm!') . PHP_EOL . 'Only ' . round($exec_time, 4) . ' milliseconds to execute this script!');
	}
	# Ping command (Bot-Admin only)
	elseif ($v->isAdmin() && strpos($v->command, 'ping') === 0) {
		$times[] = microtime(true);
		if (strpos($v->command, 'ping ') === 0 && is_numeric(str_replace('ping ', '', $v->command))) {
			$max = round(str_replace('ping ', '', $v->command));
		} else {
			$max = 3;
		}
		for ($p = 1; $p <= $max; $p++) {
			$bot->sendMessage($v->chat_id, $bot->bold('Pong!'));
			$times[] = microtime(true);
		}
		foreach ($times as $k => $time) {
			if ($k !== 0) $pings .= round(($time - $times[$k - 1]) * 1000, 2) . ' milliseconds' . PHP_EOL; // Microseconds to milliseconds
		}
		$bot->sendMessage($v->chat_id, $pings . PHP_EOL . $bot->bold('Average: ') . round((((end($times) - $times[0]) / $max) * 1000), 2) . ' milliseconds');
	}
	# Start Command (via command or query_data)
	elseif ($v->command == 'start' || $v->query_data == 'start') {
		$buttons[][] = $bot->createInlineButton('📥 Download NeleBot X!', 'https://t.me/NeleBotX', 'url');
		$buttons[][] = $bot->createInlineButton('ℹ️ About NeleBot X', 'help');
		$link_preview = $bot->text_link(' ', 'https://telegra.ph/file/f508ceecf6dedc95c3be1.jpg');
		$t = $link_preview . $bot->bold('NeleBot X Framework') . PHP_EOL . $bot->italic('Develop your own Telegram Bot in PHP!');
		if ($v->query_id) {
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
			$bot->answerCBQ($v->query_id);
		} else {
			$bot->sendMessage($v->chat_id, $t, $buttons, 'def', 0);
		}
	}
	# Help command
	elseif ($v->command == 'help' || $v->query_data == 'help') {
		$buttons[] = [
			$bot->createInlineButton('📥 Download NeleBot X!', 'https://t.me/NeleBotX', 'url'),
			$bot->createInlineButton('❓ F.A.Q.', 'https://t.me/NeleBotX', 'url')
		];
		$buttons[][] = $bot->createInlineButton('🙋🏻‍♂️ What NeleBot X can do?', 'examples');
		$buttons[][] = $bot->createInlineButton('😁 Guide', 'https://neleb54gold.github.io/NeleBotX/', 'web_app');
		$buttons[][] = $bot->createInlineButton('◀️ Back', 'start');
		# Check redis status to cache the commands list
		if ($bot->configs['redis']['status']) {
			if ($cache = $db->rget('NeleBotX-' . $bot->id . '-commandsList')) $commands = json_decode($cache, true);
			if (!$commands) $commands = $bot->getCommands();
			if ($commands['ok']) {
				$db->rset('NeleBotX-' . $bot->id . '-commandsList', json_encode($commands), 60);
			} else {
				if ($cache) $db->rdel('NeleBotX-' . $bot->id . '-commandsList');
			}
			if (isset($commands['result']) && !empty($commands['result']))  {
				foreach ($commands['result'] as $command) {
					$list .= PHP_EOL . '/' . $command['command'] . ' - ' . $bot->italic($command['description']);
				}
			}
		} else {
			$commands = $bot->getCommands();
		}
		$link_preview = $bot->text_link(" ", 'https://telegra.ph/file/f508ceecf6dedc95c3be1.jpg', 0);
		$t = $link_preview . $bot->bold('NeleBot X Framework') . PHP_EOL . $bot->italic('How can I help you?') . $list;
		if ($v->query_id) {
			$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
			$bot->answerCBQ($v->query_id);
		} else {
			$bot->sendMessage($v->chat_id, $t, $buttons, 'def', 0);
		}
	}
	# Uplolad test command [Check the read/write permissions]
	elseif ($v->command == 'upload') {
		file_put_contents('test.json', json_encode($v->getUser()));
		$file = $bot->createFileInput('test.json', 'text/json', 'Test file for ' . $v->user_first_name . '.json');
		$bot->sendDocument($v->chat_id, $file, '💾 Uploaded!');
		unlink('test.json');
	}
	# What NeleBot X can do?
	elseif ($v->query_data == 'examples') {
		$bot->answerCBQ($v->query_id);
		$t = $bot->bold('😎 What NeleBot X Framework you can do? Everything!') . PHP_EOL;
		$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
		sleep(1);
		$bot->editConfigs('response', true);
		$s = $bot->sendMessage($v->chat_id, '🙂 Ehy!');
		sleep(1);
		$bot->editText($v->chat_id, $s['result']['message_id'], '😗 I\'m alive!');
		sleep(1);
		$bot->deleteMessage($v->chat_id, $s['result']['message_id']);
		$t .= PHP_EOL . '✅ Can send, edit and delete messages.';
		$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
		sleep(1);
		$m = $bot->sendPhoto($v->chat_id, 'https://telegra.ph/file/0c5c55e503969b72c18a5.jpg', '🤓 This is the caption! :D');
		sleep(1);
		$bot->editMedia($v->chat_id, $m['result']['message_id'], $bot->createPhotoInput('https://telegra.ph/file/f508ceecf6dedc95c3be1.jpg', 'I\'m NeleBot X!'));
		sleep(2);
		$bot->deleteMessage($v->chat_id, $m['result']['message_id']);
		$t .= PHP_EOL . '✅ Can send, edit and delete ' . $bot->bold('any type') . ' of message.';
		$t .= PHP_EOL . '✅ Can reply to ' . $bot->bold('inline queries') . ' in all ways.';
		$t .= PHP_EOL . '✅ Can host multiple bots in ' . $bot->bold('one') . ' webhook.';
		$t .= PHP_EOL . '✅ Can ' . $bot->bold('report') . ' php errors.';
		$t .= PHP_EOL . '✅ Can be ' . $bot->bold('connected') . ' to Redis, MySQL/SQLite/PostgreSQL.';
		$t .= PHP_EOL . PHP_EOL . '✅ Can do everything ' . $bot->bold('with the right php developer... 😉');
		$buttons= [
			[$bot->createInlineButton('✍️ Try inline mode', '1', 'switch_inline_query')],
			[$bot->createInlineButton('🖊 Try inline mode here', '2', 'switch_inline_query_current_chat')],
			[$bot->createInlineButton('🌐 Link any url', 't.me/NeleBotX', 'url')],
			[$bot->createInlineButton('◀️ Back', 'help'), $bot->createInlineButton('🔗 Connect any callback', 'start')],
		];
		$bot->editText($v->chat_id, $v->message_id, $t, $buttons, 'def', 0);
	}
	# Unknown command/button/action
	else {
		$help = PHP_EOL . 'Try /help for a command list!';
		if ($v->command) {
			$t = '😶 Unknown command...' . $help;
		} elseif ($v->query_data) {
			$t = '😶 Unknown button...' . $help;
		} else {
			$t = '💤 Nothing to do...' . $help;
		}
		if ($v->query_id) {
			$bot->answerCBQ($v->query_id, $t);
		} else {
			$bot->sendMessage($v->chat_id, $t);
		}
	}
}

# Inline commands
if ($v->update['inline_query']) {
	$results = [];
	if ($v->query == 1) {
		# Exemples of InlineQueryResultArticle
		$results[] = $bot->createInlineArticle(
			1,
			'Text message',
			'Exemple of InlineQueryResultArticle',
			$bot->createTextInput('Text', 'html', false)
		);
		$results[] = $bot->createInlineArticle(
			2,
			'Location',
			'Exemple of InlineQueryResultArticle',
			$bot->createLocationInput(42, 12, 60)
		);
		$results[] = $bot->createInlineArticle(
			3,
			'Venue',
			'Exemple of InlineQueryResultArticle',
			$bot->createVenueInput(42, 12, 'Italy', 'Rome')
		);
		$results[] = $bot->createInlineArticle(
			4,
			'Contact',
			'Exemple of InlineQueryResultArticle',
			$bot->createContactInput('+42777', 'Telegram')
		);
	} elseif ($v->query == 2) {
		# Exemples of others InlineQueryResults
		$results[] = $bot->createInlinePhoto(
			1,
			'Photo',
			'Exemple of InlineQueryResultPhoto',
			'https://telegra.ph/file/a5962d4eedfbb52728aba.jpg',
			'This is a demo of InlineQueryResultPhoto'
		);
		/*$results[] = $bot->createInlineGif(
			2,
			'GIF',
			'Exemple of InlineQueryResultGif',
			'FILE_ID', // Here you can set a file_id or url
			'This is a demo of InlineQueryResultGif'
		);
		$results[] = $bot->createInlineVideo(
			3,
			'Video',
			'Exemple of InlineQueryResultVideo',
			'FILE_ID',
			'This is a demo of InlineQueryResultVideo'
		);
		$results[] = $bot->createInlineAudio(
			4,
			'Audio',
			'Exemple of InlineQueryResultAudio',
			'FILE_ID',
			'This is a demo of InlineQueryResultAudio'
		);
		$results[] = $bot->createInlineVoice(
			5,
			'Voice',
			'Exemple of InlineQueryResultVoice',
			'FILE_ID',
			'This is a demo of InlineQueryResultVoice'
		);
		$results[] = $bot->createInlineDocument(
			6,
			'Document',
			'Exemple of InlineQueryResultDocument',
			'FILE_ID',
			'This is a demo of InlineQueryResultDocument'
		);*/
		$results[] = $bot->createInlineLocation(
			7,
			'Location',
			42, 
			12,
			60
		);
		$results[] = $bot->createInlineVenue(
			8,
			'Rome',
			'Italy, Rome',
			42, 
			12
		);
		$results[] = $bot->createInlineContact(
			9,
			'+42777',
			'Telegram'
		);
	} else {
		$sw_text = 'Start the Bot!';
		$sw_arg = 'inline'; // The message the bot receive is '/start inline'
		$results[] = $bot->createInlineArticle(
			1,
			'This is NeleBot X Framework!',
			'Here you can find demo of bot functions!',
			$bot->createTextInput(':)')
		);
	}
	$r = $bot->answerIQ($v->id, $results, $sw_text, $sw_arg);
} 

# Get the chosen inline result choosed by the user
# To get this update type you need to enable the Inline feedback from @BotFather!
elseif ($v->update['chosen_inline_result']) {
	$bot->sendMessage($v->user_id, 'You have chosed: ' . $v->id);
}

?>