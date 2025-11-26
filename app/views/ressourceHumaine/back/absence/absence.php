<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Absences - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/styles/choices.css">
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
                            <h3>Liste des absences</h3>
                            <p class="text-subtitle text-muted">Consultez toutes les absences enregistrées.</p>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Toutes les absences</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Employé</th>
                                            <th>Type d'absence</th>
                                            <th>Date de début</th>
                                            <th>Date de fin</th>
                                            <th>Motif</th>
                                            <th>Type de document</th>
                                            <th>Date du document</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($absences) && is_array($absences) && !empty($absences)): ?>
                                            <?php foreach ($absences as $absence): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($absence['employe_prenom'] . ' ' . $absence['employe_nom']) ?></td>
                                                    <td><?= htmlspecialchars($absence['type_absence']) ?></td>
                                                    <td><?= htmlspecialchars($absence['absence_date_debut']) ?></td>
                                                    <td><?= htmlspecialchars($absence['absence_date_fin']) ?></td>
                                                    <td><?= htmlspecialchars($absence['motif']) ?></td>
                                                    <td><?= htmlspecialchars($absence['type_documentation']) ?></td>
                                                    <td><?= htmlspecialchars($absence['date_documentation']) ?></td>
                                                    <td>
                                                        <?php
                                                        $statut = htmlspecialchars($absence['validation_status']);
                                                        $badgeClass = 'bg-secondary';

                                                        if ($statut === 'Validé') {
                                                            $badgeClass = 'bg-primary';
                                                        } elseif ($statut === 'En attente') {
                                                            $badgeClass = 'bg-light';
                                                        } elseif ($statut === 'Archivé') {
                                                            $badgeClass = 'bg-secondary';
                                                        }
                                                        ?>
                                                        <span class="badge <?= $badgeClass ?>"><?= $statut ?></span>
                                                    </td>
                                                    <td class="action-buttons">
                                                        <!-- Buttons for validating and rejecting absence -->
                                                        <a href="<?= Flight::base() ?>/backOffice/absence/valider?id_absence=<?= $absence['id_absence'] ?>"
                                                            class="btn btn-sm btn-success validate-btn">
                                                            Valider
                                                        </a>
                                                        <a href="<?= Flight::base() ?>/backOffice/absence/refuser?id_absence=<?= $absence['id_absence'] ?>"
                                                            class="btn btn-sm btn-danger refuse-btn">
                                                            Refuser
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8">Aucune absence trouvée.</td>
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

    <!-- JQuery doit être chargé avant ton script -->
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/simple-datatables.js"></script>
    
    <script>
        $(document).ready(function() {
            // Hide action buttons based on validation status
            $('tr').each(function() {
                var statusCell = $(this).find('td:eq(7)'); // Adjust index if necessary, 7 is for 'Statut'
                var actionButtons = $(this).find('.action-buttons');

                if (actionButtons.length > 0 && statusCell.length > 0) {
                    var status = statusCell.text().trim();
                    if (status !== 'En attente') {
                        actionButtons.hide(); // Hide buttons if status is not 'En attente'
                    }
                }
            });
        });
    </script>
</body>

</html>