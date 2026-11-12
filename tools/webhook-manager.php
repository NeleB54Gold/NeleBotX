<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../WebhookKey.php';

final class NeleBotXWebhookManager
{
    private string $configPath;
    private array $config;

    public function __construct(string $configPath)
    {
        $this->configPath = $configPath;
        $this->config = $this->loadConfig();
    }

    public function run(): void
    {
        while (true) {
            echo "\nNeleBot X — Webhook Manager\n";
            echo "1) Installa / configura webhook\n";
            echo "2) Rigenera chiavi di sicurezza\n";
            echo "3) Aggiorna URL webhook\n";
            echo "4) Rimuovi webhook\n";
            echo "5) Stato webhook\n";
            echo "0) Esci\n";
            $choice = $this->ask('Seleziona un\'operazione: ');

            try {
                match ($choice) {
                    '1' => $this->install(),
                    '2' => $this->rotateKeys(),
                    '3' => $this->updateUrl(),
                    '4' => $this->remove(),
                    '5' => $this->status(),
                    '0' => exit(0),
                    default => print "Scelta non valida.\n",
                };
            } catch (Throwable $e) {
                fwrite(STDERR, "\nERRORE: {$e->getMessage()}\n");
            }
        }
    }

    private function install(): void
    {
        $token = $this->askSecret('Token Telegram del bot: ');
        $this->validateToken($token);
        $this->api($token, 'getMe');

        $url = $this->ask('URL HTTPS del webhook (senza parametri key): ');
        $this->validateWebhookUrl($url);

        $password = bin2hex(random_bytes(32));
        $secret = bin2hex(random_bytes(32));
        $encrypted = NeleBotXWebhookKey::encrypt($token, $password);
        $webhookUrl = $this->buildWebhookUrl($url, $encrypted);

        $oldConfig = $this->config;
        $this->config['webhook_password'] = $password;
        $this->config['webhook_secret_token'] = $secret;
        $this->saveConfig();

        try {
            $result = $this->api($token, 'setWebhook', [
                'url' => $webhookUrl,
                'secret_token' => $secret,
            ]);
            echo "\nWebhook configurato correttamente.\n";
            echo 'Bot: ' . ($result['result']['url'] ?? $webhookUrl) . "\n";
            echo "Chiavi generate e salvate in configs.php. Conserva una copia sicura del file.\n";
        } catch (Throwable $e) {
            $this->config = $oldConfig;
            $this->saveConfig();
            throw new RuntimeException('Telegram ha rifiutato setWebhook; configurazione precedente ripristinata. ' . $e->getMessage(), 0, $e);
        }
    }

    private function rotateKeys(): void
    {
        $token = $this->askSecret('Token Telegram del bot: ');
        $this->validateToken($token);
        $this->api($token, 'getMe');
        $currentUrl = $this->ask('URL HTTPS attuale del webhook (senza parametro key): ');
        $this->validateWebhookUrl($currentUrl);
        if (!$this->confirm('Rigenerare entrambe le chiavi e aggiornare subito il webhook?')) {
            return;
        }

        $oldConfig = $this->config;
        $password = bin2hex(random_bytes(32));
        $secret = bin2hex(random_bytes(32));
        $encrypted = NeleBotXWebhookKey::encrypt($token, $password);
        $this->config['webhook_password'] = $password;
        $this->config['webhook_secret_token'] = $secret;
        $this->saveConfig();

        try {
            $this->api($token, 'setWebhook', [
                'url' => $this->buildWebhookUrl($currentUrl, $encrypted),
                'secret_token' => $secret,
            ]);
            echo "Chiavi rigenerate e webhook aggiornato.\n";
        } catch (Throwable $e) {
            $this->config = $oldConfig;
            $this->saveConfig();
            throw new RuntimeException('Rotazione non completata; configurazione precedente ripristinata. ' . $e->getMessage(), 0, $e);
        }
    }

    private function updateUrl(): void
    {
        $token = $this->askSecret('Token Telegram del bot: ');
        $this->validateToken($token);
        $this->api($token, 'getMe');
        $password = (string) ($this->config['webhook_password'] ?? '');
        $secret = (string) ($this->config['webhook_secret_token'] ?? '');
        if ($password === '' || $secret === '') {
            throw new RuntimeException('Chiavi mancanti in configs.php. Usa prima Installa / configura webhook.');
        }
        $url = $this->ask('Nuovo URL HTTPS del webhook (senza parametro key): ');
        $this->validateWebhookUrl($url);
        $encrypted = NeleBotXWebhookKey::encrypt($token, $password);
        $this->api($token, 'setWebhook', [
            'url' => $this->buildWebhookUrl($url, $encrypted),
            'secret_token' => $secret,
        ]);
        echo "URL webhook aggiornato.\n";
    }

    private function remove(): void
    {
        $token = $this->askSecret('Token Telegram del bot: ');
        $this->validateToken($token);
        $drop = $this->confirm('Eliminare anche gli aggiornamenti in coda?', false);
        $this->api($token, 'deleteWebhook', ['drop_pending_updates' => $drop ? 'true' : 'false']);
        echo "Webhook rimosso.\n";
    }

    private function status(): void
    {
        $token = $this->askSecret('Token Telegram del bot: ');
        $this->validateToken($token);
        $result = $this->api($token, 'getWebhookInfo')['result'] ?? [];
        echo "\nURL: " . (($result['url'] ?? '') ?: '(non configurato)') . "\n";
        echo 'Aggiornamenti in coda: ' . ($result['pending_update_count'] ?? 'n/d') . "\n";
        echo 'Ultimo errore: ' . ($result['last_error_message'] ?? 'nessuno') . "\n";
        if (!empty($result['last_error_date'])) {
            echo 'Data ultimo errore: ' . date(DATE_ATOM, (int) $result['last_error_date']) . "\n";
        }
        if (!empty($result['has_custom_certificate'])) {
            echo "Certificato personalizzato: sì\n";
        }
    }

    private function api(string $token, string $method, array $parameters = []): array
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('Estensione PHP cURL non disponibile.');
        }
        $handle = curl_init('https://api.telegram.org/bot' . $token . '/' . $method);
        if ($handle === false) {
            throw new RuntimeException('Impossibile inizializzare cURL.');
        }
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => ['Expect:'],
        ]);
        $body = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);
        if (!is_string($body)) {
            throw new RuntimeException('Errore di rete durante la chiamata Telegram: ' . ($error ?: 'risposta vuota'));
        }
        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Telegram ha restituito una risposta JSON non valida.');
        }
        if ($status < 200 || $status >= 300 || !is_array($decoded) || !($decoded['ok'] ?? false)) {
            $description = is_array($decoded) ? (string) ($decoded['description'] ?? 'errore API sconosciuto') : 'risposta API non valida';
            throw new RuntimeException("Telegram API {$method} (HTTP {$status}): {$description}");
        }
        return $decoded;
    }

    private function loadConfig(): array
    {
        if (!is_file($this->configPath) || !is_readable($this->configPath)) {
            throw new RuntimeException('configs.php non trovato o non leggibile.');
        }
        $configs = null;
        (static function (string $path) use (&$configs): void {
            require $path;
        })($this->configPath);
        if (!is_array($configs)) {
            throw new RuntimeException('configs.php non contiene un array $configs valido.');
        }
        return $configs;
    }

    private function saveConfig(): void
    {
        $source = file_get_contents($this->configPath);
        if (!is_string($source)) {
            throw new RuntimeException('Impossibile leggere configs.php.');
        }
        foreach (['token', 'webhook_password', 'webhook_secret_token'] as $key) {
            $pattern = "~(['\"]" . preg_quote($key, '~') . "['\"]\\s*=>\\s*)('[^']*'|\"[^\"]*\")~";
            $value = var_export((string) ($this->config[$key] ?? ''), true);
            $updated = preg_replace_callback($pattern, static fn(array $m): string => $m[1] . $value, $source, 1, $count);
            if (!is_string($updated) || $count !== 1) {
                throw new RuntimeException("Impossibile aggiornare la voce {$key} in configs.php.");
            }
            $source = $updated;
        }
        $backup = $this->configPath . '.bak.' . date('YmdHis') . '.' . bin2hex(random_bytes(3));
        if (!copy($this->configPath, $backup)) {
            throw new RuntimeException('Impossibile creare il backup di configs.php.');
        }
        @chmod($backup, 0600);
        $temporary = tempnam(dirname($this->configPath), '.configs-');
        if ($temporary === false) {
            throw new RuntimeException('Impossibile creare il file temporaneo di configurazione.');
        }
        try {
            if (file_put_contents($temporary, $source, LOCK_EX) === false) {
                throw new RuntimeException('Scrittura della configurazione temporanea non riuscita.');
            }
            @chmod($temporary, fileperms($this->configPath) & 0777);
            $check = null;
            (static function (string $path) use (&$check): void {
                require $path;
                $check = $configs ?? null;
            })($temporary);
            if (!is_array($check)) {
                throw new RuntimeException('La configurazione aggiornata non supera il controllo PHP.');
            }
            if (!rename($temporary, $this->configPath)) {
                throw new RuntimeException('Sostituzione atomica di configs.php non riuscita.');
            }
            $this->config = $check;
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
    }

    private function ask(string $prompt): string
    {
        fwrite(STDOUT, $prompt);
        $value = fgets(STDIN);
        return trim(is_string($value) ? $value : '');
    }

    private function askSecret(string $prompt): string
    {
        fwrite(STDOUT, $prompt);
        $tty = function_exists('stream_isatty') && stream_isatty(STDIN);
        $windows = DIRECTORY_SEPARATOR === '\\';
        if ($tty && !$windows) {
            shell_exec('stty -echo');
        } elseif ($tty && $windows) {
            fwrite(STDERR, "Nota: questa console Windows potrebbe mostrare il token durante l'inserimento.\n");
        }
        try {
            $value = fgets(STDIN);
        } finally {
            if ($tty && !$windows) {
                shell_exec('stty echo');
                fwrite(STDOUT, PHP_EOL);
            }
        }
        return trim(is_string($value) ? $value : '');
    }

    private function confirm(string $prompt, bool $default = true): bool
    {
        $answer = strtolower($this->ask($prompt . ($default ? ' [S/n]: ' : ' [s/N]: ')));
        return $answer === '' ? $default : in_array($answer, ['s', 'si', 'sì', 'y', 'yes'], true);
    }

    private function validateToken(string $token): void
    {
        if (!preg_match('/^\d{5,15}:[A-Za-z0-9_-]{20,}$/', $token)) {
            throw new InvalidArgumentException('Formato del token Telegram non valido.');
        }
        $configuredToken = (string) ($this->config['token'] ?? '');
        if ($configuredToken !== '' && !hash_equals($configuredToken, $token)) {
            throw new InvalidArgumentException('Il token inserito non corrisponde al token configurato in configs.php.');
        }
    }

    private function validateWebhookUrl(string $url): void
    {
        $parts = parse_url($url);
        if (!is_array($parts) || strtolower((string) ($parts['scheme'] ?? '')) !== 'https' || empty($parts['host']) || isset($parts['fragment'])) {
            throw new InvalidArgumentException('Inserisci un URL HTTPS valido, senza frammenti.');
        }
        parse_str((string) ($parts['query'] ?? ''), $query);
        if (array_key_exists('key', $query) || array_key_exists('token', $query)) {
            throw new InvalidArgumentException('Non inserire key o token nell’URL: verranno gestiti dal manager.');
        }
    }

    private function buildWebhookUrl(string $url, string $key): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . http_build_query(['key' => $key], '', '&', PHP_QUERY_RFC3986);
    }

}

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath((string) $_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    try {
        (new NeleBotXWebhookManager(__DIR__ . '/../configs.php'))->run();
    } catch (Throwable $e) {
        fwrite(STDERR, $e->getMessage() . PHP_EOL);
        exit(1);
    }
}
