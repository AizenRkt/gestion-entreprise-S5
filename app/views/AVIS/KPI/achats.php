<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Achats / Supply Chain</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    
    <style>
        .stat-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .metric-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 28px;
        }
        .badge-metric {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        .progress-thin {
            height: 8px;
        }
        .supplier-risk {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .risk-low { background-color: #28a745; }
        .risk-medium { background-color: #ffc107; }
        .risk-high { background-color: #dc3545; }
        .table-actions {
            white-space: nowrap;
        }
        .card-metric-small {
            border-left: 4px solid;
            margin-bottom: 1rem;
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
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3>Dashboard Achats / Supply Chain</h3>
                    <p class="text-subtitle text-muted">Suivi des performances et des risques fournisseurs • Mis à jour le <?= date('d/m/Y à H:i') ?></p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2">
                        <i class="bi bi-funnel"></i> Filtrer
                    </button>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Exporter
                    </button>
                </div>
            </div>
        </div>

        <div class="page-content">

            <!-- ALERTES DYNAMIQUES -->
            <section class="row mb-4">
                <?php if (isset($supplierRiskShare) && $supplierRiskShare > 40): ?>
                <div class="col-12">
                    <div class="alert alert-danger border-danger" style="border-left: 4px solid;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-octagon-fill me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong>Alerte Risque Fournisseur :</strong> 
                                <span class="ms-2"><?= htmlspecialchars($topSupplierName) ?> représente <strong><?= $supplierRiskShare ?>%</strong> du volume d'achats (seuil critique > 40%)</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger">Analyser</button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (isset($urgentOrdersCount) && $urgentOrdersCount > 5): ?>
                <div class="col-12">
                    <div class="alert alert-warning border-warning" style="border-left: 4px solid;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong>Attention :</strong> 
                                <span class="ms-2"><strong><?= $urgentOrdersCount ?> commandes urgentes</strong> ce mois (Objectif < 5)</span>
                            </div>
                            <button class="btn btn-sm btn-outline-warning">Voir détails</button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </section>

            <!-- KPI CARDS PRINCIPAUX -->
            <section class="row">
                <!-- KPI 1 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Combien avons-nous dépensé ?</h6>
                                    <h3 class="font-extrabold mb-0"><?= number_format($totalSpend ?? 0, 2, ',', ' ') ?> Ar</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-primary badge-metric">
                                            <i class="bi bi-currency-euro"></i> Total Achats Global
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-primary">
                                    <i class="bi bi-wallet2 text-primary"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">Volume total des commandes validées</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI 2 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Combien de commandes en attente ?</h6>
                                    <h3 class="font-extrabold mb-0"><?= $pendingOrders ?></h3>
                                    <div class="mt-2">
                                        <span class="badge bg-warning text-dark badge-metric">
                                            <i class="bi bi-hourglass-split"></i> En cours
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-warning">
                                    <i class="bi bi-cart3 text-warning"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">Demandes d'achat (Créées ou Visées)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI 3 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Quel est le délai moyen ?</h6>
                                    <h3 class="font-extrabold mb-0"><?= $avgLeadTime ?> j</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-success badge-metric">
                                            <i class="bi bi-speedometer2"></i> Lead Time
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-success">
                                    <i class="bi bi-clock-history text-success"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">Temps moyen entre Commande et Réception</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI 4 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Qui est le Top Fournisseur ?</h6>
                                    <h3 class="font-extrabold mb-0" style="font-size: 1.2rem;"><?= htmlspecialchars($topSupplierName) ?></h3>
                                    <div class="mt-2">
                                        <span class="badge bg-info badge-metric">
                                            <?= number_format($topSupplierAmount ?? 0, 2, ',', ' ') ?> Ar
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-info">
                                    <i class="bi bi-trophy text-info"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">Fournisseur avec le plus gros volume d'achats</small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- KPI SECONDAIRES REMOVED FOR CONSISTENCY -->

            <!-- GRAPHIQUES PRINCIPAUX -->
            <section class="row">
                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Évolution Cycle Time DA→BC (12 derniers mois)</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-cycletime"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Dépenses par Famille d'Achat</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-spend-category"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- PERFORMANCE FOURNISSEURS -->
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Performance Fournisseurs</h4>
                                <div>
                                    <span class="badge bg-danger me-2">
                                        <i class="bi bi-exclamation-circle"></i> 2 fournisseurs critiques
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Export
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tableFournisseurs">
                                    <thead>
                                        <tr>
                                            <th>Fournisseur</th>
                                            <th>Volume Achats (Ar)</th>
                                            <th>Nb Commandes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($suppliersPerf)): ?>
                                            <?php foreach ($suppliersPerf as $supplier): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($supplier['nom']) ?></strong></td>
                                                <td><?= number_format($supplier['volume_achat'] ?? 0, 2, ',', ' ') ?></td>
                                                <td><span class="badge bg-info"><?= $supplier['nb_commandes'] ?></span></td>
                                                <td class="table-actions">
                                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">Aucune donnée fournisseur disponible</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CONCENTRATION & PRIX -->
            <section class="row">
                <div class="col-12 col-xl-5">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Concentration Fournisseurs</h4>
                            <p class="text-muted small mb-0">Analyse du risque de dépendance</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-concentration"></div>
                            
                            <div class="mt-4">
                                <div class="alert alert-danger mb-2">
                                    <i class="bi bi-exclamation-octagon"></i>
                                    <strong>Risque élevé :</strong> Top 2 fournisseurs = 70% des achats
                                </div>
                                <div class="alert alert-info mb-0">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Recommandation :</strong> Diversifier vers 2-3 fournisseurs alternatifs
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-7">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Évolution Prix d'Achat - Articles Clés</h4>
                            <p class="text-muted small mb-0">Index d'évolution sur 12 mois (Base 100)</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-prix-evolution"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- COMMANDES URGENTES & OTD -->
            <section class="row">
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Évolution Mensuelle des Dépenses</h4>
                            <p class="text-muted small mb-0">Montants validés (6 derniers mois)</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-monthly-spend"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Respect Délais Fournisseurs (OTD)</h4>
                            <p class="text-muted small mb-0">Par fournisseur principal</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-otd-fournisseurs"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- LITIGES ET NON-CONFORMITES -->
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-0">Litiges et Non-conformités en Cours</h4>
                                    <p class="text-muted small mb-0">Dernière mise à jour: <?= date('d/m/Y') ?></p>
                                </div>
                                <span class="badge bg-warning text-dark fs-6"><?= count($litiges) ?> litiges actifs</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tableLitiges">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Fournisseur</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Montant (Ar)</th>
                                            <th>Priorité</th>
                                            <th>Statut</th>
                                            <th>Responsable</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($litiges)): ?>
                                            <?php foreach ($litiges as $litige): ?>
                                            <tr>
                                                <td><strong>#<?= htmlspecialchars($litige['ref_litige']) ?></strong></td>
                                                <td><?= date('d/m/Y', strtotime($litige['date_litige'])) ?></td>
                                                <td><?= htmlspecialchars($litige['fournisseur_nom']) ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?= $litige['type_litige'] == 'Facture' ? 'bg-danger' : 
                                                           ($litige['type_litige'] == 'Qualité' ? 'bg-warning text-dark' : 'bg-info') ?>">
                                                        <?= htmlspecialchars($litige['type_litige']) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($litige['description']) ?></td>
                                                <td><?= $litige['montant_enjeu'] > 0 ? number_format($litige['montant_enjeu'], 0, ',', ' ') : '-' ?></td>
                                                <td>
                                                    <span class="badge <?= $litige['priorite'] == 'Haute' ? 'bg-danger' : 'bg-warning text-dark' ?>">
                                                        <?= htmlspecialchars($litige['priorite']) ?>
                                                    </span>
                                                </td>
                                                <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($litige['statut']) ?></span></td>
                                                <td><?= htmlspecialchars($litige['responsable']) ?></td>
                                                <td class="table-actions">
                                                    <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10" class="text-center">Aucun litige en cours</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- FOOTER -->
            <section class="row mt-4">
                <div class="col-12">
                    <div class="text-center text-muted small mb-3">
                        <p>Dashboard actualisé le <?= date('d/m/Y à H:i') ?> • Données du mois actuel</p>
                        <p>Pour toute question: <strong>m.lefebvre@company.com</strong> | Service Achats</p>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script>
    // Initialiser DataTables
    $(document).ready(function() {
        // Table Fournisseurs
        $('#tableFournisseurs').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            columnDefs: [
                { targets: -1, orderable: false }
            ],
            order: [[4, 'desc']]
        });

        // Table Litiges
        $('#tableLitiges').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            columnDefs: [
                { targets: -1, orderable: false }
            ],
            order: [[1, 'desc']]
        });

        // Graphique 1: Évolution Cycle Time - DONNEES REELLES
        <?php 
            $cycleMonths = array_column($cycleTimeData, 'mois');
            $cycleValues = array_column($cycleTimeData, 'avg_days');
        ?>
        var optionsCycleTime = {
            series: [
                {
                    name: 'Moyenne (jours)',
                    data: [<?php echo implode(', ', $cycleValues ?: [0]); ?>]
                }
            ],
            chart: {
                type: 'line',
                height: 350,
                toolbar: { show: true }
            },
            colors: ['#28a745'],
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: [<?php echo "'" . implode("', '", $cycleMonths ?: []) . "'"; ?>]
            },
            yaxis: {
                title: {
                    text: 'Jours'
                }
            },
            legend: {
                position: 'top'
            },
            grid: {
                show: true,
                borderColor: '#e7e7e7',
                strokeDashArray: 0,
                xaxis: {
                    lines: {
                        show: false
                    }
                }
            }
        };
        new ApexCharts(document.querySelector('#chart-cycletime'), optionsCycleTime).render();

        // Graphique 2: Dépenses par Catégorie (Pie)
        <?php if (!empty($spendByCategory)): ?>
        var optionsCategory = {
            series: [<?php echo implode(', ', array_map(function($i){ return $i['total']; }, $spendByCategory)); ?>],
            chart: {
                type: 'pie',
                height: 350,
                toolbar: { show: true }
            },
            labels: [<?php echo "'" . implode("', '", array_map(function($i){ return addslashes($i['nom']); }, $spendByCategory)) . "'"; ?>],
            colors: ['#435ebe', '#55c6e8', '#4ecdc4', '#ffc107', '#dc3545'],
            dataLabels: {
                formatter: function (val, opts) {
                    return opts.w.config.series[opts.seriesIndex].toLocaleString('fr-FR') + ' Ar';
                },
            },
            legend: { position: 'bottom' }
        };
        new ApexCharts(document.querySelector('#chart-spend-category'), optionsCategory).render();
        <?php else: ?>
        document.querySelector('#chart-spend-category').innerHTML = '<div class="alert alert-light text-center">Aucune donnée disponible</div>';
        <?php endif; ?>

        // Graphique 3: Concentration Fournisseurs (Pie) - DONNEES REELLES
        <?php if (!empty($suppliersPerf)): ?>
        var optionsConcentration = {
            series: [<?php echo implode(', ', array_column($suppliersPerf, 'volume_achat')); ?>],
            chart: {
                type: 'donut',
                height: 350
            },
            labels: [<?php echo "'" . implode("', '", array_column($suppliersPerf, 'nom')) . "'"; ?>],
            colors: ['#dc3545', '#ffc107', '#28a745', '#17a2b8', '#6f42c1'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val.toFixed(1) + '%';
                }
            },
            legend: {
                position: 'bottom'
            }
        };
        new ApexCharts(document.querySelector('#chart-concentration'), optionsConcentration).render();
        <?php endif; ?>

        // Graphique 4: Évolution Prix d'Achat - DONNEES REELLES
        <?php 
            // Simple mapping for demo: extract price for the first article found in history
            $priceMonths = array_unique(array_column($priceHistoryData, 'mois'));
            sort($priceMonths);
            
            // Check if we have data, otherwise fallback
            if (!empty($priceHistoryData)) {
                $uniqueArticles = array_unique(array_column($priceHistoryData, 'designation'));
                $seriesData = [];
                
                foreach ($uniqueArticles as $articleName) {
                    $dataPoints = [];
                    foreach ($priceMonths as $month) {
                        // Find price for this article and month
                        $price = null;
                        foreach ($priceHistoryData as $row) {
                            if ($row['designation'] === $articleName && $row['mois'] === $month) {
                                $price = $row['prix'];
                                break;
                            }
                        }
                        $dataPoints[] = $price ?: 0; // 0 or previous value would be better
                    }
                    $seriesData[] = "{ name: '" . addslashes($articleName) . "', data: [" . implode(',', $dataPoints) . "] }";
                }
            }
        ?>
        var optionsPrixEvolution = {
            series: [
                <?php echo !empty($seriesData) ? implode(',', $seriesData) : "{ name: 'Aucune donnée', data: [] }"; ?>
            ],
            chart: {
                type: 'line',
                height: 350,
                toolbar: { show: true }
            },
            colors: ['#435ebe', '#dc3545', '#ffc107'],
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: [<?php echo !empty($priceMonths) ? "'" . implode("', '", $priceMonths) . "'" : ""; ?>]
            },
            yaxis: {
                title: {
                    text: 'Prix (Ar)'
                }
            },
            legend: {
                position: 'top'
            },
            grid: {
                show: true,
                borderColor: '#e7e7e7'
            }
        };
        new ApexCharts(document.querySelector('#chart-prix-evolution'), optionsPrixEvolution).render();

        // Graphique 5: Taux Commandes Urgentes - DONNEES REELLES (Mois courant)
        // Graphique 5: Évolution Mensuelle des Dépenses (Bar)
        <?php if (!empty($monthlyData)): ?>
        var optionsMonthly = {
            series: [{
                name: 'Montant (Ar)',
                data: [<?php echo implode(', ', array_column($monthlyData, 'total_achats')); ?>]
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: true }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top', // top, center, bottom
                    },
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return (val / 1000).toFixed(1) + " kAr";
                },
                offsetY: -20,
                style: {
                    colors: ["#304758"]
                }
            },
            colors: ['#6f42c1'],
            xaxis: {
                categories: [<?php echo "'" . implode("', '", array_column($monthlyData, 'mois')) . "'"; ?>],
                position: 'bottom'
            },
            yaxis: {
                title: { text: 'Montant HT (Ar)' }
            },
            grid: {
                show: true,
                borderColor: '#e7e7e7'
            }
        };
        new ApexCharts(document.querySelector('#chart-monthly-spend'), optionsMonthly).render();
        <?php endif; ?>

        // Graphique 6: OTD par Fournisseur - DONNEES REELLES
        <?php if (!empty($suppliersPerf)): ?>
        var optionsOTD = {
            series: [
                {
                    name: 'Nb Commandes',
                    data: [<?php echo implode(', ', array_column($suppliersPerf, 'nb_commandes')); ?>]
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            colors: ['#435ebe'],
            xaxis: {
                categories: [<?php echo "'" . implode("', '", array_column($suppliersPerf, 'nom')) . "'"; ?>]
            },
            yaxis: {
                title: {
                    text: 'Nombre de commandes',
                    align: 'high'
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '55%',
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20
            },
            grid: {
                show: true,
                borderColor: '#e7e7e7'
            }
        };
        new ApexCharts(document.querySelector('#chart-otd-fournisseurs'), optionsOTD).render();
        <?php endif; ?>
    });
</script>

</body>
</html>