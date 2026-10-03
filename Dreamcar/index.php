<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
require __DIR__ . '/../inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';

$finders = [
    ['config.php', 'carIMGs/previa.webp', 'By configuration', 'Pick engine placement, drive and induction. We find the dream car you had no idea existed.', 'bi-sliders'],
    ['cat2car.php', 'catIMGs/menu.webp', 'Cat 2 Car', 'Which car is your cat? Pick a breed and meet its four-wheeled soulmate.', 'bi-heart'],
    ['wheel.php', 'wheel/img/wheel1.webp', 'Wheel of Shame', 'Spin the wheel and discover the best of the worst cars ever made.', 'bi-life-preserver'],
];

page_start([
    'title' => 'Dream Car Finder',
    'description' => 'Explore new and unusual dream cars in a completely unexpected way: by configuration, by your favourite cat, or by spinning the wheel.',
    'active' => 'dreamcar',
    'section' => 'dreamcar',
]);
?>
<div class="container-xl pt-4">
    <header class="dc-hero mb-4">
        <h1 class="visually-hidden">Dream Car Finder</h1>
        <img src="<?= url('Dreamcar/site/title.webp') ?>" alt="Dream Car Finder" width="7484" height="2064">
        <br>
        <p class="subtitle mb-0">A website that's worse than your rusty project car</p>
    </header>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($finders as $f): ?>
            <div class="col">
                <article class="card h-100 card-hover overflow-hidden">
                    <img src="<?= url('Dreamcar/' . $f[1]) ?>" alt="" class="card-img-top object-fit-cover" style="height: 220px" loading="lazy">
                    <div class="card-body">
                        <h2 class="h3"><i class="bi <?= $f[4] ?> me-2 text-accent"></i><?= e($f[2]) ?></h2>
                        <p class="text-body-secondary mb-3"><?= e($f[3]) ?></p>
                        <a href="<?= url('Dreamcar/' . $f[0]) ?>" class="btn btn-primary stretched-link">Let's go</a>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-sm-8 col-md-6 col-lg-4">
            <a href="<?= url('Dreamcar/shtbox.php') ?>" class="card card-hover overflow-hidden text-decoration-none">
                <img src="<?= url('Dreamcar/site/ad.webp') ?>" alt="Fake ad: find single shitboxes in your area" class="w-100 h-auto" loading="lazy" width="1008" height="756">
            </a>
        </div>
    </div>

    <p class="small text-body-secondary text-center mt-4 mb-0">
        Disclaimer: this section is intended for comedic purposes only. We are not liable for any damage to your car or your health.
    </p>
</div>
<?php
page_end();
