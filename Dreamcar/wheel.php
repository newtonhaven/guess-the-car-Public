<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
require __DIR__ . '/../inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';

page_start([
    'title' => 'Wheel of Shame',
    'description' => 'Dream Car Finder: spin the wheel and discover the best of the worst cars!',
    'active' => 'dreamcar',
    'section' => 'dreamcar',
]);
?>
<div class="container-xl">
    <header class="page-head text-center">
        <a href="<?= url('Dreamcar/index.php') ?>" class="small text-body-secondary text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Dream Car Finder</a>
        <h1>Wheel of Shame</h1>
        <p class="lead mb-0">Spin it. Whatever it lands on is your dream car now. No refunds.</p>
    </header>

    <div class="row g-4 align-items-center">
        <div class="col-lg-6 text-center">
            <div class="wheel-pointer" aria-hidden="true"><i class="bi bi-caret-down-fill"></i></div>
            <img id="wheelImage" src="<?= url('Dreamcar/wheel/img/wheel1.webp') ?>" alt="Wheel of the worst cars" class="wheel-img" width="1250" height="1250">
            <div>
                <button type="button" id="spinBtn" class="btn btn-danger btn-lg px-5 mt-4 fw-bold">SBINNALA!</button>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card dc-result" id="wheelResult">
                <div class="card-body p-3 text-center">
                    <img id="wheelCar" src="<?= url('Dreamcar/wheel/img/garage.webp') ?>" alt="Your car appears here" width="1000" height="520">
                    <p class="car-name mt-3 mb-1" id="wheelCarName" aria-live="polite"></p>
                    <p class="text-body-secondary fst-italic mb-0" id="wheelCarText"></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
page_end(['scripts' => ['Dreamcar/wheel/wheel.js']]);
