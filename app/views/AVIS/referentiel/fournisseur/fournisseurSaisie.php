<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie Fournisseur</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
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
                        <h3 id="pageTitle">Nouveau Fournisseur</h3>
                        <p class="text-subtitle text-muted">Saisie et gestion du fournisseur</p>
                    </div>
                    <div class="col-12 col-md-4 text-end">
                        <a href="<?= Flight::base() ?>/referentiel/fournisseur/list" class="btn btn-light">Retour à la liste</a>
                    </div>
                </div>
            </div>

            <section class="section">
                <!-- FORMULAIRE FOURNISSEUR -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations du fournisseur</h5>
                    </div>
                    <div class="card-body">
                        <form id="fournisseurForm" method="POST">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" id="nom" name="nom" class="form-control" placeholder="Nom du fournisseur" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" id="telephone" name="telephone" class="form-control" placeholder="+261...">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="email@example.com">
                                </div>
                                <div class="col-md-6"></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Adresse</label>
                                    <textarea id="adresse" name="adresse" class="form-control" placeholder="Adresse complète du fournisseur" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Enregistrer</button>
                                    <a href="<?= Flight::base() ?>/referentiel/fournisseur/list" class="btn btn-light ms-2">Annuler</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ARTICLES FOURNIS -->
                <div class="card mt-4" id="articlesCard" style="display:none;">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Articles fournis</h5>
                        <button class="btn btn-sm btn-primary" id="addArticleBtn" data-bs-toggle="modal" data-bs-target="#addArticleModal">
                            + Ajouter article
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="loadingSpinner" class="text-center py-5" style="display:none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                        <div id="emptyState" class="text-center py-5">
                            <p class="text-muted">Aucun article associé à ce fournisseur</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="articlesTable" style="display:none;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Désignation</th>
                                        <th>Prix d'achat</th>
                                        <th>Délai livraison (jours)</th>
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

<!-- Modal Ajouter Article -->
<div class="modal fade" id="addArticleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addArticleForm">
                    <div class="mb-3">
                        <label class="form-label">Article *</label>
                        <select id="id_article" name="id_article" class="form-select" required>
                            <option value="">-- Sélectionner un article --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prix d'achat</label>
                        <input type="number" id="prix_achat" name="prix_achat" class="form-control" step="0.01" placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Délai livraison (jours)</label>
                        <input type="number" id="delai_livraison" name="delai_livraison" class="form-control" min="0" placeholder="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="submitArticleBtn">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
<script>
const base = '<?= Flight::base() ?>';
const params = new URLSearchParams(window.location.search);
const id_fournisseur = params.get('id');

async function fetchJSON(url, opts) { const r = await fetch(url, opts); return r.json(); }

let articlesData = [];
let dataTable = null;

// Load supplier data if editing
async function loadFournisseur() {
    if (!id_fournisseur) return;
    
    try {
        const r = await fetchJSON(base + '/api/referentiel/fournisseurs/' + id_fournisseur);
        if (r.success) {
            document.getElementById('pageTitle').textContent = 'Modifier ' + r.data.nom;
            document.getElementById('nom').value = r.data.nom;
            document.getElementById('telephone').value = r.data.telephone || '';
            document.getElementById('email').value = r.data.email || '';
            document.getElementById('adresse').value = r.data.adresse || '';
            document.getElementById('articlesCard').style.display = 'block';
            loadArticles();
        }
    } catch (err) {
        console.error('Erreur:', err);
    }
}

// Load articles for dropdown
async function loadArticlesDropdown() {
    try {
        const r = await fetchJSON(base + '/api/referentiel/articles/all');
        if (r.success) {
            const sel = document.getElementById('id_article');
            r.data.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id_article;
                opt.textContent = a.code + ' - ' + a.designation;
                sel.appendChild(opt);
            });
        }
    } catch (err) {
        console.error('Erreur:', err);
    }
}

// Load articles supplied by this supplier
async function loadArticles() {
    if (!id_fournisseur) return;
    
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('articlesTable').style.display = 'none';
    
    try {
        const r = await fetchJSON(base + '/api/referentiel/fournisseurs/' + id_fournisseur + '/articles');
        if (r.success) {
            articlesData = r.data || [];
            renderArticles(articlesData);
        }
    } catch (err) {
        console.error('Erreur:', err);
    }
}

function renderArticles(rows) {
    document.getElementById('loadingSpinner').style.display = 'none';
    
    if (rows.length === 0) {
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('articlesTable').style.display = 'none';
        return;
    }
    
    const tb = document.querySelector('#articlesTable tbody');
    tb.innerHTML = '';
    rows.forEach(a => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><small class="text-muted">${a.id_fournisseur_article}</small></td>
            <td>${a.article_code}</td>
            <td>${a.article_designation}</td>
            <td>${a.prix_achat ? Number(a.prix_achat).toFixed(2) + ' Ar' : '-'}</td>
            <td>${a.delai_livraison || '-'}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary edit-article-btn" data-id="${a.id_fournisseur_article}" data-price="${a.prix_achat || ''}" data-delay="${a.delai_livraison || ''}">Modifier</button>
                <button class="btn btn-sm btn-outline-danger ms-1 delete-article-btn" data-id="${a.id_fournisseur_article}">Supprimer</button>
            </td>`;
        tb.appendChild(tr);
    });
    
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('articlesTable').style.display = 'table';
    
    if (dataTable) { dataTable.destroy(); }
    dataTable = new simpleDatatables.DataTable("#articlesTable", {
        searchable: false,
        fixedHeight: false,
        perPage: 15
    });
}

// Handle form submission
document.getElementById('fournisseurForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';
    
    try {
        const method = id_fournisseur ? 'PUT' : 'POST';
        const url = id_fournisseur ? 
            base + '/api/referentiel/fournisseurs/' + id_fournisseur :
            base + '/api/referentiel/fournisseurs';
        
        const data = {
            nom: document.getElementById('nom').value,
            telephone: document.getElementById('telephone').value || null,
            email: document.getElementById('email').value || null,
            adresse: document.getElementById('adresse').value || null
        };
        
        const r = await fetchJSON(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        if (r.success) {
            Toastify({text: r.message, duration: 2000, backgroundColor: 'green'}).showToast();
            if (!id_fournisseur) {
                // Redirect to edit page with new ID
                setTimeout(() => {
                    window.location.href = base + '/referentiel/fournisseur/saisie?id=' + r.id;
                }, 500);
            } else {
                document.getElementById('articlesCard').style.display = 'block';
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

// Add article
document.getElementById('submitArticleBtn').addEventListener('click', async () => {
    const id_article = document.getElementById('id_article').value;
    if (!id_article) {
        Toastify({text: 'Veuillez sélectionner un article', duration: 3000, backgroundColor: 'orange'}).showToast();
        return;
    }
    
    const btn = document.getElementById('submitArticleBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Ajout...';
    
    try {
        const data = {
            id_fournisseur: id_fournisseur,
            id_article: id_article,
            prix_achat: document.getElementById('prix_achat').value || null,
            delai_livraison: document.getElementById('delai_livraison').value || null
        };
        
        const r = await fetchJSON(base + '/api/referentiel/fournisseurs/' + id_fournisseur + '/articles', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        if (r.success) {
            Toastify({text: r.message, duration: 2000, backgroundColor: 'green'}).showToast();
            document.getElementById('addArticleForm').reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById('addArticleModal'));
            modal.hide();
            loadArticles();
        } else {
            Toastify({text: r.message || 'Erreur', duration: 3000, backgroundColor: 'red'}).showToast();
        }
    } catch (err) {
        Toastify({text: 'Erreur réseau', duration: 3000, backgroundColor: 'red'}).showToast();
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Ajouter';
    }
});

// Delete article
document.addEventListener('click', async (e) => {
    if (e.target.matches('.delete-article-btn')) {
        const id = e.target.getAttribute('data-id');
        if (!confirm('Supprimer cet article ?')) return;
        
        const btn = e.target;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        
        try {
            const r = await fetchJSON(base + '/api/referentiel/fournisseurs/articles/' + id, {method:'DELETE'});
            if (r.success) {
                Toastify({text: r.message, duration: 2000, backgroundColor: 'green'}).showToast();
                loadArticles();
            } else {
                Toastify({text: r.message || 'Erreur', duration: 3000, backgroundColor: 'red'}).showToast();
                btn.disabled = false;
                btn.innerHTML = 'Supprimer';
            }
        } catch (err) {
            Toastify({text: 'Erreur réseau', duration: 3000, backgroundColor: 'red'}).showToast();
            btn.disabled = false;
            btn.innerHTML = 'Supprimer';
        }
    }
});

// Initialize
loadFournisseur();
loadArticlesDropdown();
</script>
</body>
</html>
