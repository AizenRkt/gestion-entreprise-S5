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
                                                        <button type="button" class="btn btn-sm btn-success validate-btn"
                                                            data-id="<?= $absence['id_absence'] ?>">
                                                            Valider
                                                        </button>
                                                        <a href="<?= Flight::base() ?>/backOffice/absence/refuser?id_absence=<?= $absence['id_absence'] ?>"
                                                            class="btn btn-sm btn-danger refuse-btn">
                                                            Refuser
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9">Aucune absence trouvée.</td>
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
    
    <!-- Modal de validation -->
    <div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validationModalLabel">Valider l'absence et convertir en congé</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="validationForm">
                    <div class="modal-body">
                        <input type="hidden" id="validation_id_absence" name="id_absence">
                        <div id="soldeInfo" class="mb-3" style="display:none;">
                            <!-- Rempli dynamiquement -->
                        </div>
                        <div class="mb-3">
                            <label for="validation_date" class="form-label">Date de validation</label>
                            <input type="date" class="form-control" id="validation_date" name="date_validation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" id="validationSubmitBtn" class="btn btn-primary">Valider et convertir</button>
                    </div>
                </form>
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
    (function($){
        // Helper to get current date as YYYY-MM-DD
        function getCurrentDate() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        // --- Validation Modal ---
        $(document).on('click', 'button.validate-btn', function(){
            var id = $(this).data('id');
            $('#validation_id_absence').val(id);
            $('#validation_date').val(getCurrentDate());
            $('#soldeInfo').hide().html('<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>').show();
            $('#validationSubmitBtn').prop('disabled', true); // Désactivé pendant le chargement

            $.get('<?= Flight::base() ?>/api/absence/solde', { id: id }, function(resp){
                if (resp && resp.success) {
                    var d = resp.data;
                    var s = d.solde;
                    var b = d.breakdown;
                    
                    var html = `<p><strong>Solde disponible :</strong> ${s.balance} jour(s)</p>`;
                    html += `<p><strong>Jours d'absence (ouvrés) :</strong> ${d.days} jour(s)</p><hr>`;
                    
                    if (b.unpaid_days === 0) {
                        html += `<p class="text-success"><strong>Le solde est suffisant.</strong> L'absence sera convertie en <strong>${b.paid_days} jour(s) de congé payé.</strong></p>`;
                    } else if (b.paid_days === 0) {
                        html += `<p class="text-warning"><strong>Le solde est insuffisant.</strong> L'absence sera convertie en <strong>${b.unpaid_days} jour(s) de congé sans solde.</strong></p>`;
                    } else {
                        html += `<p class="text-info"><strong>Le solde est partiel.</strong> L'absence sera convertie en :<ul><li><strong>${b.paid_days} jour(s) de congé payé</strong></li><li><strong>${b.unpaid_days} jour(s) de congé sans solde</strong></li></ul></p>`;
                    }

                    $('#soldeInfo').html(html);
                    $('#validationSubmitBtn').prop('disabled', false); // Toujours activer si la requête réussit
                } else {
                    $('#soldeInfo').html('<p class="text-danger">' + ((resp && resp.message) ? resp.message : 'Impossible de récupérer le solde.') + '</p>');
                }
                $('#validationModal').modal('show');
            }, 'json').fail(function(){
                $('#soldeInfo').html('<p class="text-danger">Erreur de communication lors de la récupération du solde.</p>');
                $('#validationModal').modal('show');
            });
        });
        
        $('#validationForm').on('submit', function(e){
            e.preventDefault();
            $('#validationSubmitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Traitement...');

            var data = {
                id_absence: $('#validation_id_absence').val(),
                date_validation: $('#validation_date').val()
            };

            $.post('<?= Flight::base() ?>/backOffice/absence/valider', data, function(resp){
                if (resp && resp.success) {
                    alert(resp.message);
                    location.reload();
                } else {
                    alert((resp && resp.message) ? resp.message : 'Erreur lors de la validation.');
                    $('#validationSubmitBtn').prop('disabled', false).text('Valider et convertir');
                }
            }, 'json').fail(function(){
                alert('Erreur de communication avec le serveur.');
                $('#validationSubmitBtn').prop('disabled', false).text('Valider et convertir');
            });
        });

        $(document).ready(function() {
            // Hide action buttons based on validation status
            $('#table1 tbody tr').each(function() {
                var statusCell = $(this).find('td:eq(7)'); 
                var actionButtons = $(this).find('.action-buttons');

                if (actionButtons.length > 0 && statusCell.length > 0) {
                    var status = statusCell.text().trim();
                    if (status !== 'En attente') {
                        actionButtons.hide(); // Hide buttons if status is not 'En attente'
                    }
                }
            });
        });
    })(jQuery);
    </script>
</body>

</html>