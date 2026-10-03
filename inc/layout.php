<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.

// Shared page layout (head, navbar, traffic strip, footer)

function nav_items()
{
    return [
        'home' => ['Home', 'index.php', 'bi-house-door'],
        'guess' => ['Guess the Car', 'daily.php', 'bi-question-diamond'],
        'oneshot' => ['One Shot', 'oneshot.php', 'bi-bullseye'],
        'dreamcar' => ['Dream Car Finder', 'Dreamcar/index.php', 'bi-search-heart'],
        'database' => ['Database', 'database.php', 'bi-journal-richtext'],
        'news' => ['News', 'news.php', 'bi-newspaper'],
    ];
}

function page_start(array $o)
{
    $title = isset($o['title']) ? $o['title'] . ' · Guess the Car' : 'Guess the Car';
    $description = $o['description'] ?? 'Guess the Car: daily car guessing game, One Shot Prototype, Dream Car Finder, car database and car industry news.';
    $active = $o['active'] ?? '';
    $section = $o['section'] ?? 'gtc';
    ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <meta name="theme-color" content="#0f1729">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= url('apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= url('favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= url('favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= url('site.webmanifest') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- integrity = SRI hash: the browser refuses the file if the CDN ever serves anything else. Update it when changing the version. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"
          integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link href="<?= asset('assets/css/app.css') ?>" rel="stylesheet">
    <?= $o['head'] ?? '' ?>
</head>
<body class="section-<?= e($section) ?>" data-base="<?= e(base_url()) ?>">
<a class="visually-hidden-focusable btn btn-primary position-absolute m-2" style="z-index:2000" href="#main">Skip to content</a>

<nav class="navbar navbar-expand-lg sticky-top site-nav">
    <div class="container-xl">
        <a class="navbar-brand" href="<?= url('index.php') ?>">
            <img src="<?= url('Res/logo.webp') ?>" alt="Guess the Car" width="170" height="33">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto gap-lg-1">
                <?php foreach (nav_items() as $key => $item) { ?>
                    <li class="nav-item">
                        <a class="nav-link<?= $active === $key ? ' active' : '' ?>" href="<?= url($item[1]) ?>"<?= $active === $key ? ' aria-current="page"' : '' ?>>
                            <i class="bi <?= $item[2] ?> me-1"></i><?= e($item[0]) ?>
                        </a>
                    </li>
                <?php }?>
            </ul>
        </div>
    </div>
</nav>

<?php include APP_ROOT . '/Res/trafficThing.php'; ?>

<main id="main" class="flex-grow-1">
<?php
}

/**
 * Options: scripts (list of asset paths), confetti (bool), inline (raw html before </body>).
 */
function page_end(array $o = [])
{
    ?>
</main>

<footer class="site-footer mt-5">
    <div class="container-xl py-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <a href="<?= url('index.php') ?>"><img src="<?= url('Res/logo.webp') ?>" alt="Guess the Car" width="150" height="29" class="mb-2 opacity-75"></a>
                <p class="small text-body-secondary mb-0">Games, trivia and questionable taste in cars. Built for fun, not to be taken seriously.</p>
            </div>
            <div class="col-md-6">
                <ul class="list-inline small text-center text-md-end mb-0">
                    <li class="list-inline-item"><a href="https://github.com/newtonhaven/guess-the-car-Public/blob/main/LICENSE" target="_blank" rel="noopener">License (MIT)</a></li>
                    <li class="list-inline-item"><a href="https://newsapi.org/" target="_blank" rel="noopener">NewsAPI.org</a></li>
                    <li class="list-inline-item"><a href="https://www.wikipedia.org/" target="_blank" rel="noopener">Images: Wikipedia</a></li>
                    <li class="list-inline-item"><a href="https://github.com/Agezao/confetti-js" target="_blank" rel="noopener">Confetti.js</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<button type="button" id="toTop" class="btn btn-primary rounded-circle shadow to-top" aria-label="Back to top">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<?php if (!empty($o['confetti'])): ?>
<script src="<?= asset('assets/vendor/confetti.min.js') ?>"></script>
<?php endif; ?>
<script src="<?= asset('assets/js/app.js') ?>"></script>
<?php foreach (($o['scripts'] ?? []) as $script): ?>
<script src="<?= strpos($script, 'http') === 0 ? e($script) : asset($script) ?>"></script>
<?php endforeach; ?>
<?= $o['inline'] ?? '' ?>
</body>
</html>
<?php
}
