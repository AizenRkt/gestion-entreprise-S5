<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie Mouvement de Stock</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
</head>

<body>
<div id="app">
    <!-- Sidebar Mazer -->
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
                        <h3>Saisie Mouvement de Stock</h3>
                        <p class="text-subtitle text-muted">Enregistrer une entrée ou une sortie</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                                <li class="breadcrumb-item"><a href="#">Stock</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Saisie Mouvement</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Formulaire de saisie -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nouveau Mouvement</h4>
                    </div>
                    <div class="card-body">
                        <form class="form form-horizontal">
                            <div class="form-body">
                                <div class="row">
                                    <!-- Type de mouvement -->
                                    <div class="col-md-6">
                                        <label>Type de mouvement</label>
                                        <select class="form-select">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="in">Entrée</option>
                                            <option value="out">Sortie</option>
                                        </select>
                                    </div>

                                    <!-- Dépot -->
                                    <div class="col-md-6">
                                        <label>Dépôt</label>
                                        <select class="form-select">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="1">Dépôt Central</option>
                                            <option value="2">Dépôt Sud</option>
                                        </select>
                                    </div>

                                    <!-- Article -->
                                    <div class="col-md-6">
                                        <label>Article</label>
                                        <select class="form-select">
                                            <option value="">-- Sélectionner --</option>
                                            <option value="1">Papier A4</option>
                                            <option value="2">Stylo Bille</option>
                                            <option value="3">Cartouche Encre</option>
                                        </select>
                                    </div>

                                    <!-- Lot -->
                                    <div class="col-md-6">
                                        <label>Lot (si applicable)</label>
                                        <select class="form-select">
                                            <option value="">-- Aucun --</option>
                                            <option value="L001">L001 - 01/01/2026</option>
                                            <option value="L002">L002 - 15/01/2026</option>
                                        </select>
                                    </div>

                                    <!-- Quantité -->
                                    <div class="col-md-6">
                                        <label>Quantité</label>
                                        <input type="number" step="0.001" class="form-control" placeholder="Ex: 10">
                                    </div>

                                    <!-- Coût unitaire -->
                                    <div class="col-md-6">
                                        <label>Coût unitaire</label>
                                        <input type="number" step="0.01" class="form-control" placeholder="Ex: 500">
                                    </div>

                                    <!-- Motif -->
                                    <div class="col-12">
                                        <label>Motif</label>
                                        <textarea class="form-control" rows="3" placeholder="Ex: Réception fournisseur, ajustement inventaire..."></textarea>
                                    </div>

                                    <!-- Date -->
                                    <div class="col-md-6">
                                        <label>Date du mouvement</label>
                                        <input type="date" class="form-control" value="2026-01-28">
                                    </div>

                                    <!-- Bouton -->
                                    <div class="col-12 d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Enregistrer</button>
                                        <button type="reset" class="btn btn-light-secondary me-1 mb-1">Annuler</button>
                                    </div>

                                </div>
                            </div>
                        </form>
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

<!-- charts -->
<script src="<?= Flight::base() ?>/public/template/assets/extensions/dayjs/dayjs.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/apexcharts/apexcharts.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/ui-apexchart.js"></script>

<script src="<?= Flight::base() ?>/public/template/assets/extensions/chart.js/chart.umd.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/ui-chartjs.js"></script>

</body>
</html>
