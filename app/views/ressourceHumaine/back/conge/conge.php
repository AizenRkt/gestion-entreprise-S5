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
    
    <script src='<?= Flight::base() ?>/public/plugin/fullcalendar-6.1.19/dist/index.global.min.js'></script>
</head>
<body>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/initTheme.js"></script>
    <div id="app">
        <?= Flight::menuBackOffice() ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none"><i class="bi bi-justify fs-3"></i></a>
            </header>
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Gestion des Congés</h3>
                            <p class="text-subtitle text-muted">Validez les demandes et consultez le planning.</p>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Planning des congés validés</h5></div>
                        <div class="card-body"><div id="calendar"></div></div>
                    </div>
                </section>

                <section class="section">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Toutes les demandes de congé</h5></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Employé</th><th>Type</th><th>Début</th><th>Fin</th><th>Jours</th><th>Statut</th><th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($conges) && !empty($conges)): foreach ($conges as $conge): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($conge['employe_prenom'] . ' ' . $conge['employe_nom']) ?></td>
                                                <td><?= htmlspecialchars($conge['type_conge_nom']) ?></td>
                                                <td><?= htmlspecialchars($conge['date_debut']) ?></td>
                                                <td><?= htmlspecialchars($conge['date_fin']) ?></td>
                                                <td><?= htmlspecialchars($conge['nb_jours']) ?></td>
                                                <td>
                                                    <?php
                                                    $statut = htmlspecialchars($conge['validation_statut']);
                                                    $badgeClass = 'bg-light';
                                                    if ($statut === 'Validé') $badgeClass = 'bg-primary'; 
                                                    elseif ($statut === 'Refusé') $badgeClass = 'bg-danger';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= $statut ?></span>
                                                </td>
                                                <td class="action-buttons">
                                                    <button type="button" class="btn btn-sm btn-success validate-btn" data-id="<?= $conge['id_demande_conge'] ?>" style="<?= $statut !== 'En attente' ? 'display: none;' : '' ?>">Valider</button>
                                                    <button type="button" class="btn btn-sm btn-danger refuse-btn" data-id="<?= $conge['id_demande_conge'] ?>" style="<?= $statut !== 'En attente' ? 'display: none;' : '' ?>">Refuser</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="7">Aucune demande de congé trouvée.</td></tr>
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

    <!-- Modals -->
    <div class="modal fade" id="validationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Valider la demande</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="validationForm">
                    <div class="modal-body">
                        <input type="hidden" id="validation_id_demande" name="id_demande_conge">
                        <div id="soldeInfo" class="mb-3"></div>
                        <div class="mb-3"><label for="validation_date" class="form-label">Date de validation</label><input type="date" class="form-control" id="validation_date" name="date_validation" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" id="validationSubmitBtn" class="btn btn-primary">Valider</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="refusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Refuser la demande</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="refusForm">
                    <div class="modal-body"><input type="hidden" id="refus_id_demande" name="id_demande_conge"><div class="mb-3"><label for="refus_date" class="form-label">Date du refus</label><input type="date" class="form-control" id="refus_date" name="date_validation" required></div></div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-danger">Refuser</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCongeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="editModalTitle">Modifier le congé</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="editCongeForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_demande" name="id_demande_conge">
                        <p><strong>Employé :</strong> <span id="editEmployeNom"></span></p>
                        <div id="soldeInfoEdit" class="mb-3"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="edit_date_debut" class="form-label">Date de début</label><input type="date" class="form-control" id="edit_date_debut" name="new_start" required></div>
                            <div class="col-md-6 mb-3"><label for="edit_date_fin" class="form-label">Date de fin</label><input type="date" class="form-control" id="edit_date_fin" name="new_end" required></div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-danger me-auto" id="deleteCongeBtn">Supprimer</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const BASE_URL = '<?= Flight::base() ?>';

        function updateConge(info) {
            const event = info.event;
            let endDate = new Date(event.endStr);
            endDate.setDate(endDate.getDate() - 1);
            const endDateStr = endDate.toISOString().split('T')[0];

            $.post(`${BASE_URL}/api/conge/update`, {
                id_demande_conge: event.id, new_start: event.startStr, new_end: endDateStr
            }, (res) => { if (!res.success) { alert('Erreur: ' + res.message); info.revert(); } location.reload(); })
            .fail(() => { alert('Erreur serveur.'); info.revert(); });
        }

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap5',
            headerToolbar: { left: 'prevYear,prev,next,nextYear today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
            events: `${BASE_URL}/api/conges/planning`,
            editable: true,
            eventDrop: updateConge,
            eventResize: updateConge,
            eventClick: function(info) {
                const event = info.event;
                const modal = new bootstrap.Modal(document.getElementById('editCongeModal'));
                
                document.getElementById('edit_id_demande').value = event.id;
                document.getElementById('editEmployeNom').textContent = event.title;
                document.getElementById('edit_date_debut').value = event.startStr;
                
                let endDate = new Date(event.endStr);
                endDate.setDate(endDate.getDate() - 1);
                document.getElementById('edit_date_fin').value = endDate.toISOString().split('T')[0];

                const soldeInfoDiv = document.getElementById('soldeInfoEdit');
                soldeInfoDiv.innerHTML = 'Chargement du solde...';

                $.get(`${BASE_URL}/api/conge/solde`, { id: event.id }, (resp) => {
                    if (resp.success) {
                        const { solde, days } = resp.data;
                        soldeInfoDiv.innerHTML = `<p class="mb-1"><strong>Solde disponible :</strong> ${solde.balance} jours</p>`;
                    } else {
                        soldeInfoDiv.innerHTML = '<p class="text-danger">Impossible de charger le solde.</p>';
                    }
                });

                modal.show();
            },
            eventDidMount: function(info) {
                new bootstrap.Tooltip(info.el, { title: info.event.extendedProps.type, placement: 'top', trigger: 'hover', container: 'body' });
            }
        });
        calendar.render();

        // --- Logique Modals (jQuery) ---
        (function($){
            const getCurrentDate = () => new Date().toISOString().split('T')[0];

            $(document).on('click', '.validate-btn', function() {
                const id = $(this).data('id');
                $('#validation_id_demande').val(id);
                $('#validation_date').val(getCurrentDate());
                $('#soldeInfo').html('Chargement...');
                $('#validationSubmitBtn').prop('disabled', true);
                
                $.get(`${BASE_URL}/api/conge/solde`, { id }, (resp) => {
                    if (resp.success) {
                        const { solde, days, canValidate } = resp.data;
                        let html = `<p><strong>Solde disponible:</strong> ${solde.balance} j</p><p><strong>Jours demandés:</strong> ${days} j</p>`;
                        html += canValidate ? '<p class="text-success">Validation possible</p>' : '<p class="text-danger">Solde insuffisant</p>';
                        $('#soldeInfo').html(html);
                        $('#validationSubmitBtn').prop('disabled', !canValidate);
                    } else {
                        $('#soldeInfo').html(`<p class="text-danger">${resp.message || 'Erreur solde.'}</p>`);
                    }
                });
                new bootstrap.Modal('#validationModal').show();
            });

            $('#validationForm').on('submit', function(e) { e.preventDefault(); $.post(`${BASE_URL}/backOffice/conge/valider`, $(this).serialize(), (r) => { if(r.success) location.reload(); else alert(r.message); }).fail(() => alert('Erreur serveur.')); });

            $(document).on('click', '.refuse-btn', function() { $('#refus_id_demande').val($(this).data('id')); $('#refus_date').val(getCurrentDate()); new bootstrap.Modal('#refusModal').show(); });
            $('#refusForm').on('submit', function(e) { e.preventDefault(); $.post(`${BASE_URL}/backOffice/conge/refuser`, $(this).serialize(), (r) => { if(r.success) location.reload(); else alert(r.message); }).fail(() => alert('Erreur serveur.')); });

            $('#editCongeForm').on('submit', function(e) {
                e.preventDefault();
                const data = { id_demande_conge: $('#edit_id_demande').val(), new_start: $('#edit_date_debut').val(), new_end: $('#edit_date_fin').val() };
                $.post(`${BASE_URL}/api/conge/update`, data, (res) => { if (res.success) location.reload(); else alert(res.message); }).fail(() => alert('Erreur serveur.'));
            });

            $('#deleteCongeBtn').on('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer ce congé ? Cette action est irréversible.')) {
                    $.post(`${BASE_URL}/api/conge/delete`, { id_demande_conge: $('#edit_id_demande').val() }, (res) => { if (res.success) location.reload(); else alert(res.message); }).fail(() => alert('Erreur serveur.'));
                }
            });

        })(jQuery);
    });
    </script>
</body>
</html>