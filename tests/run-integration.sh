#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
HOST=127.0.0.1
PORT="${NELEBOTX_FAKE_API_PORT:-18765}"
LOG="$(mktemp)"
php -S "${HOST}:${PORT}" tests/mock_api/router.php >"$LOG" 2>&1 &
SERVER_PID=$!
sleep .2
if grep -qi "failed to listen" "$LOG"; then cat "$LOG" >&2; kill "$SERVER_PID" 2>/dev/null || true; exit 1; fi
cleanup() { kill "$SERVER_PID" 2>/dev/null || true; rm -f "$LOG" tests/mock_api/requests.jsonl; }
trap cleanup EXIT
for _ in $(seq 1 40); do
  if php -r '$s=@fsockopen("127.0.0.1", (int)$argv[1], $e, $m, .1); if ($s) { fclose($s); exit(0); } exit(1);' "$PORT"; then break; fi
  sleep .1
done
NELEBOTX_FAKE_API="http://${HOST}:${PORT}" php tests/integration.php
php -d error_reporting=E_ALL tests/smoke.php
php -d error_reporting=E_ALL tests/webhook-manager.php
while IFS= read -r -d '' file; do php -l "$file" >/dev/null; done < <(find . -type f -name '*.php' -print0)
echo "All integration, smoke, and syntax checks passed."
