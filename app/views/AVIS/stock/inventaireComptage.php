<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie comptage inventaire</title>
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
    <?php Flight::menuBackOffice() ?>
    <div id="main" class="layout-navbar">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
        <div id="main-content">
            <div class="page-heading d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3>Saisie des comptages</h3>
                    <p class="text-muted mb-0">Enregistrer les quantités comptées par campagne et dépôt.</p>
                </div>
            </div>
            <div class="page-content">
                <section class="row">
                    <div class="col-xl-5">
                        <div class="card h-100">
                            <div class="card-header"><h5 class="card-title mb-0">Nouveau comptage</h5></div>
                            <div class="card-body">
                                <form id="form-comptage" class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Campagne</label>
                                        <select class="form-select" id="count-campagne" required></select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Dépôt</label>
                                        <select class="form-select" id="count-depot" required></select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Article</label>
                                        <select class="form-select" id="count-article" required></select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Lot (optionnel)</label>
                                        <select class="form-select" id="count-lot">
                                            <option value="">Sans lot</option>
                                        </select>
                                        <div class="small-label" id="lot-helper">Sélectionner un article et un dépôt pour charger les lots.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Quantité théorique</label>
                                        <input type="text" class="form-control" id="count-qtheo" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Quantité comptée</label>
                                        <input type="number" step="0.001" class="form-control" id="count-qte" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Commentaire</label>
                                        <textarea class="form-control" id="count-comment" rows="2"></textarea>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-success">Enregistrer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-7">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-0">Comptages saisis</h5>
                                    <p class="small-label mb-0">Filtrés par campagne (et dépôt si sélectionné).</p>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary" type="button" id="btn-refresh">Rafraîchir</button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height:70vh;">
                                    <table class="table table-hover align-middle mb-0" id="counts-table">
                                        <thead>
                                            <tr>
                                                <th>Article</th>
                                                <th>Dépôt</th>
                                                <th class="text-end">Théorique</th>
                                                <th class="text-end">Compté</th>
                                                <th class="text-end">Écart</th>
                                                <th>Lot</th>
                                                <th>Date</th>
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
const campagneSelect = document.getElementById('count-campagne');
const depotSelect = document.getElementById('count-depot');
const articleSelect = document.getElementById('count-article');
const lotSelect = document.getElementById('count-lot');
const qTheoInput = document.getElementById('count-qtheo');
const countsBody = document.querySelector('#counts-table tbody');

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
    depotSelect.innerHTML = '<option value="">Choisir un dépôt</option>';
    json.data.forEach(d => {
        const opt = document.createElement('option');
        opt.value = d.id_depot;
        opt.textContent = `${d.code || d.id_depot} • ${d.nom}`;
        depotSelect.appendChild(opt);
    });
}

async function loadArticles() {
    const res = await fetch('<?= Flight::base() ?>/api/referentiel/articles/all');
    const json = await res.json();
    if (!json.success) return;
    articleSelect.innerHTML = '<option value="">Choisir un article</option>';
    json.data.forEach(a => {
        const opt = document.createElement('option');
        opt.value = a.id_article;
        opt.textContent = `${a.code || ''} • ${a.designation || a.libelle || 'Article'}`.trim();
        articleSelect.appendChild(opt);
    });
}

async function loadLots() {
    const article = articleSelect.value;
    const depot = depotSelect.value;
    lotSelect.innerHTML = '<option value="">Sans lot</option>';
    if (!article || !depot) { return; }
    const res = await fetch(`<?= Flight::base() ?>/api/stock/lots?article=${article}&depot=${depot}`);
    const json = await res.json();
    if (!json.success) { toast(json.message || 'Erreur lots', false); return; }
    json.data.forEach(l => {
        const opt = document.createElement('option');
        opt.value = l.id_lot;
        opt.textContent = `${l.lot_numero} (qty ${l.quantite_initiale ?? ''})`;
        lotSelect.appendChild(opt);
    });
}

async function loadTheoretical() {
    const article = articleSelect.value;
    const depot = depotSelect.value;
    if (!article || !depot) { qTheoInput.value = ''; return; }
    const res = await fetch(`<?= Flight::base() ?>/api/stock/courant?article=${article}&depot=${depot}`);
    const json = await res.json();
    if (!json.success) { qTheoInput.value = ''; return; }
    qTheoInput.value = json.data?.quantite ?? '';
}

async function loadCounts() {
    const campagne = campagneSelect.value;
    if (!campagne) { countsBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Sélectionnez une campagne</td></tr>'; return; }
    const depot = depotSelect.value;
    const url = `<?= Flight::base() ?>/api/stock/inventaire/comptages?campagne=${campagne}${depot ? `&depot=${depot}` : ''}`;
    countsBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Chargement...</td></tr>';
    const res = await fetch(url);
    const json = await res.json();
    if (!json.success) { countsBody.innerHTML = ''; toast(json.message || 'Erreur chargement', false); return; }
    countsBody.innerHTML = '';
    if (!json.data.length) {
        countsBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Aucun comptage</td></tr>';
        return;
    }
    json.data.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${r.article_code || ''} • ${r.article_nom || ''}</td>
            <td>${r.depot_nom || ''}</td>
            <td class="text-end">${r.quantite_theorique ?? 0}</td>
            <td class="text-end">${r.quantite_comptee ?? 0}</td>
            <td class="text-end">${r.ecart ?? 0}</td>
            <td>${r.lot_numero || ''}</td>
            <td>${r.date_comptage ? r.date_comptage.substring(0, 16) : ''}</td>`;
        countsBody.appendChild(tr);
    });
}

articleSelect.addEventListener('change', () => { loadLots(); loadTheoretical(); });
depotSelect.addEventListener('change', () => { loadLots(); loadTheoretical(); });
campagneSelect.addEventListener('change', loadCounts);

document.getElementById('btn-refresh').addEventListener('click', loadCounts);

document.getElementById('form-comptage').addEventListener('submit', async (e) => {
    e.preventDefault();
    const campagne = campagneSelect.value;
    const depot = depotSelect.value;
    const article = articleSelect.value;
    const quantite = document.getElementById('count-qte').value;
    if (!campagne || !depot || !article || quantite === '') {
        toast('Campagne, dépôt, article et quantité sont requis', false);
        return;
    }
    const payload = {
        id_inventaire_campagne: parseInt(campagne, 10),
        id_depot: parseInt(depot, 10),
        id_article: parseInt(article, 10),
        id_lot: lotSelect.value ? parseInt(lotSelect.value, 10) : null,
        quantite_comptee: parseFloat(quantite),
        commentaire: document.getElementById('count-comment').value.trim()
    };
    const res = await fetch('<?= Flight::base() ?>/api/stock/inventaire/comptages', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    const json = await res.json();
    if (!json.success) { toast(json.message || 'Erreur enregistrement', false); return; }
    toast('Comptage enregistré');
    document.getElementById('form-comptage').reset();
    lotSelect.innerHTML = '<option value="">Sans lot</option>';
    qTheoInput.value = '';
    await loadCounts();
});

loadCampaigns();
loadDepots();
loadArticles();
</script>
</body>
</html>
