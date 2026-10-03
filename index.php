<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
require __DIR__ . '/inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';
require APP_ROOT . '/inc/guess.php';
require APP_ROOT . '/inc/news.php';

$days = gtc_days();
$latest = $days ? max($days) : 0;
$news = news_get(4);

page_start(['active' => 'home']);
?>
<section class="hero">
    <div class="container-xl">
        <h1 class="visually-hidden">Guess the Car</h1>
        <img src="<?= url('Res/logo.webp') ?>" alt="Guess the Car" class="hero-logo" width="1210" height="235">
        <p class="tagline">Car games, car trivia and car nonsense. Pick your poison.</p>
    </div>
</section>

<div class="container-xl">
    <div class="row g-4">
        <!-- Guess the Car (featured) -->
        <div class="col-lg-7">
            <article class="card game-card feature card-hover h-100">
                <div><img src="<?= url('Res/FinderMain.webp') ?>" alt="" class="card-img-top object-fit-cover" style="height: 220px" loading="lazy"></div>
                <div class="card-body p-4">
                    <h2 class="h1">Guess the Car</h2>
                    <p class="card-text fs-5">A new car every day. Five pictures, five guesses, and every wrong answer reveals a bigger hint. How fast can you name the car?</p>
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                        <a href="<?= url($latest ? 'game_display.php?day=' . $latest : 'daily.php') ?>" class="btn btn-primary btn-lg stretched-link">
                            <i class="bi bi-play-fill me-1"></i><?= $latest ? "Play today's car (Day " . $latest . ')' : 'Guess the Car' ?>
                        </a>
                        <?php if ($latest): ?>
                            <span class="text-body-secondary small" data-gtc-stats data-today="<?= $latest ?>">
                                <span data-gtc-stat="won">0</span> / <?= count($days) ?> solved · streak <span data-gtc-stat="streak">0</span>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        </div>

        <!-- One Shot Prototype -->
        <div class="col-md-6 col-lg-5">
            <article class="card game-card card-hover h-100">
                <div><img src="<?= url('Res/PrototypeMian.webp') ?>" alt="" class="card-img-top object-fit-cover" style="height: 220px" loading="lazy"></div>
                <div class="card-body p-4">
                    <h2>One Shot Prototype</h2>
                    <p class="card-text">One picture of test mule. One guess. No second chances.</p>
                    <a href="<?= url('oneshot.php') ?>" class="btn btn-outline-primary stretched-link"><i class="bi bi-bullseye me-1"></i>Take the shot</a>
                </div>
            </article>
        </div>

        <!-- Dream Car Finder -->
        <div class="col-md-6 col-lg-4">
            <article class="card game-card card-hover h-100">
                <div><img src="<?= url('Dreamcar/carIMGs/previa.webp') ?>" alt="" class="card-img-top object-fit-cover" style="height: 220px" loading="lazy"></div>
                <div class="card-body p-4">
                    <h2>Dream Car Finder</h2>
                    <p class="card-text">Find the dream car you never knew you wanted: by configuration, by your cat, or by spinning the Wheel of Shame.</p>
                    <a href="<?= url('Dreamcar/index.php') ?>" class="btn btn-outline-primary stretched-link"><i class="bi bi-search-heart me-1"></i>Find my car</a>
                </div>
            </article>
        </div>

        <!-- Database -->
        <div class="col-md-6 col-lg-4">
            <article class="card game-card card-hover h-100">
                <div><img src="<?= url('Res/databaseMain.webp') ?>" alt="" class="card-img-top object-fit-cover" style="height: 220px" loading="lazy"></div>
                <div class="card-body p-4">
                    <h2>Car Database</h2>
                    <p class="card-text">Iconic cars, wild prototypes, who-owns-whom family trees.</p>
                    <a href="<?= url('database.php') ?>" class="btn btn-outline-primary stretched-link"><i class="bi bi-journal-richtext me-1"></i>Browse</a>
                </div>
            </article>
        </div>

        <!-- News -->
        <div class="col-md-6 col-lg-4">
            <article class="card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <h2 class="mb-0"><span aria-hidden="true">📰</span> Car News</h2>
                        <a href="<?= url('news.php') ?>" class="small">All news <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <?php if ($news['articles']): ?>
                        <ul class="list-unstyled news-mini mb-0">
                            <?php foreach ($news['articles'] as $a): ?>
                                <li class="py-2">
                                    <a href="<?= e($a['url']) ?>" target="_blank" rel="noopener" class="fw-semibold line-clamp-2"><?= e($a['title']) ?></a>
                                    <div class="small text-body-secondary"><?= e($a['source']) ?><?= $a['publishedAt'] ? ' · ' . e(time_ago($a['publishedAt'])) : '' ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-body-secondary mb-0">News is taking a pit stop. Check back soon.</p>
                    <?php endif; ?>
                </div>
            </article>
        </div>

        <!--  shtbox (kept from the original site) -->
        <div class="col-12">
            <a href="<?= url('Dreamcar/shtbox.php') ?>" class="card feautre-cover card-hover overflow-hidden text-decoration-none">
                <div class="row g-0 align-items-center">
                    <div class="col-sm-5 col-lg-4">
                        <img src="<?= url('Res/shtbox.webp') ?>" alt="There are hot single shitboxes in your area" loading="lazy">
                    </div>
                    <div class="col-sm-7 col-lg-8">
                        <div class="card-body p-4">
                            <span class="badge text-bg-secondary mb-2">Special Offer</span>
                            <h2 class="h3 text-body">Hot single shitboxes in your area</h2>
                            <p class="card-text text-body-secondary">Find your dream car, or at least a car that won't break your heart. The Shitbox Finder is here to help you find the perfect match.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<?php
page_end();
