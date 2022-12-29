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
	if (!$configs['token'] and $_GET['token']) { // Webhook multi-bot
		$configs['token'] = $_GET['token'];
	} elseif ($configs['token'] and $_GET['key']) { // Webhook per-bot
		if (crypt($_GET['key'], 'NeleBotX') != $configs['token']) {
			http_response_code(403);
			die;
		}
		$configs['token'] = $_GET['key'];
	} else {
		http_response_code(401);
		die;
	}
	$NeleBotX = new NeleBotX($configs);
	$bot = $NeleBotX->api;
	$v = $NeleBotX->v;
	$db = $NeleBotX->db;
	$user = $NeleBotX->user;
	$group = $NeleBotX->group;
	$channel = $NeleBotX->channel;
} catch (Exception $e) {
	# Bot Exceptions
	$NeleBotX->response = ['ok' => false, 'error_code' => 500, 'description' => 'Class Error: ' . $e->getMessage()];
}
if (!$NeleBotX->response['ok']) {
	if ($NeleBotX->response['error_code'] != 429) $bot->sendLog($bot->bold('The Bot was stopped!') . PHP_EOL . $bot->code($NeleBotX->response['description'], 1));
	die($NeleBotX->response['error_code']);
} else {
	http_response_code(200);
	# fastcgi_finish_request(); // fastcgi only
	
	# Load plugins
	if (is_array($configs['plugins']) and !empty($configs['plugins'])) {
		foreach ($configs['plugins'] as $plugin => $status) {
			if ($status and file_exists('plugins/' . $plugin)) {
				require('plugins/' . $plugin);
			}
		}
	}
}

?>
