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
                    <h5 class="card-title mb-0">Liste des contrats d'essai valables pour un contrat de travail</h5>
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

<!-- MODAL CREATION CONTRAT -->
<div class="modal fade" id="modalCreerContrat" tabindex="-1" aria-labelledby="modalCreerContratLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formCreerContrat">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreerContratLabel">Créer un contrat de travail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idContratEssai">

                    <div class="mb-3">
                        <label for="typeContrat" class="form-label">Type de contrat</label>
                        <select class="form-select" id="typeContrat" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="CDI">CDI</option>
                            <option value="CDD">CDD</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="debutContrat" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="debutContrat" required>
                    </div>

                    <div class="mb-3" id="finContratWrapper">
                        <label for="finContrat" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="finContrat">
                        <small class="text-muted">Ne remplir que pour les CDD.</small>
                    </div>

                    <div class="mb-3">
                        <label for="salaireBase" class="form-label">Salaire de base</label>
                        <input type="number" step="0.01" class="form-control" id="salaireBase" required>
                    </div>

                    <div class="mb-3">
                        <label for="dateSignature" class="form-label">Date de signature</label>
                        <input type="date" class="form-control" id="dateSignature">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le contrat</button>
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

    // Charger tous les contrats validés
    function loadContrats() {
        table.clear().draw();
        fetch('<?= Flight::base() ?>/contrat/valide/all')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let rows = [];
                    data.data.forEach(c => {
                        // Pour chaque contrat valide, on met juste le bouton "Créer un contrat"
                        let actions = `
                            <button class="btn btn-primary btn-sm creer-contrat" data-id="${c.id_contrat_essai}">
                                Créer un contrat
                            </button>
                        `;

                        rows.push([
                            c.id_contrat_essai,
                            c.nom + ' ' + c.prenom,
                            c.debut,
                            c.fin,
                            c.statut || 'Validé',
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

});
</script>
<script>
$(document).ready(function() {
    // Afficher ou masquer le champ fin selon le type de contrat
    $('#typeContrat').on('change', function() {
        if ($(this).val() === 'CDI') {
            $('#finContratWrapper').hide();
            $('#finContrat').val('');
        } else {
            $('#finContratWrapper').show();
        }
    });

    // Quand on clique sur "Créer un contrat" dans la table
    $('#tableContrats').on('click', '.creer-contrat', function() {
        const id = $(this).data('id');
        $('#idContratEssai').val(id);
        $('#typeContrat').val('');
        $('#debutContrat').val('');
        $('#finContrat').val('');
        $('#salaireBase').val('');
        $('#dateSignature').val('');
        $('#finContratWrapper').show();

        const modal = new bootstrap.Modal(document.getElementById('modalCreerContrat'));
        modal.show();
    });

    // Soumission du formulaire
    $('#formCreerContrat').on('submit', function(e) {
        e.preventDefault();
        const idContratEssai = $('#idContratEssai').val();
        const typeContrat = $('#typeContrat').val();
        const debut = $('#debutContrat').val();
        const fin = $('#finContrat').val();
        const salaire = $('#salaireBase').val();
        const signature = $('#dateSignature').val();

        // Redirection vers la route appropriée avec GET
        let url = `<?= Flight::base() ?>/contratTravail/${typeContrat}/creer/${idContratEssai}?debut=${debut}&fin=${fin}&salaire_base=${salaire}&date_signature=${signature}`;
        window.location.href = url;
    });
});
</script>

</body>
</html>
