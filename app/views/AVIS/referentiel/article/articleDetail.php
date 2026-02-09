<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Article</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
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
                    <div class="col-12 col-md-8 order-md-1 order-last">
                        <h3>Détail Article</h3>
                        <p class="text-subtitle text-muted">Informations détaillées et historique des prix</p>
                    </div>
                    <div class="col-12 col-md-4 text-end align-self-center">
                        <a id="editBtn" class="btn btn-sm btn-secondary" href="#">Modifier</a>
                        <a href="<?= Flight::base() ?>/referentiel/article/list" class="btn btn-sm btn-light">Retour</a>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="artTitle">Article</h4>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-4">Code</dt>
                                    <dd class="col-sm-8" id="artCode">-</dd>

                                    <dt class="col-sm-4">Désignation</dt>
                                    <dd class="col-sm-8" id="artDesignation">-</dd>

                                    <dt class="col-sm-4">Famille</dt>
                                    <dd class="col-sm-8" id="artFamille">-</dd>

                                    <dt class="col-sm-4">Méthode valorisation</dt>
                                    <dd class="col-sm-8" id="artValorisation">-</dd>

                                    <dt class="col-sm-4">Unité</dt>
                                    <dd class="col-sm-8" id="artUnite">-</dd>

                                    <dt class="col-sm-4">Prix achat</dt>
                                    <dd class="col-sm-8" id="artPrixAchat">-</dd>

                                    <dt class="col-sm-4">Prix vente</dt>
                                    <dd class="col-sm-8" id="artPrixVente">-</dd>

                                    <dt class="col-sm-4">Stock min</dt>
                                    <dd class="col-sm-8" id="artStockMin">-</dd>

                                    <dt class="col-sm-4">Actif</dt>
                                    <dd class="col-sm-8" id="artActif">-</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header"><h5>Historique des prix</h5></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm" id="prixHistoriqueTable">
                                        <thead>
                                            <tr><th>Date</th><th>Prix vente</th></tr>
                                        </thead>
                                        <tbody id="prixHistoriqueBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header"><h5>Actions</h5></div>
                            <div class="card-body">
                                <p><strong>ID:</strong> <span id="artId">-</span></p>
                                <p><strong>Valeur CUMP actuelle:</strong> <span id="artCump">-</span></p>
                                <p><strong>Lots disponibles:</strong></p>
                                <ul id="lotsList" class="list-group list-group-flush"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script>
const base = '<?= Flight::base() ?>';
async function fetchJSON(url, opts){ const r = await fetch(url, opts); return r.json(); }

function qsel(id){ return document.getElementById(id); }

(async function(){
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    if (!id) {
        Toastify({text:'ID article requis',duration:3000,backgroundColor:'red'}).showToast();
        return;
    }
    // load article
    const r = await fetchJSON(base + '/api/referentiel/articles/' + id);
    if (!r.success) { Toastify({text:r.message || 'Article introuvable',duration:3000,backgroundColor:'red'}).showToast(); return; }
    const a = r.data;
    qsel('artId').textContent = a.id_article;
    qsel('artCode').textContent = a.code;
    qsel('artDesignation').textContent = a.designation;
    qsel('artTitle').textContent = a.designation;
    qsel('artFamille').textContent = a.famille_nom || '-';
    qsel('artValorisation').textContent = a.valorisation_libelle || '-';
    qsel('artUnite').textContent = a.unite || '-';
    qsel('artPrixAchat').textContent = a.prix_achat ?? '-';
    qsel('artPrixVente').textContent = a.prix_vente ?? '-';
    qsel('artStockMin').textContent = a.stock_min ?? '-';
    qsel('artActif').textContent = a.actif==1 ? 'Oui' : 'Non';
    qsel('editBtn').href = base + '/referentiel/article/saisie?id=' + a.id_article;

    // price history
    const ph = await fetchJSON(base + '/api/referentiel/articles/prix-historique/' + id);
    const tb = qsel('prixHistoriqueBody'); tb.innerHTML = '';
    if (ph.success && Array.isArray(ph.data)) {
        ph.data.forEach(p => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${p.date_modification}</td><td>${p.prix_vente}</td>`;
            tb.appendChild(tr);
        });
    }

    // lots for the article in main depot (if available)
    try {
        const depotId = (new URL(window.location)).searchParams.get('depot') || '';
        if (depotId) {
            const lots = await fetchJSON(base + '/api/stock/lots?article=' + id + '&depot=' + depotId);
            if (lots.success) {
                const ll = qsel('lotsList'); ll.innerHTML = '';
                lots.data.forEach(l => { const li=document.createElement('li'); li.className='list-group-item'; li.textContent = l.lot_numero + ' • qté restante: ' + (l.quantite_restante ?? l.quantite_initiale); ll.appendChild(li); });
            }
        }
    } catch(e) { /* optional */ }

})();
</script>
</body>
</html>
