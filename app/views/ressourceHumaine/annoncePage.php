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

            <?php foreach($annonces as $annonce): ?>
            <div class="content-wrapper container">
                
                <!-- En-tête -->
                <div class="page-heading mb-4">
                    <h2 class="fw-bold"><?= htmlspecialchars($annonce['titre']) ?></h2>
                    <p class="text-muted"><?= htmlspecialchars($annonce['ville']) ?>· <?= date('d/m/Y', strtotime($annonce['date_debut'])) ?></p>
                </div>
<div class="col-lg-8 col-md-12">
                            <div class="page-content">
                    <section class="row">
                        
                        <div class="col-lg-8 col-md-12">
                            <div class="card mb-4 p-4">
                                <h5 class="mb-3">Competences requise :</h5>
                                <p>
                                    <?= htmlspecialchars($annonce['competences']) ?>
                                </p>

                                <h6 class="mt-4">Diplomes necessaires :</h6>
                                <p>
                                    <?= htmlspecialchars($annonce['diplomes']) ?>
                                </p>

                                <h6 class="mt-4">Qualite recherche :</h6>
                                <p>
                                    <?= htmlspecialchars($annonce['qualite']) ?>
                                </p>
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
                                <a href="<?= Flight::base() ?>/candidature?id_annonce=<?= $annonce['id_annonce'] ?>" class="btn btn-primary w-100 mt-2">
                                    <i class="bi bi-send me-2"></i> Postuler
                                </a>
                            </div>
                        </div>

                    </section>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/horizontal-layout.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
</body>

</html>
