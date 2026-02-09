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

                <?php
                // Extraction des données KPI
                $ca = $kpi['chiffreAffaires'] ?? [];
                $marge = $kpi['margeBrute'] ?? [];
                $valeurStock = $kpi['valeurStock'] ?? [];
                $rotation = $kpi['rotationStock'] ?? [];
                $sites = $kpi['performancesSites'] ?? [];
                $surstocks = $kpi['surstocks'] ?? [];
                $ecarts = $kpi['ecartsInventaire'] ?? [];
                $alertes = $kpi['alertes'] ?? [];
                $obsolescence = $kpi['obsolescenceAnalyse'] ?? [];
                $charts = $kpi['charts'] ?? [];
                ?>

                <!-- ALERTES & INDICATEURS CRITIQUES -->
                <?php if (!empty($alertes)): ?>
                <section class="row mb-4">
                    <div class="col-12">
                        <?php foreach ($alertes as $alerte): ?>
                        <div class="alert alert-custom alert-<?= $alerte['type'] ?>" style="border-left-color: <?= $alerte['type'] === 'danger' ? '#dc3545' : '#ffc107' ?>;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                                <div>
                                    <strong>Attention :</strong> <?= htmlspecialchars($alerte['message']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- KPI CARDS PRINCIPAUX -->
                <section class="row">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body px-4 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="text-muted font-semibold mb-2">Chiffre d'Affaires</h6>
                                        <h3 class="font-extrabold mb-0"><?= number_format($ca['valeur'] ?? 0, 0, ',', ' ') ?> Ar</h3>
                                        <div class="mt-2">
                                            <span class="badge bg-<?= ($ca['variationM1'] ?? 0) >= 0 ? 'success' : 'danger' ?>">
                                                <i class="bi bi-arrow-<?= ($ca['variationM1'] ?? 0) >= 0 ? 'up' : 'down' ?>"></i> <?= ($ca['variationM1'] ?? 0) >= 0 ? '+' : '' ?><?= number_format($ca['variationM1'] ?? 0, 1, ',', ' ') ?>% vs M-1
                                            </span>
                                            <span class="badge bg-light-<?= ($ca['variationM12'] ?? 0) >= 0 ? 'success' : 'danger' ?> ms-1">
                                                <?= ($ca['variationM12'] ?? 0) >= 0 ? '+' : '' ?><?= number_format($ca['variationM12'] ?? 0, 1, ',', ' ') ?>% vs M-12
                                            </span>
                                        </div>
                                    </div>
                                    <div class="metric-icon bg-light-success">
                                        <i class="bi bi-currency-euro text-success"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Objectif mensuel: <?= number_format($ca['objectif'] ?? 0, 0, ',', ' ') ?> Ar</small>
                                    <div class="progress progress-thin mt-2">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= min($ca['progressObjectif'] ?? 0, 100) ?>%" aria-valuenow="<?= $ca['progressObjectif'] ?? 0 ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
                                        <h3 class="font-extrabold mb-0"><?= number_format($marge['valeur'] ?? 0, 0, ',', ' ') ?> Ar</h3>
                                        <div class="mt-2">
                                            <span class="badge bg-info">
                                                <?= number_format($marge['tauxMarge'] ?? 0, 1, ',', ' ') ?>% du CA
                                            </span>
                                            <span class="badge bg-light-info ms-1">
                                                <?= ($marge['variationPts'] ?? 0) >= 0 ? '+' : '' ?><?= number_format($marge['variationPts'] ?? 0, 1, ',', ' ') ?> pts vs M-1
                                            </span>
                                        </div>
                                    </div>
                                    <div class="metric-icon bg-light-info">
                                        <i class="bi bi-graph-up text-info"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Objectif: <?= $marge['objectif'] ?? 35 ?>%</small>
                                    <div class="progress progress-thin mt-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= min($marge['progressObjectif'] ?? 0, 100) ?>%" aria-valuenow="<?= $marge['progressObjectif'] ?? 0 ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
                                        <h3 class="font-extrabold mb-0"><?= number_format($valeurStock['valeur'] ?? 0, 0, ',', ' ') ?> Ar</h3>
                                        <div class="mt-2">
                                            <span class="badge bg-<?= ($valeurStock['variationM1'] ?? 0) <= 0 ? 'warning' : 'danger' ?> text-dark">
                                                <i class="bi bi-arrow-<?= ($valeurStock['variationM1'] ?? 0) >= 0 ? 'up' : 'down' ?>"></i> <?= number_format($valeurStock['variationM1'] ?? 0, 1, ',', ' ') ?>% vs M-1
                                            </span>
                                            <span class="badge bg-light-<?= ($valeurStock['variationM12'] ?? 0) <= 0 ? 'success' : 'danger' ?> text-dark ms-1">
                                                <?= number_format($valeurStock['variationM12'] ?? 0, 1, ',', ' ') ?>% vs M-12
                                            </span>
                                        </div>
                                    </div>
                                    <div class="metric-icon bg-light-warning">
                                        <i class="bi bi-box-seam text-warning"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Couverture: <?= number_format($valeurStock['couvertureJours'] ?? 0, 1, ',', ' ') ?> jours</small>
                                    <div class="progress progress-thin mt-2">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= min(($valeurStock['couvertureJours'] ?? 0) * 3, 100) ?>%" aria-valuenow="<?= $valeurStock['couvertureJours'] ?? 0 ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
                                        <h3 class="font-extrabold mb-0"><?= number_format($rotation['valeur'] ?? 0, 1, ',', ' ') ?></h3>
                                        <div class="mt-2">
                                            <?php if ($rotation['sousCible'] ?? true): ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-exclamation-circle"></i> Sous objectif
                                            </span>
                                            <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Objectif atteint
                                            </span>
                                            <?php endif; ?>
                                            <span class="badge bg-light-secondary ms-1">
                                                Cible: ≥ <?= $rotation['objectif'] ?? 5 ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="metric-icon bg-light-<?= ($rotation['sousCible'] ?? true) ? 'danger' : 'success' ?>">
                                        <i class="bi bi-arrow-repeat text-<?= ($rotation['sousCible'] ?? true) ? 'danger' : 'success' ?>"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">Écart: <?= number_format($rotation['ecartObjectif'] ?? 0, 0, ',', ' ') ?>% vs objectif</small>
                                    <div class="progress progress-thin mt-2">
                                        <div class="progress-bar bg-<?= ($rotation['sousCible'] ?? true) ? 'danger' : 'success' ?>" role="progressbar" style="width: <?= min($rotation['progressObjectif'] ?? 0, 100) ?>%" aria-valuenow="<?= $rotation['progressObjectif'] ?? 0 ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
                                            <?php foreach (($sites['sites'] ?? []) as $site): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($site['site']) ?></strong></td>
                                                <td><?= number_format($site['ca'], 0, ',', ' ') ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $site['variationCA'] >= 0 ? 'success' : 'danger' ?>">
                                                        <?= $site['variationCA'] >= 0 ? '+' : '' ?><?= number_format($site['variationCA'], 1, ',', ' ') ?>%
                                                    </span>
                                                </td>
                                                <td><?= number_format($site['margeBrute'], 0, ',', ' ') ?></td>
                                                <td><span class="badge bg-info"><?= number_format($site['tauxMarge'], 1, ',', ' ') ?>%</span></td>
                                                <td><?= number_format($site['valeurStock'], 0, ',', ' ') ?></td>
                                                <td><?= number_format($site['rotation'], 1, ',', ' ') ?></td>
                                                <td>
                                                    <?php if ($site['statut'] === 'Bon'): ?>
                                                    <span class="badge bg-success">Bon</span>
                                                    <?php elseif ($site['statut'] === 'Moyen'): ?>
                                                    <span class="badge bg-warning text-dark">Moyen</span>
                                                    <?php else: ?>
                                                    <span class="badge bg-danger">Alerte</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td><strong>TOTAL</strong></td>
                                                <td><strong><?= number_format($sites['totaux']['ca'] ?? 0, 0, ',', ' ') ?></strong></td>
                                                <td>-</td>
                                                <td><strong><?= number_format($sites['totaux']['margeBrute'] ?? 0, 0, ',', ' ') ?></strong></td>
                                                <td><span class="badge bg-info"><?= number_format($sites['totaux']['tauxMarge'] ?? 0, 1, ',', ' ') ?>%</span></td>
                                                <td><strong><?= number_format($sites['totaux']['valeurStock'] ?? 0, 0, ',', ' ') ?></strong></td>
                                                <td><strong><?= number_format($sites['totaux']['rotation'] ?? 0, 1, ',', ' ') ?></strong></td>
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
                                        <strong class="text-<?= ($valeurStock['variationM1'] ?? 0) <= 0 ? 'warning' : 'danger' ?>"><?= number_format($valeurStock['variationM1'] ?? 0, 1, ',', ' ') ?>%</strong>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-warning" style="width: <?= min(100, abs($valeurStock['variationM1'] ?? 0) * 5 + 50) ?>%"><?= number_format(($valeurStock['valeur'] ?? 0) / 1000, 0, ',', ' ') ?> KAr</div>
                                    </div>
                                    <small class="text-muted">M-1: <?= number_format($valeurStock['valeurM1'] ?? 0, 0, ',', ' ') ?> Ar</small>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Valeur Stock vs M-12</span>
                                        <strong class="text-<?= ($valeurStock['variationM12'] ?? 0) <= 0 ? 'success' : 'danger' ?>"><?= number_format($valeurStock['variationM12'] ?? 0, 1, ',', ' ') ?>%</strong>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" style="width: <?= min(100, abs($valeurStock['variationM12'] ?? 0) * 5 + 50) ?>%"><?= number_format(($valeurStock['valeur'] ?? 0) / 1000, 0, ',', ' ') ?> KAr</div>
                                    </div>
                                    <small class="text-muted">M-12: <?= number_format($valeurStock['valeurM12'] ?? 0, 0, ',', ' ') ?> Ar</small>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <small class="text-muted d-block">Rotation actuelle</small>
                                        <h4 class="mb-0"><?= number_format($rotation['valeur'] ?? 0, 1, ',', ' ') ?></h4>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">Objectif</small>
                                        <h4 class="mb-0 text-success">≥ <?= number_format($rotation['objectif'] ?? 5, 1, ',', ' ') ?></h4>
                                    </div>
                                </div>

                                <div class="alert alert-light-<?= ($rotation['sousCible'] ?? true) ? 'danger' : 'success' ?> mb-0">
                                    <i class="bi bi-info-circle"></i> 
                                    <strong><?= number_format($rotation['ecartObjectif'] ?? 0, 0, ',', ' ') ?>%</strong> <?= ($rotation['sousCible'] ?? true) ? 'sous' : 'au-dessus de' ?> l'objectif de rotation
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
                                    <span class="badge bg-danger">Valeur immobilisée: <?= number_format($surstocks['valeurTotale'] ?? 0, 0, ',', ' ') ?> Ar</span>
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
                                            <?php foreach (($surstocks['articles'] ?? []) as $art): ?>
                                            <tr>
                                                <td><?= $art['rang'] ?></td>
                                                <td><?= htmlspecialchars($art['article']) ?></td>
                                                <td><?= htmlspecialchars($art['reference']) ?></td>
                                                <td><?= number_format($art['quantite'], 0, ',', ' ') ?></td>
                                                <td><?= number_format($art['valeur'], 0, ',', ' ') ?></td>
                                                <td><?= htmlspecialchars($art['derniereVente']) ?></td>
                                                <td>
                                                    <?php if ($art['statut'] === 'Obsolète'): ?>
                                                    <span class="badge bg-danger">Obsolète</span>
                                                    <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Surstock</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($surstocks['articles'])): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Aucun article en surstock ou obsolescence</td>
                                            </tr>
                                            <?php endif; ?>
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
                                    <?php if (($surstocks['nbObsolete'] ?? 0) > 0): ?>
                                    <div class="alert alert-light-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <strong><?= $surstocks['nbObsolete'] ?> article(s)</strong> sans mouvement depuis +12 mois
                                    </div>
                                    <?php endif; ?>
                                    <?php if (($surstocks['nbSurstock'] ?? 0) > 0): ?>
                                    <div class="alert alert-light-info">
                                        <i class="bi bi-info-circle"></i>
                                        <strong><?= $surstocks['nbSurstock'] ?> article(s)</strong> en surstock (>6 mois de couverture)
                                    </div>
                                    <?php endif; ?>
                                    <?php if (($surstocks['nbObsolete'] ?? 0) == 0 && ($surstocks['nbSurstock'] ?? 0) == 0): ?>
                                    <div class="alert alert-light-success">
                                        <i class="bi bi-check-circle"></i>
                                        <strong>Aucun</strong> article en situation critique
                                    </div>
                                    <?php endif; ?>
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
                                    <span class="badge bg-light-secondary">Dernier inventaire: <?= $ecarts['dateInventaire'] ? date('d/m/Y', strtotime($ecarts['dateInventaire'])) : 'N/A' ?></span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card bg-light-<?= ($ecarts['totaux']['ecartValeur'] ?? 0) < 0 ? 'danger' : 'success' ?>">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Écart Total (Valeur)</h6>
                                                <h3 class="text-<?= ($ecarts['totaux']['ecartValeur'] ?? 0) < 0 ? 'danger' : 'success' ?> mb-0"><?= number_format($ecarts['totaux']['ecartValeur'] ?? 0, 0, ',', ' ') ?> Ar</h3>
                                                <small class="text-muted">Sur <?= number_format($ecarts['totaux']['valeurStock'] ?? 0, 0, ',', ' ') ?> Ar</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light-<?= abs($ecarts['totaux']['ecartPourcent'] ?? 0) > 0.5 ? 'danger' : 'success' ?>">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Écart Total (%)</h6>
                                                <h3 class="text-<?= abs($ecarts['totaux']['ecartPourcent'] ?? 0) > 0.5 ? 'danger' : 'success' ?> mb-0"><?= number_format($ecarts['totaux']['ecartPourcent'] ?? 0, 1, ',', ' ') ?>%</h3>
                                                <small class="text-muted">Seuil acceptable: ±0,5%</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light-warning">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Articles en écart</h6>
                                                <h3 class="text-warning mb-0"><?= number_format($ecarts['totaux']['articlesEcart'] ?? 0, 0, ',', ' ') ?></h3>
                                                <small class="text-muted">-</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light-info">
                                            <div class="card-body text-center">
                                                <h6 class="text-muted mb-2">Fiabilité Inventaire</h6>
                                                <h3 class="text-info mb-0"><?= number_format($ecarts['totaux']['fiabilite'] ?? 100, 1, ',', ' ') ?>%</h3>
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
                                            <?php foreach (($ecarts['depots'] ?? []) as $depot): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($depot['depot']) ?></strong></td>
                                                <td><?= number_format($depot['valeurStock'], 0, ',', ' ') ?></td>
                                                <td class="text-<?= $depot['ecartValeur'] < 0 ? 'danger' : ($depot['ecartValeur'] > 0 ? 'success' : 'muted') ?>">
                                                    <?= $depot['ecartValeur'] != 0 ? number_format($depot['ecartValeur'], 0, ',', ' ') : '-' ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= abs($depot['ecartPourcent']) <= 0.5 ? 'success' : (abs($depot['ecartPourcent']) <= 2 ? 'warning' : 'danger') ?>">
                                                        <?= number_format($depot['ecartPourcent'], 1, ',', ' ') ?>%
                                                    </span>
                                                </td>
                                                <td><?= $depot['articlesEcart'] ?></td>
                                                <td><?= number_format($depot['fiabilite'], 1, ',', ' ') ?>%</td>
                                                <td>
                                                    <?php if ($depot['statut'] === 'Conforme'): ?>
                                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Conforme</span>
                                                    <?php elseif ($depot['statut'] === 'À surveiller'): ?>
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> À surveiller</span>
                                                    <?php else: ?>
                                                    <span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> Critique</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($depot['statut'] !== 'Conforme'): ?>
                                                    <button class="btn btn-sm btn-outline-primary">Analyser</button>
                                                    <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>-</button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($ecarts['depots'])): ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">Aucun inventaire clôturé disponible</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td><strong>TOTAL</strong></td>
                                                <td><strong><?= number_format($ecarts['totaux']['valeurStock'] ?? 0, 0, ',', ' ') ?></strong></td>
                                                <td class="text-<?= ($ecarts['totaux']['ecartValeur'] ?? 0) < 0 ? 'danger' : 'success' ?>"><strong><?= number_format($ecarts['totaux']['ecartValeur'] ?? 0, 0, ',', ' ') ?></strong></td>
                                                <td><span class="badge bg-<?= abs($ecarts['totaux']['ecartPourcent'] ?? 0) > 0.5 ? 'danger' : 'success' ?>"><?= number_format($ecarts['totaux']['ecartPourcent'] ?? 0, 1, ',', ' ') ?>%</span></td>
                                                <td><strong><?= $ecarts['totaux']['articlesEcart'] ?? 0 ?></strong></td>
                                                <td><strong><?= number_format($ecarts['totaux']['fiabilite'] ?? 100, 1, ',', ' ') ?>%</strong></td>
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
    // Données des graphiques depuis PHP
    const chartsData = <?= json_encode($charts ?? []) ?>;
    
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

    // Chart CA & Marge (dynamique)
    var caMargeData = chartsData.caMargeEvolution || {labels: [], ca: [], marge: [], margePct: []};
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
                data: caMargeData.ca.length > 0 ? caMargeData.ca : [0]
            },
            {
                name: "Marge Brute",
                type: 'line',
                data: caMargeData.marge.length > 0 ? caMargeData.marge : [0]
            },
            {
                name: "Marge %",
                type: 'line',
                data: caMargeData.margePct.length > 0 ? caMargeData.margePct : [0]
            }
        ],
        stroke: {
            width: [0, 3, 3],
            curve: 'smooth'
        },
        colors: ['#435ebe', '#55c6a9', '#f9b959'],
        xaxis: {
            categories: caMargeData.labels.length > 0 ? caMargeData.labels : ['']
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

    // Chart Stock par Site (Donut - dynamique)
    var stockSiteData = chartsData.stockParSite || {labels: [], data: [], total: 0};
    var optionsStockSite = {
        chart: {
            type: 'donut',
            height: 300
        },
        series: stockSiteData.data.length > 0 ? stockSiteData.data : [0],
        labels: stockSiteData.labels.length > 0 ? stockSiteData.labels : ['Aucun'],
        colors: ['#435ebe', '#55c6a9', '#f9b959', '#e74c3c', '#9b59b6'],
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
                                var total = stockSiteData.total || 0;
                                return total.toLocaleString() + ' Ar';
                            }
                        }
                    }
                }
            }
        }
    };

    var chartStockSite = new ApexCharts(document.querySelector("#stockSiteChart"), optionsStockSite);
    chartStockSite.render();

    // Chart Évolution Stock & Rotation (dynamique)
    var stockEvolData = chartsData.stockEvolution || {labels: [], valeurStock: [], rotation: []};
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
                data: stockEvolData.valeurStock.length > 0 ? stockEvolData.valeurStock : [0]
            },
            {
                name: "Rotation Stock",
                type: 'line',
                data: stockEvolData.rotation.length > 0 ? stockEvolData.rotation : [0]
            }
        ],
        stroke: {
            width: [0, 3],
            curve: 'smooth'
        },
        colors: ['#f9b959', '#e74c3c'],
        xaxis: {
            categories: stockEvolData.labels.length > 0 ? stockEvolData.labels : ['']
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

    // Chart Obsolescence (Pie - dynamique)
    var obsolescenceData = chartsData.obsolescence || {labels: [], data: []};
    var optionsObsolescence = {
        chart: {
            type: 'pie',
            height: 250
        },
        series: obsolescenceData.data.length > 0 ? obsolescenceData.data : [0],
        labels: obsolescenceData.labels.length > 0 ? obsolescenceData.labels : ['Aucun'],
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