<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventes - Tableau de bord</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <style>
        :root { --primary: #2563eb; --success: #10b981; --warning: #f59e0b; --danger: #ef4444; --gray: #6b7280; }
        body { background: #f8fafc; }
        .page-title { font-size: 1.5rem; font-weight: 600; color: #1e293b; }
        .page-subtitle { color: var(--gray); font-size: 0.875rem; }
        .stat-card { background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; transition: all 0.2s; }
        .stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); transform: translateY(-2px); }
        .stat-value { font-size: 1.75rem; font-weight: 700; color: #1e293b; }
        .stat-label { font-size: 0.8rem; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .workflow-box { background: white; border-radius: 12px; padding: 2rem; border: 1px solid #e2e8f0; }
        .workflow-step { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: inherit; padding: 1rem; border-radius: 10px; transition: all 0.2s; }
        .workflow-step:hover { background: #f1f5f9; }
        .step-num { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 0.5rem; }
        .step-1 .step-num { background: #dbeafe; color: var(--primary); }
        .step-2 .step-num { background: #fef3c7; color: var(--warning); }
        .step-3 .step-num { background: #e0e7ff; color: #6366f1; }
        .step-4 .step-num { background: #d1fae5; color: var(--success); }
        .step-label { font-size: 0.875rem; font-weight: 500; }
        .step-desc { font-size: 0.75rem; color: var(--gray); }
        .workflow-arrow { color: #cbd5e1; font-size: 1.5rem; }
        .quick-card { background: white; border-radius: 12px; padding: 1.25rem; border: 1px solid #e2e8f0; text-decoration: none; color: inherit; display: block; transition: all 0.2s; text-align: center; }
        .quick-card:hover { border-color: var(--primary); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1); }
        .quick-icon { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .quick-label { font-weight: 500; font-size: 0.875rem; }
        .quick-count { font-size: 0.75rem; color: var(--gray); }
        .section-title { font-size: 0.875rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 1rem; }
        .list-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; }
        .list-card-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; font-weight: 600; }
        .list-item { padding: 0.875rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .list-item:last-child { border-bottom: none; }
        .badge-rank { width: 24px; height: 24px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 600; }
        .empty-state { text-align: center; padding: 2rem; color: var(--gray); }
    </style>
</head>
<body>
<div id="app">
    <?= Flight::menuBackOffice() ?>
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none"><i class="bi bi-justify fs-3"></i></a>
        </header>
        
        <div class="page-content">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title">Tableau de bord</h1>
                    <p class="page-subtitle mb-0">Module Ventes - Vue d'ensemble</p>
                </div>
                <span class="text-muted"><?= date('d/m/Y') ?></span>
            </div>

            <!-- KPI Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <a href="<?= Flight::base() ?>/ventes/factures" class="stat-card d-block text-decoration-none">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">CA du mois</div>
                                <div class="stat-value"><?= number_format(($kpi['ca_mois']['ca_ttc'] ?? 0), 0, ' ', ' ') ?> Ar</div>
                                <small class="text-muted"><?= ($kpi['ca_mois']['nombre_factures'] ?? 0) ?> factures</small>
                            </div>
                            <div class="stat-icon" style="background: #dbeafe;"><i class="bi bi-currency-exchange text-primary"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= Flight::base() ?>/ventes/commandes" class="stat-card d-block text-decoration-none">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Backlog</div>
                                <div class="stat-value"><?= ($kpi['backlog']['nb_commandes'] ?? 0) ?></div>
                                <small class="text-muted"><?= number_format(($kpi['backlog']['valeur_totale'] ?? 0), 0, ' ', ' ') ?> Ar</small>
                            </div>
                            <div class="stat-icon" style="background: #fef3c7;"><i class="bi bi-box-seam text-warning"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= Flight::base() ?>/ventes/commandes" class="stat-card d-block text-decoration-none">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">En retard</div>
                                <div class="stat-value"><?= ($kpi['commandes_retard'] ?? 0) ?></div>
                                <small class="text-muted">commandes +7j</small>
                            </div>
                            <div class="stat-icon" style="background: #fee2e2;"><i class="bi bi-exclamation-triangle text-danger"></i></div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= Flight::base() ?>/ventes/encaissements" class="stat-card d-block text-decoration-none">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-label">Créances</div>
                                <div class="stat-value"><?= number_format(($kpi['total_creances'] ?? 0), 0, ' ', ' ') ?> Ar</div>
                                <small class="text-muted">impayées</small>
                            </div>
                            <div class="stat-icon" style="background: #d1fae5;"><i class="bi bi-cash-stack text-success"></i></div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Workflow -->
            <div class="workflow-box mb-4">
                <div class="section-title">Workflow des ventes</div>
                <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                    <a href="<?= Flight::base() ?>/ventes/commandes" class="workflow-step step-1">
                        <div class="step-num">1</div>
                        <span class="step-label">Commande</span>
                        <span class="step-desc">Création</span>
                    </a>
                    <span class="workflow-arrow d-none d-md-block">→</span>
                    <a href="<?= Flight::base() ?>/ventes/livraisons" class="workflow-step step-2">
                        <div class="step-num">2</div>
                        <span class="step-label">Livraison</span>
                        <span class="step-desc">Sortie stock</span>
                    </a>
                    <span class="workflow-arrow d-none d-md-block">→</span>
                    <a href="<?= Flight::base() ?>/ventes/factures" class="workflow-step step-3">
                        <div class="step-num">3</div>
                        <span class="step-label">Facture</span>
                        <span class="step-desc">Facturation</span>
                    </a>
                    <span class="workflow-arrow d-none d-md-block">→</span>
                    <a href="<?= Flight::base() ?>/ventes/encaissements" class="workflow-step step-4">
                        <div class="step-num">4</div>
                        <span class="step-label">Encaissement</span>
                        <span class="step-desc">Paiement</span>
                    </a>
                </div>
            </div>

            <!-- Accès rapide -->
            <div class="section-title">Accès rapide</div>
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-2">
                    <a href="<?= Flight::base() ?>/ventes/clients" class="quick-card">
                        <div class="quick-icon text-primary"><i class="bi bi-people"></i></div>
                        <div class="quick-label">Clients</div>
                        <div class="quick-count"><?= count($clients ?? []) ?></div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="<?= Flight::base() ?>/ventes/commandes" class="quick-card">
                        <div class="quick-icon text-warning"><i class="bi bi-cart"></i></div>
                        <div class="quick-label">Commandes</div>
                        <div class="quick-count"><?= count($commandes ?? []) ?></div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="<?= Flight::base() ?>/ventes/livraisons" class="quick-card">
                        <div class="quick-icon text-info"><i class="bi bi-truck"></i></div>
                        <div class="quick-label">Livraisons</div>
                        <div class="quick-count"><?= count($livraisons ?? []) ?></div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="<?= Flight::base() ?>/ventes/factures" class="quick-card">
                        <div class="quick-icon text-indigo"><i class="bi bi-receipt"></i></div>
                        <div class="quick-label">Factures</div>
                        <div class="quick-count"><?= count($factures ?? []) ?></div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="<?= Flight::base() ?>/ventes/encaissements" class="quick-card">
                        <div class="quick-icon text-success"><i class="bi bi-cash-coin"></i></div>
                        <div class="quick-label">Encaissements</div>
                        <div class="quick-count"><?= count($encaissements ?? []) ?></div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="#" class="quick-card" data-bs-toggle="modal" data-bs-target="#modalDepots">
                        <div class="quick-icon text-secondary"><i class="bi bi-building"></i></div>
                        <div class="quick-label">Dépôts</div>
                        <div class="quick-count"><?= count($depots ?? []) ?></div>
                    </a>
                </div>
            </div>

            <!-- Top clients & Activité -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="list-card">
                        <div class="list-card-header d-flex justify-content-between">
                            <span>Top 5 Clients</span>
                            <a href="<?= Flight::base() ?>/ventes/clients" class="text-primary text-decoration-none small">Voir tout</a>
                        </div>
                        <?php $topClients = $kpi['top_clients'] ?? []; ?>
                        <?php if (!empty($topClients)): ?>
                            <?php foreach ($topClients as $i => $client): ?>
                                <div class="list-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="badge-rank" style="background: <?= $i < 3 ? '#fef3c7' : '#f1f5f9' ?>; color: <?= $i < 3 ? '#d97706' : '#64748b' ?>;">
                                            <?= $i + 1 ?>
                                        </div>
                                        <span><?= htmlspecialchars($client['client_nom'] ?? 'N/A') ?></span>
                                    </div>
                                    <strong class="text-success"><?= number_format(($client['ca_ttc'] ?? 0), 0, ' ', ' ') ?> Ar</strong>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">Aucune donnée</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="list-card">
                        <div class="list-card-header">Activité récente</div>
                        <?php 
                        $activities = [];
                        foreach (array_slice($commandes ?? [], 0, 3) as $cmd) {
                            if (isset($cmd['commande_date'])) {
                                $activities[] = ['type' => 'cmd', 'date' => $cmd['commande_date'], 'text' => "Cmd " . ($cmd['commande_numero'] ?? '') . " - " . ($cmd['client_nom'] ?? '')];
                            }
                        }
                        foreach (array_slice($factures ?? [], 0, 2) as $fac) {
                            if (isset($fac['facture_date'])) {
                                $activities[] = ['type' => 'fac', 'date' => $fac['facture_date'], 'text' => "Fact " . ($fac['facture_numero'] ?? '') . " - " . number_format(($fac['montant_ttc'] ?? 0), 0, ' ', ' ') . " Ar"];
                            }
                        }
                        usort($activities, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
                        $activities = array_slice($activities, 0, 5);
                        ?>
                        <?php if (!empty($activities)): ?>
                            <?php foreach ($activities as $act): ?>
                                <div class="list-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi <?= $act['type'] === 'cmd' ? 'bi-cart text-primary' : 'bi-receipt text-success' ?>"></i>
                                        <span><?= htmlspecialchars($act['text']) ?></span>
                                    </div>
                                    <small class="text-muted"><?= date('d/m', strtotime($act['date'])) ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">Aucune activité</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dépôts -->
<div class="modal fade" id="modalDepots" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title">Dépôts</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php foreach (($depots ?? []) as $depot): ?>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span><?= htmlspecialchars($depot['nom'] ?? $depot['depot_nom'] ?? '') ?></span>
                        <span class="text-muted">#<?= $depot['id_depot'] ?? '' ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($depots)): ?>
                    <div class="text-center text-muted py-3">Aucun dépôt</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
</body>
</html>
