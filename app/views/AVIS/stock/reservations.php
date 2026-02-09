<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservations de stock</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <style>
        .table-responsive { max-height: 55vh; }
        .form-inline .form-control { min-width: 120px; }
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
            <div class="page-heading">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3>Réservations</h3>
                        <p class="text-muted">Créer, annuler et lister les réservations de stock.</p>
                    </div>
                </div>
            </div>

            <div class="page-content">
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Filtres</h5>
                            </div>
                            <div class="card-body">
                                <form id="filters" class="row g-2 align-items-end">
                                    <div class="col-md-2">
                                        <label class="form-label">Article (ID)</label>
                                        <input type="number" class="form-control" id="flt-article" placeholder="ex: 101">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Dépôt (ID)</label>
                                        <input type="number" class="form-control" id="flt-depot" placeholder="ex: 1">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Référence</label>
                                        <input type="text" class="form-control" id="flt-reference" placeholder="ex: BL#123">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" id="btn-apply">Appliquer</button>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <button type="button" class="btn btn-outline-secondary" id="btn-reset">Réinitialiser</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-12">
                        <div class="card">
                            <div class="card-header"><h5 class="card-title">Nouvelle réservation</h5></div>
                            <div class="card-body">
                                <form id="form-reserve" class="row g-3">
                                    <div class="col-lg-6 col-md-12">
                                        <label class="form-label">Article</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="reserve-article-search" placeholder="Recherche article (code/désignation)">
                                            <button class="btn btn-outline-secondary" type="button" id="reserve-article-search-btn">Rechercher</button>
                                        </div>
                                        <select class="form-select mt-1" id="reserve-article" required></select>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">Dépôt</label>
                                        <select class="form-select" id="reserve-depot" required></select>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">Quantité</label>
                                        <input type="number" step="0.01" class="form-control" id="reserve-quantite" required>
                                    </div>
                                    <div class="col-lg-6 col-md-12">
                                        <label class="form-label">Client</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="reserve-client-search" placeholder="Recherche client (nom, téléphone, email)">
                                            <button class="btn btn-outline-secondary" type="button" id="reserve-client-search-btn">Rechercher</button>
                                        </div>
                                        <select class="form-select mt-1" id="reserve-client"></select>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <label class="form-label">Référence (optionnel)</label>
                                        <input type="text" class="form-control" id="reserve-reference" placeholder="table#id">
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <label class="form-label">Expiration</label>
                                        <input type="datetime-local" class="form-control" id="reserve-expiration" step="900">
                                        <div class="form-text">La réservation expirera automatiquement après cette date.</div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <button class="btn btn-success" type="submit">Réserver</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-12">
                        <div class="card h-100">
                            <div class="card-header"><h5 class="card-title mb-0">Disponibilité de l'article</h5></div>
                            <div class="card-body" id="availabilityCard">
                                <div class="text-muted small" id="availabilityMessage">Sélectionnez un article et un dépôt pour consulter le stock.</div>
                                <div class="row g-2 my-3 d-none" id="availabilityStats">
                                    <div class="col-6 col-sm-3">
                                        <div class="border rounded p-2 text-center">
                                            <div class="text-muted small">En stock</div>
                                            <div class="fw-semibold" id="availabilityStockQuantite">-</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="border rounded p-2 text-center">
                                            <div class="text-muted small">Réservé</div>
                                            <div class="fw-semibold" id="availabilityStockReserved">-</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="border rounded p-2 text-center">
                                            <div class="text-muted small">Disponible</div>
                                            <div class="fw-semibold" id="availabilityStockDisponible">-</div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="border rounded p-2 text-center">
                                            <div class="text-muted small">Coût moyen</div>
                                            <div class="fw-semibold" id="availabilityStockCout">-</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive small d-none" id="availabilityLotsWrapper">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Lot</th>
                                                <th class="text-end">Restant</th>
                                                <th class="text-end">Coût</th>
                                                <th>DLUO</th>
                                                <th>DLC</th>
                                            </tr>
                                        </thead>
                                        <tbody id="availabilityLotsBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Réservations en cours</h5>
                                    <div class="text-muted small" id="summaryTotals"></div>
                                </div>
                                <span class="text-muted small" id="count-label"></span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="reservations-table">
                                        <thead>
                                        <tr>
                                            <th>Article</th>
                                            <th>Dépôt</th>
                                            <th>Référence</th>
                                            <th>Client</th>
                                            <th class="text-end">Réservé</th>
                                            <th class="text-end">Expiration</th>
                                            <th class="text-end">Dernière MAJ</th>
                                            <th class="text-end">Actions</th>
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
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/dayjs/dayjs.min.js"></script>

<script>
const base = '<?= Flight::base() ?>';
const typesMap = new Map();

const dom = {
    applyBtn: document.getElementById('btn-apply'),
    resetBtn: document.getElementById('btn-reset'),
    filters: {
        article: document.getElementById('flt-article'),
        depot: document.getElementById('flt-depot'),
        reference: document.getElementById('flt-reference')
    },
    countLabel: document.getElementById('count-label'),
    summaryTotals: document.getElementById('summaryTotals'),
    tableBody: document.querySelector('#reservations-table tbody'),
    reserveForm: document.getElementById('form-reserve'),
    reserveArticleSelect: document.getElementById('reserve-article'),
    reserveArticleSearch: document.getElementById('reserve-article-search'),
    reserveArticleSearchBtn: document.getElementById('reserve-article-search-btn'),
    reserveDepot: document.getElementById('reserve-depot'),
    reserveQuantite: document.getElementById('reserve-quantite'),
    reserveClientSelect: document.getElementById('reserve-client'),
    reserveClientSearch: document.getElementById('reserve-client-search'),
    reserveClientSearchBtn: document.getElementById('reserve-client-search-btn'),
    reserveReference: document.getElementById('reserve-reference'),
    reserveExpiration: document.getElementById('reserve-expiration'),
    availability: {
        message: document.getElementById('availabilityMessage'),
        stats: document.getElementById('availabilityStats'),
        stock: document.getElementById('availabilityStockQuantite'),
        reserved: document.getElementById('availabilityStockReserved'),
        available: document.getElementById('availabilityStockDisponible'),
        cost: document.getElementById('availabilityStockCout'),
        lotsWrapper: document.getElementById('availabilityLotsWrapper'),
        lotsBody: document.getElementById('availabilityLotsBody')
    }
};

function toast(message, type = 'info') {
    const color = type === 'error' ? '#dc3545' : (type === 'success' ? '#198754' : '#0d6efd');
    Toastify({ text: message, gravity: 'top', position: 'right', style: { background: color } }).showToast();
}

function formatNumber(value, allowNull = false) {
    if (value === null || value === undefined || Number.isNaN(Number(value))) {
        return allowNull ? '-' : '0.00';
    }
    return Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(value) {
    if (!value) return '-';
    try {
        if (window.dayjs) {
            return dayjs(value).format('YYYY-MM-DD HH:mm');
        }
    } catch (e) {}
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? value : date.toLocaleString();
}

function formatDateOnly(value) {
    if (!value) return '-';
    try {
        if (window.dayjs) {
            return dayjs(value).format('YYYY-MM-DD');
        }
    } catch (e) {}
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? value : date.toLocaleDateString();
}

async function getJson(url, params) {
    const finalUrl = params ? `${url}?${new URLSearchParams(params).toString()}` : url;
    const res = await fetch(finalUrl, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) {
        throw new Error(`HTTP ${res.status}`);
    }
    return res.json();
}

async function postJson(url, payload) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    });
    const text = await res.text();
    let json;
    try {
        json = text ? JSON.parse(text) : { success: false, message: 'Réponse vide' };
    } catch (e) {
        throw new Error(text || 'Réponse illisible du serveur');
    }
    if (!res.ok || !json.success) {
        throw new Error(json.message || `HTTP ${res.status}`);
    }
    return json;
}

function setTableLoading() {
    dom.tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Chargement...</td></tr>';
}

function renderReservations(rows) {
    if (!Array.isArray(rows) || rows.length === 0) {
        dom.tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Aucune réservation en cours</td></tr>';
        dom.countLabel.textContent = '0 élément';
        dom.summaryTotals.textContent = '';
        return;
    }
    const total = rows.reduce((acc, x) => acc + parseFloat(x.reserved || 0), 0);
    dom.countLabel.textContent = `${rows.length} élément${rows.length > 1 ? 's' : ''}`;
    dom.summaryTotals.textContent = `Quantité réservée totale : ${total.toFixed(2)}`;
    dom.tableBody.innerHTML = rows.map(row => {
        const article = row.article_designation || row.id_article;
        const depot = row.depot_nom || row.id_depot;
        const reference = row.reference || '';
        const clientLabel = row.client_nom || (row.id_client ? `#${row.id_client}` : '');
        const qty = parseFloat(row.reserved || 0).toFixed(2);
        const expiration = formatDate(row.expiration);
        const last = formatDate(row.last_date);
        const clientAttr = row.id_client ? ` data-client="${row.id_client}"` : '';
        return `<tr>
            <td>${article}</td>
            <td>${depot}</td>
            <td>${reference}</td>
            <td>${clientLabel || '-'}</td>
            <td class="text-end">${qty}</td>
            <td class="text-end">${expiration}</td>
            <td class="text-end">${last}</td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-cancel-row" data-article="${row.id_article}" data-depot="${row.id_depot}" data-reference="${reference}" data-reserved="${qty}"${clientAttr}>Annuler</button>
            </td>
        </tr>`;
    }).join('');
}

async function applyFilters() {
    setTableLoading();
    try {
        const params = {};
        if (dom.filters.article.value) params.article = dom.filters.article.value;
        if (dom.filters.depot.value) params.depot = dom.filters.depot.value;
        if (dom.filters.reference.value) params.reference = dom.filters.reference.value;
        const res = await getJson(`${base}/api/stock/reservations`, params);
        if (!res.success) throw new Error(res.message || 'Erreur lors du chargement');
        renderReservations(res.data || []);
    } catch (e) {
        dom.tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">${e.message}</td></tr>`;
        dom.countLabel.textContent = '';
        dom.summaryTotals.textContent = '';
        toast(e.message, 'error');
    }
}

async function populateDepots() {
    const res = await getJson(`${base}/api/stock/depots`);
    if (!res.success) return;
    dom.reserveDepot.innerHTML = '';
    (res.data || []).forEach(d => {
        const opt = document.createElement('option');
        opt.value = d.id_depot;
        opt.textContent = d.nom || d.code || d.id_depot;
        dom.reserveDepot.appendChild(opt);
    });
}

async function searchArticles(query = '') {
    const params = query ? { q: query } : undefined;
    const res = await getJson(`${base}/api/stock/articles`, params);
    dom.reserveArticleSelect.innerHTML = '';
    if (!res.success) {
        toast(res.message || 'Impossible de charger les articles', 'error');
        return;
    }
    const data = res.data || [];
    if (data.length === 0) {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'Aucun article';
        dom.reserveArticleSelect.appendChild(opt);
        return;
    }
    data.forEach(a => {
        const opt = document.createElement('option');
        opt.value = a.id_article;
        const label = a.code ? `${a.code} - ${a.designation || a.id_article}` : (a.designation || a.id_article);
        opt.textContent = label;
        dom.reserveArticleSelect.appendChild(opt);
    });
}

async function searchClients(query = '') {
    if (!dom.reserveClientSelect) { return; }
    let res;
    if (query && query.trim() !== '') {
        res = await getJson(`${base}/api/referentiel/clients/search`, { search: query.trim() });
    } else {
        res = await getJson(`${base}/api/referentiel/clients/all`);
    }
    dom.reserveClientSelect.innerHTML = '';
    if (!res.success) {
        toast(res.message || 'Impossible de charger les clients', 'error');
        return;
    }
    const dataPayload = res.data;
    let data = [];
    if (Array.isArray(dataPayload)) {
        data = dataPayload;
    } else if (dataPayload && Array.isArray(dataPayload.clients)) {
        data = dataPayload.clients;
    } else if (dataPayload && typeof dataPayload === 'object') {
        data = Object.values(dataPayload).filter(item => item && typeof item === 'object' && 'id_client' in item);
    }
    const previousValue = dom.reserveClientSelect.dataset.selected || '';
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = 'Sélectionnez un client (optionnel)';
    dom.reserveClientSelect.appendChild(placeholder);
    data.forEach(client => {
        const opt = document.createElement('option');
        opt.value = client.id_client;
        const labelParts = [client.nom || client.raison_sociale || client.id_client];
        if (client.telephone) { labelParts.push(client.telephone); }
        if (client.email) { labelParts.push(client.email); }
        opt.textContent = labelParts.filter(Boolean).join(' • ');
        dom.reserveClientSelect.appendChild(opt);
    });
    if (previousValue) {
        dom.reserveClientSelect.value = previousValue;
        if (dom.reserveClientSelect.value !== previousValue) {
            dom.reserveClientSelect.value = '';
        }
    }
}

function resetFilters() {
    dom.filters.article.value = '';
    dom.filters.depot.value = '';
    dom.filters.reference.value = '';
    applyFilters();
}

function showAvailabilityMessage(text) {
    if (!dom.availability || !dom.availability.message) return;
    dom.availability.message.textContent = text;
    dom.availability.message.classList.remove('d-none');
    if (dom.availability.stats) dom.availability.stats.classList.add('d-none');
    if (dom.availability.lotsWrapper) dom.availability.lotsWrapper.classList.add('d-none');
}

function setAvailabilityStats(stock) {
    if (!dom.availability) return;
    if (dom.availability.message) dom.availability.message.classList.add('d-none');
    if (dom.availability.stats) {
        dom.availability.stats.classList.remove('d-none');
        if (dom.availability.stock) dom.availability.stock.textContent = formatNumber(stock.quantite ?? 0);
        if (dom.availability.reserved) dom.availability.reserved.textContent = formatNumber(stock.reserved ?? 0);
        if (dom.availability.available) dom.availability.available.textContent = formatNumber(stock.disponible ?? 0);
        if (dom.availability.cost) dom.availability.cost.textContent = formatNumber(stock.cout_moyen, true);
    }
}

function renderAvailabilityLots(lots) {
    if (!dom.availability || !dom.availability.lotsWrapper || !dom.availability.lotsBody) return;
    dom.availability.lotsWrapper.classList.remove('d-none');
    const availableLots = (Array.isArray(lots) ? lots : []).filter(l => Math.max(0, parseFloat(l.quantite_restante ?? l.quantite_initiale ?? 0)) > 0);
    if (!availableLots.length) {
        dom.availability.lotsBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Aucun lot disponible</td></tr>';
        return;
    }
    dom.availability.lotsBody.innerHTML = availableLots.map(lot => {
        const restant = Math.max(0, parseFloat(lot.quantite_restante ?? lot.quantite_initiale ?? 0));
        const cout = lot.cout_unitaire ?? null;
        const numero = lot.lot_numero || lot.id_lot;
        return `<tr>
            <td>${numero}</td>
            <td class="text-end">${formatNumber(restant)}</td>
            <td class="text-end">${formatNumber(cout, true)}</td>
            <td>${formatDateOnly(lot.date_limite_utilisation_optimale)}</td>
            <td>${formatDateOnly(lot.date_limite_consommation)}</td>
        </tr>`;
    }).join('');
}

function setAvailabilityLoading() {
    showAvailabilityMessage('Chargement des disponibilités...');
}

async function updateAvailability() {
    if (!dom.availability || !dom.availability.message) return;
    const articleId = parseInt(dom.reserveArticleSelect ? dom.reserveArticleSelect.value : '0', 10);
    const depotId = parseInt(dom.reserveDepot ? dom.reserveDepot.value : '0', 10);
    if (!articleId || !depotId) {
        showAvailabilityMessage('Sélectionnez un article et un dépôt pour consulter le stock.');
        return;
    }
    setAvailabilityLoading();
    try {
        const [stockRes, lotsRes] = await Promise.all([
            getJson(`${base}/api/stock/courant`, { article: articleId, depot: depotId }),
            getJson(`${base}/api/stock/lots`, { article: articleId, depot: depotId })
        ]);
        if (!stockRes.success) throw new Error(stockRes.message || 'Stock courant indisponible');
        if (!lotsRes.success) throw new Error(lotsRes.message || 'Lots indisponibles');
        setAvailabilityStats(stockRes.data || {});
        renderAvailabilityLots(Array.isArray(lotsRes.data) ? lotsRes.data : []);
    } catch (error) {
        showAvailabilityMessage(error.message || 'Disponibilité indisponible');
        toast(error.message || 'Disponibilité indisponible', 'error');
    }
}

async function submitReservation(event) {
    event.preventDefault();
    if (!typesMap.has('RESERVATION')) {
        toast('Type de mouvement RESERVATION introuvable', 'error');
        return;
    }
    const idArticle = parseInt(dom.reserveArticleSelect.value || '0', 10);
    const idDepot = parseInt(dom.reserveDepot.value || '0', 10);
    const quantite = parseFloat(dom.reserveQuantite.value || '0');
    const idClient = dom.reserveClientSelect ? parseInt(dom.reserveClientSelect.value || '0', 10) : 0;
    if (!idArticle || !idDepot || Number.isNaN(quantite) || quantite <= 0) {
        toast('Veuillez renseigner l\'article, le dépôt et une quantité valide.', 'error');
        return;
    }
    const referenceInput = dom.reserveReference.value.trim();
    let tableReference = null;
    let idReference = null;
    if (referenceInput) {
        const parts = referenceInput.split('#');
        tableReference = parts[0] || null;
        if (parts.length > 1) {
            const parsed = parseInt(parts[1], 10);
            idReference = Number.isNaN(parsed) ? null : parsed;
        }
    }
    const expirationRaw = dom.reserveExpiration ? dom.reserveExpiration.value.trim() : '';
    let reservationExpiration = null;
    if (expirationRaw) {
        const expirationDate = new Date(expirationRaw);
        if (Number.isNaN(expirationDate.getTime())) {
            toast('Date d\'expiration invalide.', 'error');
            return;
        }
        if (expirationDate.getTime() <= Date.now()) {
            toast('La date d\'expiration doit être ultérieure à maintenant.', 'error');
            return;
        }
        const normalized = new Date(expirationDate.getTime() - (expirationDate.getTimezoneOffset() * 60000))
            .toISOString()
            .slice(0, 19)
            .replace('T', ' ');
        reservationExpiration = normalized;
    }
    const payload = {
        id_article: idArticle,
        id_depot: idDepot,
        id_type_mouvement_stock: typesMap.get('RESERVATION'),
        sens: 2,
        quantite: quantite,
        table_reference: tableReference,
        id_reference: idReference
    };
    if (idClient > 0) {
        payload.id_client = idClient;
    }
    if (reservationExpiration) {
        payload.reservation_expiration = reservationExpiration;
    }
    try {
        await postJson(`${base}/api/stock/mouvements/create`, payload);
        toast('Réservation enregistrée', 'success');
        if (dom.reserveQuantite) dom.reserveQuantite.value = '';
        if (dom.reserveReference) dom.reserveReference.value = '';
        if (dom.reserveExpiration) dom.reserveExpiration.value = '';
        await applyFilters();
        await updateAvailability();
    } catch (e) {
        toast(e.message, 'error');
    }
}

async function cancelReservation(button) {
    if (!typesMap.has('ANNULATION_RESERVATION')) {
        toast('Type de mouvement ANNULATION_RESERVATION introuvable', 'error');
        return;
    }
    const maxQty = parseFloat(button.dataset.reserved || '0');
    const suggested = maxQty > 0 ? maxQty.toFixed(2) : '0';
    const input = prompt(`Quantité à annuler (max ${suggested})`, suggested);
    if (input === null) { return; }
    const qty = parseFloat(input);
    if (Number.isNaN(qty) || qty <= 0) {
        toast('Quantité invalide', 'error');
        return;
    }
    if (maxQty > 0 && qty > maxQty) {
        toast('La quantité saisie dépasse la réservation restante.', 'error');
        return;
    }
    const referenceInput = button.dataset.reference || '';
    let tableReference = null;
    let idReference = null;
    if (referenceInput) {
        const parts = referenceInput.split('#');
        tableReference = parts[0] || null;
        if (parts.length > 1) {
            const parsed = parseInt(parts[1], 10);
            idReference = Number.isNaN(parsed) ? null : parsed;
        }
    }
    const clientIdAttr = button.dataset.client ? parseInt(button.dataset.client, 10) : 0;
    const payload = {
        id_article: parseInt(button.dataset.article, 10),
        id_depot: parseInt(button.dataset.depot, 10),
        id_type_mouvement_stock: typesMap.get('ANNULATION_RESERVATION'),
        sens: 2,
        quantite: qty,
        table_reference: tableReference,
        id_reference: idReference
    };
    if (clientIdAttr > 0) {
        payload.id_client = clientIdAttr;
    }
    try {
        await postJson(`${base}/api/stock/mouvements/create`, payload);
        toast('Annulation enregistrée', 'success');
        await applyFilters();
        await updateAvailability();
    } catch (e) {
        toast(e.message, 'error');
    }
}

async function loadTypes() {
    const res = await getJson(`${base}/api/stock/types`);
    if (!res.success) {
        throw new Error(res.message || 'Impossible de charger les types de mouvement');
    }
    (res.data || []).forEach(t => {
        const code = (t.code || '').toUpperCase();
        typesMap.set(code, t.id_type_mouvement_stock);
    });
}

function bindEvents() {
    if (dom.applyBtn) dom.applyBtn.addEventListener('click', applyFilters);
    if (dom.resetBtn) dom.resetBtn.addEventListener('click', resetFilters);
    if (dom.reserveForm) dom.reserveForm.addEventListener('submit', submitReservation);
    if (dom.reserveArticleSearchBtn) dom.reserveArticleSearchBtn.addEventListener('click', async () => {
        await searchArticles(dom.reserveArticleSearch.value);
        await updateAvailability();
    });
    if (dom.reserveArticleSearch) dom.reserveArticleSearch.addEventListener('keydown', async (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            await searchArticles(dom.reserveArticleSearch.value);
            await updateAvailability();
        }
    });
    if (dom.reserveArticleSelect) dom.reserveArticleSelect.addEventListener('change', updateAvailability);
    if (dom.reserveDepot) dom.reserveDepot.addEventListener('change', updateAvailability);
    if (dom.reserveClientSelect) dom.reserveClientSelect.addEventListener('change', () => {
        dom.reserveClientSelect.dataset.selected = dom.reserveClientSelect.value || '';
    });
    if (dom.reserveClientSearchBtn) dom.reserveClientSearchBtn.addEventListener('click', async () => {
        await searchClients(dom.reserveClientSearch.value);
    });
    if (dom.reserveClientSearch) dom.reserveClientSearch.addEventListener('keydown', async (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            await searchClients(dom.reserveClientSearch.value);
        }
    });
    if (dom.tableBody) dom.tableBody.addEventListener('click', (event) => {
        const btn = event.target.closest('.btn-cancel-row');
        if (!btn) return;
        cancelReservation(btn);
    });
}

(async function init() {
    try {
        bindEvents();
        await Promise.all([loadTypes(), populateDepots(), searchArticles(), searchClients()]);
        await applyFilters();
        await updateAvailability();
    } catch (e) {
        toast(e.message || 'Initialisation impossible', 'error');
    }
})();
</script>
</body>
</html>