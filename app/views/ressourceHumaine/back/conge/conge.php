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
                                                        // Assign a class based on the status
                                                        if ($statut === 'Validé') {
                                                            $badgeClass = 'bg-primary'; 
                                                        } elseif ($statut === 'Refusé') {
                                                            $badgeClass = 'bg-secondary'; 
                                                        } else {
                                                            $badgeClass = 'bg-light';
                                                        }
                                                        ?>
                                                        <span class="badge <?= $badgeClass ?>"><?= $statut ?></span>
                                                    </td>
                                                    <td class="action-buttons">
                                                        <button type="button" class="btn btn-sm btn-success validate-btn"
                                                            data-id="<?= $conge['id_demande_conge'] ?>"
                                                            style="<?= $statut === 'Validé' || $statut === 'Refusé' ? 'display: none;' : '' ?>">
                                                            Valider
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger refuse-btn"
                                                            data-id="<?= $conge['id_demande_conge'] ?>"
                                                            style="<?= $statut === 'Validé' || $statut === 'Refusé' ? 'display: none;' : '' ?>">
                                                            Refuser
                                                        </button>
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

    <!-- Modal de validation -->
    <div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validationModalLabel">Valider la demande de congé</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="validationForm">
                    <div class="modal-body">
                        <input type="hidden" id="validation_id_demande" name="id_demande_conge">
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
                        <button type="submit" id="validationSubmitBtn" class="btn btn-primary">Valider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de refus -->
    <div class="modal fade" id="refusModal" tabindex="-1" aria-labelledby="refusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refusModalLabel">Refuser la demande de congé</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="refusForm">
                    <div class="modal-body">
                        <input type="hidden" id="refus_id_demande" name="id_demande_conge">
                        <div class="mb-3">
                            <label for="refus_date" class="form-label">Date du refus</label>
                            <input type="date" class="form-control" id="refus_date" name="date_validation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Refuser</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
            $('#validationModal').data('rowBtn', $(this));
            $('#validation_id_demande').val(id);
            $('#validation_date').val(getCurrentDate());
            // fetch solde info before showing modal
            $('#soldeInfo').hide().html('Chargement du solde...').show();
            $('#validationSubmitBtn').prop('disabled', true);
            $.get('<?= Flight::base() ?>/api/conge/solde', { id: id }, function(resp){
                if (resp && resp.success) {
                    var d = resp.data;
                    var s = d.solde;
                    // Affiche la date de début demandée si fournie, sinon activation_date, puis la date de fin (period_end)
                    var displayStart = s.activation_date || s.request_start ||  s.period_start || '';
                    var html = '<p><strong>Congés acquis (période ' + displayStart + ' - ' + s.period_end + '):</strong> ' + s.accrued + ' jours</p>';
                    html += '<p><strong>Congés pris sur la période:</strong> ' + s.taken + ' jours</p>';
                    html += '<p><strong>Solde disponible:</strong> ' + s.balance + ' jours</p>';
                    html += '<p><strong>Jours demandés:</strong> ' + d.days + ' jour(s)</p>';
                    if (d.canValidate) {
                        html += '<p class="text-success"><strong>Validation possible</strong></p>';
                        $('#validationSubmitBtn').prop('disabled', false);
                    } else {
                        html += '<p class="text-danger"><strong>Solde insuffisant — Validation désactivée</strong></p>';
                        $('#validationSubmitBtn').prop('disabled', true);
                    }
                    $('#soldeInfo').html(html).show();
                    $('#validationModal').modal('show');
                } else {
                    alert((resp && resp.message) ? resp.message : 'Impossible de récupérer le solde.');
                    $('#soldeInfo').hide();
                }
            }, 'json').fail(function(){
                alert('Erreur lors de la récupération du solde.');
                $('#soldeInfo').hide();
            });
        });

        $('#validationForm').on('submit', function(e){
            e.preventDefault();
            var data = {
                id_demande_conge: $('#validation_id_demande').val(),
                date_validation: $('#validation_date').val()
            };
            var $btn = $('#validationModal').data('rowBtn');

            $.post('<?= Flight::base() ?>/backOffice/conge/valider', data, function(resp){
                if (resp && resp.success) {
                    var $tr = $btn.closest('tr');
                    $tr.find('td').eq(5).html('<span class="badge bg-primary">Validé</span>');
                    $btn.closest('.action-buttons').find('.refuse-btn').hide();
                    $btn.hide();
                    $('#validationModal').modal('hide');
                } else {
                    alert((resp && resp.message) ? resp.message : 'Erreur lors de la validation.');
                }
            }, 'json').fail(function(){
                alert('Erreur de communication avec le serveur.');
            });
        });

        // --- Refus Modal ---
        $(document).on('click', 'button.refuse-btn', function(){
            var id = $(this).data('id');
            $('#refusModal').data('rowBtn', $(this));
            $('#refus_id_demande').val(id);
            $('#refus_date').val(getCurrentDate());
            $('#refusModal').modal('show');
        });

        $('#refusForm').on('submit', function(e){
            e.preventDefault();
            var data = {
                id_demande_conge: $('#refus_id_demande').val(),
                date_validation: $('#refus_date').val()
            };
            var $btn = $('#refusModal').data('rowBtn');

            $.post('<?= Flight::base() ?>/backOffice/conge/refuser', data, function(resp){
                if (resp && resp.success) {
                    var $tr = $btn.closest('tr');
                    $tr.find('td').eq(5).html('<span class="badge bg-danger">Refusé</span>');
                    $btn.closest('.action-buttons').find('.validate-btn').hide();
                    $btn.hide();
                    $('#refusModal').modal('hide');
                } else {
                    alert((resp && resp.message) ? resp.message : 'Erreur lors du refus.');
                }
            }, 'json').fail(function(){
                alert('Erreur de communication avec le serveur.');
            });
        });

    })(jQuery);
    </script>
</body>
</html>
