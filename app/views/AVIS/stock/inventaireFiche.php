<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de comptage</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <style>
        .card h6 { font-size: 0.95rem; }
        .small-label { font-size: 0.85rem; color: #6c757d; }
    </style>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
</head>
<body>
<div id="app">
    <?php Flight::render('ui/menu/avis/stock/menuStock'); ?>
    <div id="main" class="layout-navbar">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
        <div id="main-content">
            <div class="page-heading d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3>Fiche de comptage</h3>
                    <p class="text-muted mb-0">Lister les articles à compter par campagne et exporter PDF/Excel.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" id="btn-export-csv">Exporter CSV</button>
                    <button class="btn btn-outline-secondary btn-sm" id="btn-export-pdf">Exporter PDF</button>
                    <button class="btn btn-primary btn-sm" id="btn-refresh">Rafraîchir</button>
                </div>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Campagne</label>
                                        <select class="form-select" id="filter-campagne" required></select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Dépôt</label>
                                        <select class="form-select" id="filter-depot">
                                            <option value="">Tous</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Famille</label>
                                        <select class="form-select" id="filter-famille">
                                            <option value="">Toutes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Article</label>
                                        <select class="form-select" id="filter-article">
                                            <option value="">Tous</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="table-responsive" style="max-height:75vh;">
                                    <table class="table table-hover align-middle mb-0" id="sheet-table">
                                        <thead>
                                            <tr>
                                                <th>Dépôt</th>
                                                <th>Article</th>
                                                <th>Famille</th>
                                                <th class="text-end">Qté théorique</th>
                                                <th class="text-end">Qté physique</th>
                                                <th class="text-end">Écart</th>
                                                <th class="text-end">Valeur théorique</th>
                                                <th class="text-end">Valeur physique</th>
                                                <th>Type</th>
                                                <th>Valeur écart</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script>
const campagneSelect = document.getElementById('filter-campagne');
const depotSelect = document.getElementById('filter-depot');
const familleSelect = document.getElementById('filter-famille');
const articleSelect = document.getElementById('filter-article');
const sheetBody = document.querySelector('#sheet-table tbody');

function toast(msg, success = true) {
    Toastify({text: msg, gravity: 'top', position: 'right', className: success ? 'bg-success' : 'bg-danger', duration: 2500}).showToast();
}

async function loadCampaigns() {
    const res = await fetch('<?= Flight::base() ?>/api/stock/inventaire/campagnes');
    const json = await res.json();
    if (!json.success) { toast(json.message || 'Erreur campagnes', false); return; }
    campagneSelect.innerHTML = '<option value="">Choisir une campagne</option>';
    json.data.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id_inventaire_campagne;
        opt.textContent = `${c.code} • ${c.libelle}`;
        campagneSelect.appendChild(opt);
    });
}

async function loadDepots() {
    const res = await fetch('<?= Flight::base() ?>/api/stock/depots');
    const json = await res.json();
    if (!json.success) return;
    depotSelect.innerHTML = '<option value="">Tous</option>';
    json.data.forEach(d => {
        const opt = document.createElement('option');
        opt.value = d.id_depot;
        opt.textContent = `${d.code || d.id_depot} • ${d.nom}`;
        depotSelect.appendChild(opt);
    });
}

async function loadFamilles() {
    const res = await fetch('<?= Flight::base() ?>/api/referentiel/articles/familles/all');
    const json = await res.json();
    if (!json.success) return;
    familleSelect.innerHTML = '<option value="">Toutes</option>';
    json.data.forEach(f => {
        const opt = document.createElement('option');
        opt.value = f.id_article_famille;
        opt.textContent = `${f.code} • ${f.nom}`;
        familleSelect.appendChild(opt);
    });
}

async function loadArticles() {
    const res = await fetch('<?= Flight::base() ?>/api/referentiel/articles/all');
    const json = await res.json();
    if (!json.success) return;
    articleSelect.innerHTML = '<option value="">Tous</option>';
    json.data.forEach(a => {
        const opt = document.createElement('option');
        opt.value = a.id_article;
        opt.textContent = `${a.code || ''} • ${a.designation || a.libelle || 'Article'}`.trim();
        articleSelect.appendChild(opt);
    });
}

function buildQuery(base) {
    const campagne = campagneSelect.value;
    if (!campagne) { return null; }
    const params = new URLSearchParams();
    params.set('campagne', campagne);
    if (depotSelect.value) params.set('depot', depotSelect.value);
    if (familleSelect.value) params.set('famille', familleSelect.value);
    if (articleSelect.value) params.set('article', articleSelect.value);
    return `${base}?${params.toString()}`;
}

async function loadSheet() {
    const url = buildQuery('<?= Flight::base() ?>/api/stock/inventaire/fiche');
    if (!url) {
        sheetBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Sélectionnez une campagne</td></tr>';
        return;
    }
    sheetBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Chargement...</td></tr>';
    const res = await fetch(url);
    const json = await res.json();
    if (!json.success) { sheetBody.innerHTML = ''; toast(json.message || 'Erreur chargement', false); return; }
    if (!json.data.length) {
        sheetBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Aucune ligne</td></tr>';
        return;
    }
    sheetBody.innerHTML = '';
    json.data.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${r.depot_nom || ''}</td>
            <td>${(r.code || '') + ' • ' + (r.designation || '')}</td>
            <td>${r.famille_nom || ''}</td>
            <td class="text-end">${r.quantite_theorique ?? ''}</td>
            <td class="text-end">${r.quantite_comptee ?? ''}</td>
            <td class="text-end">${r.ecart ?? ''}</td>
            <td class="text-end">${r.valeur_theorique ?? ''}</td>
            <td class="text-end">${r.valeur_physique ?? ''}</td>
            <td>${r.type_ecart || ''}</td>
            <td>${r.valeur_theorique - r.valeur_physique ?? ''}</td>`;
        sheetBody.appendChild(tr);
    });
}

function exportCsv() {
    const url = buildQuery('<?= Flight::base() ?>/api/stock/inventaire/fiche.csv');
    if (!url) { toast('Choisir une campagne', false); return; }
    window.open(url, '_blank');
}

function exportPdf() {
    const url = buildQuery('<?= Flight::base() ?>/api/stock/inventaire/fiche.pdf');
    if (!url) { toast('Choisir une campagne', false); return; }
    window.open(url, '_blank');
}

campagneSelect.addEventListener('change', loadSheet);
depotSelect.addEventListener('change', loadSheet);
familleSelect.addEventListener('change', loadSheet);
articleSelect.addEventListener('change', loadSheet);
document.getElementById('btn-refresh').addEventListener('click', loadSheet);
document.getElementById('btn-export-csv').addEventListener('click', exportCsv);
document.getElementById('btn-export-pdf').addEventListener('click', exportPdf);

loadCampaigns();
loadDepots();
loadFamilles();
loadArticles();
</script>
</body>
</html>
