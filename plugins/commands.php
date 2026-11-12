<?php


// This bundled plugin uses the legacy normalized message properties. New Bot API
// update types are exposed as $v->update_type / $v->update_data and should be
// handled by a purpose-built plugin instead.
if (!empty($v->update_type)) {
    return;
}

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

		$commands = [];
		if ($bot->configs['redis']['status']) {
			$cache = $db->rget('NeleBotX-' . $bot->id . '-commandsList');
			if ($cache) $commands = json_decode($cache, true) ?: [];
		}
		if (empty($commands)) {
			$commands = $bot->getCommands();
			if (!empty($commands['ok']) && $bot->configs['redis']['status']) {
				$db->rset('NeleBotX-' . $bot->id . '-commandsList', json_encode($commands), 60);
			}
		}

		$list = PHP_EOL . PHP_EOL . $bot->bold('🧪 Commands to test') . PHP_EOL;
		$list .= '/start - Start the bot' . PHP_EOL;
		$list .= '/help - Show this command list' . PHP_EOL;
		$list .= '/examples - Run the interactive feature demo' . PHP_EOL;
		$list .= '/upload - Test document upload and filesystem permissions' . PHP_EOL;
		$list .= PHP_EOL . $bot->bold('⚡ Inline mode') . PHP_EOL;
		$list .= 'Open any chat and send: @YourBotUsername 1' . PHP_EOL;
		$list .= 'Then try: @YourBotUsername 2';

		if ($v->isAdmin()) {
			$list .= PHP_EOL . PHP_EOL . $bot->bold('🔧 Admin commands') . PHP_EOL;
			$list .= '/performance - Measure script execution time' . PHP_EOL;
			$list .= '/ping [count] - Send ping messages and measure latency' . PHP_EOL;
			$list .= '/data - Inspect user, group and channel data' . PHP_EOL;
			$list .= '/dump - Show the raw Telegram update' . PHP_EOL;
			$list .= '/neledump - Inspect normalized NeleBot X variables';
		}

		if (!empty($commands['result']) && is_array($commands['result'])) {
			$list .= PHP_EOL . PHP_EOL . $bot->bold('📋 Telegram command menu');
			foreach ($commands['result'] as $command) {
				if (!isset($command['command'], $command['description'])) continue;
				$list .= PHP_EOL . '/' . $command['command'] . ' - ' . $bot->italic($command['description']);
			}
		}

		$link_preview = $bot->text_link(' ', 'https://telegra.ph/file/f508ceecf6dedc95c3be1.jpg', 0);
		$t = $link_preview . $bot->bold('NeleBot X Framework') . PHP_EOL . $bot->italic('Try these commands to explore the framework:') . $list;
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
	elseif ($v->command == 'examples' || $v->query_data == 'examples') {
		if ($v->query_id) {
			$bot->answerCBQ($v->query_id);
			$demo_message_id = $v->message_id;
		} else {
			$initial = $bot->sendMessage($v->chat_id, $bot->bold('😎 What NeleBot X Framework can do? Everything!'));
			$demo_message_id = $initial['result']['message_id'] ?? null;
		}
		$t = $bot->bold('😎 What NeleBot X Framework can do? Everything!') . PHP_EOL;
		sleep(1);
		$bot->editConfigs('response', true);
		$s = $bot->sendMessage($v->chat_id, '🙂 Ehy!');
		sleep(1);
		$bot->editText($v->chat_id, $s['result']['message_id'], '😗 I\'m alive!');
		sleep(1);
		$bot->deleteMessage($v->chat_id, $s['result']['message_id']);
		$t .= PHP_EOL . '✅ Can send, edit and delete messages.';
		if ($demo_message_id) $bot->editText($v->chat_id, $demo_message_id, $t);
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
		if ($demo_message_id) $bot->editText($v->chat_id, $demo_message_id, $t, $buttons, 'def', 0);
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


/*
 * NELEBOT X - DEVELOPER CODE EXAMPLES
 * Copy the examples you need into the command section above and adapt them.
 * These snippets are intentionally disabled: they are templates, not commands.
 *
 * 1. A simple command and message formatting
 *
 * if ($v->command === 'hello') {
 *     $bot->sendMessage(
 *         $v->chat_id,
 *         $bot->bold('Hello ' . $v->user_first_name) . PHP_EOL .
 *         $bot->italic('Welcome to my bot!')
 *     );
 * }
 *
 * 2. Reply to the message that triggered the command
 *
 * if ($v->command === 'reply') {
 *     $bot->sendMessage($v->chat_id, 'This is a reply.', false, 'def', 'def', $v->message_id);
 * }
 *
 * 3. Inline keyboard with callback buttons
 *
 * if ($v->command === 'menu') {
 *     $keyboard = [
 *         [$bot->createInlineButton('Show profile', 'profile')],
 *         [$bot->createInlineButton('Open website', 'https://example.com', 'url')]
 *     ];
 *     $bot->sendMessage($v->chat_id, 'Choose an action:', $keyboard);
 * }
 *
 * if ($v->query_data === 'profile') {
 *     $bot->answerCBQ($v->query_id);
 *     $bot->sendMessage($v->chat_id, 'Your Telegram ID: ' . $v->user_id);
 * }
 *
 * 4. Edit the message containing the pressed inline button
 *
 * if ($v->query_data === 'refresh') {
 *     $bot->editText($v->chat_id, $v->message_id, 'Updated at ' . date('H:i:s'));
 *     $bot->answerCBQ($v->query_id, 'Updated!');
 * }
 *
 * 5. Reply keyboard and remove it later
 *
 * if ($v->command === 'keyboard') {
 *     $keyboard = [
 *         [$bot->createButton('Option A'), $bot->createButton('Option B')],
 *         [$bot->createButton('Share contact', 'request_contact')],
 *         [$bot->createButton('Share location', 'request_location')]
 *     ];
 *     $bot->sendMessage($v->chat_id, 'Choose or share information:', $keyboard, 'def', 'def', false, 'keyboard');
 * }
 *
 * if ($v->text === 'Hide keyboard') {
 *     $bot->sendMessage($v->chat_id, 'Keyboard removed.', [[]], 'def', 'def', false, 'remove');
 * }
 *
 * 6. Send photos, documents and locations
 *
 * if ($v->command === 'photo') {
 *     $bot->sendPhoto($v->chat_id, 'https://example.com/photo.jpg', 'Photo caption');
 * }
 *
 * if ($v->command === 'document') {
 *     $bot->sendDocument($v->chat_id, 'https://example.com/manual.pdf', 'PDF example');
 * }
 *
 * if ($v->command === 'location') {
 *     $bot->sendLocation($v->chat_id, 41.9028, 12.4964);
 * }
 *
 * 7. Send a poll or a dice
 *
 * if ($v->command === 'poll') {
 *     $bot->sendPoll($v->chat_id, 'Which language do you prefer?', ['PHP', 'JavaScript', 'Python']);
 * }
 *
 * if ($v->command === 'dice') {
 *     $bot->sendDice($v->chat_id, '🎲');
 * }
 *
 * 8. Work with the current chat and member information
 *
 * if ($v->command === 'chat') {
 *     $chat = $bot->getChat($v->chat_id);
 *     if (!empty($chat['ok'])) {
 *         $title = $chat['result']['title'] ?? $chat['result']['first_name'] ?? 'Private chat';
 *         $bot->sendMessage($v->chat_id, 'Chat: ' . $title);
 *     }
 * }
 *
 * if ($v->command === 'member' && $v->chat_type !== 'private') {
 *     $member = $bot->getMember($v->chat_id, $v->user_id);
 *     if (!empty($member['ok'])) {
 *         $bot->sendMessage($v->chat_id, 'Your status: ' . $member['result']['status']);
 *     }
 * }
 *
 * 9. Handle API errors instead of assuming every request succeeds
 *
 * $result = $bot->sendMessage($v->chat_id, 'Request example');
 * if (empty($result['ok'])) {
 *     $description = $result['description'] ?? 'Unknown Telegram API error';
 *     $bot->sendMessage($v->chat_id, 'Telegram returned an error: ' . $description);
 * }
 *
 * 10. Call a Bot API method not wrapped by a convenience method
 *
 * $result = $bot->callApi('getChat', ['chat_id' => $v->chat_id], false);
 *
 * 11. Bot API 10.4 widget methods
 * These methods accept the official Bot API parameter arrays. Consult the
 * Telegram Bot API documentation for the required fields before using them.
 *
 * $result = $bot->sendWidget([
 *     // Add the required fields for sendWidget here.
 * ]);
 *
 * $result = $bot->sendSharedWidget([
 *     // Add the required fields for sendSharedWidget here.
 * ]);
 *
 * $result = $bot->answerWidgetQuery([
 *     // Add the required fields for answerWidgetQuery here.
 * ]);
 *
 * $result = $bot->editMessageWidgetData([
 *     // Add the required fields for editMessageWidgetData here.
 * ]);
 *
 * $result = $bot->editSharedWidgetData([
 *     // Add the required fields for editSharedWidgetData here.
 * ]);
 *
 * 12. Additional Bot API 10.4 methods
 *
 * $backdrops = $bot->getChatShareThemeBackdrops();
 * $gift = $bot->getUniqueGift('GIFT_NAME');
 * $deleted = $bot->deleteBusinessAccountGift('BUSINESS_CONNECTION_ID', 'OWNED_GIFT_ID');
 *
 * IMPORTANT:
 * - Never hard-code bot tokens, passwords or webhook secrets in plugins.
 * - Validate user-provided values before using them in database queries or API calls.
 * - Check the returned ['ok'] field when the result affects important operations.
 * - The bundled plugin ignores update types it does not normalize; handle newer
 *   update payloads in a dedicated plugin using the raw update structure.
 */

?>