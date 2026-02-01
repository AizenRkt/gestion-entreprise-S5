<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie Article</title>
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
                        <h3>Saisie Article</h3>
                        <p class="text-subtitle text-muted">Créer ou modifier un article</p>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h4 class="card-title mb-0">Formulaire</h4>
                                <span class="badge bg-secondary" id="formMode">Nouveau</span>
                            </div>
                            <div class="card-body">
                                <form id="articleForm">
                                    <input type="hidden" id="id_article" value="">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Code</label>
                                            <input class="form-control" id="code" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Désignation</label>
                                            <input class="form-control" id="designation" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Famille</label>
                                            <select id="id_famille_article_famille" class="form-select" required></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Méthode valorisation</label>
                                            <select id="id_methode_valorisation" class="form-select">
                                                <option value="1">FIFO</option>
                                                <option value="2">LIFO</option>
                                                <option value="3">CUMP</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Unité</label>
                                            <input class="form-control" id="unite">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Prix achat</label>
                                            <input type="number" step="0.01" class="form-control" id="prix_achat">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Prix vente</label>
                                            <input type="number" step="0.01" class="form-control" id="prix_vente">
                                        </div>
                                        <div class="col-12 d-flex justify-content-end mt-2">
                                            <button type="submit" class="btn btn-primary me-2">Enregistrer</button>
                                            <a href="<?= Flight::base() ?>/referentiel/article/list" class="btn btn-light">Retour</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Aide</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Remplissez le formulaire puis cliquez sur Enregistrer.</p>
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
<script>
const base = '<?= Flight::base() ?>';

async function fetchJSON(url, opts) {
    const r = await fetch(url, opts);
    return r.json();
}

async function loadFamilles() {
    const r = await fetchJSON(base + '/api/referentiel/articles/familles/all');
    const sel = document.getElementById('id_famille_article_famille');
    sel.innerHTML = '';
    if (r.success) {
        r.data.forEach(f => { const o=document.createElement('option'); o.value=f.id_article_famille; o.textContent = f.nom; sel.appendChild(o); });
    }
}

document.getElementById('articleForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const id = document.getElementById('id_article').value;
    const payload = {
        code: document.getElementById('code').value,
        designation: document.getElementById('designation').value,
        id_famille_article_famille: document.getElementById('id_famille_article_famille').value,
        id_methode_valorisation: document.getElementById('id_methode_valorisation').value,
        unite: document.getElementById('unite').value,
        prix_achat: document.getElementById('prix_achat').value || null,
        prix_vente: document.getElementById('prix_vente').value || null
    };
    try {
        let res;
        if (!id) {
            res = await fetchJSON(base + '/api/referentiel/articles/create', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        } else {
            res = await fetchJSON(base + '/api/referentiel/articles/' + id, {method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
        }
        if (res.success) {
            Toastify({text: res.message, duration:3000, gravity:'top', position:'right', backgroundColor:'green'}).showToast();
            setTimeout(()=> { window.location = base + '/referentiel/article/list'; }, 800);
        } else {
            Toastify({text: res.message || 'Erreur', duration:4000, gravity:'top', position:'right', backgroundColor:'red'}).showToast();
        }
    } catch (err) {
        Toastify({text: err.message, duration:4000, gravity:'top', position:'right', backgroundColor:'red'}).showToast();
    }
});

// If editing via query param ?id=NN fill the form
(function init(){
    loadFamilles();
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    if (id) {
        document.getElementById('formMode').textContent = 'Modification';
        fetchJSON(base + '/api/referentiel/articles/' + id).then(r=>{
            if (r.success && r.data) {
                const a = r.data;
                document.getElementById('id_article').value = a.id_article;
                document.getElementById('code').value = a.code;
                document.getElementById('designation').value = a.designation;
                document.getElementById('id_famille_article_famille').value = a.id_famille_article_famille;
                document.getElementById('id_methode_valorisation').value = a.id_methode_valorisation || 1;
                document.getElementById('unite').value = a.unite || '';
                document.getElementById('prix_achat').value = a.prix_achat || '';
                document.getElementById('prix_vente').value = a.prix_vente || '';
            }
        });
    }
})();
</script>
</body>
</html>
