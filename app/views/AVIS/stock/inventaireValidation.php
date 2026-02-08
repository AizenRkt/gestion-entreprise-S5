<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail campagne inventaire</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <style>
        .card h6 { font-size: 0.95rem; }
        .small-label { font-size: 0.85rem; color: #6c757d; }
    </style>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
</head>
<body>
<div id="app">
    <?php Flight::render('ui/menu/avis/stock/menuStock'); ?>
    <div id="main" class="layout-navbar">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
        <div id="main-content">
            <div class="page-heading d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3 id="camp-title">Campagne</h3>
                    <p class="text-muted mb-0" id="camp-subtitle">Chargement des informations...</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <a class="btn btn-outline-secondary btn-sm" href="<?= Flight::base() ?>/stock/inventaire/validation">Retour à la liste</a>
                    <button class="btn btn-success btn-sm" id="btn-validate" disabled>Valider la campagne</button>
                    <button class="btn btn-outline-secondary btn-sm" id="btn-reload-lines">Recharger les lignes</button>
                </div>
            </div>
            <div class="page-content">
                <section class="row g-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Informations campagne</h5></div>
                            <div class="card-body">
                                <div class="row g-3" id="camp-infos">
                                    <div class="col-md-3"><span class="small-label d-block">Code</span><strong id="info-code">-</strong></div>
                                    <div class="col-md-3"><span class="small-label d-block">Type</span><strong id="info-type">-</strong></div>
                                    <div class="col-md-3"><span class="small-label d-block">Début</span><strong id="info-debut">-</strong></div>
                                    <div class="col-md-3"><span class="small-label d-block">Fin</span><strong id="info-fin">-</strong></div>
                                    <div class="col-md-12"><span class="small-label d-block">Libellé</span><strong id="info-libelle">-</strong></div>
                                    <div class="col-md-12"><span class="small-label d-block">Statut</span><span id="info-statut" class="badge bg-light-secondary">-</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-0">Lignes de comptage</h5>
                                    <p class="small-label mb-0">Mettre à jour les quantités puis valider la campagne.</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height:70vh;">
                                    <table class="table table-hover align-middle mb-0" id="lines-table">
                                        <thead>
                                        <tr>
                                            <th>Article</th>
                                            <th>Dépôt</th>
                                            <th class="text-end">Théorique</th>
                                            <th class="text-end">Physique</th>
                                            <th class="text-end">Écart</th>
                                            <th>Type</th>
                                            <th class="text-end">Valeur écart</th>
                                            <th class="text-end">Nouveau comptage</th>
                                            <th></th>
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
<script>
const campagneId = <?= json_encode($id_inventaire_campagne ?? null) ?>;
const linesBody = document.querySelector('#lines-table tbody');
const btnValidate = document.getElementById('btn-validate');
let campagne = null;

function toast(msg, ok=true){ Toastify({text: msg, gravity:'top', position:'right', className: ok?'bg-success':'bg-danger', duration: 2500}).showToast(); }

function formatDate(d){ return d ? d.substring(0,10) : ''; }

function renderCampagne(){
    if(!campagne){
        document.getElementById('camp-title').textContent = 'Campagne inconnue';
        document.getElementById('camp-subtitle').textContent = 'Identifiant invalide.';
        btnValidate.disabled = true;
        return;
    }
    document.getElementById('camp-title').textContent = `Campagne ${campagne.code}`;
    document.getElementById('camp-subtitle').textContent = campagne.libelle;
    document.getElementById('info-code').textContent = campagne.code || '-';
    document.getElementById('info-type').textContent = campagne.type_campagne || '-';
    document.getElementById('info-debut').textContent = formatDate(campagne.date_debut_prevue || campagne.date_debut);
    document.getElementById('info-fin').textContent = formatDate(campagne.date_fin_prevue || campagne.date_fin);
    document.getElementById('info-libelle').textContent = campagne.libelle || '-';
    const badge = document.getElementById('info-statut');
    badge.textContent = campagne.statut || '-';
    badge.className = campagne.statut === 'CLOTURE' ? 'badge bg-success' : 'badge bg-light-secondary';
    btnValidate.disabled = (campagne.statut === 'CLOTURE');
}

async function loadCampagne(){
    if(!campagneId){
        toast('Identifiant de campagne manquant', false);
        btnValidate.disabled = true;
        return;
    }
    const res = await fetch('<?= Flight::base() ?>/api/stock/inventaire/campagnes');
    const json = await res.json();
    if(!json.success){ toast(json.message || 'Erreur campagnes', false); return; }
    campagne = json.data.find(c => parseInt(c.id_inventaire_campagne,10) === parseInt(campagneId,10)) || null;
    if(!campagne){ toast('Campagne introuvable', false); }
    renderCampagne();
}

function buildLinesRow(r){
    const tr=document.createElement('tr');
    const valEcart = (r.valeur_physique ?? 0) - (r.valeur_theorique ?? 0);
    const locked = campagne && campagne.statut === 'CLOTURE';
    const disabledAttr = locked ? 'disabled' : '';
    tr.innerHTML=`<td>${(r.code||'')} • ${(r.designation||'')}</td>
        <td>${r.depot_nom||''}</td>
        <td class="text-end">${r.quantite_theorique ?? ''}</td>
        <td class="text-end">${r.quantite_comptee ?? ''}</td>
        <td class="text-end">${r.ecart ?? ''}</td>
        <td>${r.type_ecart || ''}</td>
        <td class="text-end">${valEcart.toFixed ? valEcart.toFixed(2): valEcart}</td>
        <td><input type="number" step="0.001" class="form-control form-control-sm" data-article="${r.id_article}" data-depot="${r.id_depot}" value="${r.quantite_comptee ?? ''}" ${disabledAttr}></td>
        <td><button class="btn btn-sm btn-outline-primary" data-action="save" data-article="${r.id_article}" data-depot="${r.id_depot}" ${disabledAttr}>Enregistrer</button></td>`;
    return tr;
}

async function loadLines(){
    if(!campagneId){ linesBody.innerHTML='<tr><td colspan="9" class="text-center text-muted">Campagne manquante</td></tr>'; return; }
    linesBody.innerHTML='<tr><td colspan="9" class="text-center text-muted">Chargement...</td></tr>';
    const url = `<?= Flight::base() ?>/api/stock/inventaire/fiche?campagne=${campagneId}`;
    const res = await fetch(url);
    const json = await res.json();
    if(!json.success){ linesBody.innerHTML=''; toast(json.message||'Erreur lignes',false); return; }
    if(!json.data.length){ linesBody.innerHTML='<tr><td colspan="9" class="text-center text-muted">Aucune ligne</td></tr>'; return; }
    linesBody.innerHTML='';
    json.data.forEach(r=> linesBody.appendChild(buildLinesRow(r)) );
}

linesBody.addEventListener('click', async (e)=>{
    if(e.target.dataset.action === 'save'){
        if(!campagneId){ toast('Campagne manquante', false); return; }
        const art = parseInt(e.target.dataset.article,10);
        const dep = parseInt(e.target.dataset.depot,10);
        const input = linesBody.querySelector(`input[data-article="${art}"][data-depot="${dep}"]`);
        const val = input.value;
        if(val === ''){ toast('Quantité requise', false); return; }
        const payload = {
            id_inventaire_campagne: parseInt(campagneId,10),
            id_depot: dep,
            id_article: art,
            quantite_comptee: parseFloat(val)
        };
        const res = await fetch('<?= Flight::base() ?>/api/stock/inventaire/comptages/save', {
            method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)
        });
        const json = await res.json();
        if(!json.success){ toast(json.message||'Erreur sauvegarde', false); return; }
        toast('Ligne mise à jour');
        await loadLines();
    }
});

document.getElementById('btn-reload-lines').addEventListener('click', loadLines);
btnValidate.addEventListener('click', async ()=>{
    if(!campagneId){ toast('Campagne manquante', false); return; }
    if(campagne && campagne.statut === 'CLOTURE'){ toast('Déjà clôturée', false); return; }
    if(!confirm('Valider et clôturer cette campagne ?')) return;
    const res = await fetch(`<?= Flight::base() ?>/api/stock/inventaire/campagnes/${campagneId}/valider`, {method:'POST'});
    const json = await res.json();
    if(!json.success){ toast(json.message||'Erreur validation', false); return; }
    toast('Campagne validée');
    await loadCampagne();
    await loadLines();
});

loadCampagne();
loadLines();
</script>
</body>
</html>
