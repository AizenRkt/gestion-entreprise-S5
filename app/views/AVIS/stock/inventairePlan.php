<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planification d'inventaire</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <style>
        .card h6 { font-size: 0.95rem; }
        .tag { display: inline-block; padding: 4px 8px; border-radius: 6px; background: #f1f5f9; margin-right: 6px; margin-bottom: 6px; }
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
                    <h3>Planification d'inventaire</h3>
                    <p class="text-muted mb-0">Définir les campagnes, le périmètre et les équipes.</p>
                </div>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-xl-7">
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Nouvelle campagne</h5></div>
                            <div class="card-body">
                                <form id="form-campagne" class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Code</label>
                                        <input type="text" class="form-control" id="camp-code" required>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Libellé</label>
                                        <input type="text" class="form-control" id="camp-libelle" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" id="camp-description" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Type</label>
                                        <select class="form-select" id="camp-type">
                                            <option value="GENERAL">Général</option>
                                            <option value="PARTIEL">Partiel</option>
                                            <option value="CYCLE">Cycle</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Statut</label>
                                        <select class="form-select" id="camp-statut">
                                            <option value="PLANIFIE">Planifié</option>
                                            <option value="BROUILLON">Brouillon</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4"></div>
                                    <div class="col-md-6">
                                        <label class="form-label">Début prévu</label>
                                        <input type="date" class="form-control" id="camp-date-debut">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fin prévue</label>
                                        <input type="date" class="form-control" id="camp-date-fin">
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <h6 class="mb-1">Dépôts concernés</h6>
                                        <p class="small-label">Sélection multiple possible.</p>
                                        <select class="form-select" id="camp-depots" multiple></select>
                                    </div>

                                    <div class="col-12">
                                        <hr>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="mb-1">Périmètre</h6>
                                            <button class="btn btn-sm btn-outline-primary" type="button" id="btn-add-scope">Ajouter</button>
                                        </div>
                                        <div class="row g-2 align-items-end mt-1">
                                            <div class="col-md-4">
                                                <label class="form-label">Type</label>
                                                <select class="form-select" id="scope-type">
                                                    <option value="TOUS">Tous</option>
                                                    <option value="FAMILLE">Famille</option>
                                                    <option value="ARTICLE">Article</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Famille</label>
                                                <select class="form-select" id="scope-famille-select" aria-label="Sélection famille">
                                                    <option value="">Choisir une famille</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Article</label>
                                                <select class="form-select" id="scope-article-select" aria-label="Sélection article">
                                                    <option value="">Choisir un article</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="scope-lots" checked>
                                            <label class="form-check-label" for="scope-lots">Inclure les lots</label>
                                        </div>
                                        <div class="mt-2" id="scope-tags"></div>
                                    </div>

                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-success">Enregistrer la campagne</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Campagnes</h5>
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="btn-refresh">Rafraîchir</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height:70vh;">
                                    <table class="table table-hover align-middle mb-0" id="campaign-table">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Libellé</th>
                                                <th>Statut</th>
                                                <th class="text-end">Dépôts</th>
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
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/toastify.js"></script>

<script>
const depotsSelect = document.getElementById('camp-depots');
const scopeTags = document.getElementById('scope-tags');
const campaignsBody = document.querySelector('#campaign-table tbody');
const scopeFamilleSelect = document.getElementById('scope-famille-select');
const scopeArticleSelect = document.getElementById('scope-article-select');
let scopes = [];

function toast(msg, success = true) {
    Toastify({text: msg, gravity: 'top', position: 'right', className: success ? 'bg-success' : 'bg-danger', duration: 2500}).showToast();
}

async function loadDepots() {
    const res = await fetch('<?= Flight::base() ?>/api/stock/depots');
    const json = await res.json();
    if (!json.success) return;
    depotsSelect.innerHTML = '';
    json.data.forEach(d => {
        const opt = document.createElement('option');
        opt.value = d.id_depot;
        opt.textContent = `${d.id_depot} • ${d.nom}`;
        depotsSelect.appendChild(opt);
    });
}

async function loadFamilles() {
    const res = await fetch('<?= Flight::base() ?>/api/referentiel/articles/familles/all');
    const json = await res.json();
    if (!json.success) return;
    scopeFamilleSelect.innerHTML = '<option value="">Choisir une famille</option>';
    json.data.forEach(f => {
        const opt = document.createElement('option');
        opt.value = f.id_article_famille;
        opt.textContent = `${f.code} • ${f.nom}`;
        scopeFamilleSelect.appendChild(opt);
    });
}

async function loadArticles() {
    const res = await fetch('<?= Flight::base() ?>/api/referentiel/articles/all');
    const json = await res.json();
    if (!json.success) return;
    scopeArticleSelect.innerHTML = '<option value="">Choisir un article</option>';
    json.data.forEach(a => {
        const opt = document.createElement('option');
        opt.value = a.id_article;
        opt.textContent = `${a.code || ''} • ${a.designation || a.libelle || 'Article'}`.trim();
        scopeArticleSelect.appendChild(opt);
    });
}

function renderScopes() {
    scopeTags.innerHTML = '';
    scopes.forEach((s, idx) => {
        const div = document.createElement('span');
        div.className = 'tag';
        const label = s.type_cible === 'TOUS'
            ? 'Tous'
            : s.type_cible === 'FAMILLE'
                ? `Famille #${s.id_article_famille || '?'}`
                : `Article #${s.id_article || '?'}`;
        div.innerHTML = `${label} <a href="#" data-idx="${idx}" class="text-danger ms-1 remove-scope">×</a>`;
        scopeTags.appendChild(div);
    });
}

function addScope() {
    const type = document.getElementById('scope-type').value;
    const fam = scopeFamilleSelect.value;
    const art = scopeArticleSelect.value;
    if (type === 'FAMILLE' && !fam) { toast('Famille requise', false); return; }
    if (type === 'ARTICLE' && !art) { toast('Article requis', false); return; }
    scopes.push({
        type_cible: type,
        id_article_famille: fam ? parseInt(fam, 10) : null,
        id_article: art ? parseInt(art, 10) : null,
        inclure_lots: document.getElementById('scope-lots').checked ? 1 : 0
    });
    renderScopes();
}

document.getElementById('btn-add-scope').addEventListener('click', addScope);

scopeTags.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-scope')) {
        const idx = parseInt(e.target.dataset.idx, 10);
        scopes.splice(idx, 1);
        renderScopes();
        e.preventDefault();
    }
});

async function loadCampaigns() {
    campaignsBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Chargement...</td></tr>';
    const res = await fetch('<?= Flight::base() ?>/api/stock/inventaire/campagnes');
    const json = await res.json();
    if (!json.success) { campaignsBody.innerHTML = ''; toast(json.message || 'Erreur chargement', false); return; }
    campaignsBody.innerHTML = '';
    json.data.forEach(c => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${c.code}</td>
            <td>${c.libelle}</td>
            <td><span class="badge bg-light-secondary text-uppercase">${c.statut}</span></td>
            <td class="text-end">${c.depots_count || 0}</td>`;
        campaignsBody.appendChild(tr);
    });
}

document.getElementById('btn-refresh').addEventListener('click', loadCampaigns);

async function submitCampaign(evt) {
    evt.preventDefault();
    const selectedDepots = Array.from(depotsSelect.selectedOptions).map(o => ({ id_depot: parseInt(o.value, 10) }));
    const payload = {
        code: document.getElementById('camp-code').value.trim(),
        libelle: document.getElementById('camp-libelle').value.trim(),
        description: document.getElementById('camp-description').value.trim(),
        type_campagne: document.getElementById('camp-type').value,
        statut: document.getElementById('camp-statut').value,
        date_debut_prevue: document.getElementById('camp-date-debut').value || null,
        date_fin_prevue: document.getElementById('camp-date-fin').value || null,
        depots: selectedDepots,
        cibles: scopes
    };
    const res = await fetch('<?= Flight::base() ?>/api/stock/inventaire/campagnes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    const json = await res.json();
    if (!json.success) { toast(json.message || 'Erreur lors de la création', false); return; }
    toast('Campagne créée');
    scopes = [];
    renderScopes();
    document.getElementById('form-campagne').reset();
    await loadCampaigns();
}

document.getElementById('form-campagne').addEventListener('submit', submitCampaign);

loadDepots();
loadFamilles();
loadArticles();
loadCampaigns();
</script>
</body>
</html>
