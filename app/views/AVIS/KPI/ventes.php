<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Ventes / Responsable Commercial</title>

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
        .table-actions {
            white-space: nowrap;
        }
        .status-ok { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-delayed { background-color: #f8d7da; color: #721c24; }
        .customer-name {
            font-weight: 600;
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
                    <h3>Dashboard Ventes / Commercial</h3>
                    <p class="text-subtitle text-muted">Suivi des commandes, backlog, annulations & remises • Responsable: F. Laurent • Mis à jour le <?= date('d/m/Y à H:i') ?></p>
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
                                <strong>Backlog critique :</strong> 
                                <span class="ms-2"><strong>12 commandes non servies</strong> (16 800 €) • Stock insuffisant: SKU-X45, SKU-Y78</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger">Action urgente</button>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="alert alert-warning border-warning" style="border-left: 4px solid;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle me-3 fs-4"></i>
                            <div class="flex-grow-1">
                                <strong>Commandes en retard :</strong> 
                                <span class="ms-2"><strong>8 commandes</strong> avec délai > 5 jours • Impact: 9 500 € • Clients: A, B, D</span>
                            </div>
                            <button class="btn btn-sm btn-outline-warning">Détails</button>
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
                                    <h6 class="text-muted font-semibold mb-2">Commandes en cours</h6>
                                    <h3 class="font-extrabold mb-0">54</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-info badge-metric">
                                            <i class="bi bi-clock-history"></i> En traitement
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-info">
                                    <i class="bi bi-basket text-info"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Livrées: 120 / Retard: 8</small>
                                    <small class="text-info">-2 vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 87%" aria-valuenow="87" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≥ 95% livrées à temps</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Taux d'annulation</h6>
                                    <h3 class="font-extrabold mb-0">4%</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-warning badge-metric">
                                            <i class="bi bi-x-circle"></i> Motifs
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-warning">
                                    <i class="bi bi-file-earmark-x text-warning"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Client / Stock / Erreur</small>
                                    <small class="text-warning">+1% vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 4%" aria-valuenow="4" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≤ 3%</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Remises accordées</h6>
                                    <h3 class="font-extrabold mb-0">12 500 €</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-success badge-metric">
                                            <i class="bi bi-percent"></i> vs plafond
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-success">
                                    <i class="bi bi-cash-stack text-success"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Plafond: 15 000 €</small>
                                    <small class="text-success">-5% vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 83%" aria-valuenow="83" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: ≤ 90% plafond</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body px-4 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted font-semibold mb-2">Avoirs</h6>
                                    <h3 class="font-extrabold mb-0">8 300 €</h3>
                                    <div class="mt-2">
                                        <span class="badge bg-info badge-metric">
                                            <i class="bi bi-arrow-counterclockwise"></i> Retour / Casse / Erreur
                                        </span>
                                    </div>
                                </div>
                                <div class="metric-icon bg-light-info">
                                    <i class="bi bi-box-seam text-info"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">5 retours / 2 erreurs prix / 1 casse</small>
                                    <small class="text-info">+10% vs M-1</small>
                                </div>
                                <div class="progress progress-thin">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 83%" aria-valuenow="83" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">Objectif: < 5 000 €</small>
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
                                    <small class="text-muted d-block">Chiffre d'affaires</small>
                                    <h5 class="mb-0 text-primary">185 600 €</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">+12%</span>
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
                                    <small class="text-muted d-block">Taux de service</small>
                                    <h5 class="mb-0 text-success">96,2%</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success">+2,1%</span>
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
                                    <small class="text-muted d-block">Nombre clients</small>
                                    <h5 class="mb-0">42</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning text-dark">+5</span>
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
                                    <small class="text-muted d-block">Ticket moyen</small>
                                    <h5 class="mb-0 text-success">4 419 €</h5>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-info">+6%</span>
                                    <small class="text-muted d-block mt-1">vs M-1</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- GRAPHIQUES PRINCIPAUX -->
            <section class="row">
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Backlog non servi (12 derniers mois)</h4>
                            <p class="text-muted small mb-0">Commandes non livrées par manque de stock</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-backlog"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Commandes par statut</h4>
                            <p class="text-muted small mb-0">Répartition: En cours, Livrées, En retard</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-commandes-statut"></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row">
                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Taux d'annulation par motif</h4>
                            <p class="text-muted small mb-0">Analyse des causes d'annulation</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-annulation-motifs"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Avoirs par type (12 mois)</h4>
                            <p class="text-muted small mb-0">Retours, Erreurs prix, Casse, Autres</p>
                        </div>
                        <div class="card-body">
                            <div id="chart-avoirs-type"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- COMMANDES EN RETARD -->
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-0">Commandes en Retard (> 5 jours)</h4>
                                    <p class="text-muted small mb-0">Impact total: 9 500 €</p>
                                </div>
                                <span class="badge bg-danger fs-6">8 commandes retardées</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tableCommandes">
                                    <thead>
                                        <tr>
                                            <th>Commande</th>
                                            <th>Client</th>
                                            <th>Date commande</th>
                                            <th>Date prévue</th>
                                            <th>Retard (j)</th>
                                            <th>Montant (€)</th>
                                            <th>Motif</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>#CMD-001245</strong></td>
                                            <td><span class="customer-name">Client A</span></td>
                                            <td>08/01/2026</td>
                                            <td>13/01/2026</td>
                                            <td><span class="badge bg-danger">8 jours</span></td>
                                            <td>2 300</td>
                                            <td>Stock insuffisant</td>
                                            <td><span class="badge bg-danger">Critique</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-telephone"></i></button>
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-exclamation"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>#CMD-001242</strong></td>
                                            <td><span class="customer-name">Client B</span></td>
                                            <td>10/01/2026</td>
                                            <td>15/01/2026</td>
                                            <td><span class="badge bg-danger">6 jours</span></td>
                                            <td>1 850</td>
                                            <td>Retard fournisseur</td>
                                            <td><span class="badge bg-warning text-dark">Haute</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-outline-primary"><i class="bi bi-telephone"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>#CMD-001238</strong></td>
                                            <td><span class="customer-name">Client D</span></td>
                                            <td>05/01/2026</td>
                                            <td>12/01/2026</td>
                                            <td><span class="badge bg-danger">9 jours</span></td>
                                            <td>3 100</td>
                                            <td>Problème transport</td>
                                            <td><span class="badge bg-danger">Critique</span></td>
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

            <!-- TABLEAU ANNULATIONS / REMISES DÉTAILLÉ -->
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-0">Annulations & Remises (Derniers 30 jours)</h4>
                                    <p class="text-muted small mb-0">Total: 28 annulations, 12 500 € en remises, 4 200 € en avoirs</p>
                                </div>
                                <span class="badge bg-warning text-dark fs-6">44 mouvements</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3" id="tabsAnnulations" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab-annulations" data-bs-toggle="tab" data-bs-target="#panel-annulations" type="button">Annulations (28)</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-remises" data-bs-toggle="tab" data-bs-target="#panel-remises" type="button">Remises (12)</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-avoirs" data-bs-toggle="tab" data-bs-target="#panel-avoirs" type="button">Avoirs (4)</button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="panel-annulations">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped" id="tableAnnulations">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Commande</th>
                                                    <th>Client</th>
                                                    <th>Motif</th>
                                                    <th>Montant</th>
                                                    <th>Raison détail</th>
                                                    <th>Statut</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>14/01/2026</td>
                                                    <td><strong>#CMD-001240</strong></td>
                                                    <td><span class="customer-name">Groupe C</span></td>
                                                    <td><span class="badge bg-info">Stock insuffisant</span></td>
                                                    <td>1 450</td>
                                                    <td>Article en rupture prévue</td>
                                                    <td><span class="badge bg-success">Approuvée</span></td>
                                                    <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></button></td>
                                                </tr>
                                                <tr>
                                                    <td>13/01/2026</td>
                                                    <td><strong>#CMD-001235</strong></td>
                                                    <td><span class="customer-name">Petit Commerçant</span></td>
                                                    <td><span class="badge bg-warning text-dark">Demande client</span></td>
                                                    <td>2 800</td>
                                                    <td>Client a changé d'avis</td>
                                                    <td><span class="badge bg-success">Approuvée</span></td>
                                                    <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></button></td>
                                                </tr>
                                                <tr>
                                                    <td>12/01/2026</td>
                                                    <td><strong>#CMD-001230</strong></td>
                                                    <td><span class="customer-name">Client E</span></td>
                                                    <td><span class="badge bg-secondary">Erreur système</span></td>
                                                    <td>890</td>
                                                    <td>Doublon détecté à la saisie</td>
                                                    <td><span class="badge bg-success">Approuvée</span></td>
                                                    <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></button></td>
                                                </tr>
                                                <tr>
                                                    <td>11/01/2026</td>
                                                    <td><strong>#CMD-001225</strong></td>
                                                    <td><span class="customer-name">Grand Distributeur</span></td>
                                                    <td><span class="badge bg-danger">Qualité produit</span></td>
                                                    <td>3 200</td>
                                                    <td>Produits défectueux lors réception</td>
                                                    <td><span class="badge bg-warning text-dark">En attente</span></td>
                                                    <td><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="panel-remises">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Commande</th>
                                                    <th>Client</th>
                                                    <th>Type remise</th>
                                                    <th>Montant</th>
                                                    <th>% appliqué</th>
                                                    <th>Plafond</th>
                                                    <th>Responsable</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>14/01/2026</td>
                                                    <td><strong>#CMD-001245</strong></td>
                                                    <td><span class="customer-name">Groupe C</span></td>
                                                    <td><span class="badge bg-info">Promotion janvier</span></td>
                                                    <td>340</td>
                                                    <td>5%</td>
                                                    <td>500 € <span class="badge bg-success">OK</span></td>
                                                    <td>Système</td>
                                                </tr>
                                                <tr>
                                                    <td>13/01/2026</td>
                                                    <td><strong>#CMD-001242</strong></td>
                                                    <td><span class="customer-name">Client B</span></td>
                                                    <td><span class="badge bg-warning text-dark">Fidélité client</span></td>
                                                    <td>280</td>
                                                    <td>3%</td>
                                                    <td>200 € <span class="badge bg-danger">⚠️ -80€</span></td>
                                                    <td>F. Laurent</td>
                                                </tr>
                                                <tr>
                                                    <td>12/01/2026</td>
                                                    <td><strong>#CMD-001238</strong></td>
                                                    <td><span class="customer-name">Client D</span></td>
                                                    <td><span class="badge bg-success">Volume annuel</span></td>
                                                    <td>520</td>
                                                    <td>8%</td>
                                                    <td>1 000 € <span class="badge bg-success">OK</span></td>
                                                    <td>Système</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="panel-avoirs">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Commande</th>
                                                    <th>Client</th>
                                                    <th>Type avoir</th>
                                                    <th>Motif</th>
                                                    <th>Montant</th>
                                                    <th>Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>10/01/2026</td>
                                                    <td><strong>#CMD-001228</strong></td>
                                                    <td><span class="customer-name">Client A</span></td>
                                                    <td><span class="badge bg-danger">Retour produit</span></td>
                                                    <td>Article non conforme - Retourné 08/01</td>
                                                    <td>1 200</td>
                                                    <td><span class="badge bg-success">Émis</span></td>
                                                </tr>
                                                <tr>
                                                    <td>08/01/2026</td>
                                                    <td><strong>#CMD-001220</strong></td>
                                                    <td><span class="customer-name">Petit Commerçant</span></td>
                                                    <td><span class="badge bg-warning text-dark">Erreur prix</span></td>
                                                    <td>Prix facturé différent du devis</td>
                                                    <td>380</td>
                                                    <td><span class="badge bg-info">En attente signature</span></td>
                                                </tr>
                                                <tr>
                                                    <td>05/01/2026</td>
                                                    <td><strong>#CMD-001215</strong></td>
                                                    <td><span class="customer-name">Client F</span></td>
                                                    <td><span class="badge bg-danger">Casse transport</span></td>
                                                    <td>5 articles cassés à la livraison</td>
                                                    <td>1 620</td>
                                                    <td><span class="badge bg-success">Émis</span></td>
                                                </tr>
                                                <tr>
                                                    <td>02/01/2026</td>
                                                    <td><strong>#CMD-001205</strong></td>
                                                    <td><span class="customer-name">Grand Distributeur</span></td>
                                                    <td><span class="badge bg-info">Geste commercial</span></td>
                                                    <td>Retard livraison > 15 jours - compensation</td>
                                                    <td>800</td>
                                                    <td><span class="badge bg-success">Émis</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                                            <td>20%</td>
                                            <td><span class="badge bg-success">OK</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>#C-1239</strong></td>
                                            <td>16/01/2026</td>
                                            <td>Client C</td>
                                            <td><span class="badge bg-info">Avoir</span></td>
                                            <td>Erreur prix</td>
                                            <td>1 100</td>
                                            <td>0</td>
                                            <td>-</td>
                                            <td><span class="badge bg-warning text-dark">En cours</span></td>
                                            <td class="table-actions">
                                                <button class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- RESPONSABLE COMMERCIAL -->
            <section class="row">
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Responsable Commercial</h4>
                        </div>
                        <div class="card-body text-center">
                            <img src="<?= Flight::base() ?>/public/template/assets/images/faces/1.jpg" alt="F. Laurent" class="rounded-circle mb-3" width="80">
                            <h5>François Laurent</h5>
                            <p class="text-muted small">Directeur Commercial</p>
                            <div class="mt-3 text-start">
                                <p><strong>📧 Email:</strong> f.laurent@company.com</p>
                                <p><strong>☎️ Téléphone:</strong> +33 1 XX XX XX XX</p>
                                <p><strong>📱 Mobile:</strong> +33 6 XX XX XX XX</p>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <p class="mb-2"><strong>Équipe (4 commerciaux):</strong></p>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 py-2 text-start small">
                                        <span class="badge bg-info me-2">S1</span>Marie Dubois (Zone Nord)
                                    </div>
                                    <div class="list-group-item px-0 py-2 text-start small">
                                        <span class="badge bg-info me-2">S2</span>Jean Moreau (Zone Ouest)
                                    </div>
                                    <div class="list-group-item px-0 py-2 text-start small">
                                        <span class="badge bg-info me-2">S3</span>Sophie Martin (Zone Est)
                                    </div>
                                    <div class="list-group-item px-0 py-2 text-start small">
                                        <span class="badge bg-info me-2">S4</span>Pierre Laurent (Zone Sud)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Objectifs et Actions Recommandées</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="mb-3"><strong>📊 Objectifs du mois</strong></h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small><strong>Chiffre d'affaires</strong></small>
                                                <small class="text-success">+12%</small>
                                            </div>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                                            </div>
                                            <small class="text-muted">Cible: 250k€ | Actuel: 185.6k€</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small><strong>Service rate</strong></small>
                                                <small class="text-success">+2.1%</small>
                                            </div>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 96%;" aria-valuenow="96" aria-valuemin="0" aria-valuemax="100">96.2%</div>
                                            </div>
                                            <small class="text-muted">Cible: 98% | Actuel: 96.2%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-4">
                                <h6 class="mb-3"><strong>🎯 Actions Recommandées</strong></h6>
                                
                                <div class="alert alert-danger d-flex align-items-start mb-3">
                                    <div class="me-3" style="font-size: 1.2rem;">🔴</div>
                                    <div class="flex-grow-1">
                                        <strong>URGENCE - Backlog critique</strong><br>
                                        <small>12 commandes non servies (16.8k€) en raison de stocks insuffisants</small><br>
                                        <button class="btn btn-sm btn-outline-danger mt-2"><i class="bi bi-arrow-right"></i> Contacter Stock</button>
                                    </div>
                                </div>

                                <div class="alert alert-warning d-flex align-items-start mb-3">
                                    <div class="me-3" style="font-size: 1.2rem;">⚠️</div>
                                    <div class="flex-grow-1">
                                        <strong>ATTENTION - Commandes en retard</strong><br>
                                        <small>8 commandes retardées (9.5k€): Clients A, B, D. Risque insatisfaction client</small><br>
                                        <button class="btn btn-sm btn-outline-warning mt-2"><i class="bi bi-telephone"></i> Appels clients</button>
                                    </div>
                                </div>

                                <div class="alert alert-info d-flex align-items-start mb-3">
                                    <div class="me-3" style="font-size: 1.2rem;">ℹ️</div>
                                    <div class="flex-grow-1">
                                        <strong>INFO - Analyse des remises</strong><br>
                                        <small>Budget remises à 83% d'utilisation. 2 commandes dépassent plafond (-80€). À réviser avant fin mois</small><br>
                                        <button class="btn btn-sm btn-outline-info mt-2"><i class="bi bi-graph-up"></i> Rapport détaillé</button>
                                    </div>
                                </div>

                                <div class="alert alert-success d-flex align-items-start">
                                    <div class="me-3" style="font-size: 1.2rem;">✅</div>
                                    <div class="flex-grow-1">
                                        <strong>À FAIRE - Processus prévention backlog</strong><br>
                                        <small>Mettre en place vérification stock AVANT confirmation commande pour éviter futurs backlogs</small><br>
                                        <button class="btn btn-sm btn-outline-success mt-2"><i class="bi bi-pencil-square"></i> Détails action</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

<!-- JS -->
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/apexcharts/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json"></script>

<script>
// ============================================
// GRAPHIQUES APEXCHARTS - DASHBOARD VENTES
// ============================================

// 1. Backlog non servi (12 mois)
new ApexCharts(document.querySelector("#chart-backlog"), {
    chart: { type: 'line', height: 300, toolbar: { show: true, tools: { download: true, selection: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true } }, zoom: { enabled: true } },
    series: [{
        name: 'Commandes non servies',
        data: [5,6,8,10,12,9,7,6,8,12,11,10],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [20, 100, 100, 100] } }
    }],
    xaxis: { categories: ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'] },
    colors: ['#dc3545'],
    stroke: { curve: 'smooth', width: 3 },
    grid: { borderColor: '#e0e0e0', strokeDashArray: 3 },
    legend: { position: 'top', horizontalAlign: 'right' },
    responsive: [{ breakpoint: 768, options: { chart: { height: 250 } } }]
}).render();

// 2. Commandes par statut (Pie)
new ApexCharts(document.querySelector("#chart-commandes-statut"), {
    chart: { type: 'pie', height: 300 },
    series: [54,120,8],
    labels: ['En cours','Livrées','En retard'],
    colors: ['#17a2b8','#28a745','#dc3545'],
    legend: { position: 'bottom', horizontalAlign: 'center' },
    plotOptions: { pie: { donut: { size: '70%' } } },
    responsive: [{ breakpoint: 768, options: { chart: { height: 280 } } }]
}).render();

// 3. Taux d'annulation par motif
new ApexCharts(document.querySelector("#chart-annulation-motifs"), {
    chart: { type: 'bar', height: 300, toolbar: { show: true, tools: { download: true, selection: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true } }, zoom: { enabled: true } },
    series: [{ name: 'Annulations', data: [18,14,9,7,3] }],
    xaxis: { categories: ['Stock insuffisant','Demande client','Erreur système','Qualité produit','Autres'] },
    colors: ['#ffc107'],
    legend: { position: 'top' },
    grid: { borderColor: '#e0e0e0', strokeDashArray: 3 },
    responsive: [{ breakpoint: 768, options: { chart: { height: 250 } } }]
}).render();

// 4. Avoirs par type (12 mois - Stacked Column)
new ApexCharts(document.querySelector("#chart-avoirs-type"), {
    chart: { type: 'column', height: 300, toolbar: { show: true, tools: { download: true, selection: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true } }, zoom: { enabled: true } },
    series: [
        { name: 'Retours', data: [120,140,110,130,145,125,110,135,140,150,130,120] },
        { name: 'Erreurs prix', data: [45,55,50,60,65,50,55,60,50,70,60,55] },
        { name: 'Casse', data: [30,25,35,25,30,35,25,30,40,35,30,25] },
        { name: 'Autres', data: [15,20,15,20,15,20,18,15,20,15,18,20] }
    ],
    xaxis: { categories: ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'] },
    colors: ['#dc3545','#ffc107','#fd7e14','#6c757d'],
    plotOptions: { bar: { horizontal: false, columnWidth: '55%', dataLabels: { position: 'top' }, stacked: true } },
    legend: { position: 'top', horizontalAlign: 'left' },
    grid: { borderColor: '#e0e0e0', strokeDashArray: 3 },
    responsive: [{ breakpoint: 768, options: { chart: { height: 250 } } }]
}).render();

// ============================================
// DATATABLES - TABLES INTERACTIVES
// ============================================

// DataTables Commandes en Retard
$('#tableCommandes').DataTable({
    "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json"
    },
    "responsive": true,
    "ordering": true,
    "searching": true,
    "paging": true,
    "pageLength": 5,
    "lengthMenu": [[5, 10, 25], [5, 10, 25]],
    "columnDefs": [
        { "orderable": false, "targets": 8 } // Colonne Actions non triable
    ],
    "order": [[4, "desc"]], // Trier par retard décroissant
    "dom": 'lrtip'
});

// DataTables Annulations & Remises
$('#tableAnnulations').DataTable({
    "language": {
        "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json"
    },
    "responsive": true,
    "ordering": true,
    "searching": true,
    "paging": true,
    "pageLength": 5,
    "lengthMenu": [[5, 10, 25], [5, 10, 25]],
    "columnDefs": [
        { "orderable": false, "targets": 7 } // Colonne Actions non triable
    ],
    "order": [[0, "desc"]], // Trier par date décroissante
    "dom": 'lrtip'
});
</script>

</body>
</html>
