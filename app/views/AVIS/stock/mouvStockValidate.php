<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation du mouvement</title>

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
                        <h3>Validation du mouvement</h3>
                        <p class="text-subtitle text-muted">Choisissez la méthode et validez</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/stock/mouvement/list">Mouvements</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Validation</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Détails du mouvement</h4>
                            </div>
                            <div class="card-body">
                                <div id="mvSummary">
                                    <div class="row g-3">
                                        <div class="col-md-6"><strong>Date:</strong> <span id="mvDate">-</span></div>
                                        <div class="col-md-6"><strong>Utilisateur:</strong> <span id="mvUser">-</span></div>
                                        <div class="col-md-6"><strong>Article:</strong> <span id="mvArticle">-</span></div>
                                        <div class="col-md-6"><strong>Dépôt:</strong> <span id="mvDepot">-</span></div>
                                        <div class="col-md-6"><strong>Type:</strong> <span id="mvType">-</span></div>
                                        <div class="col-md-6"><strong>Sens:</strong> <span id="mvSens">-</span></div>
                                        <div class="col-md-6"><strong>Quantité:</strong> <span id="mvQty">-</span></div>
                                        <div class="col-md-6"><strong>Lot:</strong> <span id="mvLot">-</span></div>
                                        <div class="col-md-6"><strong>Référence:</strong> <span id="mvRef">-</span></div>
                                        <div class="col-md-6"><strong>Origine:</strong> <span id="mvOrig">-</span></div>
                                        <div class="col-md-12"><strong>Statut:</strong> <span id="mvStatus" class="badge bg-secondary">Brouillon</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Validation</h4>
                            </div>
                            <div class="card-body">
                                <form id="validateForm" class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Méthode de valorisation</label>
                                        <select id="valuation" class="form-select">
                                            <option value="cump">CUMP</option>
                                            <option value="fifo">FIFO</option>
                                            <option value="lifo">LIFO</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Stratégie d'allocation</label>
                                        <select id="allocation" class="form-select">
                                            <option value="auto">Auto</option>
                                            <option value="fifo">FIFO</option>
                                            <option value="fefo">FEFO</option>
                                            <option value="lifo">LIFO</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-check2-circle"></i> Valider</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title">Stock courant</h4>
                                <button class="btn btn-outline-primary btn-sm" id="refreshSide"><i class="bi bi-arrow-clockwise"></i> Actualiser</button>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4"><small>Quantité</small><div id="scQty" class="h5 mb-0">-</div></div>
                                    <div class="col-md-4"><small>Valeur</small><div id="scVal" class="h5 mb-0">-</div></div>
                                    <div class="col-md-4"><small>CUMP</small><div id="scCump" class="h5 mb-0">-</div></div>
                                </div>
                                <hr>
                                <div>
                                    <h6>Lots disponibles</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead><tr><th>Lot</th><th>Date</th><th>Qté</th><th>Coût</th><th>DLC/DLUO</th></tr></thead>
                                            <tbody id="lotsBody"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                                <div>
                                    <h6>Détails de consommation (si sortie)</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead><tr><th>Lot</th><th>Date entrée</th><th>Quantité</th><th>Coût unitaire</th><th>Valeur</th></tr></thead>
                                            <tbody id="lotDetailsBody"></tbody>
                                        </table>
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
<script src="<?= Flight::base() ?>/public/template/assets/extensions/dayjs/dayjs.min.js"></script>

<script>
const base = '<?= Flight::base() ?>';
const mvId = <?= json_encode($id_mouvement_stock ?? 0) ?>;

function toast(message, type = 'success') {
  Toastify({ text: message, duration: 3000, close: true, gravity: 'top', position: 'right', backgroundColor: type === 'success' ? '#198754' : '#dc3545' }).showToast();
}

async function fetchJSON(url) { const r = await fetch(url); return r.json(); }

async function loadMovement() {
  const res = await fetchJSON(base + '/api/stock/mouvements/' + mvId);
  if (!res.success) { toast(res.message || 'Erreur chargement mouvement', 'error'); return; }
  const m = res.data;
  document.getElementById('mvDate').textContent = m.date_mouvement ? dayjs(m.date_mouvement).format('YYYY-MM-DD HH:mm') : '-';
  document.getElementById('mvUser').textContent = m.created_by ?? '-';
  document.getElementById('mvArticle').textContent = m.article_designation ?? ('#' + m.id_article);
  document.getElementById('mvDepot').textContent = m.depot_nom ?? ('#' + m.id_depot);
  document.getElementById('mvType').textContent = m.type_libelle ?? ('#' + m.id_type_mouvement_stock);
  document.getElementById('mvSens').innerHTML = (m.sens == 1 ? '<span class="badge bg-success">Entrée</span>' : '<span class="badge bg-danger">Sortie</span>');
  document.getElementById('mvQty').textContent = m.quantite;
  document.getElementById('mvLot').textContent = m.id_lot ?? '-';
  document.getElementById('mvRef').textContent = m.id_reference ?? '-';
  document.getElementById('mvOrig').textContent = m.table_reference ?? '-';
  document.getElementById('mvStatus').className = 'badge ' + (m.date_validation ? 'bg-success' : 'bg-secondary');
  document.getElementById('mvStatus').textContent = m.date_validation ? 'Validé' : 'Brouillon';
  await loadSide(m.id_article, m.id_depot);
    await loadDetails();
}

async function loadSide(article, depot) {
  const sc = await fetchJSON(base + `/api/stock/courant?article=${article}&depot=${depot}`);
  const lots = await fetchJSON(base + `/api/stock/lots?article=${article}&depot=${depot}`);
  const s = sc.success && sc.data ? sc.data : { quantite: 0, valeur_stock: 0, cout_moyen: null };
  document.getElementById('scQty').textContent = s.quantite ?? 0;
  document.getElementById('scVal').textContent = s.valeur_stock ?? 0;
  document.getElementById('scCump').textContent = s.cout_moyen ?? '-';
  const tbody = document.getElementById('lotsBody');
  tbody.innerHTML = '';
  if (lots.success) {
    lots.data.forEach(l => {
      const tr = document.createElement('tr');
      const expiry = l.date_limite_consommation || l.date_limite_utilisation_optimale || '';
      tr.innerHTML = `<td>${l.lot_numero}</td><td>${dayjs(l.date_entree).format('YYYY-MM-DD')}</td><td>${(l.quantite_restante ?? l.quantite_initiale) ?? '-'}</td><td>${l.cout_unitaire ?? '-'}</td><td>${expiry || '-'}</td>`;
      tbody.appendChild(tr);
    });
  }
}

document.getElementById('refreshSide').addEventListener('click', async () => { await loadMovement(); toast('Données rafraîchies'); });

document.getElementById('validateForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const valuation = document.getElementById('valuation').value;
  const allocation = document.getElementById('allocation').value;
  const url = base + `/api/stock/mouvements/${mvId}/validate?valuation=${valuation}&allocation=${allocation}`;
  const res = await fetch(url, { method: 'POST' });
  const json = await res.json();
  if (json.success) { toast(json.message || 'Validé'); await loadMovement(); } else { toast(json.message || 'Erreur validation', 'error'); }
});

async function loadDetails(){
    const res = await fetchJSON(base + '/api/stock/mouvements/' + mvId + '/details');
    const tbody = document.getElementById('lotDetailsBody');
    tbody.innerHTML = '';
    if (res.success && Array.isArray(res.data) && res.data.length) {
        res.data.forEach(d => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${d.lot_numero ?? d.id_lot}</td><td>${d.date_entree ? dayjs(d.date_entree).format('YYYY-MM-DD') : '-'}</td><td>${d.quantite}</td><td>${d.cout_unitaire}</td><td>${d.valeur}</td>`;
            tbody.appendChild(tr);
        });
    }
}

(async function init(){ await loadMovement(); })();
</script>

</body>
</html>