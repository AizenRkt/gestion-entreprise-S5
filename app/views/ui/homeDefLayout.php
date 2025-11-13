<?php
if (isset($_GET['mssg'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Toastify({
                text: '" . addslashes($_GET['mssg']) . "',
                duration: 4000,
                gravity: 'top',
                position: 'right',
                backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)',
                close: true
            }).showToast();
        });
    </script>";
    unset($_GET['mssg']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - BackOffice Mazer Entreprise</title>

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
                        <h3>Bienvenue sur Mazer BackOffice</h3>
                        <p class="text-subtitle text-muted">Votre espace de gestion professionnelle</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/backOffice">Accueil</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Section hero -->
            <section class="section">
                <div class="card">
                    <div class="card-body py-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="card-title mb-3">Espace de travail collaboratif</h4>
                                <p class="card-text text-muted">
                                    Bienvenue dans votre espace de gestion BackOffice. Utilisez le menu latéral pour accéder aux différentes fonctionnalités selon vos permissions. Toutes les sections sont organisées pour faciliter votre navigation et optimiser votre productivité.
                                </p>
                                <div class="mt-4">
                                    <span class="badge bg-light-primary me-2"><i class="bi bi-shield-check me-1"></i>Sécurisé</span>
                                    <span class="badge bg-light-success me-2"><i class="bi bi-clock-history me-1"></i>Disponible 24/7</span>
                                    <span class="badge bg-light-info"><i class="bi bi-people me-1"></i>Multi-utilisateurs</span>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <img src="<?= Flight::base() ?>/public/template/assets/static/images/logo/logo.svg" alt="Illustration" class="img-fluid" style="max-width: 180px;">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Fonctionnalités principales -->
            <section class="section">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-4">Fonctionnalités principales</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon purple mb-0 me-3">
                                        <i class="iconly-boldProfile"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted font-semibold mb-1">Gestion du personnel</h6>
                                        <p class="text-xs text-muted mb-0">Employés et services</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon blue mb-0 me-3">
                                        <i class="iconly-boldDocument"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted font-semibold mb-1">Documents</h6>
                                        <p class="text-xs text-muted mb-0">Gestion documentaire</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon green mb-0 me-3">
                                        <i class="iconly-boldChart"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted font-semibold mb-1">Statistiques</h6>
                                        <p class="text-xs text-muted mb-0">Rapports et analyses</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon red mb-0 me-3">
                                        <i class="iconly-boldSetting"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted font-semibold mb-1">Paramètres</h6>
                                        <p class="text-xs text-muted mb-0">Configuration système</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sections d'information -->
            <section class="section">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Guide de démarrage rapide</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <div class="d-flex">
                                            <div class="me-3 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 32px; height: 32px;">
                                                <i class="bi bi-compass"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Explorez le menu</h6>
                                                <p class="text-muted small mb-0">
                                                    Utilisez le menu latéral pour découvrir toutes les fonctionnalités disponibles.
                                                </p>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="mb-3">
                                        <div class="d-flex">
                                            <div class="me-3 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 32px; height: 32px;">
                                                <i class="bi bi-layout-text-sidebar-reverse"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Accédez à vos sections</h6>
                                                <p class="text-muted small mb-0">
                                                    Cliquez sur les sections auxquelles vous avez accès selon vos permissions.
                                                </p>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="mb-0">
                                        <div class="d-flex">
                                            <div class="me-3 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 32px; height: 32px;">
                                                <i class="bi bi-gear-fill"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Gérez vos données</h6>
                                                <p class="text-muted small mb-0">
                                                    Consultez, créez et modifiez les informations selon vos droits.
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Informations utiles</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-light-info mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-info-circle fs-4 me-3"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Navigation intuitive</h6>
                                            <p class="mb-0 small">Toutes les sections sont accessibles via le menu principal. Votre niveau d'accès détermine les fonctionnalités disponibles.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-light-success mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-shield-check fs-4 me-3"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Sécurité renforcée</h6>
                                            <p class="mb-0 small">Vos actions sont enregistrées et sécurisées. Chaque utilisateur dispose de permissions spécifiques.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-light-warning mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clock-history fs-4 me-3"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Support disponible</h6>
                                            <p class="mb-0 small">En cas de question, contactez votre administrateur système pour obtenir de l'aide.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section liens rapides -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Raccourcis utiles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6 mb-3">
                                <a href="#" class="text-decoration-none">
                                    <div class="p-3 border rounded text-center hover-shadow">
                                        <i class="bi bi-question-circle text-primary fs-1 mb-2"></i>
                                        <p class="mb-0 text-muted small">Aide</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="#" class="text-decoration-none">
                                    <div class="p-3 border rounded text-center hover-shadow">
                                        <i class="bi bi-book text-success fs-1 mb-2"></i>
                                        <p class="mb-0 text-muted small">Documentation</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="#" class="text-decoration-none">
                                    <div class="p-3 border rounded text-center hover-shadow">
                                        <i class="bi bi-envelope text-info fs-1 mb-2"></i>
                                        <p class="mb-0 text-muted small">Contact</p>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="#" class="text-decoration-none">
                                    <div class="p-3 border rounded text-center hover-shadow">
                                        <i class="bi bi-gear text-warning fs-1 mb-2"></i>
                                        <p class="mb-0 text-muted small">Préférences</p>
                                    </div>
                                </a>
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
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/toastify.js"></script>

<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}
</style>

</body>
</html>