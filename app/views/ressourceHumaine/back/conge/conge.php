<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Congés - Mazer</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
</head>
<body>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/initTheme.js"></script>
    <div id="app">
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
                            <h3>Liste des Congés</h3>
                            <p class="text-subtitle text-muted">Consultez toutes les demandes de congé.</p>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Toutes les demandes</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Employé</th>
                                            <th>Type de congé</th>
                                            <th>Date début</th>
                                            <th>Date fin</th>
                                            <th>Nombre de jours</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($conges) && is_array($conges) && !empty($conges)): ?>
                                            <?php foreach ($conges as $conge): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($conge['employe_prenom'] . ' ' . $conge['employe_nom']) ?></td>
                                                    <td><?= htmlspecialchars($conge['type_conge_nom']) ?></td>
                                                    <td><?= htmlspecialchars($conge['date_debut']) ?></td>
                                                    <td><?= htmlspecialchars($conge['date_fin']) ?></td>
                                                    <td><?= htmlspecialchars($conge['nb_jours']) ?></td>
                                                    <td>
                                                        <?php
                                                        $statut = htmlspecialchars($conge['validation_statut']);
                                                        $badgeClass = ($statut === 'Validé') ? 'bg-primary' : 'bg-light';
                                                        ?>
                                                        <span class="badge <?= $badgeClass ?>"><?= $statut ?></span>
                                                    </td>
                                                    <td class="action-buttons">
                                                        <a href="<?= Flight::base() ?>/conge/valider?id_conge=<?= $conge['id_demande_conge'] ?>"
                                                            class="btn btn-sm btn-success validate-btn" 
                                                            style="<?= $statut === 'Validé' ? 'display: none;' : '' ?>">
                                                            Valider
                                                        </a>
                                                        <a href="<?= Flight::base() ?>/conge/refuser?id_conge=<?= $conge['id_demande_conge'] ?>"
                                                            class="btn btn-sm btn-danger refuse-btn" 
                                                            style="<?= $statut === 'Validé' ? 'display: none;' : '' ?>">
                                                            Refuser
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7">Aucune demande de congé trouvée.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/simple-datatables.js"></script>
</body>
</html>
