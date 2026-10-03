<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
require __DIR__ . '/../inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';

page_start([
    'title' => 'Cat 2 Car',
    'description' => 'Dream Car Finder: discover which car your favourite cat is!',
    'active' => 'dreamcar',
    'section' => 'dreamcar',
]);
?>
<div class="container-xl">
    <header class="page-head">
        <a href="<?= url('Dreamcar/index.php') ?>" class="small text-body-secondary text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Dream Car Finder</a>
        <h1>Cat 2 Car</h1>
        <p class="lead mb-0">Every cat has a car soulmate. Find yours.</p>
    </header>

    <div class="row g-4">
        <div class="col-lg-5">
            <form class="card" id="catForm">
                <img src="<?= url('Dreamcar/catIMGs/menu.webp') ?>" class="card-img-top" alt="Cat breeds: Persian, Siamese, Maine Coon, Savannah, Scottish, Sphynx, domestic orange and black" width="2350" height=auto>
                <div class="card-body p-4">
                    <label for="catSelection" class="form-label">Your cat</label>
                    <select class="form-select form-select-lg mb-3" id="catSelection" required>
                        <option value="" selected disabled>Car by cat…</option>
                        <option value="persian">Persian Cat</option>
                        <option value="siamese">Siamese Cat</option>
                        <option value="mainecoon">Maine Coon</option>
                        <option value="savannah">Savannah</option>
                        <option value="scottish">Scottish Cat</option>
                        <option value="sphynx">Sphynx Cat</option>
                        <option value="bornana">Domestic Orange</option>
                        <option value="black">Domestic Black</option>
                        <option value="zelda">Your Cat Zelda</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-heart me-1"></i>Find your car!</button>
                </div>
            </form>
        </div>
        <div class="col-lg-7">
            <div class="card dc-result" id="catResultCard">
                <div class="card-body p-3 text-center">
                    <img id="catResult" src="<?= url('Dreamcar/site/cat2car.webp') ?>" alt="Your cat's car appears here" width="600" height="600">
                    <p class="car-name mt-3 mb-1" id="carName"></p>
                    <p class="text-body-secondary mb-0" id="carText">(Disclaimer: this website believes in cat supremacy.)</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
page_end(['scripts' => ['Dreamcar/cat2car/catpage.js']]);
