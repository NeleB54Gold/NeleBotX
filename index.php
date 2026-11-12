<?php

$start_time = microtime(true);
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
require_once __DIR__ . '/WebhookKey.php';

# Initialize framework
$NeleBotX = null;
$bot = null;
try {
	if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
		http_response_code(405);
		header('Allow: POST');
		die;
	}
	$expectedWebhookSecret = (string) ($configs['webhook_secret_token'] ?? '');
	$receivedWebhookSecret = (string) ($_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '');
	if ($expectedWebhookSecret === '' || $receivedWebhookSecret === '' || !hash_equals($expectedWebhookSecret, $receivedWebhookSecret)) {
		http_response_code(403);
		die;
	}
	if (!empty($_GET['key'])) {
		$webhookPassword = (string) ($configs['webhook_password'] ?? '');
		$decryptedToken = NeleBotXWebhookKey::decrypt((string) $_GET['key'], $webhookPassword);
		if ($decryptedToken === null || (!empty($configs['token']) && !hash_equals((string) $configs['token'], $decryptedToken))) {
			http_response_code(403);
			die;
		}
		$configs['token'] = $decryptedToken;
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
} catch (Throwable $e) {
	# Catch both Exceptions and PHP Errors on modern PHP versions.
	if ($NeleBotX instanceof NeleBotX) {
		$NeleBotX->response = ['ok' => false, 'error_code' => 500, 'description' => 'Class Error: ' . $e->getMessage()];
	} else {
		http_response_code(500);
		error_log('NeleBot X initialization failed: ' . $e->getMessage());
		die('500');
	}
}
if (!$NeleBotX->response['ok']) {
	if (($NeleBotX->response['error_code'] ?? 500) != 429 && is_object($bot)) $bot->sendLog($bot->bold('The Bot was stopped!') . PHP_EOL . $bot->code($NeleBotX->response['description'] ?? 'Unknown error', 1));
	die((string) ($NeleBotX->response['error_code'] ?? 500));
} else {
	http_response_code(200);
	//fastcgi_finish_request(); // fastcgi only
	
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
