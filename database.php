<?php
// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
require __DIR__ . '/inc/bootstrap.php';
require APP_ROOT . '/inc/layout.php';

$cars = load_json('data/cars.json')['cars'] ?? [];
$concepts = load_json('data/concepts.json')['concepts'] ?? [];
$mules = load_json('data/oneshot.json')['rounds'] ?? [];
$groups = load_json('data/manufacturers.json')['groups'] ?? [];
usort($concepts, function ($a, $b) { return $a['year'] <=> $b['year']; });

// Family trees grouped by country (alphabetical), keeping the file order inside each country.
$countries = [];
foreach ($groups as $g) {
    $countries[$g['country']]['flag'] = $g['flag'];
    $countries[$g['country']]['count'] = ($countries[$g['country']]['count'] ?? 0) + 1;
}
ksort($countries);
$order = array_flip(array_keys($countries));
uksort($groups, function ($a, $b) use ($groups, $order) {
    return [$order[$groups[$a]['country']], $a] <=> [$order[$groups[$b]['country']], $b];
});

$eras = ['modern' => 'Kept the option maybe add something later idk.', 'concept' => 'Concept cars'];

page_start([
    'title' => 'Car database',
    'description' => 'Concept cars, test mules and car manufacturer family trees.',
    'active' => 'database',
]);
?>
<div class="container-xl">
    <header class="page-head">
        <h1>Car Database</h1>
        <p class="lead mb-0">Iconic cars, wild test mules, who owns whom.</p>
    </header>

    <ul class="nav nav-pills db-tabs gap-2 mb-4" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#cars" type="button" role="tab" aria-controls="cars" aria-selected="true"><i class="bi bi-car-front me-1"></i>Concept Cars</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#mules" type="button" role="tab" aria-controls="mules" aria-selected="false"><i class="bi bi-cone-striped me-1"></i>Test Mules</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#trees" type="button" role="tab" aria-controls="trees" aria-selected="false"><i class="bi bi-diagram-3 me-1"></i>Family trees</button></li>
    </ul>

    <div class="tab-content">
        <!-- ============================== Cars -->
        <section class="tab-pane fade show active" id="cars" role="tabpanel" tabindex="0">
            <div class="row g-2 mb-4">
                <div class="col-sm-8 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control" id="carSearch" placeholder="Search cars, makers, countries…" aria-label="Search cars">
                    </div>
                </div>
                <div class="col-sm-4 col-md-3">
                    <select class="form-select" id="carEra" aria-label="Era">
                        <option value="">All Cars</option>
                        <?php foreach ($eras as $key => $label): ?>
                            <option value="<?= e($key) ?>"><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4" id="carGrid">
                <?php foreach ($concepts as $c): ?>
                    <div class="col" id="concept-<?= e($c['id']) ?>" data-era="concept"
                         data-search="<?= e(strtolower($c['name'] . ' ' . $c['maker'] . ' ' . ($c['designer'] ?? '') . ' ' . $c['year'] . ' concept ' . implode(' ', $c['aliases'] ?? []))) ?>">
                        <article class="card h-100 card-hover overflow-hidden">
                            <div class="thumb" data-wiki="<?= e($c['wiki']) ?>"><i class="bi bi-rocket-takeoff"></i></div>
                            <div class="card-body">
                                <h2 class="h5 mb-1"><a href="#concept-<?= e($c['id']) ?>" class="stretched-link text-body text-decoration-none" data-detail="concept:<?= e($c['id']) ?>"><?= e($c['name']) ?></a></h2>
                                <p class="small text-body-secondary mb-2"><?= e($c['maker']) ?> · <?= (int) $c['year'] ?></p>
                                <p class="small mb-0 line-clamp-3"><?= e($c['fact']) ?></p>
                            </div>
                            <div class="card-footer small text-body-secondary d-flex flex-wrap gap-1 column-gap-3 justify-content-between">
                                <span><i class="bi bi-rocket-takeoff me-1"></i>Concept car</span>
                                <?php if (!empty($c['designer'])): ?><span><i class="bi bi-pencil me-1"></i><?= e($c['designer']) ?></span><?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="text-body-secondary d-none mt-3" id="carEmpty">No cars match your search.</p>
        </section>

        <!-- ============================== Test mules (the One Shot Prototype photos) -->
        <section class="tab-pane fade" id="mules" role="tabpanel" tabindex="0">
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-between mb-4">
                <div class="input-group" style="max-width: 420px">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control" id="muleSearch" placeholder="Search test mules…" aria-label="Search test mules">
                </div>
                <a href="<?= url('oneshot.php') ?>" class="btn btn-outline-primary"><i class="bi bi-bullseye me-1"></i>Play One Shot</a>
            </div>
            <div class="alert alert-secondary small"><i class="bi bi-eye-slash me-2"></i>Spoiler warning: these are the answers for One Shot Prototype. Drag a photo to see the test mule turn into the real car.</div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="muleGrid">
                <?php foreach ($mules as $m): ?>
                    <div class="col" id="mule-<?= (int) $m['id'] ?>"
                         data-search="<?= e(strtolower($m['name'] . ' ' . $m['maker'] . ' ' . $m['year'] . ' ' . implode(' ', $m['aliases'] ?? []))) ?>">
                        <article class="card h-100 overflow-hidden">
                            <div class="mule-compare" data-before="<?= e(url('Res/oneshot/' . (int) $m['id'] . '/1.webp')) ?>"
                                 data-after="<?= e(url('Res/oneshot/' . (int) $m['id'] . '/2.webp')) ?>" data-name="<?= e($m['name']) ?>"></div>
                            <div class="card-body">
                                <h2 class="h5 mb-1"><?= e($m['name']) ?></h2>
                                <p class="small text-body-secondary mb-2"><?= e($m['maker']) ?> · <?= (int) $m['year'] ?></p>
                                <p class="small mb-0"><?= e($m['fact']) ?></p>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="text-body-secondary d-none mt-3" id="muleEmpty">No test mules match your search.</p>
        </section>

        <!-- ============================== Family trees -->
        <section class="tab-pane fade" id="trees" role="tabpanel" tabindex="0">
            <div class="row g-2 mb-3">
                <div class="col-sm-8 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control" id="treeSearch" placeholder="Who owns… (e.g. Lamborghini)" aria-label="Search brands">
                    </div>
                </div>
                <div class="col-sm-4 col-md-3">
                    <select class="form-select" id="treeCountry" aria-label="Country">
                        <option value="">All countries (<?= count($groups) ?> groups)</option>
                        <?php foreach ($countries as $country => $info): ?>
                            <option value="<?= e($country) ?>"><?= e($info['flag'] . ' ' . $country . ' (' . $info['count'] . ')') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-3 small text-body-secondary mb-4">
                <span><span class="chip">Brand</span> owned</span>
                <span><span class="chip" style="border-style:dashed">Brand</span> stake / joint venture</span>
                <span><span class="chip" style="opacity:.6;text-decoration:line-through">Brand</span> formerly owned</span>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="treeGrid">
                <?php foreach ($groups as $g): ?>
                    <div class="col tree-col" data-country="<?= e($g['country']) ?>">
                        <article class="card tree-card h-100">
                            <div class="card-body">
                                <div class="tree-root mb-1"><span aria-hidden="true"><?= e($g['flag']) ?></span><?= e($g['name']) ?></div>
                                <p class="small text-body-secondary mb-3">
                                    <?= e($g['country']) ?><?= $g['founded'] ? ' · founded ' . (int) $g['founded'] : '' ?>
                                    <?= !empty($g['note']) ? '<br>' . e($g['note']) : '' ?>
                                </p>
                                <div class="tree-branch">
                                    <div class="tree-branch-title">Brands</div>
                                    <div class="brand-chips">
                                        <?php foreach ($g['brands'] as $b): ?><span class="chip"><?= e($b) ?></span><?php endforeach; ?>
                                    </div>
                                </div>
                                <?php if (!empty($g['stakes'])): ?>
                                    <div class="tree-branch">
                                        <div class="tree-branch-title">Stakes &amp; partners</div>
                                        <div class="brand-chips">
                                            <?php foreach ($g['stakes'] as $s): ?><span class="chip stake" title="<?= e($s['note']) ?>"><?= e($s['name']) ?> <small class="opacity-75">(<?= e($s['note']) ?>)</small></span><?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($g['former'])): ?>
                                    <div class="tree-branch">
                                        <div class="tree-branch-title">Formerly</div>
                                        <div class="brand-chips">
                                            <?php foreach ($g['former'] as $f): ?><span class="chip former" title="<?= e($f['note']) ?>"><?= e($f['name']) ?></span><?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="text-body-secondary d-none mt-3" id="treeEmpty">No group found for that brand or country.</p>
            <p class="small text-body-secondary mt-4 mb-0">Simplified to passenger-car brands. Shareholdings change often; hover a chip for details.</p>
        </section>

    </div>
</div>

<!-- Detail modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content bg-surface">
            <div class="modal-header">
                <h2 class="modal-title h3" id="detailTitle"></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="thumb rounded-3 mb-3" id="detailImage"><i class="bi bi-image"></i></div>
                <dl class="spec-list mb-3" id="detailSpecs"></dl>
                <p class="fw-semibold" id="detailFact"></p>
                <p class="text-body-secondary small" id="detailExtract"></p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-outline-primary" id="detailWiki" target="_blank" rel="noopener"><i class="bi bi-wikipedia me-1"></i>Read on Wikipedia</a>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="dbData"><?= json_encode(['cars' => $cars, 'concepts' => $concepts], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<?php
page_end(['scripts' => ['assets/js/database.js']]);
