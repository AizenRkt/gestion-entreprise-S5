<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mouvements de stock</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
</head>

<body>
<div id="app">
    <!-- Sidebar-->
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
                        <h3>Mouvements de stock</h3>
                        <p class="text-subtitle text-muted">Visualisez toutes les entrées, sorties et transferts</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Mouvements de stock</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Section filtres -->
            <section class="section">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title">Filtres</h4>
                        <button type="button" id="refreshMovBtn" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-clockwise"></i> Actualiser</button>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Article</label>
                                <select id="filter_article" class="form-select">
                                    <option value="">Tous</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Dépôt</label>
                                <select id="filter_depot" class="form-select">
                                    <option value="">Tous</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type de mouvement</label>
                                <select id="filter_type" class="form-select">
                                    <option value="">Tous</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sens</label>
                                <select id="filter_sens" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="1">Entrée</option>
                                    <option value="0">Sortie</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date début</label>
                                <input type="date" id="filter_date_debut" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date fin</label>
                                <input type="date" id="filter_date_fin" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary mt-2">Filtrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Section tableau mouvements -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Liste des mouvements</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="movementsTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Article</th>
                                        <th>Dépôt</th>
                                        <th>Type</th>
                                        <th>Sens</th>
                                        <th>Quantité</th>
                                        <th>Lot</th>
                                        <th>Référence</th>
                                        <th>Origine</th>
                                        <th>Statut</th>
                                        <th>Utilisateur</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="movementsBody"></tbody>
                            </table>
                        </div>
                        <!-- Modal: Lot details -->
                        <div class="modal fade" id="lotDetailsModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Détails de consommation par lot</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead><tr><th>Lot</th><th>Date entrée</th><th>Quantité</th><th>Coût unitaire</th><th>Valeur</th></tr></thead>
                                                <tbody id="modalLotDetailsBody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <a id="exportCsvLink" href="#" class="btn btn-outline-secondary"><i class="bi bi-filetype-csv"></i> Export CSV</a>
                                        <a id="exportPdfLink" href="#" class="btn btn-outline-secondary"><i class="bi bi-filetype-pdf"></i> Export PDF</a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    </div>
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
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/toastify.js"></script>

<!-- charts -->
<script src="<?= Flight::base() ?>/public/template/assets/extensions/dayjs/dayjs.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/apexcharts/apexcharts.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/ui-apexchart.js"></script>

<script src="<?= Flight::base() ?>/public/template/assets/extensions/chart.js/chart.umd.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/ui-chartjs.js"></script>

<script>
const base = '<?= Flight::base() ?>';

function toast(message, type = 'success') {
    Toastify({ text: message, duration: 3000, close: true, gravity: 'top', position: 'right', backgroundColor: type === 'success' ? '#198754' : '#dc3545' }).showToast();
}

async function fetchJSON(url) { const res = await fetch(url); return res.json(); }

async function loadFilters() {
    // articles
    const aSel = document.getElementById('filter_article');
    const aRes = await fetchJSON(base + '/api/referentiel/articles/active/list');
    if (aRes.success) {
        aRes.data.forEach(a => { const opt = document.createElement('option'); opt.value = a.id_article; opt.textContent = `${a.designation} (${a.code})`; aSel.appendChild(opt); });
    }
    // depots
    const dSel = document.getElementById('filter_depot');
    const dRes = await fetchJSON(base + '/api/stock/depots');
    if (dRes.success) {
        dRes.data.forEach(d => { const opt = document.createElement('option'); opt.value = d.id_depot; opt.textContent = `${d.nom} (${d.code})`; dSel.appendChild(opt); });
    }
    // types
    const tSel = document.getElementById('filter_type');
    const tRes = await fetchJSON(base + '/api/stock/types');
    if (tRes.success) {
        tRes.data.forEach(t => { const opt = document.createElement('option'); opt.value = t.id_type_mouvement_stock; opt.textContent = `${t.libelle} (${t.code})`; tSel.appendChild(opt); });
    }
}

let movementsData = [];

async function loadMovements() {
    const res = await fetch(base + '/api/stock/mouvements');
    const json = await res.json();
    if (json.success) { movementsData = json.data || []; renderMovements(movementsData); }
}

function renderMovements(rows) {
    const tbody = document.getElementById('movementsBody');
    tbody.innerHTML = '';
    rows.forEach(r => {
        const tr = document.createElement('tr');
        tr.setAttribute('data-id', r.id_mouvement_stock);
        const date = r.date_mouvement ? dayjs(r.date_mouvement).format('YYYY-MM-DD HH:mm') : '-';
        const sensBadge = r.sens == 1 ? '<span class="badge bg-success">Entrée</span>' : '<span class="badge bg-danger">Sortie</span>';
        const statutBadge = r.date_validation ? '<span class="badge bg-success">Validé</span>' : '<span class="badge bg-secondary">Brouillon</span>';
        const actionButtons = [];
        if (!r.date_validation) {
            actionButtons.push(`<a href="${base}/stock/mouvement/${r.id_mouvement_stock}/valider" class="btn btn-sm btn-primary"><i class=\"bi bi-check2-circle\"></i> Valider</a>`);
        }
        // Details modal
        actionButtons.push(`<button class="btn btn-sm btn-outline-secondary" data-action="details" data-id="${r.id_mouvement_stock}" onclick="event.stopPropagation()"><i class=\"bi bi-list-ul\"></i> Détails</button>`);
        const actionCell = actionButtons.join(' ');
        tr.innerHTML = `
            <td>${date}</td>
            <td>${r.article_designation ?? '-'}</td>
            <td>${r.depot_nom ?? '-'}</td>
            <td>${r.type_libelle ?? '-'}</td>
            <td>${sensBadge}</td>
            <td>${r.quantite}</td>
            <td>${r.id_lot ?? '-'}</td>
            <td>${r.id_reference ?? '-'}</td>
            <td>${r.table_reference ?? '-'}</td>
            <td>${statutBadge}</td>
            <td>${r.created_by ?? '-'}</td>
            <td>${actionCell}</td>
        `;
        tbody.appendChild(tr);

        // Navigate to detail page when clicking the row
        tr.addEventListener('click', () => {
            const id = tr.getAttribute('data-id');
            if (id) { window.location.href = `${base}/stock/mouvement/${id}`; }
        });
    });

    // Attach details handlers
    tbody.querySelectorAll('button[data-action="details"]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const id = e.currentTarget.getAttribute('data-id');
            const res = await fetchJSON(base + '/api/stock/mouvements/' + id + '/details');
            const tbodyModal = document.getElementById('modalLotDetailsBody');
            tbodyModal.innerHTML = '';
            if (res.success && res.data && res.data.length) {
                res.data.forEach(d => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${d.lot_numero ?? d.id_lot}</td><td>${d.date_entree ? dayjs(d.date_entree).format('YYYY-MM-DD') : '-'}</td><td>${d.quantite}</td><td>${d.cout_unitaire}</td><td>${d.valeur}</td>`;
                    tbodyModal.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="5" class="text-center text-muted">Aucun détail</td>';
                tbodyModal.appendChild(tr);
            }
            // set export link
            document.getElementById('exportCsvLink').setAttribute('href', base + '/api/stock/mouvements/' + id + '/details.csv');
            document.getElementById('exportPdfLink').setAttribute('href', base + '/api/stock/mouvements/' + id + '/details.pdf');
            // show modal
            const modal = new bootstrap.Modal(document.getElementById('lotDetailsModal'));
            modal.show();
        });
    });
}

document.getElementById('refreshMovBtn').addEventListener('click', async () => { await loadMovements(); toast('Liste actualisée'); });

document.getElementById('filterForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const a = document.getElementById('filter_article').value;
    const d = document.getElementById('filter_depot').value;
    const t = document.getElementById('filter_type').value;
    const s = document.getElementById('filter_sens').value;
    const d1 = document.getElementById('filter_date_debut').value;
    const d2 = document.getElementById('filter_date_fin').value;
    const filtered = movementsData.filter(m => {
        if (a && String(m.id_article) !== a) return false;
        if (d && String(m.id_depot) !== d) return false;
        if (t && String(m.id_type_mouvement_stock) !== t) return false;
        if (typeof m.sens !== 'undefined' && s !== '' && String(m.sens) !== s) return false;
        if (d1 && (!m.date_mouvement || dayjs(m.date_mouvement).isBefore(dayjs(d1)))) return false;
        if (d2 && (!m.date_mouvement || dayjs(m.date_mouvement).isAfter(dayjs(d2).endOf('day')))) return false;
        return true;
    });
    renderMovements(filtered);
});

(async function init() { await loadFilters(); await loadMovements(); })();
</script>

</body>
</html>
