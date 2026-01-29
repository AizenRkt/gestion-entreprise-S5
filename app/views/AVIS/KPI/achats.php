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

            <!-- ALERTES -->
            <section class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-danger border-danger" style="border-left: 4px solid;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-octagon-fill me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong>Alerte Risque Fournisseur :</strong> 
                                <span class="ms-2">Fournisseur A représente <strong>42%</strong> du volume d'achats (seuil critique > 40%)</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger">Analyser</button>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="alert alert-warning border-warning" style="border-left: 4px solid;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-history me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong>Attention :</strong> 
                                <span class="ms-2"><strong>18 commandes urgentes</strong> ce mois (15% du total) - Objectif < 10%</span>
                            </div>
                            <button class="btn btn-sm btn-outline-warning">Voir détails</button>
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
                                    <h6 class="text-muted font-semibold mb-2">Cycle Time DA→BC</h6>
                                    <h3 class="font-extrabold mb-0">3,5 j</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-success badge-metric">
                                            <i class="bi bi-check-circle"></i> Médiane
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-success">
                                    <i class="bi bi-clock-history text-success"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">P90: 7,0 jours</small>
                                    <small class="text-success">-5% vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≤ 3 jours</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Respect Délais Fournisseurs</h6>
                                    <h3 class="font-extrabold mb-0">92%</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-success badge-metric">
                                            <i class="bi bi-truck"></i> OTD Supplier
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-success">
                                    <i class="bi bi-calendar-check text-success"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">108/117 livraisons à temps</small>
                                    <small class="text-success">+3% vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 92%" aria-valuenow="92" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≥ 95%</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Réception Conforme</h6>
                                    <h3 class="font-extrabold mb-0">96%</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-info badge-metric">
                                            <i class="bi bi-clipboard-check"></i> Qualité/Quantité
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-info">
                                    <i class="bi bi-box-seam-fill text-info"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">112/117 réceptions OK</small>
                                    <small class="text-info">+1% vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 96%" aria-valuenow="96" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≥ 98%</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Litiges Facture</h6>
                                    <h3 class="font-extrabold mb-0">3%</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-warning text-dark badge-metric">
                                            <i class="bi bi-exclamation-triangle"></i> 3-Way Match
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-warning">
                                    <i class="bi bi-file-earmark-x text-warning"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">4/117 factures en litige</small>
                                    <small class="text-warning">+1% vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 97%" aria-valuenow="97" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≤ 2%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- KPI SECONDAIRES -->
            <section class="row">
                <div class="col-12 col-md-3">
                    <div class="card card-metric-small" style="border-left-color: #435ebe;">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Commandes urgentes</small>
                                    <h5 class="mb-0 text-danger">15%</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger">18/120</span>
                                    <small class="text-muted d-block mt-1">Objectif < 10%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="card card-metric-small" style="border-left-color: #28a745;">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Taux de service</small>
                                    <h5 class="mb-0 text-success">97,5%</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">+2%</span>
                                    <small class="text-muted d-block mt-1">vs M-1</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="card card-metric-small" style="border-left-color: #ffc107;">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Lead Time moyen</small>
                                    <h5 class="mb-0">12,3 j</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">-8%</span>
                                    <small class="text-muted d-block mt-1">vs M-1</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="card card-metric-small" style="border-left-color: #17a2b8;">
                        <div class="card-body py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Économies réalisées</small>
                                    <h5 class="mb-0 text-success">42 500 €</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-info">+15%</span>
                                    <small class="text-muted d-block mt-1">vs M-1</small>
                                </div>
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
                            <h4 class="card-title">Distribution Cycle Time</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-cycletime-distribution"></div>
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
                                            <th>Volume Achats (€)</th>
                                            <th>% Total</th>
                                            <th>OTD %</th>
                                            <th>Qualité %</th>
                                            <th>Lead Time (j)</th>
                                            <th>Litiges</th>
                                            <th>Risque</th>
                                            <th>Score Global</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Fournisseur A</strong></td>
                                            <td>420 000</td>
                                            <td><span class="badge bg-danger">42%</span></td>
                                            <td><span class="badge bg-warning text-dark">88%</span></td>
                                            <td><span class="badge bg-success">97%</span></td>
                                            <td>15,2</td>
                                            <td>5</td>
                                            <td><span class="supplier-risk risk-high"></span> Élevé</td>
                                            <td><span class="badge bg-warning text-dark">72/100</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                                <button class="btn btn-sm btn-outline-warning"><i class="bi bi-exclamation-triangle"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fournisseur B</strong></td>
                                            <td>280 000</td>
                                            <td><span class="badge bg-warning text-dark">28%</span></td>
                                            <td><span class="badge bg-success">94%</span></td>
                                            <td><span class="badge bg-success">96%</span></td>
                                            <td>11,5</td>
                                            <td>2</td>
                                            <td><span class="supplier-risk risk-medium"></span> Moyen</td>
                                            <td><span class="badge bg-success">85/100</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                                <button class="btn btn-sm btn-outline-success"><i class="bi bi-check-circle"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fournisseur C</strong></td>
                                            <td>180 000</td>
                                            <td><span class="badge bg-success">18%</span></td>
                                            <td><span class="badge bg-success">96%</span></td>
                                            <td><span class="badge bg-success">98%</span></td>
                                            <td>9,8</td>
                                            <td>1</td>
                                            <td><span class="supplier-risk risk-low"></span> Faible</td>
                                            <td><span class="badge bg-success">92/100</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                                <button class="btn btn-sm btn-outline-success"><i class="bi bi-star"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fournisseur D</strong></td>
                                            <td>120 000</td>
                                            <td><span class="badge bg-info">12%</span></td>
                                            <td><span class="badge bg-danger">82%</span></td>
                                            <td><span class="badge bg-warning text-dark">92%</span></td>
                                            <td>18,5</td>
                                            <td>8</td>
                                            <td><span class="supplier-risk risk-high"></span> Élevé</td>
                                            <td><span class="badge bg-danger">65/100</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle"></i></button>
                                            </td>
                                        </tr>
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
                            <h4 class="card-title">Taux de Commandes Urgentes</h4>
                            <p class="text-muted small mb-0">Évolution mensuelle (objectif < 10%)</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-urgent"></div>
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
                                <span class="badge bg-warning text-dark fs-6">4 litiges actifs</span>
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
                                            <th>Montant (€)</th>
                                            <th>Priorité</th>
                                            <th>Statut</th>
                                            <th>Responsable</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>#L-2401</strong></td>
                                            <td>15/01/2026</td>
                                            <td>Fournisseur A</td>
                                            <td><span class="badge bg-danger">Facture</span></td>
                                            <td>Écart prix unitaire vs BC</td>
                                            <td>4 200</td>
                                            <td><span class="badge bg-danger">Haute</span></td>
                                            <td><span class="badge bg-warning text-dark">En cours</span></td>
                                            <td>J. Martin</td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>#L-2398</strong></td>
                                            <td>12/01/2026</td>
                                            <td>Fournisseur D</td>
                                            <td><span class="badge bg-warning text-dark">Qualité</span></td>
                                            <td>Pièces non-conformes aux specs</td>
                                            <td>8 500</td>
                                            <td><span class="badge bg-danger">Haute</span></td>
                                            <td><span class="badge bg-info">Analyse</span></td>
                                            <td>S. Dubois</td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>#L-2387</strong></td>
                                            <td>08/01/2026</td>
                                            <td>Fournisseur B</td>
                                            <td><span class="badge bg-info">Quantité</span></td>
                                            <td>Livraison partielle non signalée</td>
                                            <td>2 100</td>
                                            <td><span class="badge bg-warning text-dark">Moyenne</span></td>
                                            <td><span class="badge bg-warning text-dark">En cours</span></td>
                                            <td>J. Martin</td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>#L-2375</strong></td>
                                            <td>03/01/2026</td>
                                            <td>Fournisseur A</td>
                                            <td><span class="badge bg-secondary">Délai</span></td>
                                            <td>Retard de livraison > 5 jours</td>
                                            <td>-</td>
                                            <td><span class="badge bg-info">Faible</span></td>
                                            <td><span class="badge bg-success">Résolu</span></td>
                                            <td>S. Dubois</td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- INFOS RESPONSABLE & ACTIONS RECOMMANDÉES -->
            <section class="row">
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Informations - Responsable Achats</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Responsable Achats / Supply Chain</strong>
                                    <span class="badge bg-primary">Assigné</span>
                                </div>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-person-fill"></i> <strong>Marc Lefebvre</strong><br>
                                    <i class="bi bi-envelope"></i> m.lefebvre@company.com<br>
                                    <i class="bi bi-telephone"></i> +33 1 23 45 67 89
                                </p>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <h6 class="font-semibold mb-2">Objectifs du Mois</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success"></i> 
                                        <span>Réduire cycle time à ≤ 3 jours</span>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-clock text-warning"></i> 
                                        <span>Atteindre OTD supplier ≥ 95%</span>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-exclamation-triangle text-danger"></i> 
                                        <span>Réduire urgences < 10%</span>
                                    </li>
                                    <li>
                                        <i class="bi bi-piggy-bank text-info"></i> 
                                        <span>Réaliser €50k d'économies</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Actions Recommandées</h4>
                        </div>
                        <div class="card-body">
                            <div class="action-item mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-danger me-3 mt-1">🔴 URGENCE</span>
                                    <div>
                                        <h6 class="mb-1">Audit Fournisseur A</h6>
                                        <p class="text-muted small mb-1">Risque concentration 42% + délais 88%</p>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-arrow-right"></i> Planifier visite
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="action-item mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-warning text-dark me-3 mt-1">⚠️ ATTENTION</span>
                                    <div>
                                        <h6 class="mb-1">Réduire Commandes Urgentes</h6>
                                        <p class="text-muted small mb-1">18 urgences ce mois (objectif 12)</p>
                                        <button class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-arrow-right"></i> Revoir prévisions
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="action-item mb-3">
                                <div class="d-flex align-items-start">
                                    <span class="badge bg-info me-3 mt-1">ℹ️ INFO</span>
                                    <div>
                                        <h6 class="mb-1">Diversifier Fournisseurs</h6>
                                        <p class="text-muted small mb-1">Challenger Fournisseur C pour 10% volume</p>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-arrow-right"></i> Voir propositions
                                        </button>
                                    </div>
                                </div>
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

        // Graphique 1: Évolution Cycle Time
        var optionsCycleTime = {
            series: [
                {
                    name: 'Médiane (jours)',
                    data: [4.2, 4.0, 3.8, 3.6, 3.5, 3.5, 3.7, 3.4, 3.3, 3.5, 3.5, 3.5]
                },
                {
                    name: 'P90 (jours)',
                    data: [7.8, 7.5, 7.2, 7.0, 7.0, 7.1, 7.3, 6.8, 6.9, 7.0, 7.0, 7.0]
                }
            ],
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                sparkline: { enabled: false }
            },
            colors: ['#28a745', '#ffc107'],
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
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

        // Graphique 2: Distribution Cycle Time
        var optionsDistribution = {
            series: [
                {
                    name: 'Nombre de commandes',
                    data: [5, 12, 25, 22, 18, 8, 4, 2]
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            colors: ['#435ebe'],
            xaxis: {
                categories: ['1-2j', '2-3j', '3-4j', '4-5j', '5-6j', '6-7j', '7-8j', '>8j']
            },
            yaxis: {
                title: {
                    text: 'Nombre de commandes'
                }
            },
            grid: {
                show: true,
                borderColor: '#e7e7e7'
            }
        };
        new ApexCharts(document.querySelector('#chart-cycletime-distribution'), optionsDistribution).render();

        // Graphique 3: Concentration Fournisseurs (Pie)
        var optionsConcentration = {
            series: [42, 28, 18, 12],
            chart: {
                type: 'donut',
                height: 350
            },
            labels: ['Fournisseur A', 'Fournisseur B', 'Fournisseur C', 'Fournisseur D'],
            colors: ['#dc3545', '#ffc107', '#28a745', '#17a2b8'],
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

        // Graphique 4: Évolution Prix d'Achat
        var optionsPrixEvolution = {
            series: [
                {
                    name: 'Article A (Composant électronique)',
                    data: [100, 101, 102, 103, 104, 105, 103, 102, 101, 100, 99, 98]
                },
                {
                    name: 'Article B (Matière première)',
                    data: [100, 102, 105, 108, 110, 112, 111, 109, 107, 105, 103, 101]
                },
                {
                    name: 'Article C (Service)',
                    data: [100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100]
                }
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
                categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
            },
            yaxis: {
                title: {
                    text: 'Index (Base 100)'
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

        // Graphique 5: Taux Commandes Urgentes
        var optionsUrgent = {
            series: [
                {
                    name: 'Taux urgences (%)',
                    data: [8, 9, 7, 11, 13, 14, 15, 16, 17, 16, 15, 15]
                },
                {
                    name: 'Objectif (%)',
                    data: [10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10]
                }
            ],
            chart: {
                type: 'line',
                height: 350,
                toolbar: { show: true }
            },
            colors: ['#dc3545', '#28a745'],
            stroke: {
                curve: 'smooth',
                width: [2, 2],
                dashArray: [0, 5]
            },
            xaxis: {
                categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
            },
            yaxis: {
                title: {
                    text: 'Pourcentage (%)'
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
        new ApexCharts(document.querySelector('#chart-urgent'), optionsUrgent).render();

        // Graphique 6: OTD par Fournisseur
        var optionsOTD = {
            series: [
                {
                    name: 'OTD (%)',
                    data: [88, 94, 96, 82]
                }
            ],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            colors: ['#435ebe'],
            xaxis: {
                categories: ['Fournisseur A', 'Fournisseur B', 'Fournisseur C', 'Fournisseur D']
            },
            yaxis: {
                title: {
                    text: 'OTD (%)',
                    align: 'high'
                },
                min: 0,
                max: 100
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
                formatter: function(val) {
                    return val + '%';
                },
                offsetY: -20
            },
            grid: {
                show: true,
                borderColor: '#e7e7e7'
            }
        };
        new ApexCharts(document.querySelector('#chart-otd-fournisseurs'), optionsOTD).render();
    });
</script>

</body>
</html>