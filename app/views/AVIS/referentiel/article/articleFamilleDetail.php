<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Famille Article</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
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
                        <h3 id="familleName">Chargement...</h3>
                        <p class="text-subtitle text-muted">Détails de la famille d'article</p>
                    </div>
                    <div class="col-12 col-md-4 text-end">
                        <a href="<?= Flight::base() ?>/referentiel/article-famille/list" class="btn btn-light">Retour à la liste</a>
                    </div>
                </div>
            </div>

            <!-- INFOS FAMILLE -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-5">Code</label>
                                    <p id="code">-</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-5">Nom</label>
                                    <p id="nom">-</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <a href="<?= Flight::base() ?>/referentiel/article-famille/saisie?id=<?= Flight::request()->query->id ?>" class="btn btn-primary">Modifier</a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-5">Description</label>
                            <p id="description">-</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ARTICLES -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Articles de cette famille</h5>
                    </div>
                    <div class="card-body">
                        <div id="loadingSpinner" class="text-center py-5" style="display:none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                        <div id="emptyState" class="text-center py-5">
                            <p class="text-muted">Aucun article dans cette famille</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="articlesTable" style="display:none;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Désignation</th>
                                        <th>Valorisation</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script>
const base = '<?= Flight::base() ?>';
const params = new URLSearchParams(window.location.search);
const id_article_famille = params.get('id');

async function fetchJSON(url, opts) { const r = await fetch(url, opts); return r.json(); }

let dataTable = null;

async function loadFamille() {
    if (!id_article_famille) {
        document.getElementById('familleName').textContent = 'Famille non trouvée';
        return;
    }
    
    try {
        const response = await fetch(base + '/api/referentiel/articles/familles/' + id_article_famille);
        const r = await response.json();
        if (r.success) {
            const f = r.data;
            document.getElementById('familleName').textContent = f.nom;
            document.getElementById('code').textContent = f.code;
            document.getElementById('nom').textContent = f.nom;
            document.getElementById('description').textContent = f.description || '-';
            loadArticles();
        } else {
            document.getElementById('familleName').textContent = 'Erreur de chargement';
        }
    } catch (err) {
        console.error('Erreur:', err);
        document.getElementById('familleName').textContent = 'Erreur réseau';
    }
}

async function loadArticles() {
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('articlesTable').style.display = 'none';
    
    try {
        const r = await fetchJSON(base + '/api/referentiel/articles/famille/' + id_article_famille);
        if (r.success) {
            const articles = r.data || [];
            
            if (articles.length === 0) {
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('emptyState').style.display = 'block';
                return;
            }
            
            const tb = document.querySelector('#articlesTable tbody');
            tb.innerHTML = '';
            articles.forEach(a => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><small class="text-muted">${a.id_article}</small></td>
                    <td><strong>${a.code}</strong></td>
                    <td>
                        <a href="${base}/referentiel/article/detail?id=${a.id_article}" class="text-decoration-none text-primary">
                            ${a.designation}
                        </a>
                    </td>
                    <td>${a.methode_valorisation || '-'}</td>
                    <td class="text-end">
                        <a href="${base}/referentiel/article/saisie?id=${a.id_article}" class="btn btn-sm btn-outline-secondary">Voir</a>
                    </td>`;
                tb.appendChild(tr);
            });
            
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('articlesTable').style.display = 'table';
            
            if (dataTable) { dataTable.destroy(); }
            dataTable = new simpleDatatables.DataTable("#articlesTable", {
                searchable: false,
                fixedHeight: false,
                perPage: 25
            });
        }
    } catch (err) {
        console.error('Erreur:', err);
    }
}

loadFamille();
</script>
</body>
</html>
