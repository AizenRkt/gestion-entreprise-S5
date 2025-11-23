<!DOCTYPE html> 
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des contrats d'essai - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">

    <!-- datatables -->
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
</head>

<body>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/initTheme.js"></script>
<div id="app">
    <?= Flight::menuBackOffice() ?>
    <div id="main">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des contrats d'essai</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tableContrats">
                            <thead>
                                <tr>
                                    <th>ID Contrat</th>
                                    <th>Candidat</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- sera rempli par JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- MODAL RENOUVELLEMENT -->
<div class="modal fade" id="modalRenouvellement" tabindex="-1" aria-labelledby="modalRenouvellementLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRenouvellement">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRenouvellementLabel">Renouveler le contrat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idContratRenouvellement">

                    <div class="mb-3">
                        <label for="nouvelleDateFin" class="form-label">Nouvelle date de fin</label>
                        <input type="date" class="form-control" id="nouvelleDateFin" required>
                        <small class="text-muted">La durée maximale d’un renouvellement est de 6 mois.</small>
                    </div>

                    <div class="mb-3">
                        <label for="commentaireRenouvellement" class="form-label">Commentaire</label>
                        <textarea class="form-control" id="commentaireRenouvellement"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Renouveler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    let table = $('#tableContrats').DataTable();

    // Charger tous les contrats
    function loadContrats() {
        table.clear().draw();
        fetch('<?= Flight::base() ?>/contrat/all')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let rows = [];
                    data.data.forEach(c => {
                        let peutRenouveler = !(c.statut);

                        let actions = `
                            <button class="btn btn-success btn-sm valider-contrat" data-id="${c.id_contrat_essai}">Valider</button>
                            <button class="btn btn-danger btn-sm annuler-contrat" data-id="${c.id_contrat_essai}">Rejeter</button>
                            <button class="btn btn-primary btn-sm renouveler-contrat" style="margin-top: 5px" data-id="${c.id_contrat_essai}"
                                ${!peutRenouveler ? "disabled" : ""}>
                                Renouveler
                            </button>
                        `;

                        rows.push([
                            c.id_contrat_essai,
                            c.nom + ' ' + c.prenom,
                            c.debut,
                            c.fin,
                            c.statut || 'En attente',
                            actions
                        ]);
                    });
                    table.rows.add(rows).draw(false);
                } else {
                    table.row.add(["Aucun contrat trouvé", "", "", "", "", ""]).draw();
                }
            });
    }

    loadContrats();

    // Valider contrat
    $('#tableContrats').on('click', '.valider-contrat', function() {
        const id = $(this).data('id');

        if (!confirm("Voulez-vous vraiment VALIDER ce contrat d'essai ?")) {
            return;
        }

        fetch(`<?= Flight::base() ?>/contrat/valider/${id}?commentaire=Contrat validé`)
            .then(res => res.json())
            .then(data => { alert(data.message); loadContrats(); });
    });

    // Annuler contrat
    $('#tableContrats').on('click', '.annuler-contrat', function() {
        const id = $(this).data('id');

        if (!confirm("Voulez-vous vraiment REJETER ce contrat d'essai ?")) {
            return;
        }

        fetch(`<?= Flight::base() ?>/contrat/rejeter/${id}?commentaire=Contrat annulé`)
            .then(res => res.json())
            .then(data => { alert(data.message); loadContrats(); });
    });

    // Ouvrir modal renouvellement
    $('#tableContrats').on('click', '.renouveler-contrat', function() {
        const id = $(this).data('id');
        $('#idContratRenouvellement').val(id);
        $('#nouvelleDateFin').val('');
        $('#commentaireRenouvellement').val('');
        const modal = new bootstrap.Modal(document.getElementById('modalRenouvellement'));
        modal.show();
    });

    // Envoyer formulaire de renouvellement
    $('#formRenouvellement').on('submit', function(e) {
        e.preventDefault();
        const id = $('#idContratRenouvellement').val();
        const nouvelleDateFin = $('#nouvelleDateFin').val();
        const commentaire = $('#commentaireRenouvellement').val();

        fetch(`<?= Flight::base() ?>/contrat/renouveller/${id}?date_fin=${nouvelleDateFin}&commentaire=${encodeURIComponent(commentaire)}`)
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) $('#modalRenouvellement').modal('hide');
                loadContrats();
            });
    });
});
</script>

</body>
</html>
