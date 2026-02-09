<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Direction Générale</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <style>
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .trend-up { color: #198754; }
        .trend-down { color: #dc3545; }
        .trend-neutral { color: #6c757d; }
        .metric-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 24px;
        }
        .alert-custom {
            border-left: 4px solid;
            border-radius: 0.5rem;
        }
        .progress-thin {
            height: 8px;
        }
    </style>
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Dashboard Direction Générale</h3>
                        <p class="text-subtitle text-muted">Vue synthétique de la performance globale • Mis à jour le <?= date('d/m/Y à H:i') ?></p>
                    </div>
                    <div>
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Exporter
                        </button>
                    </div>
                </div>
            </div>

            <div class="page-content">

                <!-- ALERTES & INDICATEURS CRITIQUES -->
                <section class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-custom alert-warning" style="border-left-color: #ffc107;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                                <div>
                                    <strong>Attention :</strong> Rotation stock (4,2) en dessous de l'objectif (≥5). 
                                    Valeur immobilisée en surstock : <strong>148 000 Ar</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- KPI CARDS PRINCIPAUX -->
                <section class="row">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body px-4 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted font-semibold mb-2">Chiffre d'Affaires</h6>
                                        <h3 class="font-extrabold mb-0">1 250 000 Ar</h3>
                                        <div class="mt-2">
                                            <span class="badge bg-success">
                                                <i class="bi bi-arrow-up"></i> +8,0% vs M-1
                                            </span>
                                            <span class="badge bg-light-success ms-1">
                                                +12,5% vs M-12
                                            </span>
                                        </div>
                                    </div>
                                    <div class="metric-icon bg-light-success">
                                        <i class="bi bi-currency-euro text-success"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Objectif mensuel: 1 200 000 Ar</small>
                                    <div class="progress progress-thin mt-2">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 104%" aria-valuenow="104" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body px-4 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted font-semibold mb-2">Marge Brute</h6>
                                        <h3 class="font-extrabold mb-0">420 000 Ar</h3>
                                        <div class="mt-2">
                                            <span class="badge bg-info">
                                                33,6% du CA
                                            </span>
                                            <span class="badge bg-light-info ms-1">
                                                +1,2 pts vs M-1
                                            </span>
                                        </div>
                                    </div>
                                    <div class="metric-icon bg-light-info">
                                        <i class="bi bi-graph-up text-info"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Objectif: 35%</small>
                                    <div class="progress progress-thin mt-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 96%" aria-valuenow="96" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body px-4 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted font-semibold mb-2">Valeur Stock Total</h6>
                                        <h3 class="font-extrabold mb-0">310 000 Ar</h3>
                                        <div class="mt-2">
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-arrow-down"></i> -5,0% vs M-1
                                            </span>
                                            <span class="badge bg-light-warning text-dark ms-1">
                                                -8,2% vs M-12
                                            </span>
                                        </div>
                                    </div>
                                    <div class="metric-icon bg-light-warning">
                                        <i class="bi bi-box-seam text-warning"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Couverture: 9,4 jours</small>
                                    <div class="progress progress-thin mt-2">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body px-4 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted font-semibold mb-2">Rotation Stock</h6>
                                        <h3 class="font-extrabold mb-0">4,2</h3>
                                        <div class="mt-2">
                                            <span class="badge bg-danger">
                                                <i class="bi bi-exclamation-circle"></i> Sous objectif
                                            </span>
                                            <span class="badge bg-light-secondary ms-1">
                                                Cible: ≥ 5
                                            </span>
                                        </div>
                                    </div>
                                    <div class="metric-icon bg-light-danger">
                                        <i class="bi bi-arrow-repeat text-danger"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Écart: -16% vs objectif</small>
                                    <div class="progress progress-thin mt-2">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 84%" aria-valuenow="84" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- CA ET MARGE PAR SITE -->
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Performance par Site / Dépôt</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="tableSites">
                                        <thead>
                                            <tr>
                                                <th>Site</th>
                                                <th>CA (Ar)</th>
                                                <th>Évol. M-1</th>
                                                <th>Marge Brute (Ar)</th>
                                                <th>Marge %</th>
                                                <th>Valeur Stock (Ar)</th>
                                                <th>Rotation</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Central</strong></td>
                                                <td>620 000</td>
                                                <td><span class="badge bg-success">+9,2%</span></td>
                                                <td>215 000</td>
                                                <td><span class="badge bg-info">34,7%</span></td>
                                                <td>180 000</td>
                                                <td>4,5</td>
                                                <td><span class="badge bg-success">Bon</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nord</strong></td>
                                                <td>380 000</td>
                                                <td><span class="badge bg-success">+7,8%</span></td>
                                                <td>125 000</td>
                                                <td><span class="badge bg-info">32,9%</span></td>
                                                <td>80 000</td>
                                                <td>4,2</td>
                                                <td><span class="badge bg-warning text-dark">Moyen</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sud</strong></td>
                                                <td>250 000</td>
                                                <td><span class="badge bg-success">+5,1%</span></td>
                                                <td>80 000</td>
                                                <td><span class="badge bg-info">32,0%</span></td>
                                                <td>50 000</td>
                                                <td>3,8</td>
                                                <td><span class="badge bg-danger">Alerte</span></td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td><strong>TOTAL</strong></td>
                                                <td><strong>1 250 000</strong></td>
                                                <td><span class="badge bg-success">+8,0%</span></td>
                                                <td><strong>420 000</strong></td>
                                                <td><span class="badge bg-info">33,6%</span></td>
                                                <td><strong>310 000</strong></td>
                                                <td><strong>4,2</strong></td>
                                                <td>-</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- GRAPHIQUES PRINCIPAUX -->
                <section class="row">
                    <div class="col-12 col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Évolution CA & Marge Brute (12 derniers mois)</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-ca"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Répartition Valeur Stock par Site</h4>
                            </div>
                            <div class="card-body">
                                <div id="stockSiteChart"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ÉVOLUTION STOCK -->
                <section class="row">
                    <div class="col-12 col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Évolution Valeur Stock & Rotation (12 mois)</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-stock-evolution"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Indicateurs Clés Stock</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Valeur Stock vs M-1</span>
                                        <strong class="text-warning">-5,0%</strong>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-warning" style="width: 95%">310 KAr</div>
                                    </div>
                                    <small class="text-muted">M-1: 326 500 Ar</small>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Valeur Stock vs M-12</span>
                                        <strong class="text-success">-8,2%</strong>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" style="width: 91.8%">310 KAr</div>
                                    </div>
                                    <small class="text-muted">M-12: 337 800 Ar</small>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <small class="text-muted d-block">Rotation actuelle</small>
                                        <h4 class="mb-0">4,2</h4>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">Objectif</small>
                                        <h4 class="mb-0 text-success">≥ 5,0</h4>
                                    </div>
                                </div>

                                <div class="alert alert-light-danger mb-0">
                                    <i class="bi bi-info-circle"></i> 
                                    <strong>-16%</strong> sous l'objectif de rotation
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- SURSTOCK & OBSOLESCENCE -->
                <section class="row">
                    <div class="col-12 col-xl-7">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Top 10 Surstocks / Obsolescence</h4>
                                    <span class="badge bg-danger">Valeur immobilisée: 148 000 Ar</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tableSurstock">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Article</th>
                                                <th>Référence</th>
                                                <th>Qty Stock</th>
                                                <th>Valeur (Ar)</th>
                                                <th>Dernière vente</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Composant électronique XR45</td>
                                                <td>ART-2841</td>
                                                <td>850</td>
                                                <td>42 000</td>
                                                <td>Il y a 18 mois</td>
                                                <td><span class="badge bg-danger">Obsolète</span></td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Pièce mécanique série B</td>
                                                <td>ART-1923</td>
                                                <td>620</td>
                                                <td>38 000</td>
                                                <td>Il y a 14 mois</td>
                                                <td><span class="badge bg-danger">Obsolète</span></td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Câble assemblage V2</td>
                                                <td>ART-3015</td>
                                                <td>1200</td>
                                                <td>29 000</td>
                                                <td>Il y a 8 mois</td>
                                                <td><span class="badge bg-warning text-dark">Surstock</span></td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Module LED Gen3</td>
                                                <td>ART-4782</td>
                                                <td>380</td>
                                                <td>21 000</td>
                                                <td>Il y a 11 mois</td>
                                                <td><span class="badge bg-danger">Obsolète</span></td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Connecteur standard ISO</td>
                                                <td>ART-2156</td>
                                                <td>950</td>
                                                <td>18 000</td>
                                                <td>Il y a 6 mois</td>
                                                <td><span class="badge bg-warning text-dark">Surstock</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-5">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Analyse Obsolescence</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-obsolescence"></div>
                                
                                <div class="mt-4">
                                    <h6 class="mb-3">Actions recommandées</h6>
                                    <div class="alert alert-light-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <strong>3 articles</strong> sans mouvement depuis +12 mois
                                    </div>
                                    <div class="alert alert-light-info">
                                        <i class="bi bi-info-circle"></i>
                                        <strong>2 articles</strong> en surstock (>6 mois de couverture)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- ÉCARTS INVENTAIRE -->
                <section class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Écarts Inventaire par Dépôt</h4>
                                    <span class="badge bg-light-secondary">Dernier inventaire: <?= date('d/m/Y', strtotime('-15 days')) ?></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card bg-light-danger">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Écart Total (Valeur)</h6>
                                                <h3 class="text-danger mb-0">-3 100 Ar</h3>
                                                <small class="text-muted">Sur 310 000 Ar</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light-danger">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Écart Total (%)</h6>
                                                <h3 class="text-danger mb-0">-1,0%</h3>
                                                <small class="text-muted">Seuil acceptable: ±0,5%</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light-warning">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Articles en écart</h6>
                                                <h3 class="text-warning mb-0">47</h3>
                                                <small class="text-muted">Sur 1 253 articles</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light-info">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Fiabilité Inventaire</h6>
                                                <h3 class="text-info mb-0">96,2%</h3>
                                                <small class="text-muted">Objectif: ≥98%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover" id="tableEcarts">
                                        <thead>
                                            <tr>
                                                <th>Dépôt</th>
                                                <th>Valeur Stock (Ar)</th>
                                                <th>Écart Valeur (Ar)</th>
                                                <th>Écart %</th>
                                                <th>Articles en écart</th>
                                                <th>Fiabilité</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Central</strong></td>
                                                <td>180 000</td>
                                                <td class="text-danger">-4 200</td>
                                                <td><span class="badge bg-danger">-2,3%</span></td>
                                                <td>28</td>
                                                <td>94,1%</td>
                                                <td><span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> Critique</span></td>
                                                <td><button class="btn btn-sm btn-outline-primary">Analyser</button></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nord</strong></td>
                                                <td>80 000</td>
                                                <td class="text-success">+1 100</td>
                                                <td><span class="badge bg-success">+1,4%</span></td>
                                                <td>12</td>
                                                <td>97,8%</td>
                                                <td><span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> À surveiller</span></td>
                                                <td><button class="btn btn-sm btn-outline-primary">Analyser</button></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Sud</strong></td>
                                                <td>50 000</td>
                                                <td class="text-success">-</td>
                                                <td><span class="badge bg-success">0,0%</span></td>
                                                <td>7</td>
                                                <td>98,9%</td>
                                                <td><span class="badge bg-success"><i class="bi bi-check-circle"></i> Conforme</span></td>
                                                <td><button class="btn btn-sm btn-outline-secondary" disabled>-</button></td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td><strong>TOTAL</strong></td>
                                                <td><strong>310 000</strong></td>
                                                <td class="text-danger"><strong>-3 100</strong></td>
                                                <td><span class="badge bg-danger">-1,0%</span></td>
                                                <td><strong>47</strong></td>
                                                <td><strong>96,2%</strong></td>
                                                <td>-</td>
                                                <td>-</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>

    </div>
</body>

</html>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>

<!-- Charts -->
<script src="<?= Flight::base() ?>/public/template/assets/extensions/apexcharts/apexcharts.min.js"></script>

<!-- DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    // DataTables
    $(document).ready(function() {
        $('#tableSites').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            order: [[1, 'desc']]
        });

        $('#tableSurstock').DataTable({
            responsive: true,
            pageLength: 5,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            order: [[4, 'desc']]
        });

        $('#tableEcarts').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            paging: false,
            searching: false,
            info: false
        });
    });

    // Chart CA & Marge
    var optionsCA = {
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
            }
        },
        series: [
            {
                name: "Chiffre d'Affaires",
                type: 'column',
                data: [950000, 980000, 1020000, 1050000, 1100000, 1120000, 1150000, 1130000, 1180000, 1200000, 1220000, 1250000]
            },
            {
                name: "Marge Brute",
                type: 'line',
                data: [310000, 320000, 335000, 345000, 360000, 370000, 380000, 375000, 390000, 400000, 410000, 420000]
            },
            {
                name: "Marge %",
                type: 'line',
                data: [32.6, 32.7, 32.8, 32.9, 32.7, 33.0, 33.0, 33.2, 33.1, 33.3, 33.6, 33.6]
            }
        ],
        stroke: {
            width: [0, 3, 3],
            curve: 'smooth'
        },
        colors: ['#435ebe', '#55c6a9', '#f9b959'],
        xaxis: {
            categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
        },
        yaxis: [
            {
                title: {
                    text: 'CA (Ar)'
                },
                labels: {
                    formatter: function(val) {
                        return val.toLocaleString() + ' Ar';
                    }
                }
            },
            {
                opposite: true,
                title: {
                    text: 'Marge Brute (Ar)'
                },
                labels: {
                    formatter: function(val) {
                        return val.toLocaleString() + ' Ar';
                    }
                }
            },
            {
                opposite: true,
                show: false,
                title: {
                    text: 'Marge %'
                },
                labels: {
                    formatter: function(val) {
                        return val.toFixed(1) + '%';
                    }
                }
            }
        ],
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(val, opts) {
                    if (opts.seriesIndex === 2) {
                        return val.toFixed(1) + '%';
                    }
                    return val.toLocaleString() + ' Ar';
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        },
        dataLabels: {
            enabled: false
        }
    };

    var chartCA = new ApexCharts(document.querySelector("#chart-ca"), optionsCA);
    chartCA.render();

    // Chart Stock par Site (Donut)
    var optionsStockSite = {
        chart: {
            type: 'donut',
            height: 300
        },
        series: [180000, 80000, 50000],
        labels: ['Central (58%)', 'Nord (26%)', 'Sud (16%)'],
        colors: ['#435ebe', '#55c6a9', '#f9b959'],
        legend: {
            position: 'bottom'
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toLocaleString() + ' Ar';
                }
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Stock',
                            formatter: function(w) {
                                return '310 000 Ar';
                            }
                        }
                    }
                }
            }
        }
    };

    var chartStockSite = new ApexCharts(document.querySelector("#stockSiteChart"), optionsStockSite);
    chartStockSite.render();

    // Chart Évolution Stock & Rotation
    var optionsStockEvol = {
        chart: {
            type: 'line',
            height: 300,
            toolbar: {
                show: true
            }
        },
        series: [
            {
                name: "Valeur Stock",
                type: 'column',
                data: [337800, 340000, 335000, 332000, 328000, 325000, 322000, 320000, 318000, 315000, 326500, 310000]
            },
            {
                name: "Rotation Stock",
                type: 'line',
                data: [3.8, 3.9, 4.0, 4.1, 4.0, 4.2, 4.3, 4.1, 4.2, 4.3, 4.0, 4.2]
            }
        ],
        stroke: {
            width: [0, 3],
            curve: 'smooth'
        },
        colors: ['#f9b959', '#e74c3c'],
        xaxis: {
            categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
        },
        yaxis: [
            {
                title: {
                    text: 'Valeur Stock (Ar)'
                },
                labels: {
                    formatter: function(val) {
                        return val.toLocaleString() + ' Ar';
                    }
                }
            },
            {
                opposite: true,
                title: {
                    text: 'Rotation'
                },
                min: 0,
                max: 6,
                labels: {
                    formatter: function(val) {
                        return val.toFixed(1);
                    }
                }
            }
        ],
        tooltip: {
            shared: true,
            intersect: false
        },
        legend: {
            position: 'top'
        },
        annotations: {
            yaxis: [
                {
                    y: 5,
                    y2: null,
                    yAxisIndex: 1,
                    borderColor: '#00E396',
                    label: {
                        borderColor: '#00E396',
                        style: {
                            color: '#fff',
                            background: '#00E396'
                        },
                        text: 'Objectif Rotation: 5'
                    }
                }
            ]
        }
    };

    var chartStockEvol = new ApexCharts(document.querySelector("#chart-stock-evolution"), optionsStockEvol);
    chartStockEvol.render();

    // Chart Obsolescence (Pie)
    var optionsObsolescence = {
        chart: {
            type: 'pie',
            height: 250
        },
        series: [101000, 47000, 162000],
        labels: ['Obsolète (>12 mois)', 'Surstock (6-12 mois)', 'Stock Actif'],
        colors: ['#e74c3c', '#f39c12', '#27ae60'],
        legend: {
            position: 'bottom'
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toLocaleString() + ' Ar';
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val.toFixed(1) + '%';
            }
        }
    };

    var chartObsolescence = new ApexCharts(document.querySelector("#chart-obsolescence"), optionsObsolescence);
    chartObsolescence.render();
</script>