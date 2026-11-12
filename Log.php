<?php

final class NeleBotXLog
{
    public static function configure(array $configs): bool
    {
        $path = (string) ($configs['log_file'] ?? '/var/log/bots/NeleBotX.log');
        if ($path === '' || $path[0] !== '/') {
            return false;
        }
        $directory = dirname($path);
        if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
            return false;
        }
        if (!is_writable($directory)) {
            return false;
        }
        if (!is_file($path) && @file_put_contents($path, '', LOCK_EX) === false) {
            return false;
        }
        @chmod($path, 0640);
        if (!is_writable($path)) {
            return false;
        }
        ini_set('log_errors', '1');
        ini_set('error_log', $path);
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        return true;
    }
}
