<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Lot</title>
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
                        <h3>Détail Lot</h3>
                        <p class="text-subtitle text-muted">Informations du lot et mouvements associés</p>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Informations Lot</h4>
                            </div>
                            <div class="card-body">
                                <dl class="row" id="lotInfo">
                                    <dt class="col-sm-4">Numéro</dt><dd class="col-sm-8" id="lotNumero">-</dd>
                                    <dt class="col-sm-4">Article</dt><dd class="col-sm-8" id="lotArticle">-</dd>
                                    <dt class="col-sm-4">Dépôt</dt><dd class="col-sm-8" id="lotDepot">-</dd>
                                    <dt class="col-sm-4">Date entrée</dt><dd class="col-sm-8" id="lotDate">-</dd>
                                    <dt class="col-sm-4">Quantité initiale</dt><dd class="col-sm-8" id="lotQi">-</dd>
                                    <dt class="col-sm-4">Quantité restante</dt><dd class="col-sm-8" id="lotQr">-</dd>
                                    <dt class="col-sm-4">Coût unitaire</dt><dd class="col-sm-8" id="lotCu">-</dd>
                                    <dt class="col-sm-4">DLUO</dt><dd class="col-sm-8" id="lotDluo">-</dd>
                                    <dt class="col-sm-4">DLC</dt><dd class="col-sm-8" id="lotDlc">-</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Mouvements liés</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group" id="mvList"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script>
const base = '<?= Flight::base() ?>';
const id = <?= (int)($id_lot ?? 0) ?>;
function toast(msg, type='info') { Toastify({ text: msg, gravity: 'top', position: 'right', backgroundColor: type==='error'?'#dc3545':'#0d6efd' }).showToast(); }
async function loadLot() {
    try {
        const res = await fetch(`${base}/api/stock/lots/${id}`);
        const json = await res.json();
        if (!json.success) { toast(json.message || 'Introuvable', 'error'); return; }
        const lot = json.data.lot;
        document.getElementById('lotNumero').textContent = lot.lot_numero;
        document.getElementById('lotArticle').textContent = lot.id_article;
        document.getElementById('lotDepot').textContent = lot.id_depot;
        document.getElementById('lotDate').textContent = lot.date_entree;
        document.getElementById('lotQi').textContent = lot.quantite_initiale;
        document.getElementById('lotQr').textContent = lot.quantite_restante ?? '-';
        document.getElementById('lotCu').textContent = lot.cout_unitaire ?? '-';
        document.getElementById('lotDluo').textContent = lot.date_limite_utilisation_optimale ?? '-';
        document.getElementById('lotDlc').textContent = lot.date_limite_consommation ?? '-';
        const list = document.getElementById('mvList');
        list.innerHTML = '';
        (json.data.details || []).forEach(d => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `<div><div class='fw-bold'>Mouvement: ${d.mouvement_numero || d.id_mouvement_stock}</div><div class='small text-muted'>Date: ${d.date_mouvement} | Qté: ${d.quantite} | Coût: ${d.cout_unitaire} | Valeur: ${d.valeur}</div></div><a class='btn btn-sm btn-outline-secondary' href='${base}/stock/mouvement/${d.id_mouvement_stock}'>Voir mouvement</a>`;
            list.appendChild(li);
        });
    } catch (e) { toast('Erreur de chargement: ' + e.message, 'error'); }
}
loadLot();
</script>
</body>
</html>
