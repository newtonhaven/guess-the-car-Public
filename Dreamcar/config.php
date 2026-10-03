<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
require __DIR__ . '/../inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';

page_start([
    'title' => 'Dream car by configuration',
    'description' => 'Discover your REAL dream car that you had no idea exists, by selecting your preferred configuration.',
    'active' => 'dreamcar',
    'section' => 'dreamcar',
]);
?>
<div class="container-xl">
    <header class="page-head">
        <a href="<?= url('Dreamcar/index.php') ?>" class="small text-body-secondary text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Dream Car Finder</a>
        <h1>Find it by configuration</h1>
        <p class="lead mb-0">Welcome to the home of shitboxes. Choose wisely.</p>
    </header>

    <div class="row g-4">
        <div class="col-lg-5">
            <form class="card" id="configForm">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="enginePlacement" class="form-label">Engine placement</label>
                        <select class="form-select form-select-lg" id="enginePlacement" required>
                            <option value="" selected disabled>Choose…</option>
                            <option value="front">Front engine</option>
                            <option value="mid">Mid engine</option>
                            <option value="rear">Rear engine</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="driving" class="form-label">Driving</label>
                        <select class="form-select form-select-lg" id="driving" required>
                            <option value="" selected disabled>Choose…</option>
                            <option value="fwd">FWD</option>
                            <option value="rwd">RWD</option>
                            <option value="awd">AWD</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="induction" class="form-label">Induction type</label>
                        <select class="form-select form-select-lg" id="induction" required disabled>
                            <option value="" selected disabled>Pick the first two</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100" id="findBtn" disabled>
                        <i class="bi bi-search-heart me-1"></i>Find your car!
                    </button>
                    <p class="small text-body-secondary mt-3 mb-0">(It may take a moment to load the image, please be patient.)</p>
                </div>
            </form>
        </div>
        <div class="col-lg-7">
            <div class="card dc-result">
                <div class="card-body p-3 text-center">
                    <img id="configResult" src="<?= url('Dreamcar/carIMGs/moon.webp') ?>" alt="Your dream car appears here" width="600" height="595">
                </div>
            </div>
        </div>
    </div>
</div>
<?php
page_end(['scripts' => ['Dreamcar/config/config.js']]);
