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
                                <form id="formCompte" action="<?= Flight::base() ?>/backOffice/user/update" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">username</label>
                                            <input type="text" class="form-control" name="username" value="<?= $_SESSION['user']['username'] ?>" required>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card mt-4 border-danger">
                            <div class="card-header">
                                <h5 class="text-danger"><i class="bi bi-exclamation-triangle"></i> Zone de danger</h5>
                            </div>
                            <div class="card-body">
                                <p>vous pouvez descativer votre compte. Cette action ne peut être réversible que par l'admin</p>
                                <button class="btn btn-outline-danger" id="disableAccount">
                                    <i class="bi bi-plugin"></i> désactiver mon compte
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
document.getElementById('btnDeleteAccount').addEventListener('click', function () {
    if (confirm("Êtes-vous sûr de vouloir supprimer votre compte ?")) {
        fetch("<?= Flight::base() ?>/backOffice/user/delete", { method: "POST" })
            .then(() => {
                Toastify({
                    text: "Compte supprimé avec succès",
                    backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    duration: 3000
                }).showToast();
                setTimeout(() => window.location.href = "<?= Flight::base() ?>/logout", 1500);
            })
            .catch(() => {
                Toastify({
                    text: "Erreur lors de la suppression du compte",
                    backgroundColor: "red",
                    duration: 3000
                }).showToast();
            });
    }
});
</script>

</body>
</html>
