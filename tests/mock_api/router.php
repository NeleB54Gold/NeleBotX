<?php
// Local-only fake Telegram Bot API. Never use this router on a public interface.
header('Content-Type: application/json; charset=utf-8');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (!preg_match('~^/bot[0-9]+:[A-Za-z0-9_-]+/([A-Za-z][A-Za-z0-9]*)$~', $path, $m)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error_code' => 404, 'description' => 'Fake API: unknown route']);
    return;
}
$method = $m[1];
$raw = file_get_contents('php://input');
$params = [];
parse_str($raw ?: '', $params);
if ($_SERVER['REQUEST_METHOD'] === 'GET') $params = array_merge($params, $_GET);
$log = ['method' => $method, 'http_method' => $_SERVER['REQUEST_METHOD'], 'params' => $params];
file_put_contents(__DIR__ . '/requests.jsonl', json_encode($log, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
if ($method === 'simulateHttpError') {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error_code' => 429, 'description' => 'Too Many Requests: retry after 1', 'parameters' => ['retry_after' => 1]]);
    return;
}
if ($method === 'simulateInvalidJson') {
    http_response_code(200);
    echo '{this is not valid JSON';
    return;
}
if ($method === 'simulateServerError') {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error_code' => 503, 'description' => 'Fake service unavailable']);
    return;
}
if ($method === 'simulateTimeout') { sleep(3); }
$result = match ($method) {
    'getMe' => ['id' => 123456, 'is_bot' => true, 'first_name' => 'NeleBotX Fake', 'username' => 'nelebotx_test_bot'],
    'getWebhookInfo' => ['url' => '', 'has_custom_certificate' => false, 'pending_update_count' => 0],
    'sendMessage' => ['message_id' => 9001, 'date' => time(), 'chat' => ['id' => (int)($params['chat_id'] ?? 0), 'type' => 'private'], 'text' => (string)($params['text'] ?? '')],
    'getChatMemberCount' => 3,
    'getChat' => ['id' => (int)($params['chat_id'] ?? 0), 'type' => 'supergroup', 'title' => 'Fake test group'],
    'setWebhook', 'deleteWebhook', 'banChatMember', 'unbanChatMember', 'answerCallbackQuery', 'setMyCommands', 'deleteMyCommands' => true,
    default => ['method' => $method, 'received_parameters' => $params],
};
echo json_encode(['ok' => true, 'result' => $result], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
