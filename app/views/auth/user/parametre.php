<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres du compte - BackOffice</title>

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
                        <h3>Paramètres du compte</h3>
                        <p class="text-subtitle text-muted">Gérez vos informations personnelles et vos préférences.</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/backOffice">Accueil</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Paramètre</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Informations personnelles</h5>
                            </div>
                            <div class="card-body">
                                <form id="formCompte" action="<?= Flight::base() ?>/backOffice/user/update" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom</label>
                                            <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($employe['nom'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Prénom</label>
                                            <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($employe['prenom'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($employe['email'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Téléphone</label>
                                            <input type="text" class="form-control" name="telephone" value="<?= htmlspecialchars($employe['telephone'] ?? '') ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($_SESSION['user']['username'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Genre</label>
                                            <select class="form-select" name="genre">
                                                <option value="M" <?= ($employe['genre'] ?? '') == 'M' ? 'selected' : '' ?>>Masculin</option>
                                                <option value="F" <?= ($employe['genre'] ?? '') == 'F' ? 'selected' : '' ?>>Féminin</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Poste</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($employe['poste_titre'] ?? '') ?>" readonly disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date d'embauche</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($employe['date_embauche'] ?? '') ?>" readonly disabled>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Mettre à jour
                                        </button>
                                        <a href="<?= Flight::base() ?>/backOffice" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Retour
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card mt-4 border-danger">
                            <div class="card-header">
                                <h5 class="text-danger"><i class="bi bi-exclamation-triangle"></i> Zone de danger</h5>
                            </div>
                            <div class="card-body">
                                <p>Vous pouvez désactiver votre compte. Cette action ne peut être réversible que par l'admin.</p>
                                <button class="btn btn-outline-danger" id="disableAccount">
                                    <i class="bi bi-plugin"></i> Désactiver mon compte
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la soumission du formulaire
    document.getElementById('formCompte').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch("<?= Flight::base() ?>/backOffice/user/update", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toastify({
                    text: data.message,
                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                    duration: 3000
                }).showToast();
            } else {
                Toastify({
                    text: data.message,
                    backgroundColor: "red",
                    duration: 3000
                }).showToast();
            }
        })
        .catch(error => {
            Toastify({
                text: "Erreur lors de la mise à jour",
                backgroundColor: "red",
                duration: 3000
            }).showToast();
        });
    });

    // Gestion de la désactivation du compte
    document.getElementById('disableAccount').addEventListener('click', function() {
        if (confirm("Êtes-vous sûr de vouloir désactiver votre compte ?")) {
            // Ici vous pouvez ajouter la logique pour désactiver le compte
            Toastify({
                text: "Fonctionnalité de désactivation à implémenter",
                backgroundColor: "orange",
                duration: 3000
            }).showToast();
        }
    });
});
</script>

</body>
</html>