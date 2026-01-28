<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mouvements de stock</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
</head>

<body>
<div id="app">
    <!-- Sidebar-->
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
                        <h3>Mouvements de stock</h3>
                        <p class="text-subtitle text-muted">Visualisez toutes les entrées, sorties et transferts</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Mouvements de stock</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Section filtres -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Filtres</h4>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" class="row g-3">
                            <div class="col-md-3">
                                <label for="article" class="form-label">Article</label>
                                <select id="article" class="form-select">
                                    <option>Tous</option>
                                    <option>Article A</option>
                                    <option>Article B</option>
                                    <option>Article C</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="depot" class="form-label">Dépôt</label>
                                <select id="depot" class="form-select">
                                    <option>Tous</option>
                                    <option>Dépôt 1</option>
                                    <option>Dépôt 2</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="type" class="form-label">Type de mouvement</label>
                                <select id="type" class="form-select">
                                    <option>Tous</option>
                                    <option>Réception fournisseur</option>
                                    <option>Livraison client</option>
                                    <option>Transfert inter-dépôt</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sens" class="form-label">Sens</label>
                                <select id="sens" class="form-select">
                                    <option>Tous</option>
                                    <option>Entrée</option>
                                    <option>Sortie</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_debut" class="form-label">Date début</label>
                                <input type="date" id="date_debut" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="date_fin" class="form-label">Date fin</label>
                                <input type="date" id="date_fin" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary mt-2">Filtrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Section tableau mouvements -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Liste des mouvements</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Article</th>
                                        <th>Dépôt</th>
                                        <th>Type</th>
                                        <th>Sens</th>
                                        <th>Quantité</th>
                                        <th>Lot</th>
                                        <th>Référence</th>
                                        <th>Origine</th>
                                        <th>Statut</th>
                                        <th>Utilisateur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>2026-01-28</td>
                                        <td>Article A</td>
                                        <td>Dépôt 1</td>
                                        <td>Réception fournisseur</td>
                                        <td><span class="badge bg-success">Entrée</span></td>
                                        <td>100</td>
                                        <td>L001</td>
                                        <td>BC123</td>
                                        <td>bon_commande_fournisseur</td>
                                        <td><span class="badge bg-success">Validé</span></td>
                                        <td>Admin</td>
                                    </tr>
                                    <tr>
                                        <td>2026-01-27</td>
                                        <td>Article B</td>
                                        <td>Dépôt 2</td>
                                        <td>Livraison client</td>
                                        <td><span class="badge bg-danger">Sortie</span></td>
                                        <td>50</td>
                                        <td>L002</td>
                                        <td>LC456</td>
                                        <td>livraison_client</td>
                                        <td><span class="badge bg-secondary">Annulé</span></td>
                                        <td>John</td>
                                    </tr>
                                    <tr>
                                        <td>2026-01-26</td>
                                        <td>Article C</td>
                                        <td>Dépôt 1</td>
                                        <td>Transfert inter-dépôt</td>
                                        <td><span class="badge bg-danger">Sortie</span></td>
                                        <td>30</td>
                                        <td>L003</td>
                                        <td>TR789</td>
                                        <td>transfert</td>
                                        <td><span class="badge bg-success">Validé</span></td>
                                        <td>Mary</td>
                                    </tr>
                                </tbody>
                            </table>
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

<!-- charts -->
<script src="<?= Flight::base() ?>/public/template/assets/extensions/dayjs/dayjs.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/apexcharts/apexcharts.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/ui-apexchart.js"></script>

<script src="<?= Flight::base() ?>/public/template/assets/extensions/chart.js/chart.umd.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/ui-chartjs.js"></script>

</body>
</html>
