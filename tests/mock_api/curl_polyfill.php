<?php
// Test-only minimal cURL-compatible shim for environments lacking ext-curl.
// It uses PHP streams to make real HTTP requests to the local fake API server.
if (extension_loaded('curl')) return;
foreach ([
 'CURLOPT_URL'=>10002, 'CURLOPT_POST'=>47, 'CURLOPT_TIMEOUT'=>13,
 'CURLOPT_CONNECTTIMEOUT'=>78, 'CURLOPT_RETURNTRANSFER'=>19913,
 'CURLOPT_FOLLOWLOCATION'=>52, 'CURLOPT_HTTPHEADER'=>10023,
 'CURLOPT_POSTFIELDS'=>10015, 'CURLINFO_HTTP_CODE'=>2097154
] as $name => $value) if (!defined($name)) define($name, $value);
class NeleBotXFakeCurlHandle { public array $options = []; public int $httpCode = 0; public string $error = ''; }
function curl_init($url = null) { $h = new NeleBotXFakeCurlHandle(); if ($url) $h->options[CURLOPT_URL] = $url; return $h; }
function curl_reset($h) { $h->options = []; $h->httpCode = 0; $h->error = ''; return true; }
function curl_setopt_array($h, array $options) { $h->options = array_replace($h->options, $options); return true; }
function curl_exec($h) {
    $url = $h->options[CURLOPT_URL] ?? '';
    $post = !empty($h->options[CURLOPT_POST]);
    $headers = $h->options[CURLOPT_HTTPHEADER] ?? [];
    $body = $h->options[CURLOPT_POSTFIELDS] ?? [];
    if (is_array($body)) $body = http_build_query($body);
    $timeout = max(1, (int)($h->options[CURLOPT_TIMEOUT] ?? 10));
    $ctx = stream_context_create(['http'=>['method'=>$post?'POST':'GET','header'=>implode("\r\n", $headers).($post?"\r\nContent-Type: application/x-www-form-urlencoded":""),'content'=>$post?(string)$body:'','timeout'=>$timeout,'ignore_errors'=>true,'follow_location'=>0]]);
    $out = @file_get_contents($url, false, $ctx);
    $responseHeaders = $http_response_header ?? [];
    foreach ($responseHeaders as $line) if (preg_match('~^HTTP/\S+\s+(\d{3})~', $line, $m)) $h->httpCode = (int)$m[1];
    if ($out === false) { $h->error = 'HTTP stream request failed or timed out'; return false; }
    return $out;
}
function curl_error($h) { return $h->error; }
function curl_getinfo($h, $option = null) { return $option === CURLINFO_HTTP_CODE ? $h->httpCode : ['http_code'=>$h->httpCode]; }
