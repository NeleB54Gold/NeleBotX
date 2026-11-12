<?php

require_once __DIR__ . '/../TelegramBot.php';
require_once __DIR__ . '/../Variables.php';
require_once __DIR__ . '/../WebhookKey.php';
require_once __DIR__ . '/../Log.php';

function expect(bool $condition, string $message): void {
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

class TestTelegramBot extends TelegramBot {
    public $lastRequest = null;

    public function request($url, $args = [], $post = 'def', $response = 'def', $timeout = 'def') {
        $this->lastRequest = compact('url', 'args', 'post', 'response', 'timeout');
        return ['ok' => true, 'result' => true];
    }
}

$testLog = sys_get_temp_dir() . '/nelebotx-test-' . bin2hex(random_bytes(5)) . '/NeleBotX.log';
expect(NeleBotXLog::configure(['log_file' => $testLog]), 'log setup should create a protected log file outside the project root');
expect(is_file($testLog) && ini_get('display_errors') === '0', 'log setup should create the file and disable PHP error display');
@unlink($testLog);
@rmdir(dirname($testLog));

$webhookPassword = bin2hex(random_bytes(32));
$webhookToken = '123456:abcdefghijklmnopqrstuvwxyz0123456789ABCDE';
$encryptedWebhookKey = NeleBotXWebhookKey::encrypt($webhookToken, $webhookPassword);
expect($encryptedWebhookKey !== $webhookToken, 'webhook key must not expose the Telegram token');
expect(NeleBotXWebhookKey::decrypt($encryptedWebhookKey, $webhookPassword) === $webhookToken, 'webhook key should decrypt with the configured password');
expect(NeleBotXWebhookKey::decrypt($encryptedWebhookKey, 'wrong-password') === null, 'webhook key must fail with the wrong password');
expect(NeleBotXWebhookKey::decrypt(substr($encryptedWebhookKey, 0, -1) . 'A', $webhookPassword) === null, 'tampered webhook key must fail authentication');
expect(NeleBotXWebhookKey::encrypt($webhookToken, $webhookPassword) !== $encryptedWebhookKey, 'webhook encryption should use fresh random values');

$configs = [
    'token' => '123456:TEST_TOKEN',
    'telegram_bot_api' => 'https://api.telegram.org',
    'post' => true,
    'response' => true,
    'timeout' => 5,
    'admins' => [],
];

$bot = new TestTelegramBot($configs);
expect($bot->response['ok'] === true, 'token format should initialize the API client');
expect($bot->getConfigs() === $configs, 'getConfigs should return the configuration injected into the instance');
$bot->editConfigs('test_runtime_key', 'runtime-value');
expect($bot->getConfigs()['test_runtime_key'] === 'runtime-value', 'getConfigs should expose runtime config edits without reloading configs.php');

$result = $bot->callApi('sendMessage', [
    'chat_id' => 123,
    'text' => 'hello',
    'reply_markup' => ['inline_keyboard' => [[['text' => 'Open', 'url' => 'https://example.com']]]],
]);
expect($result['ok'] === true, 'generic API calls should return the mocked response');
expect($bot->lastRequest['url'] === 'https://api.telegram.org/bot123456:TEST_TOKEN/sendMessage', 'generic API URL should be constructed correctly');
expect($bot->lastRequest['args']['reply_markup'] === json_encode(['inline_keyboard' => [[['text' => 'Open', 'url' => 'https://example.com']]]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'nested API parameters should be JSON encoded');

$bot->sendWidget(['chat_id' => 123, 'widget' => ['html' => '<p>Test</p>']]);
expect(str_ends_with($bot->lastRequest['url'], '/sendWidget'), 'Bot API 10.4 widget wrapper should target sendWidget');
$bot->getUniqueGift('test_gift');
expect(str_ends_with($bot->lastRequest['url'], '/getUniqueGift') && $bot->lastRequest['post'] === false, 'Bot API 10.4 unique gift lookup should use GET');
$bot->deleteBusinessAccountGift('business-connection', 'gift-id');
expect(str_ends_with($bot->lastRequest['url'], '/deleteBusinessAccountGift'), 'Bot API 10.4 business gift deletion wrapper should target the correct method');

$widgetUpdate = new Variables($configs, [
    'update_id' => 101,
    'widget_query' => ['id' => 'widget-query-id', 'from' => ['id' => 42]],
]);
expect($widgetUpdate->response['ok'] === 1 && $widgetUpdate->update_type === 'widget_query', 'Bot API 10.4 widget_query update should be exposed');
$sharedWidgetUpdate = new Variables($configs, [
    'update_id' => 102,
    'shared_widget_query' => ['id' => 'shared-widget-query-id'],
]);
expect($sharedWidgetUpdate->response['ok'] === 1 && $sharedWidgetUpdate->update_type === 'shared_widget_query', 'Bot API 10.4 shared_widget_query update should be exposed');

$invalid = $bot->callApi('../getMe', []);
expect($invalid['ok'] === false && $invalid['error_code'] === 400, 'invalid method names should be rejected');


$bot->banMember(-100123, 42);
expect(str_ends_with($bot->lastRequest['url'], '/banChatMember'), 'banMember should use the current banChatMember endpoint');
$bot->kickMember(-100123, 42);
expect(str_ends_with($bot->lastRequest['url'], '/banChatMember'), 'legacy kickMember should use the current endpoint');
$bot->getMembersCount(-100123);
expect(str_ends_with($bot->lastRequest['url'], '/getChatMemberCount'), 'legacy member count helper should use the current endpoint');

$variables = new Variables($configs);
expect($variables->response['ok'] === 1, 'Variables should support construction without an update');
expect($variables->isAdmin() === false, 'isAdmin should be safe when an update has no user');

$newApiUpdate = new Variables($configs, [
    'update_id' => 100,
    'business_message' => ['message_id' => 1, 'text' => 'forward-compatible update'],
]);
expect($newApiUpdate->response['ok'] === 1, 'unknown/new update types should be accepted for plugin handling');
expect($newApiUpdate->update_type === 'business_message', 'unknown update type should be exposed');
expect($newApiUpdate->update_data['text'] === 'forward-compatible update', 'raw update payload should be exposed');

echo "All smoke tests passed.\n";
