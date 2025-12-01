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
                                                    elseif ($statut === 'Congé') $badgeClass = 'bg-danger';
                                                    elseif ($statut === 'Congé non payé') $badgeClass = 'bg-light';
                                                    elseif ($statut === 'Jour Ferié') $badgeClass = 'bg-dark';
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

    

            // --- Logique Modals (jQuery) ---

            (function($){

                const getCurrentDate = () => new Date().toISOString().split('T')[0];

    

                /**

                 * Vérifie le solde pour une demande et des dates données, et met à jour l'UI.

                 * @param {string} id - L'ID de la demande de congé.

                 * @param {string} start - La date de début au format YYYY-MM-DD.

                 * @param {string} end - La date de fin au format YYYY-MM-DD.

                 * @param {jQuery} soldeInfoDiv - L'élément jQuery où afficher les informations du solde.

                 * @param {jQuery} submitBtn - Le bouton de soumission à activer/désactiver.

                 * @returns {Promise} La promesse de la requête AJAX.

                 */

                function checkSoldeAndUpdateDisplay(id, start, end, soldeInfoDiv, submitBtn) {

                    soldeInfoDiv.html('Vérification du solde...');

                    submitBtn.prop('disabled', true);

    

                    return $.get(`${BASE_URL}/api/conge/solde`, { id, start_override: start, end_override: end }, (resp) => {

                        if (resp.success) {

                            const { solde, days, canValidate, taken_during_period } = resp.data;

                            let html = `<p><strong>Solde disponible:</strong> ${solde.balance} j</p>`;

                            html += `<p><strong>Jours pour la nouvelle période:</strong> ${days} j</p>`;

                            if (typeof taken_during_period !== 'undefined') {

                                html += `<p><strong>Jours déjà pris sur la période de calcul:</strong> ${taken_during_period} j</p>`;

                            }

                            if (solde.period_start && solde.period_end) {

                                html += `<p class="mt-2"><small>Période de calcul: <strong>${solde.period_start}</strong> au <strong>${solde.period_end}</strong>.</small></p>`;

                            }

                            html += canValidate 

                                ? '<p class="text-success mt-2">Modification possible</p>' 

                                : '<p class="text-danger mt-2">Solde insuffisant pour ces dates</p>';

                            

                            soldeInfoDiv.html(html);

                            submitBtn.prop('disabled', !canValidate);

                        } else {

                            soldeInfoDiv.html(`<p class="text-danger">${resp.message || 'Erreur lors de la vérification du solde.'}</p>`);

                            submitBtn.prop('disabled', true);

                        }

                    }).fail(() => {

                        soldeInfoDiv.html('<p class="text-danger">Erreur serveur lors de la vérification du solde.</p>');

                        submitBtn.prop('disabled', true);

                    });

                }

    

                // --- Initialisation FullCalendar ---

                var calendarEl = document.getElementById('calendar');

                var calendar = new FullCalendar.Calendar(calendarEl, {

                    initialView: 'dayGridMonth',

                    themeSystem: 'bootstrap5',

                    headerToolbar: { left: 'prevYear,prev,next,nextYear today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },

                    events: `${BASE_URL}/api/conges/planning`,

                    editable: true,

                    eventDrop: handleEventUpdate,

                    eventResize: handleEventUpdate,

                    eventClick: function(info) {

                        const event = info.event;

                        const modal = new bootstrap.Modal(document.getElementById('editCongeModal'));

                        

                        $('#edit_id_demande').val(event.id);

                        $('#editEmployeNom').text(event.title);

                        $('#edit_date_debut').val(event.startStr);

                        

                        let endDate = new Date(event.endStr);

                        endDate.setDate(endDate.getDate() - 1);

                        $('#edit_date_fin').val(endDate.toISOString().split('T')[0]);

    

                        // Vérifier le solde initial au chargement du modal

                        checkSoldeAndUpdateDisplay(

                            event.id, 

                            $('#edit_date_debut').val(), 

                            $('#edit_date_fin').val(), 

                            $('#soldeInfoEdit'), 

                            $('#editCongeForm button[type="submit"]')

                        );

    

                        modal.show();

                    },

                    eventDidMount: function(info) {

                        new bootstrap.Tooltip(info.el, { title: info.event.extendedProps.type, placement: 'top', trigger: 'hover', container: 'body' });

                    }

                });

                calendar.render();

    

                /**

                 * Gère le drag-and-drop et le redimensionnement d'événements.

                 * @param {object} info - L'objet d'information de l'événement FullCalendar.

                 */

                function handleEventUpdate(info) {

                    const event = info.event;

                    let endDate = new Date(event.endStr);

                    endDate.setDate(endDate.getDate() - 1);

                    const endDateStr = endDate.toISOString().split('T')[0];

    

                    // 1. Vérifier le solde avant de faire la mise à jour

                    $.get(`${BASE_URL}/api/conge/solde`, { id: event.id, start_override: event.startStr, end_override: endDateStr }, (resp) => {

                        if (resp.success && resp.data.canValidate) {

                            // 2. Si le solde est suffisant, procéder à la mise à jour

                            $.post(`${BASE_URL}/api/conge/update`, {

                                id_demande_conge: event.id, 

                                new_start: event.startStr, 

                                new_end: endDateStr

                            }, (res) => { 

                                if (!res.success) { 

                                    alert('Erreur lors de la mise à jour: ' + res.message); 

                                    info.revert(); 

                                } 

                                location.reload(); 

                            }).fail(() => { 

                                alert('Erreur serveur lors de la mise à jour.'); 

                                info.revert(); 

                            });

                        } else {

                            // 3. Si le solde est insuffisant, annuler l'opération

                            alert('Modification impossible: ' + (resp.data.solde ? 'le solde est insuffisant.' : resp.message));

                            info.revert();

                        }

                    }).fail(() => {

                        alert('Erreur serveur lors de la vérification du solde.');

                        info.revert();

                    });

                }

    

                // --- Logique pour les Modals ---

                

                // Validation depuis la liste

                $(document).on('click', '.validate-btn', function() {

                    const id = $(this).data('id');

                    $('#validation_id_demande').val(id);

                    $('#validation_date').val(getCurrentDate());

                    $('#soldeInfo').html('Chargement...');

                    $('#validationSubmitBtn').prop('disabled', true);

                    

                    $.get(`${BASE_URL}/api/conge/solde`, { id }, (resp) => {

                        if (resp.success) {

                            const { solde, days, canValidate, taken_during_period } = resp.data;

                            let html = `<p><strong>Solde disponible:</strong> ${solde.balance} j</p><p><strong>Jours demandés:</strong> ${days} j</p>`;

                            if (typeof taken_during_period !== 'undefined') {

                                html += `<p><strong>Jours pris sur la période:</strong> ${taken_during_period} j</p>`;

                            }

                            if (solde.period_start && solde.period_end) {

                                html += `<p class="mt-2"><small>Calculé sur la période du <strong>${solde.period_start}</strong> au <strong>${solde.period_end}</strong>.</small></p>`;

                            }

                            html += canValidate ? '<p class="text-success mt-2">Validation possible</p>' : '<p class="text-danger mt-2">Solde insuffisant</p>';

                            $('#soldeInfo').html(html);

                            $('#validationSubmitBtn').prop('disabled', !canValidate);

                        } else {

                            $('#soldeInfo').html(`<p class="text-danger">${resp.message || 'Erreur solde.'}</p>`);

                        }

                    });

                    new bootstrap.Modal('#validationModal').show();

                });

    

                $('#validationForm').on('submit', function(e) { e.preventDefault(); $.post(`${BASE_URL}/backOffice/conge/valider`, $(this).serialize(), (r) => { if(r.success) location.reload(); else alert(r.message); }).fail(() => alert('Erreur serveur.')); });

    

                // Refus depuis la liste

                $(document).on('click', '.refuse-btn', function() { $('#refus_id_demande').val($(this).data('id')); $('#refus_date').val(getCurrentDate()); new bootstrap.Modal('#refusModal').show(); });

                $('#refusForm').on('submit', function(e) { e.preventDefault(); $.post(`${BASE_URL}/backOffice/conge/refuser`, $(this).serialize(), (r) => { if(r.success) location.reload(); else alert(r.message); }).fail(() => alert('Erreur serveur.')); });

    

                // Modification depuis le modal du calendrier

                $('#edit_date_debut, #edit_date_fin').on('change', function() {

                    checkSoldeAndUpdateDisplay(

                        $('#edit_id_demande').val(),

                        $('#edit_date_debut').val(),

                        $('#edit_date_fin').val(),

                        $('#soldeInfoEdit'),

                        $('#editCongeForm button[type="submit"]')

                    );

                });

    

                $('#editCongeForm').on('submit', function(e) {

                    e.preventDefault();

                    const data = { id_demande_conge: $('#edit_id_demande').val(), new_start: $('#edit_date_debut').val(), new_end: $('#edit_date_fin').val() };

                    $.post(`${BASE_URL}/api/conge/update`, data, (res) => { if (res.success) location.reload(); else alert(res.message); }).fail(() => alert('Erreur serveur.'));

                });

    

                // Suppression depuis le modal du calendrier

                $('#deleteCongeBtn').on('click', function() {

                    if (confirm('Êtes-vous sûr de vouloir supprimer ce congé ? Cette action est irréversible.')) {

                        $.post(`${BASE_URL}/api/conge/delete`, { id_demande_conge: $('#edit_id_demande').val() }, (res) => { if (res.success) location.reload(); else alert(res.message); }).fail(() => alert('Erreur serveur.'));

                    }

                });

    

            })(jQuery);

        });

        </script></body>

    </html>

    