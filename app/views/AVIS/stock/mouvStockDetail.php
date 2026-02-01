<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Mouvement</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Mouvement</title>
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
                        <h3>Détail Mouvement</h3>
                        <p class="text-subtitle text-muted">Visualiser les informations et consommations par lot</p>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0">Informations Mouvement</h4>
                                <div>
                                    <a id="exportCsv" class="btn btn-sm btn-outline-primary" target="_blank"><i class="bi bi-filetype-csv"></i> CSV</a>
                                    <a id="exportPdf" class="btn btn-sm btn-outline-danger" target="_blank"><i class="bi bi-filetype-pdf"></i> PDF</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <dl class="row" id="mvInfo">
                                    <dt class="col-sm-4">Numéro</dt><dd class="col-sm-8" id="mvNumero">-</dd>
                                    <dt class="col-sm-4">Article</dt><dd class="col-sm-8" id="mvArticle">-</dd>
                                    <dt class="col-sm-4">Dépôt</dt><dd class="col-sm-8" id="mvDepot">-</dd>
                                    <dt class="col-sm-4">Type</dt><dd class="col-sm-8" id="mvType">-</dd>
                                    <dt class="col-sm-4">Sens</dt><dd class="col-sm-8" id="mvSens">-</dd>
                                    <dt class="col-sm-4">Quantité</dt><dd class="col-sm-8" id="mvQuantite">-</dd>
                                    <dt class="col-sm-4">Coût unitaire</dt><dd class="col-sm-8" id="mvCout">-</dd>
                                    <dt class="col-sm-4">Date</dt><dd class="col-sm-8" id="mvDate">-</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Consommations par lot</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group" id="lotDetails"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/toastify.js"></script>


<script>
const base = '<?= Flight::base() ?>';
const id = <?= (int)($id_mouvement_stock ?? 0) ?>;
function toast(msg, type='info') {
    Toastify({ text: msg, gravity: 'top', position: 'right', backgroundColor: type==='error'?'#dc3545':'#0d6efd' }).showToast();
}
async function loadDetail() {
    try {
        const mvRes = await fetch(`${base}/api/stock/mouvements/${id}`);
        const mvJson = await mvRes.json();
        if (!mvJson.success) { toast(mvJson.message || 'Introuvable', 'error'); return; }
        const m = mvJson.data;
        document.getElementById('mvNumero').textContent = m.mouvement_numero || id;
        document.getElementById('mvArticle').textContent = m.article_designation || m.id_article;
        document.getElementById('mvDepot').textContent = m.depot_nom || m.id_depot;
        document.getElementById('mvType').textContent = m.type_libelle || m.id_type_mouvement_stock;
        document.getElementById('mvSens').textContent = m.sens == 1 ? 'Entrée' : 'Sortie';
        document.getElementById('mvQuantite').textContent = m.quantite;
        document.getElementById('mvCout').textContent = m.cout_unitaire ?? '-';
        document.getElementById('mvDate').textContent = m.date_mouvement;
        document.getElementById('exportCsv').href = `${base}/api/stock/mouvements/${id}/details.csv`;
        document.getElementById('exportPdf').href = `${base}/api/stock/mouvements/${id}/details.pdf`;
        const detRes = await fetch(`${base}/api/stock/mouvements/${id}/details`);
        const detJson = await detRes.json();
        const list = document.getElementById('lotDetails');
        list.innerHTML = '';
        (detJson.data || []).forEach(d => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            const lotNum = d.lot_numero || d.id_lot;
            const dluo = d.date_limite_utilisation_optimale ? d.date_limite_utilisation_optimale : 'N/A';
            const dlc = d.date_limite_consommation ? d.date_limite_consommation : 'N/A';
            li.innerHTML = `<div><div class='fw-bold'>Lot: ${lotNum}</div><div class='small text-muted'>Quantité: ${d.quantite} | Coût: ${d.cout_unitaire} | Valeur: ${d.valeur}</div><div class='small text-muted'>DLUO: ${dluo} | DLC: ${dlc}</div></div>`;
            list.appendChild(li);
        });
    } catch (e) {
        toast('Erreur de chargement: ' + e.message, 'error');
    }
}
loadDetail();
</script>
</body>
</html>
