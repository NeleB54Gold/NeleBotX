<?php

# These are your NeleBotX configurations
$configs = [

	# 1. Telegram API configs
		# 1. Set the Telegram Bot API token		(Optional)	[Number:Text/Crypted]
		'token'						=> '',
		# 2. Telegram Local/Public API			(Required)	[URL]
		'telegram_bot_api'			=> 'https://api.telegram.org',
		# 3. Default parse mode					(Optional)	[Text: html/markdown/markdownv2]
		'parse_mode'				=> 'html',
		# 4. Default web page preview			(Optional)	[Bool]
		'disable_web_page_preview'	=> 1,
		# 5. Send without reply					(Optional)	[Bool]
		'allow_sending_w/out_reply'	=> 1,
		# 6. Send without notification sound	(Optional)	[Bool]
		'disable_notification'		=> 0,
		# 7. Protect contents					(Optional)	[Bool]
		'protect_content'			=> 0,
		# 8. Commands alias						(Optional)	[Array of Text(Only 1 character)]
		'commands_alias'			=> ['/', '!'],
	
	# 2. Logging configs
		# 1. Set the chat ID for Bot logs		(Optional)	[Number]
		'log_chat'					=> 244432022,
		# 2. Choose the errors to log			(Optional)	[Array of Number => bool]
		'log_types'					=> [
			# https://www.php.net/manual/en/errorfunc.constants.php
			E_ERROR						=> 1,
			E_PARSE						=> 1,
			E_CORE_ERROR				=> 1,
			E_COMPILE_ERROR				=> 1,
			E_USER_WARNING				=> 0,
			E_USER_NOTICE				=> 0,
			E_RECOVERABLE_ERROR			=> 0
		],
	
	# 3. Databases
		# 1. Redis								(Optional)
		'redis'						=> [
			# 1. Turn on/off Redis					(Optional)	[Bool]
			'status'					=> 1,
			# 2. Select the Redis database			(Optional)	[Number]
			'database'					=> 1,
			# 3. Select Redis Server host			(Required)	[Redis Server IP]
			'host'						=> 'localhost',
			# 4. Select Redis Server port			(Optional)	[Number]
			'port'						=> 6379,
			# 5. Password for your Redis Server		(Optional)	[Text/0]
			'password'					=> 0,
			# 6. Anti Flood System					(Optional)
			'antiflood'					=> [
				# 1. Turn on/off the AntiFlood			(Optional)	[Bool]
				'status'					=> 1,
				# 2. Set the messages to be sent		(Required)	[Number]
				'messages'					=> 5,
				# 3. Set the seconds to be sent			(Required)	[Number]
				'seconds'					=> 2,
				# 4. Ban time							(Optional)	[Number]
				'ban_time'					=> 60,
				# 5. Message displayed when ban 		(Optional)	[Text]
				'notice'					=> '‼️ You have been banned due to message flood for 1 minute!'
			]
		],
		# 2. MySQL/SQLite/PostgreSQL			(Optional)
		'database' 					=> [
			# 1. Turn on/off the database 			(Optional)	[Bool]
			'status'					=> 1,
			# 2. Choose your database				(Required)	[Text: sqlite, mysql, pgsql]
			'type'						=> '',
			# 3. Database name (file for SQLite)	(Required)	[Text]
			'database_name'				=> '',
			# 4. MySQL/PostgreSQL user				(Required for MySQL/PostgreSQL)	[Text]
			'user'						=> '',
			# 5. MySQL/PostgreSQL password			(Required for MySQL/PostgreSQL)	[Text]
			'password'					=> '',
			# 6. Select Database host				(Required for MySQL/PostgreSQL)	[Database Server IP]
			'host'						=> 'localhost'
		],
	
	# 4. NeleBotX configs
		# 1. Bot Administrators					(Optional)	[Array of Telegram user ID]
		'admins'					=> [
			244432022 // Your ID
		],
		# 2. Plugins list						(Optional)	[Array of file name(text) => status(bool)]
		'plugins'					=> [
			'management.php'			=> 1,
			'commands.php'				=> 1
		],
		# 2. Default requests response			(Optional)	[Bool]
		'response'					=> 0,
		# 3. Default requests method			(Optional)	[Bool: 1(POST)/0(GET)]
		'post'						=> 1,
		# 4. Default requests timeout seconds	(Optional)	[Number]
		'timeout'					=> 2,
		# 5. NeleBotX Framework version
		'version'					=> '1.0.0'
];

?>
