<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail Fournisseur</title>
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
                        <h3 id="fournisseurName">Chargement...</h3>
                        <p class="text-subtitle text-muted">Détails du fournisseur</p>
                    </div>
                    <div class="col-12 col-md-4 text-end">
                        <a href="<?= Flight::base() ?>/referentiel/fournisseur/list" class="btn btn-light">Retour à la liste</a>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-5">Nom</label>
                                    <p id="nom">-</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-5">Email</label>
                                    <p id="email">-</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-5">Téléphone</label>
                                    <p id="telephone">-</p>
                                </div>
                                <div class="mb-3">
                                    <a href="<?= Flight::base() ?>/referentiel/fournisseur/saisie?id=<?= Flight::request()->query->id ?>" class="btn btn-primary">Modifier</a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-5">Adresse</label>
                            <p id="adresse">-</p>
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
const params = new URLSearchParams(window.location.search);
const id_fournisseur = params.get('id');

async function loadFournisseur() {
    if (!id_fournisseur) {
        document.getElementById('fournisseurName').textContent = 'Fournisseur non trouvé';
        return;
    }
    
    try {
        const response = await fetch(base + '/api/referentiel/fournisseurs/' + id_fournisseur);
        const r = await response.json();
        if (r.success) {
            const f = r.data;
            document.getElementById('fournisseurName').textContent = f.nom;
            document.getElementById('nom').textContent = f.nom;
            document.getElementById('email').textContent = f.email || '-';
            document.getElementById('telephone').textContent = f.telephone || '-';
            document.getElementById('adresse').textContent = f.adresse || '-';
        } else {
            document.getElementById('fournisseurName').textContent = 'Erreur de chargement';
        }
    } catch (err) {
        console.error('Erreur:', err);
        document.getElementById('fournisseurName').textContent = 'Erreur réseau';
    }
}

loadFournisseur();
</script>
</body>
</html>
