<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie Famille Article</title>
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
                        <h3 id="pageTitle">Nouvelle Famille</h3>
                        <p class="text-subtitle text-muted">Saisie et gestion de la famille d'article</p>
                    </div>
                    <div class="col-12 col-md-4 text-end">
                        <a href="<?= Flight::base() ?>/referentiel/article-famille/list" class="btn btn-light">Retour à la liste</a>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations de la famille</h5>
                    </div>
                    <div class="card-body">
                        <form id="familleForm" method="POST">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Code *</label>
                                    <input type="text" id="code" name="code" class="form-control" placeholder="Code unique" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" id="nom" name="nom" class="form-control" placeholder="Nom de la famille" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea id="description" name="description" class="form-control" placeholder="Description détaillée de la famille" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Enregistrer</button>
                                    <a href="<?= Flight::base() ?>/referentiel/article-famille/list" class="btn btn-light ms-2">Annuler</a>
                                </div>
                            </div>
                        </form>
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
const params = new URLSearchParams(window.location.search);
const id_article_famille = params.get('id');

async function fetchJSON(url, opts) { const r = await fetch(url, opts); return r.json(); }

// Load famille data if editing
async function loadFamille() {
    if (!id_article_famille) return;
    
    try {
        const r = await fetchJSON(base + '/api/referentiel/articles/familles/' + id_article_famille);
        if (r.success) {
            document.getElementById('pageTitle').textContent = 'Modifier ' + r.data.nom;
            document.getElementById('code').value = r.data.code;
            document.getElementById('nom').value = r.data.nom;
            document.getElementById('description').value = r.data.description || '';
        }
    } catch (err) {
        console.error('Erreur:', err);
    }
}

// Handle form submission
document.getElementById('familleForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';
    
    try {
        const method = id_article_famille ? 'PUT' : 'POST';
        const url = id_article_famille ? 
            base + '/api/referentiel/articles/familles/' + id_article_famille :
            base + '/api/referentiel/articles/familles';
        
        const data = {
            code: document.getElementById('code').value,
            nom: document.getElementById('nom').value,
            description: document.getElementById('description').value || null
        };
        
        const r = await fetchJSON(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        if (r.success) {
            Toastify({text: r.message, duration: 2000, backgroundColor: 'green'}).showToast();
            if (!id_article_famille) {
                // Redirect to edit page with new ID
                setTimeout(() => {
                    window.location.href = base + '/referentiel/article-famille/saisie?id=' + r.id;
                }, 500);
            }
        } else {
            Toastify({text: r.message || 'Erreur', duration: 3000, backgroundColor: 'red'}).showToast();
        }
    } catch (err) {
        Toastify({text: 'Erreur réseau', duration: 3000, backgroundColor: 'red'}).showToast();
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Enregistrer';
    }
});

// Initialize
loadFamille();
</script>
</body>
</html>
