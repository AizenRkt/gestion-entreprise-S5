<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles Fournisseurs</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
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
                    <div class="col-12 col-md-8 order-md-1 order-last">
                        <h3>Articles Fournisseurs</h3>
                        <p class="text-subtitle text-muted">Tarifs et délais de livraison par fournisseur</p>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tous les articles fournisseurs</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filtres -->
                        <div class="row mb-4 g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-5 small mb-2">Recherche</label>
                                <input id="filterSearch" class="form-control form-control-sm" placeholder="Fournisseur ou article...">
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
                                <p class="text-muted">Aucun article fournisseur trouvé</p>
                            </div>
                            <table class="table table-striped table-hover" id="articlesTable" style="display:none;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fournisseur</th>
                                        <th>Article</th>
                                        <th>Code</th>
                                        <th>Prix d'achat</th>
                                        <th>Délai livraison</th>
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
        tr.innerHTML = `
            <td><small class="text-muted">${a.id_fournisseur_article}</small></td>
            <td>
                <a href="${base}/referentiel/fournisseur/saisie?id=${a.id_fournisseur}" class="text-decoration-none text-primary fw-5">
                    ${a.fournisseur_nom}
                </a>
            </td>
            <td>${a.article_designation}</td>
            <td><small class="text-muted">${a.article_code}</small></td>
            <td>${a.prix_achat ? Number(a.prix_achat).toFixed(2) + ' Ar' : '-'}</td>
            <td>${a.delai_livraison ? a.delai_livraison + ' j' : '-'}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary edit-btn" data-id="${a.id_fournisseur_article}">Modifier</button>
                <button class="btn btn-sm btn-outline-danger ms-1 delete-btn" data-id="${a.id_fournisseur_article}">Supprimer</button>
            </td>`;
        tb.appendChild(tr);
    });
    
    showTable();
    
    if (dataTable) { dataTable.destroy(); }
    dataTable = new simpleDatatables.DataTable("#articlesTable", {
        searchable: false,
        fixedHeight: false,
        perPage: 25
    });
}

function applyFilters() {
    const q = (document.getElementById('filterSearch').value || '').toLowerCase();
    
    const filtered = articlesData.filter(a => {
        if (q) {
            const searchStr = (a.fournisseur_nom + ' ' + a.article_designation + ' ' + a.article_code).toLowerCase();
            if (searchStr.indexOf(q) === -1) return false;
        }
        return true;
    });
    
    renderTable(filtered);
}

document.addEventListener('click', async (e) => {
    if (e.target.matches('.delete-btn')) {
        const btn = e.target;
        const id = btn.getAttribute('data-id');
        if (!confirm('Supprimer cette association ?')) return;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        
        try {
            const r = await fetchJSON(base + '/api/referentiel/fournisseurs/articles/' + id, {method:'DELETE'});
            if (r.success) { 
                Toastify({text: r.message, duration: 2000, backgroundColor: 'green'}).showToast();
                articlesData = articlesData.filter(x => String(x.id_fournisseur_article) !== String(id));
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
document.getElementById('resetFilters').addEventListener('click', () => {
    document.getElementById('filterSearch').value = '';
    applyFilters();
});

async function loadArticles() {
    showLoading(true);
    try {
        const r = await fetchJSON(base + '/api/referentiel/fournisseurs/articles/all');
        if (r.success) {
            articlesData = r.data || [];
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

loadArticles();
</script>
</body>
</html>
