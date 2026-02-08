<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campagnes d'inventaire</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <style>
        .card h6 { font-size: 0.95rem; }
        .small-label { font-size: 0.85rem; color: #6c757d; }
        #campaign-table tbody tr { cursor: pointer; }
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
                    <h3>Campagnes d'inventaire</h3>
                    <p class="text-muted mb-0">Consultez les campagnes et ouvrez le détail pour valider.</p>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <div class="input-group input-group-sm" style="max-width:220px;">
                        <span class="input-group-text">Code</span>
                        <input type="text" class="form-control" placeholder="Rechercher" id="search-code">
                    </div>
                    <select class="form-select form-select-sm" id="filter-type" style="max-width:150px;">
                        <option value="">Type (tous)</option>
                        <option value="GENERAL">GENERAL</option>
                        <option value="PARTIEL">PARTIEL</option>
                        <option value="CYCLE">CYCLE</option>
                    </select>
                    <select class="form-select form-select-sm" id="filter-statut" style="max-width:150px;">
                        <option value="">Statut (tous)</option>
                        <option value="PLANIFIE">PLANIFIE</option>
                        <option value="BROUILLON">BROUILLON</option>
                        <option value="EN_COURS">EN_COURS</option>
                        <option value="CLOTURE">CLOTURE</option>
                    </select>
                    <button class="btn btn-outline-secondary btn-sm" id="btn-refresh">Rafraîchir</button>
                </div>
            </div>
            <div class="page-content">
                <section class="row g-3">
                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="table-responsive" style="max-height:75vh;">
                                    <table class="table table-hover align-middle mb-0" id="campaign-table">
                                        <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Libellé</th>
                                            <th>Type</th>
                                            <th>Début</th>
                                            <th>Fin</th>
                                            <th>Statut</th>
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
<script>
const campaignsBody = document.querySelector('#campaign-table tbody');
const searchInput = document.getElementById('search-code');
const filterType = document.getElementById('filter-type');
const filterStatut = document.getElementById('filter-statut');
let campaigns = [];

function toast(msg, ok=true){ Toastify({text: msg, gravity:'top', position:'right', className: ok?'bg-success':'bg-danger', duration: 2500}).showToast(); }

function renderCampaigns(){
    campaignsBody.innerHTML = '';
    const term = searchInput.value.trim().toLowerCase();
    const type = filterType.value;
    const statut = filterStatut.value;
    const filtered = campaigns.filter(c => {
        const matchCode = !term || (c.code || '').toLowerCase().includes(term);
        const matchType = !type || c.type_campagne === type;
        const matchStatut = !statut || c.statut === statut;
        return matchCode && matchType && matchStatut;
    });
    if(!filtered.length){
        campaignsBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Aucune campagne</td></tr>';
        return;
    }
    filtered.forEach(c=>{
        const tr=document.createElement('tr');
        const badgeClass = c.statut === 'CLOTURE' ? 'bg-success' : 'bg-light-secondary';
        const dateDebut = c.date_debut_prevue || c.date_debut || '';
        const dateFin = c.date_fin_prevue || c.date_fin || '';
        const detailUrl = `<?= Flight::base() ?>/stock/inventaire/validation/${c.id_inventaire_campagne}`;
        tr.innerHTML=`<td>${c.code}</td>
            <td>${c.libelle}</td>
            <td>${c.type_campagne || ''}</td>
            <td>${dateDebut ? dateDebut.substring(0,10) : ''}</td>
            <td>${dateFin ? dateFin.substring(0,10) : ''}</td>
            <td><span class="badge ${badgeClass}">${c.statut}</span></td>
            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="${detailUrl}">Détails</a></td>`;
        campaignsBody.appendChild(tr);
    });
}

document.getElementById('btn-refresh').addEventListener('click', loadCampaigns);

async function loadCampaigns(){
    campaignsBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Chargement...</td></tr>';
    const res = await fetch('<?= Flight::base() ?>/api/stock/inventaire/campagnes');
    const json = await res.json();
    if(!json.success){ campaignsBody.innerHTML=''; toast(json.message||'Erreur campagnes',false); return; }
    campaigns = json.data || [];
    renderCampaigns();
}

searchInput.addEventListener('input', renderCampaigns);
filterType.addEventListener('change', renderCampaigns);
filterStatut.addEventListener('change', renderCampaigns);

loadCampaigns();
</script>
</body>
</html>
