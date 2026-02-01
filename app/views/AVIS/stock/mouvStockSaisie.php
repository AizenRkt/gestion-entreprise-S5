<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie Mouvement de Stock</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
</head>

<body>
<div id="app">
    <!-- Sidebar Mazer -->
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
                        <h3>Saisie Mouvement de Stock</h3>
                        <p class="text-subtitle text-muted">Enregistrer une entrée ou une sortie</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                                <li class="breadcrumb-item"><a href="#">Stock</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Saisie Mouvement</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Formulaire de saisie -->
            <section class="section">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0">Nouveau Mouvement</h4>
                                <span class="badge bg-primary" id="movementBadge">Saisie</span>
                            </div>
                            <div class="card-body">
                                <form id="mvtForm" class="form">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Type de mouvement</label>
                                            <select class="form-select" id="id_type_mouvement_stock" required></select>
                                            <small class="text-muted">Choisissez un type (réception, livraison, etc.)</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Dépôt</label>
                                            <select class="form-select" id="id_depot" required></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Article</label>
                                            <select class="form-select" id="id_article" required></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Lot (si applicable)</label>
                                            <select class="form-select" id="id_lot">
                                                <option value="">-- Aucun --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6" id="lotNumberDiv" style="display: none;">
                                            <label class="form-label">Numéro du lot</label>
                                            <input type="text" class="form-control" id="lot_numero" placeholder="Ex: LAIT-2025-001">
                                        </div>
                                        <div class="col-md-6" id="dluoDiv" style="display: none;">
                                            <label class="form-label">DLUO (Date limite utilisation optimale)</label>
                                            <input type="date" class="form-control" id="date_limite_utilisation_optimale">
                                        </div>
                                        <div class="col-md-6" id="dlcDiv" style="display: none;">
                                            <label class="form-label">DLC (Date limite consommation)</label>
                                            <input type="date" class="form-control" id="date_limite_consommation">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Quantité</label>
                                            <input type="number" step="0.001" class="form-control" id="quantite" placeholder="Ex: 10" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Coût unitaire</label>
                                            <input type="number" step="0.0001" class="form-control" id="cout_unitaire" placeholder="Ex: 0.50">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Date du mouvement</label>
                                            <input type="datetime-local" class="form-control" id="date_mouvement" value="<?= date('Y-m-d\TH:i') ?>">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Motif</label>
                                            <textarea class="form-control" rows="3" id="motif" placeholder="Ex: Réception fournisseur, ajustement inventaire..."></textarea>
                                        </div>
                                        <div class="col-12 d-flex justify-content-end mt-2">
                                            <button type="submit" class="btn btn-primary me-2"><i class="bi bi-save"></i> Enregistrer</button>
                                            <button type="button" class="btn btn-light" id="resetBtn"><i class="bi bi-x-circle"></i> Annuler</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Aperçu Stock</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar bg-primary me-3"><i class="bi bi-box-seam text-white"></i></div>
                                    <div>
                                        <div class="small text-muted">Quantité disponible</div>
                                        <div class="h5" id="stockQuantite">-</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar bg-success me-3"><i class="bi bi-currency-dollar text-white"></i></div>
                                    <div>
                                        <div class="small text-muted">Valeur du stock</div>
                                        <div class="h5" id="stockValeur">-</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-warning me-3"><i class="bi bi-graph-up text-white"></i></div>
                                    <div>
                                        <div class="small text-muted">Coût moyen (CUMP)</div>
                                        <div class="h5" id="stockCump">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Lots disponibles</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group" id="lotsList"></ul>
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
    Toastify({
        text: message,
        duration: 3000,
        close: true,
        gravity: 'top',
        position: 'right',
        backgroundColor: type === 'success' ? '#198754' : '#dc3545',
    }).showToast();
}

async function fetchJSON(url) {
    const res = await fetch(url);
    return res.json();
}

async function loadTypes() {
    const sel = document.getElementById('id_type_mouvement_stock');
    sel.innerHTML = '<option value="">-- Sélectionner --</option>';
    const r = await fetchJSON(base + '/api/stock/types');
    if (r.success) {
        r.data.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id_type_mouvement_stock;
            opt.textContent = `${t.libelle} (${t.code})`;
            opt.dataset.categorie = t.id_categorie_mouvement_stock;
            sel.appendChild(opt);
        });
    }
}

async function loadDepots() {
    const sel = document.getElementById('id_depot');
    sel.innerHTML = '<option value="">-- Sélectionner --</option>';
    const r = await fetchJSON(base + '/api/stock/depots');
    if (r.success) {
        r.data.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id_depot;
            opt.textContent = `${d.nom} (${d.code})`;
            sel.appendChild(opt);
        });
    }
}

async function loadArticles() {
    const sel = document.getElementById('id_article');
    sel.innerHTML = '<option value="">-- Sélectionner --</option>';
    const r = await fetchJSON(base + '/api/referentiel/articles/active/list');
    if (r.success) {
        r.data.forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id_article;
            opt.textContent = `${a.designation} (${a.code})`;
            opt.dataset.methode = a.id_methode_valorisation;
            sel.appendChild(opt);
        });
    }
}

async function refreshStockAndLots() {
    const a = document.getElementById('id_article').value;
    const d = document.getElementById('id_depot').value;
    if (!a || !d) return;
    const stock = await fetchJSON(`${base}/api/stock/courant?article=${a}&depot=${d}`);
    if (stock.success) {
        const s = stock.data || {quantite: 0, valeur_stock: 0, cout_moyen: null};
        document.getElementById('stockQuantite').textContent = s.quantite ?? 0;
        document.getElementById('stockValeur').textContent = s.valeur_stock ?? 0;
        document.getElementById('stockCump').textContent = s.cout_moyen ?? '-';
    }
    const lots = await fetchJSON(`${base}/api/stock/lots?article=${a}&depot=${d}`);
    if (lots.success) {
        const ul = document.getElementById('lotsList');
        ul.innerHTML = '';
        lots.data.forEach(l => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            const dluo = l.date_limite_utilisation_optimale ? l.date_limite_utilisation_optimale : 'N/A';
            const dlc = l.date_limite_consommation ? l.date_limite_consommation : 'N/A';
            li.innerHTML = `<div class="d-flex justify-content-between"><span><strong>${l.lot_numero}</strong> • entrée: ${l.date_entree}</span><span class="badge bg-light text-dark">Qté init: ${l.quantite_initiale} • Coût: ${l.cout_unitaire}</span></div><div class="small text-muted mt-1">DLUO: ${dluo} • DLC: ${dlc}</div>`;
            ul.appendChild(li);
        });
        const lotSel = document.getElementById('id_lot');
        lotSel.innerHTML = '<option value="">-- Aucun --</option>';
        lots.data.forEach(l => {
            const opt = document.createElement('option');
            const dluo = l.date_limite_utilisation_optimale ? l.date_limite_utilisation_optimale : 'N/A';
            const dlc = l.date_limite_consommation ? l.date_limite_consommation : 'N/A';
            opt.value = l.id_lot;
            opt.textContent = `${l.lot_numero} - ${new Date(l.date_entree).toLocaleDateString()} | DLUO: ${dluo} | DLC: ${dlc}`;
            lotSel.appendChild(opt);
        });
    }
}

// Afficher/masquer les champs de péremption selon le contexte
async function togglePeremptionFields() {
    const typeSelect = document.getElementById('id_type_mouvement_stock');
    const articleSelect = document.getElementById('id_article');
    const lotSelect = document.getElementById('id_lot');
    const lotNumberDiv = document.getElementById('lotNumberDiv');
    const dluoDiv = document.getElementById('dluoDiv');
    const dlcDiv = document.getElementById('dlcDiv');
    
    if (!typeSelect || !articleSelect || !lotSelect) return;
    
    const selectedOpt = typeSelect.options[typeSelect.selectedIndex];
    const categorie = selectedOpt ? selectedOpt.dataset.categorie : '';
    const isEntree = categorie == '1'; // 1 = entrée
    const articleId = articleSelect.value;
    
    let showFields = false;
    let showLotNumber = false;
    
    if (isEntree && articleId) {
        // Charger la famille de l'article pour vérifier necessite_lot
        try {
            const res = await fetch(`${base}/api/referentiel/articles/${articleId}`);
            const json = await res.json();
            if (json.success && json.data) {
                const article = json.data;
                const necessite_lot = article.necessite_lot === 1 || article.necessite_lot === true;
                const idLot = lotSelect.value;
                
                if (necessite_lot) {
                    showFields = true;
                    // Si aucun lot sélectionné, afficher le champ lot_numero pour créer un nouveau
                    if (!idLot) {
                        showLotNumber = true;
                    }
                }
            }
        } catch (err) {
            console.error('Erreur lors du chargement de l\'article:', err);
        }
    }
    
    // Appliquer la visibilité
    if (lotNumberDiv) lotNumberDiv.style.display = showLotNumber ? 'block' : 'none';
    if (dluoDiv) dluoDiv.style.display = showFields ? 'block' : 'none';
    if (dlcDiv) dlcDiv.style.display = showFields ? 'block' : 'none';
}

document.getElementById('id_article').addEventListener('change', async () => {
    await refreshStockAndLots();
    await togglePeremptionFields();
});
document.getElementById('id_depot').addEventListener('change', refreshStockAndLots);
document.getElementById('id_type_mouvement_stock').addEventListener('change', togglePeremptionFields);
document.getElementById('id_lot').addEventListener('change', togglePeremptionFields);

document.getElementById('mvtForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    let idLot = document.getElementById('id_lot').value;
    const lotNumber = document.getElementById('lot_numero').value;
    const dluo = document.getElementById('date_limite_utilisation_optimale').value;
    const dlc = document.getElementById('date_limite_consommation').value;
    const quantite = parseFloat(document.getElementById('quantite').value || '0');
    const coutUnitaire = document.getElementById('cout_unitaire').value;
    
    // Si c'est une entrée et qu'il y a un nouveau lot à créer
    let newLotId = null;
    if (idLot === '' && lotNumber) {
        const typeSelect = document.getElementById('id_type_mouvement_stock');
        const selectedOpt = typeSelect.options[typeSelect.selectedIndex];
        const categorie = selectedOpt ? selectedOpt.dataset.categorie : '';
        const isEntree = categorie == '1';
        
        if (isEntree) {
            const lotPayload = {
                id_article: parseInt(document.getElementById('id_article').value || '0'),
                id_depot: parseInt(document.getElementById('id_depot').value || '0'),
                lot_numero: lotNumber,
                cout_unitaire: coutUnitaire ? parseFloat(coutUnitaire) : 0,
                quantite_initiale: quantite,
                date_limite_utilisation_optimale: dluo || null,
                date_limite_consommation: dlc || null
            };
            
            try {
                const lotRes = await fetch(base + '/api/stock/lots/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(lotPayload)
                });
                const lotJson = await lotRes.json();
                if (!lotJson.success) {
                    toast(lotJson.message || 'Erreur à la création du lot', 'error');
                    return;
                }
                // Stocker l'ID du nouveau lot dans une variable (le select ne peut pas accepter une valeur hors options)
                newLotId = lotJson.data.id_lot;
            } catch (err) {
                toast('Erreur à la création du lot: ' + err.message, 'error');
                return;
            }
        }
    }
    
    const payload = {
        id_article: parseInt(document.getElementById('id_article').value || '0'),
        id_depot: parseInt(document.getElementById('id_depot').value || '0'),
        id_lot: newLotId || (idLot ? parseInt(idLot) : null),
        id_type_mouvement_stock: parseInt(document.getElementById('id_type_mouvement_stock').value || '0'),
        sens: (() => {
            const sel = document.getElementById('id_type_mouvement_stock');
            const cat = sel.options[sel.selectedIndex]?.dataset.categorie;
            return cat == '1' ? 1 : 0; // 1=in, 0=out
        })(),
        quantite: quantite,
        cout_unitaire: coutUnitaire ? parseFloat(coutUnitaire) : null,
        motif: document.getElementById('motif').value || null,
        date_mouvement: document.getElementById('date_mouvement').value.replace('T', ' ')
    };

    if (!payload.id_article || !payload.id_depot || !payload.id_type_mouvement_stock || !payload.quantite) {
        toast('Champs requis manquants', 'error');
        return;
    }

    const res = await fetch(base + '/api/stock/mouvements/create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    const json = await res.json();
    if (json.success) {
        toast('Mouvement enregistré');
        refreshStockAndLots();
        document.getElementById('mvtForm').reset();
        await togglePeremptionFields();
    } else {
        toast(json.message || 'Erreur à l\'enregistrement', 'error');
    }
});

document.getElementById('resetBtn').addEventListener('click', () => {
    document.getElementById('mvtForm').reset();
});

(async function init() {
    await Promise.all([loadTypes(), loadDepots(), loadArticles()]);
})();
</script>

</body>
</html>
