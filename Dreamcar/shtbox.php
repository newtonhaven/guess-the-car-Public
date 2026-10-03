<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
require __DIR__ . '/../inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';

page_start([
    'title' => 'Single Shitbox Finder',
    'description' => 'Find single shitboxes in your area! Find the single shitboxes that need to be maintained!!',
    'active' => 'dreamcar',
    'section' => 'dreamcar',
]);
?>
<div class="container-xl pt-4">
    <header class="text-center mb-4">
        <h1 class="visually-hidden">Single Shitbox Finder</h1>
        <img src="<?= url('Dreamcar/site/faketitle.webp') ?>" alt="Single Shitbox Finder" class="img-fluid rounded-3" style="max-width: 600px; width: 100%" width="940" height="430">
    </header>

    <div class="row g-4 justify-content-center">
        <div class="col-md-6">
            <a href="https://www.google.com/maps/search/junk+yard/" target="_blank" rel="noopener" class="card card-hover overflow-hidden text-decoration-none">
                <img src="<?= url('Dreamcar/site/map.webp') ?>" alt="Single shitbox finder: Google Maps" class="w-100 h-auto" loading="lazy">
                <div class="card-body"><i class="bi bi-geo-alt me-2 text-accent"></i>Shitboxes near you</div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="https://www.youtube.com/@chrisfix" target="_blank" rel="noopener" class="card card-hover overflow-hidden text-decoration-none">
                <img src="<?= url('Dreamcar/site/chrisfix.webp') ?>" alt="Free shitbox maintenance videos: ChrisFix on YouTube" class="w-100 h-auto" loading="lazy">
                <div class="card-body"><i class="bi bi-youtube me-2 text-accent"></i>Free maintenance lessons</div>
            </a>
        </div>
    </div>

    <p class="small text-body-secondary font-monospace bg-surface border rounded-3 p-3 mt-4 mb-0">
        [DISCLAIMER: This page is intended for comedic purposes only. We explicitly state that we are not liable for any
        damage to your car or your health. The map is purely for entertainment and displays random junkyards with no actual
        relevance. Please use this website responsibly and understand that its content is not to be taken seriously.]
    </p>
</div>
<?php
page_end();
