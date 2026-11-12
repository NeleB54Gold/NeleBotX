<?php

declare(strict_types=1);

require_once __DIR__ . '/../tools/webhook-manager.php';

function managerAssert(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$source = file_get_contents(__DIR__ . '/../configs.php');
if (!is_string($source)) {
    throw new RuntimeException('Unable to read fixture config.');
}
$directory = sys_get_temp_dir() . '/nelebotx-manager-' . bin2hex(random_bytes(5));
if (!mkdir($directory, 0700)) {
    throw new RuntimeException('Unable to create test directory.');
}
$configPath = $directory . '/configs.php';
file_put_contents($configPath, $source);

try {
    $manager = new NeleBotXWebhookManager($configPath);
    $reflection = new ReflectionClass($manager);
    $configProperty = $reflection->getProperty('config');
    $configProperty->setAccessible(true);
    $config = $configProperty->getValue($manager);
    $config['webhook_password'] = bin2hex(random_bytes(32));
    $config['webhook_secret_token'] = bin2hex(random_bytes(32));
    $configProperty->setValue($manager, $config);

    $save = $reflection->getMethod('saveConfig');
    $save->setAccessible(true);
    $save->invoke($manager);

    $loaded = null;
    (static function (string $path) use (&$loaded): void {
        require $path;
        $loaded = $configs ?? null;
    })($configPath);
    managerAssert(is_array($loaded), 'Updated config must remain valid PHP.');
    managerAssert($loaded['webhook_password'] === $config['webhook_password'], 'Webhook password must be written.');
    managerAssert($loaded['webhook_secret_token'] === $config['webhook_secret_token'], 'Webhook secret token must be written.');
    managerAssert(($loaded['token'] ?? '') === '', 'Telegram token must not be stored in configs.php.');
    managerAssert(count(glob($configPath . '.bak.*') ?: []) === 1, 'A timestamped config backup must be created.');

    $buildUrl = $reflection->getMethod('buildWebhookUrl');
    $buildUrl->setAccessible(true);
    $url = $buildUrl->invoke($manager, 'https://example.test/hook?source=cli', 'v1.a+b/c=');
    managerAssert($url === 'https://example.test/hook?source=cli&key=v1.a%2Bb%2Fc%3D', 'Encrypted key must be RFC 3986 URL-encoded.');

    $validate = $reflection->getMethod('validateWebhookUrl');
    $validate->setAccessible(true);
    $validate->invoke($manager, 'https://example.test/hook');
    $rejected = false;
    try {
        $validate->invoke($manager, 'http://example.test/hook');
    } catch (ReflectionException|InvalidArgumentException|Throwable $e) {
        $rejected = true;
    }
    managerAssert($rejected, 'Non-HTTPS webhook URLs must be rejected.');

    echo "All webhook manager tests passed.\n";
} finally {
    foreach (glob($directory . '/*') ?: [] as $file) {
        unlink($file);
    }
    rmdir($directory);
}
