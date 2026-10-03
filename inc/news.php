<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
// Car industry news from NewsAPI.org, cached in news/news_data.json.

define('NEWS_CACHE', APP_ROOT . '/news/news_data.json');
define('NEWS_LOCK', APP_ROOT . '/news/.refresh.lock');
define('NEWS_TTL', 3 * 3600);       // refresh cached news every 3 hours
define('NEWS_RETRY_AFTER', 15 * 60); // after a failed refresh, wait before trying again
define('NEWS_DOMAINS', implode(',', [
    'motor1.com', 'carscoops.com', 'autoblog.com', 'caranddriver.com', 'motortrend.com',
    'thedrive.com', 'jalopnik.com', 'autocar.co.uk', 'topgear.com', 'carbuzz.com',
    'insideevs.com', 'roadandtrack.com', 'autoevolution.com', 'autoexpress.co.uk',
    'hagerty.com', 'autoweek.com', 'evo.co.uk', 'pistonheads.com', 'electrek.co',
    'speedhunters.com', 'drive.com.au', 'carexpert.com.au',
]));

function news_api_url()
{
    $params = [
        'domains' => NEWS_DOMAINS,
        'language' => 'en',
        'sortBy' => 'publishedAt',
        'pageSize' => 100,
        'from' => gmdate('Y-m-d', time() - 28 * 86400),
    ];
    return 'https://newsapi.org/v2/everything?' . http_build_query($params);
}

function news_http_get($url)
{
    $headers = ['X-Api-Key: ' . NEWSAPI_KEY, 'User-Agent: GuessTheCar/2.0 (+https://guessthecar.page)'];
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_TIMEOUT => 8,
        ]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        return [$status, $body === false ? null : $body, $error];
    }
    $context = stream_context_create(['http' => [
        'header' => implode("\r\n", $headers),
        'timeout' => 8,
        'ignore_errors' => true,
    ]]);
    $body = @file_get_contents($url, false, $context);
    $status = 0;
    if (isset($http_response_header[0]) && preg_match('#\s(\d{3})\s#', $http_response_header[0], $m)) {
        $status = (int) $m[1];
    }
    return [$status, $body === false ? null : $body, $body === false ? 'request failed' : ''];
}

// Fetch fresh news and write the cache. Returns true on success.
function news_refresh()
{
    if (NEWSAPI_KEY === '') {
        return false;
    }
    $lock = @fopen(NEWS_LOCK, 'c');
    if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
        return false; // another request is already refreshing
    }
    touch(NEWS_LOCK);

    list($status, $body, $error) = news_http_get(news_api_url());
    $data = $body ? json_decode($body, true) : null;
    $ok = $status === 200 && is_array($data) && ($data['status'] ?? '') === 'ok' && !empty($data['articles']);

    if ($ok) {
        $data['fetchedAt'] = time();
        $tmp = NEWS_CACHE . '.tmp';
        if (file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) !== false) {
            rename($tmp, NEWS_CACHE);
        }
    } else {
        $message = is_array($data) ? (($data['code'] ?? '') . ': ' . ($data['message'] ?? '')) : $error;
        error_log('NewsAPI refresh failed (HTTP ' . $status . ') ' . $message);
    }

    flock($lock, LOCK_UN);
    fclose($lock);
    return $ok;
}

function news_cache_age()
{
    return is_file(NEWS_CACHE) ? time() - filemtime(NEWS_CACHE) : PHP_INT_MAX;
}

// Returns ['articles' => [...], 'updated' => timestamp|null]
function news_get($limit = 0)
{
    $lastAttempt = is_file(NEWS_LOCK) ? time() - filemtime(NEWS_LOCK) : PHP_INT_MAX;
    if (news_cache_age() > NEWS_TTL && $lastAttempt > NEWS_RETRY_AFTER) {
        news_refresh();
    }

    $data = is_file(NEWS_CACHE) ? json_decode(file_get_contents(NEWS_CACHE), true) : null;
    $articles = [];
    $seen = [];
    foreach (($data['articles'] ?? []) as $a) {
        $title = trim((string) ($a['title'] ?? ''));
        $url = (string) ($a['url'] ?? '');
        if ($title === '' || $title === '[Removed]' || safe_url($url) === '#' || isset($seen[$title])) {
            continue;
        }
        $seen[$title] = true;
        $articles[] = [
            'title' => $title,
            'url' => $url,
            'image' => safe_url($a['urlToImage'] ?? '') === '#' ? '' : $a['urlToImage'],
            'description' => trim(strip_tags((string) ($a['description'] ?? ''))),
            'source' => (string) ($a['source']['name'] ?? 'Unknown'),
            'publishedAt' => strtotime((string) ($a['publishedAt'] ?? '')) ?: null,
        ];
        if ($limit > 0 && count($articles) >= $limit) {
            break;
        }
    }
    return [
        'articles' => $articles,
        'updated' => $data['fetchedAt'] ?? (is_file(NEWS_CACHE) ? filemtime(NEWS_CACHE) : null),
    ];
}
