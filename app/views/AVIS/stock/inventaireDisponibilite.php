<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilité et Valeur des Articles</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <style>
        .table-responsive { max-height: 60vh; overflow-y: auto; }
        .stats-card { transition: all 0.3s ease; }
        .stats-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .table-sticky thead th { position: sticky; top: 0; background: var(--bs-body-bg); z-index: 10; }
        .positive { color: #198754; }
        .negative { color: #dc3545; }
        .highlight-row { background-color: rgba(13, 110, 253, 0.08) !important; }
        .filter-badge { font-size: 0.75rem; }
    </style>
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
            <div class="page-heading">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h3>Disponibilité et Valeur des Articles</h3>
                        <p class="text-muted mb-0">Consultez le stock, les réservations et la valeur des articles par dépôt.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" id="btn-export-csv">
                            <i class="bi bi-download"></i> Exporter CSV
                        </button>
                    </div>
                </div>
            </div>

            <div class="page-content">
                <section class="row">
                    <!-- Filtres -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header py-2">
                                <h5 class="card-title mb-0">Filtres</h5>
                            </div>
                            <div class="card-body py-3">
                                <form id="filters" class="row g-2 align-items-end">
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label small mb-1">Article</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="flt-article-search" placeholder="Recherche article...">
                                            <button class="btn btn-outline-secondary" type="button" id="flt-article-search-btn">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                        <select class="form-select form-select-sm mt-1" id="flt-article">
                                            <option value="">Tous les articles</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-6">
                                        <label class="form-label small mb-1">Dépôt</label>
                                        <select class="form-select form-select-sm" id="flt-depot">
                                            <option value="">Tous les dépôts</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-6">
                                        <label class="form-label small mb-1">Date début</label>
                                        <input type="date" class="form-control form-control-sm" id="flt-date-debut">
                                    </div>
                                    <div class="col-lg-2 col-md-6">
                                        <label class="form-label small mb-1">Date fin</label>
                                        <input type="date" class="form-control form-control-sm" id="flt-date-fin">
                                    </div>
                                    <div class="col-lg-1 col-md-6">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="flt-stock-positif" checked>
                                            <label class="form-check-label small" for="flt-stock-positif">Stock > 0</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6">
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-primary btn-sm flex-grow-1" id="btn-apply">
                                                <i class="bi bi-filter"></i> Appliquer
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-reset">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques globales -->
                    <div class="col-12">
                        <div class="row g-3" id="stats-row">
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="card stats-card h-100">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-lg bg-light-primary me-3">
                                                <span class="avatar-content"><i class="bi bi-box-seam fs-4 text-primary"></i></span>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-0 small">Quantité totale</h6>
                                                <h4 class="mb-0" id="stat-quantite">-</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="card stats-card h-100">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-lg bg-light-success me-3">
                                                <span class="avatar-content"><i class="bi bi-currency-dollar fs-4 text-success"></i></span>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-0 small">Valeur totale</h6>
                                                <h4 class="mb-0" id="stat-valeur">-</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="card stats-card h-100">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-lg bg-light-warning me-3">
                                                <span class="avatar-content"><i class="bi bi-bookmark-check fs-4 text-warning"></i></span>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-0 small">Quantité réservée</h6>
                                                <h4 class="mb-0" id="stat-reservee">-</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6">
                                <div class="card stats-card h-100">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-lg bg-light-info me-3">
                                                <span class="avatar-content"><i class="bi bi-check2-circle fs-4 text-info"></i></span>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-0 small">Quantité disponible</h6>
                                                <h4 class="mb-0" id="stat-disponible">-</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau principal -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Stock par article</h5>
                                <span class="badge bg-secondary" id="count-label">0 élément</span>
                            </div>
                            <div class="card-body py-2">
                                <div class="table-responsive table-sticky">
                                    <table class="table table-hover table-sm align-middle mb-0" id="availability-table">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Désignation</th>
                                                <th>Famille</th>
                                                <th>Dépôt</th>
                                                <th class="text-end">En stock</th>
                                                <th class="text-end">Réservé</th>
                                                <th class="text-end">Disponible</th>
                                                <th class="text-end">Coût moyen</th>
                                                <th class="text-end">Valeur stock</th>
                                                <th>Dernier mvt</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="availability-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historique des mouvements (affiché quand un article est sélectionné) -->
                    <div class="col-12 d-none" id="history-section">
                        <div class="card">
                            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-0">Historique des mouvements</h5>
                                    <small class="text-muted" id="history-article-label"></small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success" id="history-entrees">Entrées: -</span>
                                    <span class="badge bg-danger" id="history-sorties">Sorties: -</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-close-history">
                                        <i class="bi bi-x"></i> Fermer
                                    </button>
                                </div>
                            </div>
                            <div class="card-body py-2">
                                <div class="table-responsive" style="max-height: 300px;">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Sens</th>
                                                <th>Dépôt</th>
                                                <th class="text-end">Quantité</th>
                                                <th class="text-end">Coût unit.</th>
                                                <th class="text-end">Valeur</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody id="history-body"></tbody>
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
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/dayjs/dayjs.min.js"></script>

<script>
const base = '<?= Flight::base() ?>';
let currentData = [];

const dom = {
    filters: {
        articleSearch: document.getElementById('flt-article-search'),
        articleSearchBtn: document.getElementById('flt-article-search-btn'),
        article: document.getElementById('flt-article'),
        depot: document.getElementById('flt-depot'),
        dateDebut: document.getElementById('flt-date-debut'),
        dateFin: document.getElementById('flt-date-fin'),
        stockPositif: document.getElementById('flt-stock-positif')
    },
    applyBtn: document.getElementById('btn-apply'),
    resetBtn: document.getElementById('btn-reset'),
    exportBtn: document.getElementById('btn-export-csv'),
    countLabel: document.getElementById('count-label'),
    tableBody: document.getElementById('availability-body'),
    stats: {
        quantite: document.getElementById('stat-quantite'),
        valeur: document.getElementById('stat-valeur'),
        reservee: document.getElementById('stat-reservee'),
        disponible: document.getElementById('stat-disponible')
    },
    history: {
        section: document.getElementById('history-section'),
        label: document.getElementById('history-article-label'),
        entrees: document.getElementById('history-entrees'),
        sorties: document.getElementById('history-sorties'),
        body: document.getElementById('history-body'),
        closeBtn: document.getElementById('btn-close-history')
    }
};

function toast(message, type = 'info') {
    const color = type === 'error' ? '#dc3545' : (type === 'success' ? '#198754' : '#0d6efd');
    Toastify({ text: message, gravity: 'top', position: 'right', style: { background: color }, duration: 3000 }).showToast();
}

function formatNumber(value, decimals = 2) {
    if (value === null || value === undefined || Number.isNaN(Number(value))) return '-';
    return Number(value).toLocaleString('fr-FR', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
}

function formatCurrency(value) {
    if (value === null || value === undefined || Number.isNaN(Number(value))) return '-';
    return Number(value).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' Ar';
}

function formatDate(value) {
    if (!value) return '-';
    try {
        if (window.dayjs) return dayjs(value).format('DD/MM/YYYY HH:mm');
    } catch (e) {}
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? value : date.toLocaleDateString('fr-FR');
}

function formatDateShort(value) {
    if (!value) return '-';
    try {
        if (window.dayjs) return dayjs(value).format('DD/MM/YY');
    } catch (e) {}
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? value : date.toLocaleDateString('fr-FR');
}

async function getJson(url, params) {
    const finalUrl = params ? `${url}?${new URLSearchParams(params).toString()}` : url;
    const res = await fetch(finalUrl, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
}

function setTableLoading() {
    dom.tableBody.innerHTML = '<tr><td colspan="11" class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm me-2"></div>Chargement...</td></tr>';
}

function updateStats(totaux) {
    dom.stats.quantite.textContent = formatNumber(totaux.quantite || 0, 0);
    dom.stats.valeur.textContent = formatCurrency(totaux.valeur || 0);
    dom.stats.reservee.textContent = formatNumber(totaux.reservee || 0, 0);
    dom.stats.disponible.textContent = formatNumber(totaux.disponible || 0, 0);
}

function renderAvailability(rows) {
    currentData = rows;
    
    if (!Array.isArray(rows) || rows.length === 0) {
        dom.tableBody.innerHTML = '<tr><td colspan="11" class="text-center text-muted py-4">Aucun article trouvé</td></tr>';
        dom.countLabel.textContent = '0 élément';
        return;
    }
    
    dom.countLabel.textContent = `${rows.length} élément${rows.length > 1 ? 's' : ''}`;
    
    dom.tableBody.innerHTML = rows.map(row => {
        const quantite = parseFloat(row.quantite || 0);
        const reservee = parseFloat(row.quantite_reservee || 0);
        const disponible = parseFloat(row.quantite_disponible || 0);
        const valeur = parseFloat(row.valeur_stock || 0);
        const cout = parseFloat(row.cout_moyen || 0);
        
        const dispoClass = disponible < 0 ? 'negative' : (disponible > 0 ? 'positive' : '');
        
        return `<tr data-article="${row.id_article}" data-depot="${row.id_depot}">
            <td><code class="text-primary">${row.article_code || '-'}</code></td>
            <td>${row.article_designation || '-'}</td>
            <td><span class="badge bg-light text-dark">${row.famille || '-'}</span></td>
            <td>${row.depot_nom || '-'}</td>
            <td class="text-end fw-semibold">${formatNumber(quantite, 2)}</td>
            <td class="text-end ${reservee > 0 ? 'text-warning' : ''}">${formatNumber(reservee, 2)}</td>
            <td class="text-end fw-semibold ${dispoClass}">${formatNumber(disponible, 2)}</td>
            <td class="text-end">${formatCurrency(cout)}</td>
            <td class="text-end fw-semibold">${formatCurrency(valeur)}</td>
            <td class="small">${formatDateShort(row.dernier_mouvement)}</td>

        </tr>`;
    }).join('');
}

async function applyFilters() {
    setTableLoading();
    try {
        const params = {};
        
        if (dom.filters.article.value) params.article = dom.filters.article.value;
        if (dom.filters.depot.value) params.depot = dom.filters.depot.value;
        if (dom.filters.stockPositif.checked) params.stock_positif = '1';
        
        const res = await getJson(`${base}/api/stock/disponibilite`, params);
        
        if (!res.success) throw new Error(res.message || 'Erreur lors du chargement');
        
        renderAvailability(res.data || []);
        updateStats(res.totaux || {});
        
        // Masquer l'historique si affiché
        dom.history.section.classList.add('d-none');
        
    } catch (e) {
        dom.tableBody.innerHTML = `<tr><td colspan="11" class="text-center text-danger py-4">${e.message}</td></tr>`;
        dom.countLabel.textContent = '';
        toast(e.message, 'error');
    }
}

async function loadHistory(articleId, depotId, articleLabel) {
    try {
        const params = { article: articleId };
        if (depotId) params.depot = depotId;
        if (dom.filters.dateDebut.value) params.date_debut = dom.filters.dateDebut.value;
        if (dom.filters.dateFin.value) params.date_fin = dom.filters.dateFin.value;
        
        dom.history.body.innerHTML = '<tr><td colspan="8" class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></td></tr>';
        dom.history.section.classList.remove('d-none');
        dom.history.label.textContent = articleLabel;
        
        const res = await getJson(`${base}/api/stock/disponibilite/historique`, params);
        
        if (!res.success) throw new Error(res.message || 'Erreur');
        
        const rows = res.data || [];
        const totaux = res.totaux || {};
        
        dom.history.entrees.textContent = `Entrées: ${formatNumber(totaux.entrees || 0, 2)}`;
        dom.history.sorties.textContent = `Sorties: ${formatNumber(totaux.sorties || 0, 2)}`;
        
        if (rows.length === 0) {
            dom.history.body.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-3">Aucun mouvement sur cette période</td></tr>';
            return;
        }
        
        dom.history.body.innerHTML = rows.map(row => {
            const sens = parseInt(row.sens);
            const sensLabel = sens === 1 ? '<span class="badge bg-success">Entrée</span>' : '<span class="badge bg-danger">Sortie</span>';
            const statutBadge = row.statut === 'VALIDE' 
                ? '<span class="badge bg-success">Validé</span>' 
                : '<span class="badge bg-warning">En attente</span>';
            
            return `<tr>
                <td class="small">${formatDate(row.date_mouvement)}</td>
                <td>${row.type_libelle || row.type_code || '-'}</td>
                <td>${sensLabel}</td>
                <td>${row.depot_nom || '-'}</td>
                <td class="text-end">${formatNumber(row.quantite, 2)}</td>
                <td class="text-end">${formatCurrency(row.cout_unitaire)}</td>
                <td class="text-end fw-semibold">${formatCurrency(row.valeur)}</td>
                <td>${statutBadge}</td>
            </tr>`;
        }).join('');
        
        // Scroll to history section
        dom.history.section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
    } catch (e) {
        dom.history.body.innerHTML = `<tr><td colspan="8" class="text-center text-danger">${e.message}</td></tr>`;
        toast(e.message, 'error');
    }
}

async function populateDepots() {
    try {
        const res = await getJson(`${base}/api/stock/depots`);
        if (!res.success) return;
        
        dom.filters.depot.innerHTML = '<option value="">Tous les dépôts</option>';
        (res.data || []).forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id_depot;
            opt.textContent = d.nom || d.code || d.id_depot;
            dom.filters.depot.appendChild(opt);
        });
    } catch (e) {
        console.error('Erreur chargement dépôts:', e);
    }
}

async function searchArticles(query = '') {
    try {
        const params = query ? { q: query } : undefined;
        const res = await getJson(`${base}/api/stock/articles`, params);
        
        dom.filters.article.innerHTML = '<option value="">Tous les articles</option>';
        
        if (!res.success) return;
        
        (res.data || []).forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id_article;
            const label = a.code ? `${a.code} - ${a.designation || a.id_article}` : (a.designation || a.id_article);
            opt.textContent = label;
            dom.filters.article.appendChild(opt);
        });
    } catch (e) {
        console.error('Erreur recherche articles:', e);
    }
}

function resetFilters() {
    dom.filters.articleSearch.value = '';
    dom.filters.article.value = '';
    dom.filters.depot.value = '';
    dom.filters.dateDebut.value = '';
    dom.filters.dateFin.value = '';
    dom.filters.stockPositif.checked = true;
    searchArticles();
    applyFilters();
}

function exportCSV() {
    if (!currentData.length) {
        toast('Aucune donnée à exporter', 'error');
        return;
    }
    
    const headers = ['Code', 'Désignation', 'Famille', 'Dépôt', 'Quantité', 'Réservée', 'Disponible', 'Coût moyen', 'Valeur stock', 'Dernier mouvement'];
    const rows = currentData.map(row => [
        row.article_code || '',
        row.article_designation || '',
        row.famille || '',
        row.depot_nom || '',
        row.quantite || 0,
        row.quantite_reservee || 0,
        row.quantite_disponible || 0,
        row.cout_moyen || 0,
        row.valeur_stock || 0,
        row.dernier_mouvement || ''
    ]);
    
    const csvContent = [headers, ...rows]
        .map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(';'))
        .join('\n');
    
    const BOM = '\uFEFF';
    const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `disponibilite_stock_${new Date().toISOString().slice(0,10)}.csv`;
    link.click();
    URL.revokeObjectURL(url);
    
    toast('Export CSV téléchargé', 'success');
}

function bindEvents() {
    dom.applyBtn.addEventListener('click', applyFilters);
    dom.resetBtn.addEventListener('click', resetFilters);
    dom.exportBtn.addEventListener('click', exportCSV);
    
    dom.filters.articleSearchBtn.addEventListener('click', () => searchArticles(dom.filters.articleSearch.value));
    dom.filters.articleSearch.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchArticles(dom.filters.articleSearch.value);
        }
    });
    
    // Clic sur une ligne pour voir l'historique
    dom.tableBody.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-view-history');
        if (!btn) return;
        
        const row = btn.closest('tr');
        if (!row) return;
        
        const articleId = row.dataset.article;
        const depotId = row.dataset.depot;
        const articleLabel = row.querySelector('td:nth-child(2)')?.textContent || `Article #${articleId}`;
        
        // Highlight la ligne
        dom.tableBody.querySelectorAll('tr').forEach(tr => tr.classList.remove('highlight-row'));
        row.classList.add('highlight-row');
        
        loadHistory(articleId, depotId, articleLabel);
    });
    
    // Fermer l'historique
    dom.history.closeBtn.addEventListener('click', () => {
        dom.history.section.classList.add('d-none');
        dom.tableBody.querySelectorAll('tr').forEach(tr => tr.classList.remove('highlight-row'));
    });
    
    // Appliquer les filtres sur Enter
    document.getElementById('filters').addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
            e.preventDefault();
            applyFilters();
        }
    });
}

(async function init() {
    try {
        bindEvents();
        
        // Initialiser les dates par défaut (30 derniers jours)
        const today = new Date();
        const thirtyDaysAgo = new Date(today);
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        dom.filters.dateFin.value = today.toISOString().slice(0, 10);
        dom.filters.dateDebut.value = thirtyDaysAgo.toISOString().slice(0, 10);
        
        await Promise.all([populateDepots(), searchArticles()]);
        await applyFilters();
        
    } catch (e) {
        toast(e.message || 'Erreur d\'initialisation', 'error');
    }
})();
</script>
</body>
</html>
