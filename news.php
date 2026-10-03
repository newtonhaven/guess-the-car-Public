<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
require __DIR__ . '/inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';
require APP_ROOT . '/inc/news.php';

$news = news_get(60);
$articles = $news['articles'];
$sources = array_count_values(array_column($articles, 'source'));
arsort($sources);
$featured = array_shift($articles);

function news_image($article, $class)
{
    if (!$article['image']) {
        return '<div class="' . $class . ' d-flex align-items-center justify-content-center text-body-secondary fs-1"><i class="bi bi-newspaper"></i></div>';
    }
    return '<img src="' . e($article['image']) . '" alt="" class="' . $class . '" loading="lazy" referrerpolicy="no-referrer" onerror="this.style.visibility=\'hidden\'">';
}

page_start([
    'title' => 'Car industry news',
    'description' => 'The latest car industry news from around the web.',
    'active' => 'news',
]);
?>
<div class="container-xl">
    <header class="page-head d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-end">
        <div>
            <h1>Car Industry News</h1>
            <p class="lead mb-0">Fresh from the car press, refreshed every few hours.</p>
        </div>
        <?php if ($news['updated']): ?>
            <span class="small text-body-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Updated <?= e(time_ago($news['updated'])) ?></span>
        <?php endif; ?>
    </header>

    <?php if (!$featured): ?>
        <div class="alert alert-secondary"><i class="bi bi-cone-striped me-2"></i>Sorry, we can't get the news right now. Please check back later.</div>
    <?php else: ?>
        <div class="d-flex flex-wrap gap-2 mb-4" id="sourceFilter" role="group" aria-label="Filter by source">
            <button type="button" class="btn btn-sm btn-primary" data-source="">All</button>
            <?php foreach ($sources as $name => $count): ?>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-source="<?= e($name) ?>"><?= e($name) ?> <span class="opacity-75"><?= $count ?></span></button>
            <?php endforeach; ?>
        </div>

        <article class="card news-card news-feature overflow-hidden mb-4" data-source="<?= e($featured['source']) ?>">
            <div class="row g-0">
                <div class="col-md-7"><?= news_image($featured, 'news-img') ?></div>
                <div class="col-md-5">
                    <div class="card-body p-4 h-100 d-flex flex-column">
                        <span class="badge text-bg-primary align-self-start mb-2">Latest</span>
                        <h2 class="card-title h2"><a href="<?= e($featured['url']) ?>" target="_blank" rel="noopener"><?= e($featured['title']) ?></a></h2>
                        <p class="text-body-secondary line-clamp-3"><?= e($featured['description']) ?></p>
                        <div class="mt-auto small text-body-secondary"><?= e($featured['source']) ?><?= $featured['publishedAt'] ? ' · ' . e(time_ago($featured['publishedAt'])) : '' ?></div>
                    </div>
                </div>
            </div>
        </article>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4" id="newsGrid">
            <?php foreach ($articles as $a): ?>
                <div class="col" data-source="<?= e($a['source']) ?>">
                    <article class="card news-card h-100 overflow-hidden card-hover">
                        <?= news_image($a, 'news-img') ?>
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5 line-clamp-3"><a href="<?= e($a['url']) ?>" target="_blank" rel="noopener" class="stretched-link"><?= e($a['title']) ?></a></h3>
                            <p class="small text-body-secondary line-clamp-3 mb-3"><?= e($a['description']) ?></p>
                            <div class="mt-auto small text-body-secondary"><?= e($a['source']) ?><?= $a['publishedAt'] ? ' · ' . e(time_ago($a['publishedAt'])) : '' ?></div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
page_end(['inline' => '<script>
(function () {
  var buttons = document.querySelectorAll("#sourceFilter button");
  buttons.forEach(function (btn) {
    btn.addEventListener("click", function () {
      var source = btn.dataset.source;
      buttons.forEach(function (b) {
        b.classList.toggle("btn-primary", b === btn);
        b.classList.toggle("btn-outline-secondary", b !== btn);
      });
      document.querySelectorAll("[data-source]").forEach(function (el) {
        if (el.tagName === "BUTTON") return;
        el.classList.toggle("d-none", source !== "" && el.dataset.source !== source);
      });
    });
  });
})();
</script>']);
