<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page annonce - Mazer Corporation</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">
</head>

<body>
    <div id="app">
        <div id="main" class="layout-horizontal">
            <?= Flight::menuFrontOffice() ?>

            <div class="content-wrapper container">
                
                <!-- En-tête -->
                <div class="page-heading mb-4">
                    <h2 class="fw-bold">Développeur Full Stack</h2>
                    <p class="text-muted">CDI · Antananarivo · Publié le 15/09/2025</p>
                </div>

                <div class="page-content">
                    <section class="row">
                        
                        <!-- Détails annonce -->
                        <div class="col-lg-8 col-md-12">
                            <div class="card mb-4 p-4">
                                <h5 class="mb-3">Description du poste</h5>
                                <p>
                                    Nous recherchons un développeur Full Stack passionné pour rejoindre notre équipe.
                                    Vous participerez à la conception, au développement et au déploiement
                                    d’applications web et mobiles pour nos clients internationaux.
                                </p>

                                <h6 class="mt-4">Missions principales :</h6>
                                <ul>
                                    <li>Développer et maintenir des applications web modernes.</li>
                                    <li>Collaborer avec l’équipe design et produit.</li>
                                    <li>Participer aux revues de code et proposer des améliorations.</li>
                                    <li>Assurer la qualité et la sécurité des livrables.</li>
                                </ul>

                                <h6 class="mt-4">Profil recherché :</h6>
                                <ul>
                                    <li>Expérience en développement web (JavaScript, PHP, ou équivalent).</li>
                                    <li>Connaissance des frameworks modernes (React, Laravel, etc.).</li>
                                    <li>Bon esprit d’équipe et capacité d’adaptation.</li>
                                    <li>Maîtrise du français, l’anglais est un plus.</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Sidebar entreprise -->
                        <div class="col-lg-4 col-md-12">
                            <div class="card p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" 
                                         alt="Mazer Entreprise" 
                                         class="me-3" style="width:60px;">
                                    <div>
                                        <h6 class="mb-0">Mazer Entreprise</h6>
                                        <small class="text-muted">Technologie & Innovation</small>
                                    </div>
                                </div>
                                <p>
                                    Mazer est un acteur majeur dans le développement de solutions digitales innovantes.
                                    Nous aidons nos clients à transformer leurs idées en produits performants.
                                </p>
                                <a href="<?= Flight::base() ?>/candidature" class="btn btn-primary w-100 mt-2">
                                    <i class="bi bi-send me-2"></i> Postuler
                                </a>
                            </div>
                        </div>

                    </section>
                </div>

            </div>
        </div>
    </div>

    <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/horizontal-layout.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
</body>

</html>
