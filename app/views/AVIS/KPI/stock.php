<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Stock / Magasin</title>

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
        .risk-low { background-color: #28a745; }
        .risk-medium { background-color: #ffc107; }
        .risk-high { background-color: #dc3545; }
        .card-metric-small {
            border-left: 4px solid;
            margin-bottom: 1rem;
        }
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .status-ok { background-color: #28a745; }
        .status-warning { background-color: #ffc107; }
        .status-critical { background-color: #dc3545; }
        .table-actions {
            white-space: nowrap;
        }
        .sku-code {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 0.85rem;
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
                    <h3>Dashboard Magasin / Stock</h3>
                    <p class="text-subtitle text-muted">Suivi des opérations stock • Responsable: C. Bergmann • Mis à jour le <?= date('d/m/Y à H:i') ?></p>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#filterModal">
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
                            <i class="bi bi-x-circle me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong>Écart stock :</strong> 
                                <span class="ms-2"><strong>2 références</strong> avec écart > 10% (SKU-A215, SKU-C902) • Impacts: 2 840 €</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger">Analyser</button>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="alert alert-warning border-warning" style="border-left: 4px solid;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong>Obsolescence :</strong> 
                                <span class="ms-2"><strong>5 lots</strong> à risque de péremption dans ≤ 30 jours • Valeur: 7 200 €</span>
                            </div>
                            <button class="btn btn-sm btn-outline-warning">Voir détails</button>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="alert alert-info border-info" style="border-left: 4px solid;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong>Performance :</strong> 
                                <span class="ms-2">Productivité picking <strong>-4%</strong> vs M-1 (48 lignes/h au lieu de 50) • Investigation équipe requise</span>
                            </div>
                            <button class="btn btn-sm btn-outline-info">Détails</button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- KPI PRINCIPAUX -->
            <section class="row">
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Taux de précision stock</h6>
                                    <h3 class="font-extrabold mb-0">94%</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-success badge-metric">
                                            <i class="bi bi-check2-circle"></i> Théorique vs Physique
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-success">
                                    <i class="bi bi-box2-heart text-success"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Écart moyen: 6%</small>
                                    <small class="text-success">+2% vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 94%" aria-valuenow="94" aria-valuemin="0" aria-valuemax="100"></div>
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
                                    <h6 class="text-muted font-semibold mb-2">Obsolescence / Péremption</h6>
                                    <h3 class="font-extrabold mb-0">7 200 €</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-danger badge-metric">
                                            <i class="bi bi-clock-history"></i> Lots à risque
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-danger">
                                    <i class="bi bi-box-seam text-danger"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">5 lots critiques</small>
                                    <small class="text-danger">+3 vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: < 5 lots</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Productivité Préparation</h6>
                                    <h3 class="font-extrabold mb-0">48 lignes/h</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-info badge-metric">
                                            <i class="bi bi-people"></i> Picking
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-info">
                                    <i class="bi bi-speedometer2 text-info"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Erreurs: 2%</small>
                                    <small class="text-info">+0,5% vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 48%" aria-valuenow="48" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≥ 50 lignes/h</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Temps de traitement réception</h6>
                                    <h3 class="font-extrabold mb-0">6,8 h</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-warning badge-metric">
                                            <i class="bi bi-clock"></i> Dock-to-stock
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-warning">
                                    <i class="bi bi-box-arrow-in-down text-warning"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Moyenne cible: 5 h</small>
                                    <small class="text-warning">+1,8 h vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≤ 6 h</small>
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
                                    <small class="text-muted d-block">Valeur stock total</small>
                                    <h5 class="mb-0 text-primary">485 200 €</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">+3,2%</span>
                                    <small class="text-muted d-block mt-1">vs M-1</small>
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
                                    <small class="text-muted d-block">Références actives</small>
                                    <h5 class="mb-0 text-success">2 847</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">+24</span>
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
                                    <small class="text-muted d-block">Rotation des stocks</small>
                                    <h5 class="mb-0">8,2x/an</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning text-dark">+0,3</span>
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
                                    <small class="text-muted d-block">Taux de service</small>
                                    <h5 class="mb-0 text-success">98,1%</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">+1,2%</span>
                                    <small class="text-muted d-block mt-1">vs M-1</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="row">
            <section class="row">
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Taux de précision stock (12 derniers mois)</h4>
                            <p class="text-muted small mb-0">Écart théorique vs physique</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-stock-precision"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Obsolescence & Péremption</h4>
                            <p class="text-muted small mb-0">Valeur des lots à risque par mois</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-obsolescence"></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row">
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Productivité Préparation (Picking)</h4>
                            <p class="text-muted small mb-0">Lignes/heure avec objectif</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-productivite"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Temps de traitement réception (Dock-to-Stock)</h4>
                            <p class="text-muted small mb-0">Délai réception → mise en stock (heures)</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-docktostock"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ERREURS DE PICKING & TAUX DE SERVICE -->
            <section class="row">
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Taux d'erreurs Picking</h4>
                            <p class="text-muted small mb-0">Picking errors vs commandes préparées</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-picking-errors"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Taux de Service par Zone</h4>
                            <p class="text-muted small mb-0">Disponibilité article en stock (objectif ≥ 98%)</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-service-rate"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- RÉFÉRENCES CRITIQUES & ACTIONS -->
            <section class="row">
                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Lots à Risque - Obsolescence / Péremption</h4>
                                <div>
                                    <span class="badge bg-danger me-2">
                                        <i class="bi bi-exclamation-circle"></i> 5 lots critiques
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Export
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tableObsolescence">
                                    <thead>
                                        <tr>
                                            <th>Lot ID</th>
                                            <th>SKU / Réference</th>
                                            <th>Description</th>
                                            <th>Quantité</th>
                                            <th>Valeur Unit.</th>
                                            <th>Valeur Total</th>
                                            <th>Date Expiration</th>
                                            <th>Jours restants</th>
                                            <th>Risque</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>LOT-2024-001</strong></td>
                                            <td><span class="sku-code">SKU-A215</span></td>
                                            <td>Composant électronique</td>
                                            <td>125</td>
                                            <td>28,50 €</td>
                                            <td><strong>3 562 €</strong></td>
                                            <td>28/02/2026</td>
                                            <td><span class="badge bg-danger">38 jours</span></td>
                                            <td><span class="status-indicator status-warning"></span> Moyen</td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-warning" title="Promouvoir"><i class="bi bi-tag"></i></button>
                                                <button class="btn btn-sm btn-outline-info" title="Détails"><i class="bi bi-eye"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>LOT-2024-015</strong></td>
                                            <td><span class="sku-code">SKU-B402</span></td>
                                            <td>Matière première (résine)</td>
                                            <td>240</td>
                                            <td>12,30 €</td>
                                            <td><strong>2 952 €</strong></td>
                                            <td>15/02/2026</td>
                                            <td><span class="badge bg-danger">25 jours</span></td>
                                            <td><span class="status-indicator status-critical"></span> Élevé</td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-danger" title="Action urgente"><i class="bi bi-exclamation-triangle"></i></button>
                                                <button class="btn btn-sm btn-outline-info" title="Détails"><i class="bi bi-eye"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>LOT-2024-028</strong></td>
                                            <td><span class="sku-code">SKU-C902</span></td>
                                            <td>Packaging (emballages)</td>
                                            <td>500</td>
                                            <td>2,80 €</td>
                                            <td><strong>1 400 €</strong></td>
                                            <td>10/03/2026</td>
                                            <td><span class="badge bg-warning text-dark">48 jours</span></td>
                                            <td><span class="status-indicator status-ok"></span> Faible</td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-info" title="Détails"><i class="bi bi-eye"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>LOT-2024-042</strong></td>
                                            <td><span class="sku-code">SKU-D110</span></td>
                                            <td>Accessoires (joints)</td>
                                            <td>180</td>
                                            <td>8,50 €</td>
                                            <td><strong>1 530 €</strong></td>
                                            <td>05/02/2026</td>
                                            <td><span class="badge bg-danger">15 jours</span></td>
                                            <td><span class="status-indicator status-critical"></span> Critique</td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-danger" title="Destruire"><i class="bi bi-trash"></i></button>
                                                <button class="btn btn-sm btn-outline-info" title="Détails"><i class="bi bi-eye"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>LOT-2024-056</strong></td>
                                            <td><span class="sku-code">SKU-E225</span></td>
                                            <td>Produits chimiques (solvant)</td>
                                            <td>60</td>
                                            <td>22,00 €</td>
                                            <td><strong>1 320 €</strong></td>
                                            <td>12/03/2026</td>
                                            <td><span class="badge bg-warning text-dark">50 jours</span></td>
                                            <td><span class="status-indicator status-ok"></span> Faible</td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-info" title="Détails"><i class="bi bi-eye"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Informations - Responsable Stock</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong>Magasin / Stock Manager</strong>
                                    <span class="badge bg-primary">Assigné</span>
                                </div>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-person-fill"></i> <strong>Cédric Bergmann</strong><br>
                                    <i class="bi bi-envelope"></i> c.bergmann@company.com<br>
                                    <i class="bi bi-telephone"></i> +33 2 45 67 89 01
                                </p>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <h6 class="font-semibold mb-2">Équipe</h6>
                                <small class="text-muted">
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-person"></i> Pierre Dumont (Picking - Morning)</li>
                                        <li><i class="bi bi-person"></i> Valérie Marchand (Réception)</li>
                                        <li><i class="bi bi-person"></i> Antoine Lefevre (Inventaire)</li>
                                    </ul>
                                </small>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <h6 class="font-semibold mb-2">Objectifs du Mois</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success"></i> 
                                        <span>Précision stock ≥ 98%</span>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-clock text-warning"></i> 
                                        <span>Dock-to-stock ≤ 6h</span>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-exclamation-triangle text-danger"></i> 
                                        <span>Réduire obsolescence < 5 lots</span>
                                    </li>
                                    <li>
                                        <i class="bi bi-speedometer2 text-info"></i> 
                                        <span>Productivité ≥ 50 lignes/h</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ÉCARTS DE STOCK DÉTAILLÉS -->
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-0">Écarts de Stock - Audit Théorique vs Physique</h4>
                                    <p class="text-muted small mb-0">Derniers inventaires • Écarts > 2%</p>
                                </div>
                                <span class="badge bg-danger fs-6">2 écarts critiques</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tableEcarts">
                                    <thead>
                                        <tr>
                                            <th>SKU / Réference</th>
                                            <th>Description</th>
                                            <th>Stock Théorique</th>
                                            <th>Stock Physique</th>
                                            <th>Écart</th>
                                            <th>% Écart</th>
                                            <th>Valeur Écart (€)</th>
                                            <th>Date Audit</th>
                                            <th>Priorité</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong><span class="sku-code">SKU-A215</span></strong></td>
                                            <td>Composant électronique</td>
                                            <td>350</td>
                                            <td>310</td>
                                            <td><span class="badge bg-danger">-40</span></td>
                                            <td><span class="badge bg-danger">-11,4%</span></td>
                                            <td>-1 140 €</td>
                                            <td>18/01/2026</td>
                                            <td><span class="badge bg-danger">Critique</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-search"></i></button>
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><span class="sku-code">SKU-C902</span></strong></td>
                                            <td>Packaging (emballages)</td>
                                            <td>1200</td>
                                            <td>1060</td>
                                            <td><span class="badge bg-danger">-140</span></td>
                                            <td><span class="badge bg-danger">-11,7%</span></td>
                                            <td>-1 700 €</td>
                                            <td>17/01/2026</td>
                                            <td><span class="badge bg-danger">Critique</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-search"></i></button>
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong><span class="sku-code">SKU-B402</span></strong></td>
                                            <td>Matière première (résine)</td>
                                            <td>450</td>
                                            <td>440</td>
                                            <td><span class="badge bg-warning text-dark">-10</span></td>
                                            <td><span class="badge bg-warning text-dark">-2,2%</span></td>
                                            <td>-123 €</td>
                                            <td>16/01/2026</td>
                                            <td><span class="badge bg-warning text-dark">Moyen</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-info"><i class="bi bi-search"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ACTIONS RECOMMANDÉES -->
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Actions Recommandées</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="action-item mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-danger me-3 mt-1">🔴 URGENCE</span>
                                            <div>
                                                <h6 class="mb-1">Résoudre écarts SKU-A215 & C902</h6>
                                                <p class="text-muted small mb-1">Perte totale: 2 840 € • Investigation & correction inventaire</p>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-arrow-right"></i> Enquête terrain
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="action-item mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-warning text-dark me-3 mt-1">⚠️ ATTENTION</span>
                                            <div>
                                                <h6 class="mb-1">Liquider lots périmés LOT-2024-015</h6>
                                                <p class="text-muted small mb-1">Expiration 15/02 → 2 952 € • Moins de 25 jours</p>
                                                <button class="btn btn-sm btn-outline-warning">
                                                    <i class="bi bi-arrow-right"></i> Démarque urgente
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="action-item mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-info me-3 mt-1">ℹ️ INFO</span>
                                            <div>
                                                <h6 class="mb-1">Améliorer productivité picking</h6>
                                                <p class="text-muted small mb-1">48 lignes/h vs 50 objectif (-4%) • Causede ralentissements?</p>
                                                <button class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-arrow-right"></i> Audit équipe
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="action-item mb-3">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-success me-3 mt-1">✅ À FAIRE</span>
                                            <div>
                                                <h6 class="mb-1">Accélérer dock-to-stock</h6>
                                                <p class="text-muted small mb-1">6,8h vs 6h objectif • Formation contrôle réception?</p>
                                                <button class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-arrow-right"></i> Optimiser process
                                                </button>
                                            </div>
                                        </div>
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
                        <p>Pour toute question: <strong>c.bergmann@company.com</strong> | Magasin & Logistique</p>
                    </div>
                </div>
            </section>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>

<script>
    // Initialiser DataTables
    $(document).ready(function() {
        // Table Obsolescence
        $('#tableObsolescence').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            columnDefs: [
                { targets: -1, orderable: false }
            ],
            order: [[7, 'asc']]
        });

        // Table Écarts
        $('#tableEcarts').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            columnDefs: [
                { targets: -1, orderable: false }
            ],
            order: [[5, 'desc']]
        });

        // Graphique 1: Taux de précision stock
        var optionsPrecision = {
            series: [
                {
                    name: 'Précision %',
                    data: [92, 93, 91, 94, 92, 93, 94, 95, 94, 94, 93, 94]
                },
                {
                    name: 'Objectif %',
                    data: [98, 98, 98, 98, 98, 98, 98, 98, 98, 98, 98, 98]
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
                }
            },
            colors: ['#28a745', '#435ebe'],
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
                },
                min: 80,
                max: 100
            },
            legend: {
                position: 'top'
            },
            grid: {
                show: true,
                borderColor: '#e7e7e7',
                strokeDashArray: 0
            }
        };
        new ApexCharts(document.querySelector('#chart-stock-precision'), optionsPrecision).render();

        // Graphique 2: Obsolescence
        var optionsObsolescence = {
            series: [
                {
                    name: 'Valeur €',
                    data: [5000, 4800, 4700, 5200, 5000, 5100, 5300, 5400, 5200, 5300, 5000, 7200]
                }
            ],
            chart: {
                type: 'column',
                height: 350,
                toolbar: {
                    show: true
                }
            },
            colors: ['#dc3545'],
            xaxis: {
                categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
            },
            yaxis: {
                title: {
                    text: 'Valeur (€)'
                }
            },
            plotOptions: {
                column: {
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
                    return val.toLocaleString() + '€';
                },
                offsetY: -20
            },
            grid: {
                show: true,
                borderColor: '#e7e7e7'
            }
        };
        new ApexCharts(document.querySelector('#chart-obsolescence'), optionsObsolescence).render();

        // Graphique 3: Productivité Picking
        var optionsProductivite = {
            series: [
                {
                    name: 'Lignes/heure',
                    data: [45, 46, 47, 48, 46, 47, 48, 49, 48, 48, 47, 48]
                },
                {
                    name: 'Objectif',
                    data: [50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50]
                }
            ],
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: true
                }
            },
            colors: ['#435ebe', '#28a745'],
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
                    text: 'Lignes par heure'
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
        new ApexCharts(document.querySelector('#chart-productivite'), optionsProductivite).render();

        // Graphique 4: Dock-to-Stock
        var optionsDockToStock = {
            series: [
                {
                    name: 'Temps réel (h)',
                    data: [6.5, 6.7, 6.4, 6.8, 6.5, 6.6, 6.7, 6.8, 6.7, 6.8, 6.6, 6.8]
                },
                {
                    name: 'Objectif (h)',
                    data: [6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6]
                }
            ],
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: true
                }
            },
            colors: ['#ffc107', '#28a745'],
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
                    text: 'Heures'
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
        new ApexCharts(document.querySelector('#chart-docktostock'), optionsDockToStock).render();

        // Graphique 5: Erreurs de Picking
        var optionsPickingErrors = {
            series: [
                {
                    name: 'Taux d\'erreurs (%)',
                    data: [2.1, 1.9, 2.2, 1.8, 2.0, 1.9, 2.1, 1.7, 2.0, 2.1, 1.8, 2.0]
                },
                {
                    name: 'Objectif (%)',
                    data: [1.5, 1.5, 1.5, 1.5, 1.5, 1.5, 1.5, 1.5, 1.5, 1.5, 1.5, 1.5]
                }
            ],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: true
                }
            },
            colors: ['#dc3545', '#28a745'],
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                categories: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc']
            },
            yaxis: {
                title: {
                    text: 'Taux (%)'
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    opacityFrom: 0.45,
                    opacityTo: 0.05
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
        new ApexCharts(document.querySelector('#chart-picking-errors'), optionsPickingErrors).render();

        // Graphique 6: Taux de Service par Zone
        var optionsServiceRate = {
            series: [
                {
                    name: 'Taux service (%)',
                    data: [96.5, 97.2, 96.8, 97.5, 97.2, 97.8, 98.1, 97.9, 98.0, 97.6, 97.4, 98.1]
                },
                {
                    name: 'Objectif (%)',
                    data: [98, 98, 98, 98, 98, 98, 98, 98, 98, 98, 98, 98]
                }
            ],
            chart: {
                type: 'line',
                height: 350,
                toolbar: {
                    show: true
                }
            },
            colors: ['#17a2b8', '#28a745'],
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
                    text: 'Taux de service (%)'
                },
                min: 95,
                max: 100
            },
            legend: {
                position: 'top'
            },
            grid: {
                show: true,
                borderColor: '#e7e7e7'
            }
        };
        new ApexCharts(document.querySelector('#chart-service-rate'), optionsServiceRate).render();
    });
</script>

</body>
</html>
