<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration Stock</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
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
                        <h3>Administration Stock</h3>
                        <p class="text-subtitle text-muted">Paramètres par article et clôture mensuelle</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                                <li class="breadcrumb-item"><a href="#">Stock</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Administration</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0">Paramètres par article</h4>
                                <span class="badge bg-primary">Référentiel</span>
                            </div>
                            <div class="card-body">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="filterText" placeholder="Filtrer par code ou désignation">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="filterMethod">
                                            <option value="">Méthode: Tous</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="filterAlloc">
                                            <option value="">Allocation: Tous</option>
                                            <option value="fifo">FIFO</option>
                                            <option value="lifo">LIFO</option>
                                            <option value="fefo">FEFO</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" id="pageSize">
                                            <option value="10">10 / page</option>
                                            <option value="25">25 / page</option>
                                            <option value="50">50 / page</option>
                                            <option value="100">100 / page</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm" id="tblArticles">
                                        <thead>
                                            <tr>
                                                <th id="sortCode" style="cursor:pointer">Code <i class="bi bi-arrow-down-up"></i></th>
                                                <th id="sortDesignation" style="cursor:pointer">Designation <i class="bi bi-arrow-down-up"></i></th>
                                                <th>Méthode</th>
                                                <th>Allocation</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div id="articlesPagerInfo" class="small text-muted">0 résultats</div>
                                    <div class="btn-group" role="group" aria-label="Pagination">
                                        <button class="btn btn-outline-secondary btn-sm" id="btnPrev">Précédent</button>
                                        <button class="btn btn-outline-secondary btn-sm" id="btnNext">Suivant</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Clôture mensuelle</h5>
                                <span class="badge bg-warning text-dark">Périodes</span>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Ouvrir/Clôturer une période et snapshot des stocks.</p>
                                <div class="row g-2 mb-3">
                                    <div class="col-auto">
                                        <label class="form-label">Année</label>
                                        <input type="number" class="form-control" id="closureYear" value="<?= date('Y') ?>">
                                    </div>
                                    <div class="col-auto">
                                        <label class="form-label">Mois</label>
                                        <input type="number" class="form-control" id="closureMonth" min="1" max="12" value="<?= date('n') ?>">
                                    </div>
                                    <div class="col-auto align-self-end">
                                        <button class="btn btn-outline-primary" id="btnOpen"><i class="bi bi-unlock"></i> Ouvrir période</button>
                                        <button class="btn btn-primary" id="btnClose"><i class="bi bi-lock"></i> Clôturer période</button>
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4">
                                        <select class="form-select" id="closureFilterStatus">
                                            <option value="">Statut: Tous</option>
                                            <option value="OUVERT">Ouverte</option>
                                            <option value="CLOTURE">Clôturée</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" id="closureFilterYear">
                                            <option value="">Année: Toutes</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-select" id="closureFilterMonth">
                                            <option value="">Mois: Tous</option>
                                            <option value="1">Jan</option>
                                            <option value="2">Fév</option>
                                            <option value="3">Mar</option>
                                            <option value="4">Avr</option>
                                            <option value="5">Mai</option>
                                            <option value="6">Jun</option>
                                            <option value="7">Jul</option>
                                            <option value="8">Aoû</option>
                                            <option value="9">Sep</option>
                                            <option value="10">Oct</option>
                                            <option value="11">Nov</option>
                                            <option value="12">Déc</option>
                                        </select>
                                    </div>
                                </div>
                                <h6>Statut des périodes</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm" id="tblClosure">
                                        <thead>
                                            <tr>
                                                <th>Année</th>
                                                <th>Mois</th>
                                                <th>Statut</th>
                                                <th>Date clôture</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>

<script>
const base = '<?= Flight::base() ?>';
let methods = [];

function toast(message, type = 'success') {
    Toastify({ text: message, duration: 3000, close: true, gravity: 'top', position: 'right', backgroundColor: type === 'success' ? '#198754' : '#dc3545' }).showToast();
}

async function fetchMethods() {
    const res = await fetch(base + '/api/stock/admin/methods');
    const j = await res.json();
    if (j.success) methods = j.data; else methods = [];
}

function methodOptions(selectedId) {
    return methods.map(m => `<option value="${m.id_methode_valorisation}" ${selectedId==m.id_methode_valorisation?'selected':''}>${m.code}</option>`).join('');
}

function allocationOptions(selected) {
    const opts = ['fifo','lifo','fefo'];
    return opts.map(o => `<option value="${o}" ${selected===o?'selected':''}>${o.toUpperCase()}</option>`).join('');
}

let articlesCache = [];
let filteredArticles = [];
let currentPage = 1;
let pageSize = 10;
let sortKey = '';
let sortAsc = true;

function renderArticles(rows) {
    const tbody = document.querySelector('#tblArticles tbody');
    tbody.innerHTML = '';
    if (!rows || rows.length === 0) { tbody.innerHTML = '<tr><td colspan="5">Aucun article</td></tr>'; return; }
    rows.forEach(a => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><span class="badge bg-light text-dark">${a.code}</span></td>
            <td>${a.designation}</td>
            <td><select class="form-select form-select-sm" data-art="${a.id_article}" data-type="method">${methodOptions(a.id_methode_valorisation)}</select></td>
            <td><select class="form-select form-select-sm" data-art="${a.id_article}" data-type="alloc">${allocationOptions(a.allocation_defaut)}</select></td>
            <td><button class="btn btn-sm btn-success" data-art="${a.id_article}"><i class="bi bi-save"></i> Sauver</button></td>
        `;
        tbody.appendChild(tr);
    });
    tbody.querySelectorAll('button[data-art]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-art');
            const selMethod = tbody.querySelector(`select[data-art="${id}"][data-type="method"]`);
            const selAlloc = tbody.querySelector(`select[data-art="${id}"][data-type="alloc"]`);
            const payload = { id_methode_valorisation: parseInt(selMethod.value), allocation_defaut: selAlloc.value };
            btn.disabled = true; btn.classList.add('disabled');
            try {
                const res = await fetch(base + `/api/stock/admin/article/${id}/update-defaults`, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams(payload) });
                const j = await res.json();
                if (j.success) { toast('Paramètres mis à jour'); }
                else { toast(j.message || 'Erreur lors de la mise à jour', 'error'); }
            } catch (e) { toast('Erreur réseau', 'error'); }
            finally { btn.disabled = false; btn.classList.remove('disabled'); }
        });
    });
}

function renderArticlesPaged(rows) {
    const info = document.getElementById('articlesPagerInfo');
    if (!rows || rows.length === 0) { renderArticles([]); if (info) info.textContent = '0 résultats'; return; }
    // sort
    const sorted = [...rows];
    if (sortKey) {
        sorted.sort((x,y) => {
            const ax = (x[sortKey] ?? '').toString().toLowerCase();
            const ay = (y[sortKey] ?? '').toString().toLowerCase();
            if (ax < ay) return sortAsc ? -1 : 1;
            if (ax > ay) return sortAsc ? 1 : -1;
            return 0;
        });
    }
    // paginate
    const total = sorted.length;
    const start = (currentPage - 1) * pageSize;
    const end = Math.min(start + pageSize, total);
    const pageRows = sorted.slice(start, end);
    if (info) info.textContent = `${total} résultats · Page ${currentPage}`;
    renderArticles(pageRows);
}

async function loadArticles() {
    const res = await fetch(base + '/api/stock/admin/articles');
    const j = await res.json();
    if (!j.success) { renderArticles([]); toast('Erreur de chargement des articles', 'error'); return; }
    articlesCache = j.data || [];
    applyArticleFilters();
}

function applyArticleFilters() {
    const q = (document.getElementById('filterText').value || '').toLowerCase();
    const methodId = document.getElementById('filterMethod').value;
    const alloc = document.getElementById('filterAlloc').value;
    filteredArticles = articlesCache.filter(a => {
        const matchText = (a.code||'').toLowerCase().includes(q) || (a.designation||'').toLowerCase().includes(q);
        const matchMethod = !methodId || String(a.id_methode_valorisation) === String(methodId);
        const matchAlloc = !alloc || (a.allocation_defaut||'').toLowerCase() === alloc.toLowerCase();
        return matchText && matchMethod && matchAlloc;
    });
    currentPage = 1;
    renderArticlesPaged(filteredArticles);
}

let debounceTimer;
document.getElementById('filterText').addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyArticleFilters, 200);
});
document.getElementById('filterAlloc').addEventListener('change', applyArticleFilters);
document.getElementById('filterMethod').addEventListener('change', applyArticleFilters);
document.getElementById('pageSize').addEventListener('change', (e) => {
    pageSize = parseInt(e.target.value || '10');
    currentPage = 1;
    renderArticlesPaged(filteredArticles);
});
document.getElementById('btnPrev').addEventListener('click', () => {
    if (currentPage > 1) { currentPage--; renderArticlesPaged(filteredArticles); }
});
document.getElementById('btnNext').addEventListener('click', () => {
    const totalPages = Math.ceil((filteredArticles.length || 0) / pageSize);
    if (currentPage < totalPages) { currentPage++; renderArticlesPaged(filteredArticles); }
});
document.getElementById('sortCode').addEventListener('click', () => { sortKey = 'code'; sortAsc = !sortAsc; renderArticlesPaged(filteredArticles); });
document.getElementById('sortDesignation').addEventListener('click', () => { sortKey = 'designation'; sortAsc = !sortAsc; renderArticlesPaged(filteredArticles); });

async function loadClosureStatus() {
    const res = await fetch(base + '/api/stock/closure/status');
    const j = await res.json();
    const tbody = document.querySelector('#tblClosure tbody');
    tbody.innerHTML = '';
    if (!j.success) { tbody.innerHTML = '<tr><td colspan="4">Erreur</td></tr>'; return; }
    window.closureCache = j.data || [];
    const years = [...new Set(window.closureCache.map(r => r.annee))].sort((a,b)=>b-a);
    const yearSel = document.getElementById('closureFilterYear');
    yearSel.innerHTML = '<option value="">Année: Toutes</option>' + years.map(y => `<option value="${y}">${y}</option>`).join('');
    applyClosureFilters();
}

function applyClosureFilters() {
    const status = document.getElementById('closureFilterStatus').value;
    const year = document.getElementById('closureFilterYear').value;
    const month = document.getElementById('closureFilterMonth').value;
    const rows = (window.closureCache || []).filter(p => {
        const s = !status || p.statut === status;
        const y = !year || String(p.annee) === String(year);
        const m = !month || String(p.mois) === String(month);
        return s && y && m;
    });
    const tbody = document.querySelector('#tblClosure tbody');
    tbody.innerHTML = '';
    rows.forEach(p => {
        const statBadge = p.statut === 'CLOTURE' ? '<span class="badge bg-primary">Clôturée</span>' : '<span class="badge bg-light text-dark">Ouverte</span>';
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${p.annee}</td><td>${p.mois}</td><td>${statBadge}</td><td>${p.date_cloture || ''}</td>`;
        tbody.appendChild(tr);
    });
}

document.getElementById('closureFilterStatus').addEventListener('change', applyClosureFilters);
document.getElementById('closureFilterYear').addEventListener('change', applyClosureFilters);
document.getElementById('closureFilterMonth').addEventListener('change', applyClosureFilters);

document.getElementById('btnOpen').addEventListener('click', async () => {
    const year = parseInt(document.getElementById('closureYear').value);
    const month = parseInt(document.getElementById('closureMonth').value);
    const res = await fetch(base + '/api/stock/closure/open', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ annee: year, mois: month }) });
    const j = await res.json();
    if (j.success) toast(j.message || 'Période ouverte'); else toast(j.message || 'Erreur ouverture', 'error');
    loadClosureStatus();
});

document.getElementById('btnClose').addEventListener('click', async () => {
    const year = parseInt(document.getElementById('closureYear').value);
    const month = parseInt(document.getElementById('closureMonth').value);
    const res = await fetch(base + '/api/stock/closure/close', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ annee: year, mois: month }) });
    const j = await res.json();
    if (j.success) toast(j.message || 'Période clôturée'); else toast(j.message || 'Erreur clôture', 'error');
    loadClosureStatus();
});

(async function init() {
    await fetchMethods();
    // populate method filter options
    const methodSel = document.getElementById('filterMethod');
    methods.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.id_methode_valorisation;
        opt.textContent = `Méthode: ${m.code}`;
        methodSel.appendChild(opt);
    });
    await loadArticles();
    await loadClosureStatus();
})();
</script>
</body>
</html>
