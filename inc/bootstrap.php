<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
//
// Shared bootstrap: paths, settings, error logging and small helpers.
// Every page includes this file first, before any output.

define('APP_ROOT', realpath(__DIR__ . '/..'));

$gtcConfig = dirname(APP_ROOT) . '/config.php';
if (getenv('DB_SERVER') === false && is_file($gtcConfig)) {
    require_once $gtcConfig;
}
$gtcDefaults = [
    'DB_SERVER' => '127.0.0.1', 'DB_USERNAME' => '', 'DB_PASSWORD' => '', 'DB_DATABASE' => '', 'NEWSAPI_KEY' => '',
    'GTC_MAX_DAY' => 28, 'GTC_START_DATE' => '2026-09-15', 'GTC_TIMEZONE' => 'Europe/Istanbul',
];
foreach ($gtcDefaults as $name => $default) {
    if (!defined($name)) {
        $value = getenv($name);
        $value = ($value === false || $value === '') ? $default : $value;
        define($name, is_int($default) ? (int) $value : $value);
    }
}
unset($gtcConfig, $gtcDefaults, $name, $default, $value);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
// Optional: create a "logs" folder next to config.php to collect PHP errors outside the web root.
if (is_dir(dirname(APP_ROOT) . '/logs')) {
    ini_set('error_log', dirname(APP_ROOT) . '/logs/php-error.log');
}
if (DB_USERNAME === '') {
    error_log('Guess the Car: no database settings. Put config.php next to public_html or set DB_* environment variables.');
}

// URL prefix of the site (empty when installed at the web root, "/sub" when in a sub folder).
function base_url()
{
    static $base = null;
    if ($base === null) {
        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
        $base = '';
        if ($docRoot !== '' && strpos(APP_ROOT, $docRoot) === 0) {
            $base = str_replace('\\', '/', substr(APP_ROOT, strlen($docRoot)));
        }
        $base = rtrim($base, '/');
    }
    return $base;
}

function url($path = '')
{
    return base_url() . '/' . ltrim($path, '/');
}

// Asset URL with a cache-busting version based on the file's modification time.
function asset($path)
{
    $file = APP_ROOT . '/' . ltrim($path, '/');
    $version = is_file($file) ? filemtime($file) : 0;
    return url($path) . '?v=' . $version;
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Only allow http(s) links coming from external data (news feed etc.).
function safe_url($value)
{
    $value = trim((string) $value);
    return preg_match('#^https?://#i', $value) ? $value : '#';
}

function load_json($relativePath)
{
    $file = APP_ROOT . '/' . ltrim($relativePath, '/');
    if (!is_file($file)) {
        return null;
    }
    return json_decode(file_get_contents($file), true);
}

function time_ago($timestamp)
{
    $diff = time() - (int) $timestamp;
    if ($diff < 60) {
        return 'just now';
    }
    $units = [86400 => 'day', 3600 => 'hour', 60 => 'minute'];
    foreach ($units as $seconds => $name) {
        if ($diff >= $seconds) {
            $n = (int) floor($diff / $seconds);
            return $n . ' ' . $name . ($n > 1 ? 's' : '') . ' ago';
        }
    }
    return 'just now';
}

// No PHP session: game progress is kept in the visitor's browser (localStorage), so the site sets no cookies.
