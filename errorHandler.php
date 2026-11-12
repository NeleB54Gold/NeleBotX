<?php

/**
 * Optional Telegram logging for PHP errors.
 *
 * The framework's regular PHP error handler receives the actual error details;
 * the shutdown handler is reserved for fatal errors to avoid duplicate logs.
 */

set_error_handler('errorHandler');
register_shutdown_function('shutdownHandler');

function errorHandler($error_type, $error, $error_file, $error_line) {
    global $bot;

    if (!(error_reporting() & $error_type)) {
        return false;
    }

    if (isset($bot) && is_object($bot) && !empty($bot->configs['log_chat'])) {
        $logTypes = $bot->configs['log_types'] ?? [];
        if (!empty($logTypes[$error_type])) {
            $message = (string) $error . PHP_EOL
                . 'File: ' . $bot->code((string) $error_file)
                . ' on line ' . $bot->code((string) $error_line);
            $bot->sendLog($bot->bold('[PHP Error]' . PHP_EOL) . $message);
        }
    }

    // Preserve PHP's normal handling, including display/logging configured by the host.
    return false;
}

function shutdownHandler() {
    global $bot;

    $error = error_get_last();
    if (!$error || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        return;
    }

    if (isset($bot) && is_object($bot) && !empty($bot->configs['log_chat'])) {
        $logTypes = $bot->configs['log_types'] ?? [];
        if (!empty($logTypes[$error['type']])) {
            $message = (string) $error['message'] . PHP_EOL
                . 'File: ' . $bot->code((string) $error['file'])
                . ' on line ' . $bot->code((string) $error['line']);
            $bot->sendLog($bot->bold('[PHP Fatal Error]' . PHP_EOL) . $message);
        }
    }
}

?>
