<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
require __DIR__ . '/inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';
require APP_ROOT . '/inc/guess.php';

$days = gtc_days();
$latest = $days ? max($days) : 0;
$moreComing = gtc_today() <= GTC_MAX_DAY;

page_start([
    'title' => 'Daily game',
    'description' => 'Guess the Car: a new car every day. Name it from five picture hints.',
    'active' => 'guess',
]);
?>
<div class="container-xl">
    <header class="page-head d-flex flex-column flex-md-row gap-3 justify-content-between align-items-md-end">
        <div>
            <h1>Guess the Car</h1>
            <p class="lead mb-0">A new car every day. Five guesses, and each wrong one unlocks a bigger picture.</p>
            <?php if ($latest): ?>
                <p class="small text-body-secondary mt-2 mb-0">
                    <i class="bi bi-clock me-1"></i><?php if ($moreComing): ?>Next car in <span data-countdown="<?= gtc_seconds_to_next_day() ?>"></span><?php else: ?>New cars are on the way. Check back soon.<?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
        <?php if ($latest): ?>
            <a class="btn btn-primary btn-lg flex-shrink-0" href="<?= url('game_display.php?day=' . $latest) ?>">
                <i class="bi bi-lightning-charge-fill me-1"></i>Play today's car (Day <?= $latest ?>)
            </a>
        <?php endif; ?>
    </header>

    <?php if (!$days): ?>
        <div class="alert alert-secondary">The first car comes out soon.</div>
    <?php else: ?>
        <div class="card mb-4" style="max-width: 32rem">
            <div class="card-body">
                <div class="row row-cols-4 g-2" data-gtc-stats data-today="<?= $latest ?>">
                    <div class="col stat-tile"><div class="value" data-gtc-stat="played">0</div><div class="label">Played</div></div>
                    <div class="col stat-tile"><div class="value" data-gtc-stat="win">0%</div><div class="label">Win rate</div></div>
                    <div class="col stat-tile"><div class="value" data-gtc-stat="streak">0</div><div class="label">Streak</div></div>
                    <div class="col stat-tile"><div class="value" data-gtc-stat="best">0</div><div class="label">Best</div></div>
                </div>
            </div>
        </div>

        <div class="day-grid">
            <?php foreach (array_reverse($days) as $day): ?>
                <a class="day-tile<?= $day === $latest ? ' is-latest' : '' ?>" href="<?= url('game_display.php?day=' . $day) ?>" data-gtc-day="<?= $day ?>"
                   aria-label="Game of day <?= $day ?><?= $day === $latest ? ' (today)' : '' ?>">
                    <span class="day-label"><?= $day === $latest ? 'Today' : 'Day' ?></span>
                    <span class="day-num"><?= $day ?></span>
                    <span class="day-status" aria-hidden="true">🔘</span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="row g-4 mt-4">
        <div class="col-md-6">
            <a href="<?= url('oneshot.php') ?>" class="card card-hover h-100 text-decoration-none">
                <div class="card-body d-flex gap-3 align-items-center">
                    <span class="fs-1" aria-hidden="true">🎯</span>
                    <div>
                        <h2 class="h4 mb-1 text-body">Want a harder one?</h2>
                        <p class="mb-0 text-body-secondary">Try One Shot Prototype: one concept car, one guess.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="<?= url('Dreamcar/shtbox.php') ?>" class="card card-hover h-100 text-decoration-none overflow-hidden">
                <div class="row g-0 h-100">
                    <div class="col-5"><img src="<?= url('Res/shtbox.webp') ?>" alt="" class="w-100 h-100 object-fit-cover" loading="lazy"></div>
                    <div class="col-7 card-body">
                        <h2 class="h5 mb-1 text-body">Hot single shitboxes in your area</h2>
                        <p class="small mb-0 text-body-secondary">Ad Place: please close your ad blocker and switch to uBlock Origin. It's better.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<?php
page_end();
