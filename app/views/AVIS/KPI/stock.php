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

            <?php
            // Extraction des données KPI
            $precision = $kpi['precision'] ?? [];
            $obsolescence = $kpi['obsolescence'] ?? [];
            $productivite = $kpi['productivite'] ?? [];
            $dockToStock = $kpi['dockToStock'] ?? [];
            $valeurStock = $kpi['valeurStock'] ?? [];
            $refsActives = $kpi['referencesActives'] ?? [];
            $rotation = $kpi['rotationStock'] ?? [];
            $tauxService = $kpi['tauxService'] ?? [];
            $lotsRisque = $kpi['lotsRisque'] ?? [];
            $ecartsStock = $kpi['ecartsStock'] ?? [];
            $charts = $kpi['charts'] ?? [];
            ?>
            
            <!-- KPI PRINCIPAUX -->
            <section class="row">
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Taux de précision stock</h6>
                                    <h3 class="font-extrabold mb-0"><?= number_format($precision['taux'] ?? 0, 1, ',', ' ') ?>%</h3>
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
                                    <small class="text-muted">Écart moyen: <?= number_format($precision['ecart_moyen'] ?? 0, 1, ',', ' ') ?>%</small>
                                    <small class="<?= ($precision['variation'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= ($precision['variation'] ?? 0) >= 0 ? '+' : '' ?><?= number_format($precision['variation'] ?? 0, 1, ',', ' ') ?>% vs M-1
                                    </small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $precision['taux'] ?? 0 ?>%" aria-valuenow="<?= $precision['taux'] ?? 0 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≥ <?= $precision['objectif'] ?? 98 ?>%</small>
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
                                    <h3 class="font-extrabold mb-0"><?= number_format($obsolescence['valeur_risque'] ?? 0, 0, ',', ' ') ?> Ar</h3>
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
                                    <small class="text-muted"><?= $obsolescence['nb_critiques'] ?? 0 ?> lots critiques</small>
                                    <small class="<?= ($obsolescence['variation'] ?? 0) > 0 ? 'text-danger' : 'text-success' ?>">
                                        <?= ($obsolescence['variation'] ?? 0) >= 0 ? '+' : '' ?><?= $obsolescence['variation'] ?? 0 ?> vs M-1
                                    </small>
                                </div>
                                <div class="progress progress-thin">
                                    <?php $obsProgress = min(100, (($obsolescence['nb_lots'] ?? 0) / max($obsolescence['objectif'] ?? 5, 1)) * 100); ?>
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $obsProgress ?>%" aria-valuenow="<?= $obsProgress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: < <?= $obsolescence['objectif'] ?? 5 ?> lots</small>
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
                                    <h3 class="font-extrabold mb-0"><?= $productivite['lignes_par_heure'] ?? 0 ?> lignes/h</h3>
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
                                    <small class="text-muted">Erreurs: <?= number_format($productivite['taux_erreur'] ?? 0, 1, ',', ' ') ?>%</small>
                                    <small class="<?= ($productivite['variation'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= ($productivite['variation'] ?? 0) >= 0 ? '+' : '' ?><?= $productivite['variation'] ?? 0 ?> vs M-1
                                    </small>
                                </div>
                                <div class="progress progress-thin">
                                    <?php $prodProgress = min(100, (($productivite['lignes_par_heure'] ?? 0) / max($productivite['objectif'] ?? 50, 1)) * 100); ?>
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?= $prodProgress ?>%" aria-valuenow="<?= $prodProgress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≥ <?= $productivite['objectif'] ?? 50 ?> lignes/h</small>
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
                                    <h3 class="font-extrabold mb-0"><?= number_format($dockToStock['temps_moyen'] ?? 0, 1, ',', ' ') ?> h</h3>
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
                                    <small class="text-muted">Moyenne cible: <?= $dockToStock['objectif_cible'] ?? 5 ?> h</small>
                                    <small class="<?= ($dockToStock['variation'] ?? 0) <= 0 ? 'text-success' : 'text-warning' ?>">
                                        <?= ($dockToStock['variation'] ?? 0) >= 0 ? '+' : '' ?><?= number_format($dockToStock['variation'] ?? 0, 1, ',', ' ') ?> h vs M-1
                                    </small>
                                </div>
                                <div class="progress progress-thin">
                                    <?php $dtsProgress = min(100, (($dockToStock['temps_moyen'] ?? 0) / max($dockToStock['objectif'] ?? 6, 1)) * 100); ?>
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $dtsProgress ?>%" aria-valuenow="<?= $dtsProgress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≤ <?= $dockToStock['objectif'] ?? 6 ?> h</small>
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
                                    <h5 class="mb-0 text-primary"><?= number_format($valeurStock['valeur'] ?? 0, 0, ',', ' ') ?> Ar</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge <?= ($valeurStock['variation'] ?? 0) >= 0 ? 'bg-primary' : 'bg-danger' ?>">
                                        <?= ($valeurStock['variation'] ?? 0) >= 0 ? '+' : '' ?><?= number_format($valeurStock['variation'] ?? 0, 1, ',', ' ') ?>%
                                    </span>
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
                                    <h5 class="mb-0 text-success"><?= number_format($refsActives['count'] ?? 0, 0, ',', ' ') ?></h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success"><?= ($refsActives['variation'] ?? 0) >= 0 ? '+' : '' ?><?= $refsActives['variation'] ?? 0 ?></span>
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
                                    <h5 class="mb-0"><?= number_format($rotation['rotation'] ?? 0, 1, ',', ' ') ?>x/an</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning text-dark"><?= ($rotation['variation'] ?? 0) >= 0 ? '+' : '' ?><?= number_format($rotation['variation'] ?? 0, 1, ',', ' ') ?></span>
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
                                    <h5 class="mb-0 text-success"><?= number_format($tauxService['taux'] ?? 0, 1, ',', ' ') ?>%</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success"><?= ($tauxService['variation'] ?? 0) >= 0 ? '+' : '' ?><?= number_format($tauxService['variation'] ?? 0, 1, ',', ' ') ?>%</span>
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
                                        <i class="bi bi-exclamation-circle"></i> <?= $obsolescence['nb_critiques'] ?? 0 ?> lots critiques
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
                                        <?php if (empty($lotsRisque)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">Aucun lot à risque</td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($lotsRisque as $lot): ?>
                                        <?php 
                                            $jours = (int)($lot['jours_restants'] ?? 0);
                                            $badgeClass = $jours <= 30 ? 'bg-danger' : 'bg-warning text-dark';
                                            $statusClass = $lot['risque'] === 'critique' ? 'status-critical' : ($lot['risque'] === 'eleve' || $lot['risque'] === 'moyen' ? 'status-warning' : 'status-ok');
                                        ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($lot['lot_numero'] ?? '') ?></strong></td>
                                            <td><span class="sku-code"><?= htmlspecialchars($lot['article_code'] ?? '') ?></span></td>
                                            <td><?= htmlspecialchars($lot['article_designation'] ?? '') ?></td>
                                            <td><?= number_format($lot['quantite_restante'] ?? 0, 0, ',', ' ') ?></td>
                                            <td><?= number_format($lot['cout_unitaire'] ?? 0, 2, ',', ' ') ?> Ar</td>
                                            <td><strong><?= number_format($lot['valeur_totale'] ?? 0, 0, ',', ' ') ?> Ar</strong></td>
                                            <td><?= $lot['date_expiration'] ? date('d/m/Y', strtotime($lot['date_expiration'])) : '-' ?></td>
                                            <td><span class="badge <?= $badgeClass ?>"><?= $jours ?> jours</span></td>
                                            <td><span class="status-indicator <?= $statusClass ?>"></span> <?= $lot['risque_label'] ?? '' ?></td>
                                            <td class="table-actions">
                                                <?php if ($lot['risque'] === 'critique'): ?>
                                                <button class="btn btn-sm btn-danger" title="Destruire"><i class="bi bi-trash"></i></button>
                                                <?php elseif ($lot['risque'] === 'eleve'): ?>
                                                <button class="btn btn-sm btn-outline-danger" title="Action urgente"><i class="bi bi-exclamation-triangle"></i></button>
                                                <?php else: ?>
                                                <button class="btn btn-sm btn-outline-warning" title="Promouvoir"><i class="bi bi-tag"></i></button>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-outline-info" title="Détails"><i class="bi bi-eye"></i></button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
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
                                <?php $nbCritiques = count(array_filter($ecartsStock, fn($e) => $e['priorite'] === 'critique')); ?>
                                <span class="badge bg-danger fs-6"><?= $nbCritiques ?> écarts critiques</span>
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
                                            <th>Valeur Écart (Ar)</th>
                                            <th>Date Audit</th>
                                            <th>Priorité</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($ecartsStock)): ?>
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">Aucun écart significatif</td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($ecartsStock as $ecart): ?>
                                        <?php 
                                            $pctEcart = (float)($ecart['pourcent_ecart'] ?? 0);
                                            $badgeClass = $ecart['priorite'] === 'critique' ? 'bg-danger' : ($ecart['priorite'] === 'moyen' ? 'bg-warning text-dark' : 'bg-secondary');
                                        ?>
                                        <tr>
                                            <td><strong><span class="sku-code"><?= htmlspecialchars($ecart['article_code'] ?? '') ?></span></strong></td>
                                            <td><?= htmlspecialchars($ecart['article_designation'] ?? '') ?></td>
                                            <td><?= number_format($ecart['quantite_theorique'] ?? 0, 0, ',', ' ') ?></td>
                                            <td><?= number_format($ecart['quantite_comptee'] ?? 0, 0, ',', ' ') ?></td>
                                            <td><span class="badge <?= $badgeClass ?>"><?= ($ecart['ecart'] ?? 0) >= 0 ? '+' : '' ?><?= number_format($ecart['ecart'] ?? 0, 0, ',', ' ') ?></span></td>
                                            <td><span class="badge <?= $badgeClass ?>"><?= $pctEcart >= 0 ? '+' : '' ?><?= number_format($pctEcart, 1, ',', ' ') ?>%</span></td>
                                            <td><?= number_format($ecart['valeur_ecart'] ?? 0, 0, ',', ' ') ?> Ar</td>
                                            <td><?= $ecart['date_audit'] ? date('d/m/Y', strtotime($ecart['date_audit'])) : '-' ?></td>
                                            <td><span class="badge <?= $badgeClass ?>"><?= $ecart['priorite_label'] ?? '' ?></span></td>
                                            <td class="table-actions">
                                                <?php if ($ecart['priorite'] === 'critique'): ?>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-search"></i></button>
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></button>
                                                <?php else: ?>
                                                <button class="btn btn-sm btn-outline-info"><i class="bi bi-search"></i></button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
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
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script>
    // Données des graphiques depuis PHP
    const chartsData = <?= json_encode($charts ?? []) ?>;
    
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
        var precisionData = chartsData.precision || {labels: [], data: [], objectif: []};
        var optionsPrecision = {
            series: [
                {
                    name: 'Précision %',
                    data: precisionData.data.length > 0 ? precisionData.data : [0]
                },
                {
                    name: 'Objectif %',
                    data: precisionData.objectif.length > 0 ? precisionData.objectif : [98]
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
                categories: precisionData.labels.length > 0 ? precisionData.labels : ['']
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
        var obsolescenceData = chartsData.obsolescence || {labels: [], data: []};
        var optionsObsolescence = {
            series: [
                {
                    name: 'Valeur Ar',
                    data: obsolescenceData.data.length > 0 ? obsolescenceData.data : [0]
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
                categories: obsolescenceData.labels.length > 0 ? obsolescenceData.labels : ['']
            },
            yaxis: {
                title: {
                    text: 'Valeur (Ar)'
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
                    return val.toLocaleString() + 'Ar';
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
        var productiviteData = chartsData.productivite || {labels: [], data: [], objectif: []};
        var optionsProductivite = {
            series: [
                {
                    name: 'Lignes/heure',
                    data: productiviteData.data.length > 0 ? productiviteData.data : [0]
                },
                {
                    name: 'Objectif',
                    data: productiviteData.objectif.length > 0 ? productiviteData.objectif : [50]
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
                categories: productiviteData.labels.length > 0 ? productiviteData.labels : ['']
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
        var dockToStockData = chartsData.dockToStock || {labels: [], data: [], objectif: []};
        var optionsDockToStock = {
            series: [
                {
                    name: 'Temps réel (h)',
                    data: dockToStockData.data.length > 0 ? dockToStockData.data : [0]
                },
                {
                    name: 'Objectif (h)',
                    data: dockToStockData.objectif.length > 0 ? dockToStockData.objectif : [6]
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
                categories: dockToStockData.labels.length > 0 ? dockToStockData.labels : ['']
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
        var pickingErrorsData = chartsData.pickingErrors || {labels: [], data: [], objectif: []};
        var optionsPickingErrors = {
            series: [
                {
                    name: 'Taux d\'erreurs (%)',
                    data: pickingErrorsData.data.length > 0 ? pickingErrorsData.data : [0]
                },
                {
                    name: 'Objectif (%)',
                    data: pickingErrorsData.objectif.length > 0 ? pickingErrorsData.objectif : [1.5]
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
                categories: pickingErrorsData.labels.length > 0 ? pickingErrorsData.labels : ['']
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
        var serviceRateData = chartsData.serviceRate || {labels: [], data: [], objectif: []};
        var optionsServiceRate = {
            series: [
                {
                    name: 'Taux service (%)',
                    data: serviceRateData.data.length > 0 ? serviceRateData.data : [0]
                },
                {
                    name: 'Objectif (%)',
                    data: serviceRateData.objectif.length > 0 ? serviceRateData.objectif : [98]
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
                categories: serviceRateData.labels.length > 0 ? serviceRateData.labels : ['']
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
