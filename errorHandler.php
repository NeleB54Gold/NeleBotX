<?php

# Error Reporting via Telegram Bot
# Work only if you set the log_chat in configs!

set_error_handler("errorHandler");
register_shutdown_function("shutdownHandler");

function errorHandler($error_type, $error, $error_file, $error_line, $error_context = '') {
	global $bot;
	
	if (isset($bot) and $bot->configs['log_chat'] and $error = error_get_last()) {
		if ($bot->configs['log_types'][$error['type']]) {
			$error_message = $error['message'] . PHP_EOL . 'File: ' . $bot->code($error['file']) . ' on line ' . $bot->code($error['line']);
			$bot->sendLog($bot->bold('[Error]' . PHP_EOL) . $error_message);
		}
	}
}

function shutdownHandler() {
	global $bot;
	
	if (isset($bot) and $bot->configs['log_chat'] and $error = error_get_last()) {
		if ($bot->configs['log_types'][$error['type']]) {
			$error_message = $error['message'] . PHP_EOL . 'File: ' . $bot->code($error['file']) . ' on line ' . $bot->code($error['line']);
			$bot->sendLog($bot->bold('[Shutdown]' . PHP_EOL) . $error_message);
		}
	}
}

?>