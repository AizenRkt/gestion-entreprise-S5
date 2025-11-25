<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Heures Supplémentaires - Mazer</title>
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
                            <h3>Liste des Heures Supplémentaires</h3>
                            <p class="text-subtitle text-muted">Consultez toutes les demandes d'heures supplémentaires.</p>
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
                                            <th>Date de demande</th>
                                            <th>Heure début</th>
                                            <th>Heure fin</th>
                                            <th>Date début</th>
                                            <th>Date fin</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($heuresSupp) && is_array($heuresSupp) && !empty($heuresSupp)): ?>
                                            <?php foreach ($heuresSupp as $heureSupp): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($heureSupp['employe_prenom'] . ' ' . $heureSupp['employe_nom']) ?></td>
                                                    <td><?= htmlspecialchars($heureSupp['date_demande']) ?></td>
                                                    <td><?= htmlspecialchars($heureSupp['heure_debut']) ?></td>
                                                    <td><?= htmlspecialchars($heureSupp['heure_fin']) ?></td>
                                                    <td><?= htmlspecialchars($heureSupp['date_heure_debut']) ?></td>
                                                    <td><?= htmlspecialchars($heureSupp['date_heure_fin']) ?></td>
                                                    <td>
                                                        <?php
                                                        $statut = htmlspecialchars($heureSupp['validation_statut']);
                                                        $badgeClass = ($statut === 'Validé') ? 'bg-primary' : 'bg-light';
                                                        ?>
                                                        <span class="badge <?= $badgeClass ?>"><?= $statut ?></span>
                                                    </td>
                                                    <td class="action-buttons">
                                                        <!-- Buttons for validating and rejecting absence -->
                                                        <a href="<?= Flight::base() ?>/heureSupp/valider?id_heureSupp=<?= $heureSupp['id_demande_heure_sup'] ?>"
                                                            class="btn btn-sm btn-success validate-btn">
                                                            Valider
                                                        </a>
                                                        <a href="<?= Flight::base() ?>/heureSupp/refuser?id_heureSupp=<?= $heureSupp['id_demande_heure_sup'] ?>"
                                                            class="btn btn-sm btn-danger refuse-btn">
                                                            Refuser
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7">Aucune demande d'heure supplémentaire trouvée.</td>
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
