<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
// Guess the Car: one new car per day, and the server-side answer check.
//
// Player progress is NOT kept on the server. Like One Shot Prototype it lives in the visitor's
// browser (localStorage "gtc:v1", see GTC.progress in assets/js/app.js); the server only checks
// guesses (serverThings/guess.php), so the answer stays secret until the game is over.

define('GTC_MAX_GUESSES', 5);

// Day number of today's car: GTC_START_DATE is Day 1, a new day starts at midnight in GTC_TIMEZONE.
function gtc_today()
{
    static $today = null;
    if ($today === null) {
        $tz = new DateTimeZone(GTC_TIMEZONE);
        $start = new DateTimeImmutable(GTC_START_DATE, $tz);
        $today = (int) $start->diff(new DateTimeImmutable('today', $tz))->format('%r%a') + 1;
    }
    return $today;
}

// Seconds until the next day starts (midnight in GTC_TIMEZONE).
function gtc_seconds_to_next_day()
{
    return (new DateTimeImmutable('tomorrow', new DateTimeZone(GTC_TIMEZONE)))->getTimestamp() - time();
}

// Days that are out: 1 up to today (never past GTC_MAX_DAY), and only when the hint images exist.
function gtc_days()
{
    static $days = null;
    if ($days === null) {
        $days = [];
        $last = min(gtc_today(), GTC_MAX_DAY);
        for ($day = 1; $day <= $last; $day++) {
            if (is_file(APP_ROOT . '/Res/games/' . $day . '/1.webp')) {
                $days[] = $day;
            }
        }
    }
    return $days;
}

function gtc_normalize($text)
{
    $text = trim((string) $text);
    $text = function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    return preg_replace('/\s+/u', ' ', $text);
}

// Error message for a guess that can't be checked, or null when it's fine.
function gtc_guess_error($guess)
{
    if ($guess === '') {
        return 'Type a car name first.';
    }
    if ((function_exists('mb_strlen') ? mb_strlen($guess, 'UTF-8') : strlen($guess)) > 60) {
        return 'Answer is too long. Maximum length is 60 characters.';
    }
    if (!preg_match('/^[\p{L}\p{N}()\/#&.\'\- ]*$/u', $guess)) {
        return 'Answers can only contain letters, numbers, spaces and ( ) / # & . \' -';
    }
    return null;
}

// Adds one to games.enters (a player started this day) or games.corrects (a player solved it).
function gtc_bump_counter($column, $day)
{
    // $column goes into the SQL text, so only these two fixed names are allowed.
    if (!in_array($column, ['enters', 'corrects'], true)) {
        return;
    }
    try {
        $stmt = db()->prepare("UPDATE games SET $column = $column + 1 WHERE id = ?");
        $stmt->bind_param('i', $day);
        $stmt->execute();
    } catch (Throwable $e) {
        error_log('guess counter: ' . $e->getMessage());
    }
}
