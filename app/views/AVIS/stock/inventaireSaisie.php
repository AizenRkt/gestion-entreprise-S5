<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inventaire' ?></title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <style>
        .table-input { width: 100px; }
        .row-gain { color: #198754; font-weight: bold; }
        .row-loss { color: #dc3545; font-weight: bold; }
    </style>
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
                        <h3><?= $id_inventaire ? 'Détails Inventaire' : 'Nouvel Inventaire' ?></h3>
                        <p class="text-subtitle text-muted" id="invSubtitle">Saisie des écarts de stock</p>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="card">
                    <div class="card-body">
                        <form id="invForm">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Numéro</label>
                                    <input type="text" id="inventaire_numero" class="form-control" readonly placeholder="Auto-généré">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Dépôt</label>
                                    <select id="id_depot" class="form-select" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" id="date_inventaire" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Statut</label>
                                    <div id="statutContainer"></div>
                                </div>
                                <div class="col-12 mt-2">
                                    <label class="form-label">Commentaire</label>
                                    <input type="text" id="commentaire" class="form-control">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Lignes d'inventaire</h4>
                        <div id="btnGroup">
                             <button type="button" id="addRowBtn" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus"></i> Ajouter un article</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="linesTable">
                                <thead>
                                    <tr>
                                        <th>Article</th>
                                        <th>Lot</th>
                                        <th>Qté Théorique</th>
                                        <th>Qté Physique</th>
                                        <th>Écart</th>
                                        <th>Coût Unit.</th>
                                        <th>Valeur Écart</th>
                                        <th class="action-col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="linesBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-2" id="footerActions">
                        <button type="button" id="saveDraftBtn" class="btn btn-secondary">Enregistrer Brouillon</button>
                        <button type="button" id="validateBtn" class="btn btn-success">Valider & Ajuster Stock</button>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>

<script>
const base = '<?= Flight::base() ?>';
const idInventaire = <?= $id_inventaire ?? 'null' ?>;
let statut = 'BROUILLON';
let articles = [];
let depots = [];

function toast(msg, type='success') {
    Toastify({ text: msg, duration: 3000, backgroundColor: type==='success'?'#198754':'#dc3545' }).showToast();
}

async function fetchJSON(url) { const res = await fetch(url); return res.json(); }

async function init() {
    // Load depots
    const dRes = await fetchJSON(base + '/api/stock/depots');
    const dSel = document.getElementById('id_depot');
    if (dRes.success) {
        depots = dRes.data;
        dRes.data.forEach(d => {
            const opt = document.createElement('option'); opt.value = d.id_depot; opt.textContent = d.nom; dSel.appendChild(opt);
        });
    }

    // Load articles
    const aRes = await fetchJSON(base + '/api/referentiel/articles/active/list');
    if (aRes.success) articles = aRes.data;

    if (idInventaire) {
        await loadInventaire();
    }
}

async function loadInventaire() {
    const res = await fetchJSON(base + '/api/stock/inventaires/' + idInventaire);
    if (res.success) {
        const inv = res.data.inventaire;
        statut = inv.statut;
        document.getElementById('inventaire_numero').value = inv.inventaire_numero;
        document.getElementById('id_depot').value = inv.id_depot;
        document.getElementById('date_inventaire').value = inv.date_inventaire.split(' ')[0];
        document.getElementById('commentaire').value = inv.commentaire || '';
        document.getElementById('statutContainer').innerHTML = `<span class="badge bg-${statut==='VALIDE'?'success':'warning'}">${statut}</span>`;
        
        if (statut !== 'BROUILLON') {
            document.querySelectorAll('input, select, textarea, button').forEach(e => {
                if (!e.closest('.action-col') && e.id !== 'app') e.disabled = true;
            });
            document.getElementById('btnGroup').style.display = 'none';
            document.getElementById('footerActions').style.display = 'none';
        }

        const linesBody = document.getElementById('linesBody');
        linesBody.innerHTML = '';
        res.data.lignes.forEach(l => addRow(l));
    }
}

function addRow(data = {}) {
    const tbody = document.getElementById('linesBody');
    const tr = document.createElement('tr');
    
    // Article select
    let options = '<option value="">-- Choisir --</option>';
    articles.forEach(a => {
        const selected = (data.id_article == a.id_article) ? 'selected' : '';
        options += `<option value="${a.id_article}" ${selected}>${a.designation}</option>`;
    });

    tr.innerHTML = `
        <td><select class="form-select art-sel">${options}</select></td>
        <td><select class="form-select lot-sel"></select></td>
        <td><input type="number" step="0.001" class="form-control qt-theo" value="${data.quantite_theorique || 0}" readonly></td>
        <td><input type="number" step="0.001" class="form-control qt-phys" value="${data.quantite_physique || 0}"></td>
        <td><span class="diff">-</span></td>
        <td><input type="number" step="0.01" class="form-control cout-unit" value="${data.cout_unitaire || 0}"></td>
        <td><span class="val-diff">-</span></td>
        <td class="action-col"><button type="button" class="btn btn-sm btn-danger remove-btn"><i class="bi bi-trash"></i></button></td>
    `;

    tbody.appendChild(tr);

    const artSel = tr.querySelector('.art-sel');
    const lotSel = tr.querySelector('.lot-sel');
    const qtPhys = tr.querySelector('.qt-phys');
    const coutUnit = tr.querySelector('.cout-unit');

    artSel.addEventListener('change', () => updateLots(tr, data.id_lot));
    lotSel.addEventListener('change', () => updateTheorique(tr));
    qtPhys.addEventListener('input', () => updateCalculs(tr));
    coutUnit.addEventListener('input', () => updateCalculs(tr));
    tr.querySelector('.remove-btn').addEventListener('click', () => tr.remove());

    if (data.id_article) {
        updateLots(tr, data.id_lot);
    }

    if (statut !== 'BROUILLON') {
        tr.querySelectorAll('input, select, button').forEach(e => e.disabled = true);
    }
}

async function updateLots(tr, selectedLotId = null) {
    const artId = tr.querySelector('.art-sel').value;
    const depotId = document.getElementById('id_depot').value;
    const lotSel = tr.querySelector('.lot-sel');
    lotSel.innerHTML = '<option value="">-- Standard --</option>';

    if (artId && depotId) {
        const res = await fetchJSON(`${base}/api/stock/lots?article=${artId}&depot=${depotId}`);
        if (res.success) {
            res.data.forEach(l => {
                const opt = document.createElement('option');
                opt.value = l.id_lot;
                opt.textContent = `${l.lot_numero} (${l.quantite_disponible})`;
                if (selectedLotId == l.id_lot) opt.selected = true;
                lotSel.appendChild(opt);
            });
        }
    }
    updateTheorique(tr);
}

async function updateTheorique(tr) {
    const artId = tr.querySelector('.art-sel').value;
    const depotId = document.getElementById('id_depot').value;
    const lotId = tr.querySelector('.lot-sel').value;
    const qtTheoField = tr.querySelector('.qt-theo');
    const coutUnitField = tr.querySelector('.cout-unit');

    if (artId && depotId) {
        const res = await fetchJSON(`${base}/api/stock/courant?article=${artId}&depot=${depotId}`);
        if (res.success) {
            // If lot selected, we might need specific lot qty, but our API returns global stock courant.
            // Let's assume for theorique we take what's in stock_courant if no lot, or maybe we need a new API for lot qty.
            // For now, let's use global or fetch lots again to find specific qty.
            if (lotId) {
                const lotsRes = await fetchJSON(`${base}/api/stock/lots?article=${artId}&depot=${depotId}`);
                const lot = lotsRes.data.find(l => l.id_lot == lotId);
                qtTheoField.value = lot ? lot.quantite_disponible : 0;
                coutUnitField.value = lot ? lot.cout_unitaire : (res.data.cout_moyen || 0);
            } else {
                qtTheoField.value = res.data.quantite || 0;
                coutUnitField.value = res.data.cout_moyen || 0;
            }
        }
    }
    updateCalculs(tr);
}

function updateCalculs(tr) {
    const theo = parseFloat(tr.querySelector('.qt-theo').value || 0);
    const phys = parseFloat(tr.querySelector('.qt-phys').value || 0);
    const cout = parseFloat(tr.querySelector('.cout-unit').value || 0);
    const diff = phys - theo;
    const spanDiff = tr.querySelector('.diff');
    const spanVal = tr.querySelector('.val-diff');

    spanDiff.textContent = diff.toFixed(3);
    spanDiff.className = diff > 0 ? 'row-gain' : (diff < 0 ? 'row-loss' : '');
    spanVal.textContent = (diff * cout).toFixed(2);
}

document.getElementById('addRowBtn').addEventListener('click', () => addRow());

async function save(shouldValidate = false) {
    const payload = {
        id_depot: document.getElementById('id_depot').value,
        date_inventaire: document.getElementById('date_inventaire').value,
        commentaire: document.getElementById('commentaire').value,
        inventaire_numero: document.getElementById('inventaire_numero').value || null
    };

    if (!payload.id_depot) return toast('Veuillez choisir un dépôt', 'error');

    const lines = [];
    document.querySelectorAll('#linesBody tr').forEach(tr => {
        const artId = tr.querySelector('.art-sel').value;
        if (artId) {
            lines.push({
                id_article: artId,
                id_lot: tr.querySelector('.lot-sel').value || null,
                qt_theorique: tr.querySelector('.qt-theo').value,
                qt_physique: tr.querySelector('.qt-phys').value,
                cout_unitaire: tr.querySelector('.cout-unit').value
            });
        }
    });

    if (lines.length === 0 && shouldValidate) return toast('L\'inventaire doit contenir au moins un article', 'error');

    try {
        let currentId = idInventaire;
        if (!currentId) {
            const res = await fetch(base + '/api/stock/inventaires/create', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (!json.success) throw new Exception(json.message);
            currentId = json.id;
        }

        // Save lines
        const lRes = await fetch(base + '/api/stock/inventaires/' + currentId + '/lignes', {
            method: 'POST',
            body: JSON.stringify(lines)
        });
        const lJson = await lRes.json();
        if (!lJson.success) throw new Error(lJson.message);

        if (shouldValidate) {
            const vRes = await fetch(base + '/api/stock/inventaires/' + currentId + '/validate', { method: 'POST' });
            const vJson = await vRes.json();
            if (!vJson.success) throw new Error(vJson.message);
            toast('Inventaire validé et stock ajusté !');
            setTimeout(() => window.location.href = base + '/stock/inventaires', 1500);
        } else {
            toast('Brouillon enregistré');
            if (!idInventaire) window.location.href = base + '/stock/inventaires/saisie/' + currentId;
        }

    } catch (e) {
        toast(e.message, 'error');
    }
}

document.getElementById('saveDraftBtn').addEventListener('click', () => save(false));
document.getElementById('validateBtn').addEventListener('click', () => {
    if (confirm('Voulez-vous vraiment valider cet inventaire ? Les écarts seront ajustés en stock.')) {
        save(true);
    }
});

init();
</script>
</body>
</html>
