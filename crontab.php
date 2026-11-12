<?php

$start_time = microtime(true);
$NeleBotX = null;
$bot = null;
ini_set('error_reporting', E_ALL);
ini_set('ignore_repeated_errors', 1);
require_once __DIR__ . '/Log.php';
require_once __DIR__ . '/configs.php';
if (!NeleBotXLog::configure($configs)) {
	error_log('NeleBot X could not initialize the configured log file.');
	http_response_code(500);
	die('Logging initialization failed');
}

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
} catch (Throwable $e) {
	if ($NeleBotX instanceof NeleBotX) {
		$NeleBotX->response = ['ok' => false, 'error_code' => 500, 'description' => 'Class Error: ' . $e->getMessage()];
	} else {
		error_log('NeleBot X crontab initialization failed: ' . $e->getMessage());
		die('500');
	}
}
if (!$NeleBotX->response['ok']) {
	if (($NeleBotX->response['error_code'] ?? 500) != 429 && is_object($bot)) $bot->sendLog($bot->bold('The Bot crontab was stopped!') . PHP_EOL . $bot->code($NeleBotX->response['description'] ?? 'Unknown error', 1));
	die((string) ($NeleBotX->response['error_code'] ?? 500));
} else {
	# Test
	$bot->sendMessage($v->configs['admins'][0], 'Testing my crontab!');
}

?>
