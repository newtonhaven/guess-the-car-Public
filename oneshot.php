<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
// One Shot Prototype: a photo of a test mule (Res/oneshot/<id>/1.webp), one guess, then a slider
// reveals the real car (2.webp). Rounds and hints live in data/oneshot.json.
require __DIR__ . '/inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';

page_start([
    'title' => 'One Shot Prototype',
    'description' => 'One photo of a disguised test mule. One guess. Can you name the car it became?',
    'active' => 'oneshot',
]);
?>
<div class="container-xl">
    <header class="page-head">
        <h1>One Shot Prototype</h1>
        <p class="lead mb-0">One test mule. One guess. Name the car it turned into.</p>
    </header>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="oneshot-stage" id="stage">
                <div class="loading" id="stageLoading"><div class="spinner-border" role="status"><span class="visually-hidden">Loading</span></div></div>
                <img id="shotImage" alt="Mystery test mule" class="d-none">
            </div>
            <p class="small text-body-secondary mt-2 mb-0 d-none" id="compareHelp"><i class="bi bi-arrows-expand-vertical me-1"></i>Drag the slider to reveal the real car.</p>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row row-cols-4 g-2">
                        <div class="col stat-tile"><div class="value" id="statPlayed">0</div><div class="label">Played</div></div>
                        <div class="col stat-tile"><div class="value" id="statWin">0%</div><div class="label">Hit rate</div></div>
                        <div class="col stat-tile"><div class="value" id="statStreak">0</div><div class="label">Streak</div></div>
                        <div class="col stat-tile"><div class="value" id="statBest">0</div><div class="label">Best</div></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <p class="small text-body-secondary mb-2" id="roundLabel">Test mule</p>

                    <div class="mb-3 d-none" id="hintBox">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="small text-body-secondary me-1">Hints</span>
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary hint-btn" data-hint="<?= $i ?>" disabled>
                                    <i class="bi bi-lightbulb me-1"></i><?= $i ?>
                                </button>
                            <?php endfor; ?>
                        </div>
                        <ol class="small mb-0 ps-3" id="hintList"></ol>
                    </div>

                    <form id="shotForm" autocomplete="off">
                        <label for="shotInput" class="form-label">Which car is it?</label>
                        <div class="ac-wrap mb-3">
                            <input id="shotInput" type="text" class="form-control form-control-lg" placeholder="Name the car" maxlength="80" spellcheck="false">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-bullseye me-1"></i>Take the shot</button>
                        <div class="form-text">You only get one. Choose wisely.</div>
                    </form>

                    <div id="result" class="d-none fade-in">
                        <div id="resultBanner" class="result-banner border rounded-3 p-3 mb-3">
                            <div class="small text-body-secondary" id="resultHeadline"></div>
                            <div class="h3 mb-1" id="resultName"></div>
                            <div class="small text-body-secondary" id="resultMeta"></div>
                        </div>
                        <p id="resultFact"></p>
                        <p class="small text-body-secondary d-none" id="resultGuess"></p>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-primary" id="nextBtn"><i class="bi bi-arrow-repeat me-1"></i>Next test mule</button>
                            <button type="button" class="btn btn-outline-primary" id="shareBtn"><i class="bi bi-share me-1"></i>Share</button>
                        </div>
                        <p class="small text-body-secondary mt-3 mb-0 d-none" id="allDone"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
page_end(['confetti' => true, 'scripts' => ['assets/js/oneshot.js']]);
