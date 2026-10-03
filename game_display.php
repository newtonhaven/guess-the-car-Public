<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
// One Guess the Car day. The page is the same for every visitor; assets/js/guess.js fills in the
// player's progress from localStorage and sends guesses to serverThings/guess.php.
require __DIR__ . '/inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';
require APP_ROOT . '/inc/guess.php';
require APP_ROOT . '/inc/db.php';

$days = gtc_days();
$today = gtc_today();
$day = filter_var($_GET['day'] ?? null, FILTER_VALIDATE_INT);
$validDay = $day !== false && in_array($day, $days, true);
$notOutYet = $day !== false && !$validDay && $day > $today && $day <= GTC_MAX_DAY;

$row = null;
$dbError = false;
if ($validDay) {
    try {
        $stmt = db()->prepare('SELECT corrects, enters FROM games WHERE id = ?');
        $stmt->bind_param('i', $day);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
    } catch (Throwable $e) {
        error_log('game_display: ' . $e->getMessage());
        $dbError = true;
    }
}

$index = $validDay ? array_search($day, $days, true) : false;
$prevDay = $index !== false && $index > 0 ? $days[$index - 1] : null;
$nextDay = $index !== false && $index < count($days) - 1 ? $days[$index + 1] : null;
$isToday = $validDay && $day === max($days);

$solveRate = null;
if ($row && (int) $row['enters'] > 0) {
    $solveRate = (int) round(100 * (int) $row['corrects'] / (int) $row['enters']);
}

if (!$validDay) {
    http_response_code(404);
}
page_start([
    'title' => $validDay ? 'Day ' . $day : ($notOutYet ? 'Not out yet' : 'Game not found'),
    'description' => 'Guess the Car, day ' . ($validDay ? $day : '') . ': name the car from five picture hints.',
    'active' => 'guess',
]);
?>
<div class="container-xl">
    <?php if (!$validDay): ?>
        <div class="page-head text-center">
            <?php if ($notOutYet): ?>
                <h1>Not out yet</h1>
                <p class="lead">Day <?= (int) $day ?> comes out in <?= $day - $today ?> day<?= $day - $today === 1 ? '' : 's' ?>. No peeking.</p>
            <?php else: ?>
                <h1>Game not found</h1>
                <p class="lead">That day doesn't exist (yet). Pick one from the list.</p>
            <?php endif; ?>
            <a class="btn btn-primary" href="<?= url('daily.php') ?>"><i class="bi bi-grid-3x3-gap me-1"></i>All days</a>
        </div>
    <?php else: ?>
        <header class="page-head d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <div>
                <a href="<?= url('daily.php') ?>" class="small text-body-secondary text-decoration-none"><i class="bi bi-arrow-left me-1"></i>All days</a>
                <h1 class="mb-0">Day <?= $day ?><?php if ($isToday): ?> <span class="badge text-bg-primary align-middle fs-6">Today</span><?php endif; ?></h1>
            </div>
            <nav class="btn-group" aria-label="Other days">
                <a class="btn btn-outline-secondary<?= $prevDay ? '' : ' disabled' ?>" href="<?= $prevDay ? url('game_display.php?day=' . $prevDay) : '#' ?>"><i class="bi bi-chevron-left"></i><?php if ($prevDay): ?><span class="d-none d-sm-inline ms-1">Day <?= $prevDay ?></span><?php endif; ?></a>
                <a class="btn btn-outline-secondary<?= $nextDay ? '' : ' disabled' ?>" href="<?= $nextDay ? url('game_display.php?day=' . $nextDay) : '#' ?>"><?php if ($nextDay): ?><span class="d-none d-sm-inline me-1">Day <?= $nextDay ?></span><?php endif; ?><i class="bi bi-chevron-right"></i></a>
            </nav>
        </header>

        <?php if ($dbError || !$row): ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= $dbError ? 'The game server is taking a pit stop. Please try again in a minute.' : 'No data found for this day.' ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="hint-stage">
                    <img id="hintImage" src="<?= url('Res/games/' . $day . '/1.webp') ?>" alt="Hint 1 for day <?= $day ?>">
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2 mt-3" role="group" aria-label="Hints">
                    <span class="small text-body-secondary me-1">Hints</span>
                    <?php for ($i = 1; $i <= GTC_MAX_GUESSES; $i++): ?>
                        <button type="button" class="btn btn-sm hint-btn <?= $i === 1 ? 'btn-primary' : 'btn-outline-secondary' ?>"
                                data-hint="<?= $i ?>" <?= $i > 1 ? 'disabled aria-label="Hint ' . $i . ' (locked)"' : 'aria-label="Show hint 1"' ?>>
                            <?= $i > 1 ? '<i class="bi bi-lock-fill"></i>' : 1 ?>
                        </button>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row row-cols-4 g-2" data-gtc-stats data-today="<?= max($days) ?>">
                            <div class="col stat-tile"><div class="value" data-gtc-stat="played">0</div><div class="label">Played</div></div>
                            <div class="col stat-tile"><div class="value" data-gtc-stat="win">0%</div><div class="label">Win rate</div></div>
                            <div class="col stat-tile"><div class="value" data-gtc-stat="streak">0</div><div class="label">Streak</div></div>
                            <div class="col stat-tile"><div class="value" data-gtc-stat="best">0</div><div class="label">Best</div></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small text-body-secondary">Guesses</span>
                            <div class="guess-dots" id="guessDots" aria-hidden="true">
                                <?php for ($i = 0; $i < GTC_MAX_GUESSES; $i++): ?><span></span><?php endfor; ?>
                            </div>
                        </div>

                        <div class="alert alert-warning py-2 small d-none" id="guessMsg" role="alert"></div>

                        <div class="result-banner border rounded-3 p-3 mb-3 fade-in d-none" id="result">
                            <div class="small text-body-secondary" id="resultHeadline"></div>
                            <div class="h3 mb-0" id="resultAnswer"></div>
                        </div>

                        <?php if ($row): ?>
                            <form id="guessForm" autocomplete="off">
                                <label for="guessInput" class="form-label">Which car is it?</label>
                                <div class="d-flex gap-2">
                                    <div class="ac-wrap">
                                        <input id="guessInput" name="answer" type="text" class="form-control form-control-lg"
                                               placeholder="Search cars / models" maxlength="60" spellcheck="false" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg" id="guessSubmit"><i class="bi bi-send"></i><span class="visually-hidden">Submit</span></button>
                                </div>
                                <div class="form-text" id="guessLeft"><?= GTC_MAX_GUESSES ?> guesses left.</div>
                            </form>
                            <noscript><p class="small text-body-secondary mt-2 mb-0">Guess the Car needs JavaScript.</p></noscript>
                        <?php endif; ?>

                        <ul class="list-unstyled wrong-list mt-3 mb-0" id="wrongList"></ul>

                        <div class="d-none mt-3" id="finishedActions">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-primary" id="shareBtn"><i class="bi bi-share me-1"></i>Share</button>
                                <?php if ($nextDay): ?>
                                    <a class="btn btn-primary" href="<?= url('game_display.php?day=' . $nextDay) ?>">Next day<i class="bi bi-arrow-right ms-1"></i></a>
                                <?php else: ?>
                                    <a class="btn btn-primary" href="<?= url('oneshot.php') ?>">Try One Shot<i class="bi bi-arrow-right ms-1"></i></a>
                                <?php endif; ?>
                            </div>
                            <?php if ($isToday && $today <= GTC_MAX_DAY): ?>
                                <p class="small text-body-secondary mt-3 mb-0"><i class="bi bi-clock me-1"></i>Next car in <span data-countdown="<?= gtc_seconds_to_next_day() ?>"></span></p>
                            <?php elseif ($isToday): ?>
                                <p class="small text-body-secondary mt-3 mb-0"><i class="bi bi-cone-striped me-1"></i>New cars are on the way. Check back soon.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($solveRate !== null): ?>
                        <div class="card-footer small text-body-secondary">
                            <i class="bi bi-people me-1"></i><?= $solveRate ?>% of <?= (int) $row['enters'] ?> players got this one.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <script type="application/json" id="gtcGame"><?= json_encode(['day' => $day, 'maxGuesses' => GTC_MAX_GUESSES]) ?></script>
    <?php endif; ?>
</div>
<?php
page_end($validDay ? ['confetti' => true, 'scripts' => ['assets/js/guess.js']] : []);
