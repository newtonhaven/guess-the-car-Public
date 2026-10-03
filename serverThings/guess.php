<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
// Answer check for Guess the Car: POST day, guess, attempt (1-5) -> {"correct": bool, "answer": string|null}
// The answer only leaves the server when the guess is right or it was the last attempt.
require __DIR__ . '/../inc/bootstrap.php';
require APP_ROOT . '/inc/guess.php';
require APP_ROOT . '/inc/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function guess_reply($status, array $data)
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    guess_reply(405, ['error' => 'Use POST.']);
}

$day = filter_var($_POST['day'] ?? null, FILTER_VALIDATE_INT);
$attempt = filter_var($_POST['attempt'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => GTC_MAX_GUESSES]]);
$guess = trim((string) ($_POST['guess'] ?? ''));

if ($day === false || $attempt === false || !in_array($day, gtc_days(), true)) {
    guess_reply(400, ['error' => 'That game is not available.']);
}
$error = gtc_guess_error($guess);
if ($error !== null) {
    guess_reply(422, ['error' => $error]);
}

try {
    $stmt = db()->prepare('SELECT answer FROM games WHERE id = ?');
    $stmt->bind_param('i', $day);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
} catch (Throwable $e) {
    error_log('guess: ' . $e->getMessage());
    guess_reply(503, ['error' => 'The game server is taking a pit stop. Please try again in a minute.']);
}
if (!$row) {
    guess_reply(404, ['error' => 'No data found for this day.']);
}

$correct = gtc_normalize($guess) === gtc_normalize($row['answer']);
if ($attempt === 1) {
    gtc_bump_counter('enters', $day); // counts each player once per game
}
if ($correct) {
    gtc_bump_counter('corrects', $day);
}
guess_reply(200, [
    'correct' => $correct,
    'answer' => ($correct || $attempt === GTC_MAX_GUESSES) ? $row['answer'] : null,
]);
