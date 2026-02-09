<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listing Articles</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <!-- datatables -->
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
</head>
<body>
<div id="app">
    <?= Flight::menuBackOffice() ?>
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Articles</h3>
                        <p class="text-subtitle text-muted">Gestion du référentiel articles</p>
                    </div>
                    <div class="col-12 col-md-6 text-end">
                        <a href="<?= Flight::base() ?>/referentiel/article/saisie" class="btn btn-primary">+ Nouvel Article</a>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Liste des articles</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filtres -->
                        <div class="row mb-4 g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-5 small mb-2">Recherche</label>
                                <input id="filterSearch" class="form-control form-control-sm" placeholder="Code ou désignation...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-5 small mb-2">Famille</label>
                                <select id="filterFamille" class="form-select form-select-sm">
                                    <option value="">-- Toutes --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-5 small mb-2">Valorisation</label>
                                <select id="filterValorisation" class="form-select form-select-sm">
                                    <option value="">-- Toutes --</option>
                                    <option value="FIFO">FIFO</option>
                                    <option value="LIFO">LIFO</option>
                                    <option value="CUMP">CUMP</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-5 small mb-2">Statut</label>
                                <select id="filterActif" class="form-select form-select-sm">
                                    <option value="">-- Tous --</option>
                                    <option value="1">Actif</option>
                                    <option value="0">Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button id="resetFilters" class="btn btn-sm btn-light w-100">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tableau -->
                        <div class="table-responsive">
                            <div id="loadingSpinner" class="text-center py-5" style="display:none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                            <div id="emptyState" class="text-center py-5" style="display:none;">
                                <p class="text-muted">Aucun article trouvé</p>
                            </div>
                            <table class="table table-striped table-hover" id="articlesTable" style="display:none;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Désignation</th>
                                        <th>Famille</th>
                                        <th>Valorisation</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script>
const base = '<?= Flight::base() ?>';
async function fetchJSON(url, opts){ const r = await fetch(url, opts); return r.json(); }

let articlesData = [];
let dataTable = null;

function showLoading(show = true) {
    document.getElementById('loadingSpinner').style.display = show ? 'block' : 'none';
    document.getElementById('articlesTable').style.display = show ? 'none' : 'table';
    document.getElementById('emptyState').style.display = 'none';
}

function showEmptyState() {
    document.getElementById('loadingSpinner').style.display = 'none';
    document.getElementById('articlesTable').style.display = 'none';
    document.getElementById('emptyState').style.display = 'block';
}

function showTable() {
    document.getElementById('loadingSpinner').style.display = 'none';
    document.getElementById('articlesTable').style.display = 'table';
    document.getElementById('emptyState').style.display = 'none';
}

function renderTable(rows) {
    if (rows.length === 0) {
        showEmptyState();
        return;
    }
    
    const tb = document.querySelector('#articlesTable tbody');
    tb.innerHTML = '';
    rows.forEach(a => {
        const tr = document.createElement('tr');
        const statusBadge = a.actif == 1 ? '<span class="badge bg-success">Actif</span>' : '<span class="badge bg-secondary">Inactif</span>';
        tr.innerHTML = `
            <td><small class="text-muted">${a.id_article}</small></td>
            <td><strong>${a.code}</strong></td>
            <td>
                <a href="${base}/referentiel/article/detail?id=${a.id_article}" class="text-decoration-none text-primary fw-5">
                    ${a.designation}
                </a>
            </td>
            <td>${a.famille_nom ? '<small class="badge bg-light text-dark">' + a.famille_nom + '</small>' : '<small class="text-muted">-</small>'}</td>
            <td>${a.valorisation_libelle ? '<small class="badge bg-info text-dark">' + a.valorisation_libelle + '</small>' : '<small class="text-muted">-</small>'}</td>
            <td>${statusBadge}</td>
            <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="${base}/referentiel/article/saisie?id=${a.id_article}">Modifier</a>
                <button class="btn btn-sm btn-outline-danger ms-1" data-id="${a.id_article}">Supprimer</button>
            </td>`;
        tb.appendChild(tr);
    });
    
    showTable();
    
    // Initialize or refresh simple-datatables
    if (dataTable) { dataTable.destroy(); }
    dataTable = new simpleDatatables.DataTable("#articlesTable", {
        searchable: false,
        fixedHeight: false,
        perPage: 25
    });
}

function applyFilters() {
    const q = (document.getElementById('filterSearch').value || '').toLowerCase();
    const fam = (document.getElementById('filterFamille').value || '').toString();
    const val = (document.getElementById('filterValorisation').value || '').toUpperCase();
    const actif = document.getElementById('filterActif').value;
    
    const filtered = articlesData.filter(a => {
        // Search filter
        if (q) {
            const searchStr = (a.code + ' ' + a.designation).toLowerCase();
            if (searchStr.indexOf(q) === -1) return false;
        }
        
        // Family filter
        if (fam) {
            const aFamId = (a.id_article_famille || '').toString();
            if (aFamId !== fam) return false;
        }
        
        // Valorisation filter
        if (val) {
            const aMeth = (a.valorisation_libelle || '').toUpperCase();
            if (aMeth !== val) return false;
        }
        
        // Actif filter
        if (actif !== '') {
            if (String(a.actif) !== String(actif)) return false;
        }
        
        return true;
    });
    
    renderTable(filtered);
}

document.addEventListener('click', async (e) => {
    if (e.target.matches('button[data-id]')) {
        const btn = e.target;
        const id = btn.getAttribute('data-id');
        if (!confirm('Supprimer cet article ?')) return;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        
        try {
            const r = await fetchJSON(base + '/api/referentiel/articles/' + id, {method:'DELETE'});
            if (r.success) { 
                Toastify({text: r.message, duration: 2000, backgroundColor: 'green'}).showToast();
                articlesData = articlesData.filter(x => String(x.id_article) !== String(id));
                applyFilters();
            } else {
                Toastify({text: r.message || 'Erreur', duration: 3000, backgroundColor: 'red'}).showToast();
                btn.disabled = false;
                btn.innerHTML = 'Supprimer';
            }
        } catch (err) {
            Toastify({text: 'Erreur réseau', duration: 3000, backgroundColor: 'red'}).showToast();
            btn.disabled = false;
            btn.innerHTML = 'Supprimer';
        }
    }
});

document.getElementById('filterSearch').addEventListener('input', applyFilters);
document.getElementById('filterFamille').addEventListener('change', applyFilters);
document.getElementById('filterValorisation').addEventListener('change', applyFilters);
document.getElementById('filterActif').addEventListener('change', applyFilters);
document.getElementById('resetFilters').addEventListener('click', () => {
    document.getElementById('filterSearch').value = '';
    document.getElementById('filterFamille').value = '';
    document.getElementById('filterValorisation').value = '';
    document.getElementById('filterActif').value = '';
    applyFilters();
});

async function loadFamillesForFilter() {
    try {
        const r = await fetchJSON(base + '/api/referentiel/articles/familles/all');
        const sel = document.getElementById('filterFamille');
        sel.innerHTML = '<option value="">-- Toutes --</option>';
        
        if (r.success && r.data && Array.isArray(r.data)) {
            r.data.forEach(f => {
                const o = document.createElement('option');
                o.value = f.id_article_famille;
                o.textContent = f.nom;
                sel.appendChild(o);
            });
        }
    } catch (err) {
        console.error('Erreur chargement familles:', err);
    }
}

async function loadArticles() {
    showLoading(true);
    try {
        const r = await fetchJSON(base + '/api/referentiel/articles/all');
        if (r.success) {
            articlesData = r.data || [];
            await loadFamillesForFilter();
            renderTable(articlesData);
        } else {
            Toastify({text: r.message || 'Erreur lors du chargement', duration: 3000, backgroundColor: 'red'}).showToast();
            showEmptyState();
        }
    } catch (err) {
        console.error('Erreur:', err);
        Toastify({text: 'Erreur réseau', duration: 3000, backgroundColor: 'red'}).showToast();
        showEmptyState();
    }
}

// Load on page ready
loadArticles();
</script>
</body>
</html>
