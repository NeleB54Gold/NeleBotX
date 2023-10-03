<?php

$start_time = microtime(true);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set('error_log', __DIR__ . '/logs.txt');
ini_set('ignore_repeated_errors', 1);

require_once('configs.php');
require_once('errorHandler.php');
require_once('TelegramBot.php');
require_once('Variables.php');
require_once('Database.php');
require_once('AntiFlood.php');
require_once('NeleBotX.php');

# Initialize framework
try {
	# Here you have to set the token or get in a more secure way.
	$configs['token'] = '';
	$NeleBotX = new NeleBotX($configs);
	$bot = $NeleBotX->api;
	$db = $NeleBotX->db;
	# Variables not available:
	$v = $NeleBotX->v;
	$user = $NeleBotX->user;
	$group = $NeleBotX->group;
	$channel = $NeleBotX->channel;
} catch (Exception $e) {
	# Bot Exceptions
	$NeleBotX->response = ['ok' => false, 'error_code' => 500, 'description' => 'Class Error: ' . $e->getMessage()];
}
if (!$NeleBotX->response['ok']) {
	if ($NeleBotX->response['error_code'] != 429) $bot->sendLog($bot->bold('The Bot crontab was stopped!') . PHP_EOL . $bot->code($NeleBotX->response['description'], 1));
	die($NeleBotX->response['error_code']);
} else {
	# Test
	$bot->sendMessage($v->configs['admins'][0], 'Testing my crontab!');
}

?>
