<!DOCTYPE html> 
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrats de travail - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">

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
                    <h5 class="card-title mb-0">Liste des contrats de travail</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tableContrats">
                            <thead>
                                <tr>
                                    <th>Employé</th>
                                    <th>Type</th>
                                    <th>Poste</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
    </div>

</div>

<!-- MODAL RENOUVELLEMENT -->
<div class="modal fade" id="modalRenouvellement" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRenouvellement">
                <div class="modal-header">
                    <h5 class="modal-title">Renouveler le contrat CDD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="idContratRenouvellement">

                    <div class="mb-3">
                        <label class="form-label">Nouvelle date de fin</label>
                        <input type="date" class="form-control" id="nouvelleDateFin" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Commentaire</label>
                        <textarea class="form-control" id="commentaireRenouvellement"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date de renouvellement</label>
                        <input type="date" class="form-control" id="dateRenouvellement" required>
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

<!-- MODAL REQUALIFICATION -->
<div class="modal fade" id="modalRequalification" tabindex="-1" aria-labelledby="modalRequalificationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRequalification">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRequalificationLabel">basculer un CDD vers un CDI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="idContratRequalification">

                    <div class="mb-3">
                        <label for="debutContrat" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="debutContrat" required>
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
<script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    let table = $('#tableContrats').DataTable();

    // LOAD CONTRATS
    function loadContrats() {
        table.clear().draw();

        fetch("<?= Flight::base() ?>/contratTravail/all")
            .then(res => res.json())
            .then(result => {

                if (!result.success || result.data.length === 0) {
                    table.row.add(["Aucun contrat", "", "", "", "", ""]).draw();
                    return;
                }

                let rows = [];

                result.data.forEach(c => {

                    let date_fin = c.fin || "/";
                    if (c.nouvelle_date_fin) date_fin = c.nouvelle_date_fin;


                    pathPDF = c.pathPdf;
                    if(c.renouvellement_pdf != null) {
                        pathPDF = c.renouvellement_pdf;
                    }

                    // Bouton RENOUVELER seulement si CDD
                    let btnRenouveler = "";
                    if (c.type_contrat === "CDD" && (c.date_renouvellement == null)) {
                        btnRenouveler = `
                            <button class="btn btn-primary btn-sm renouvelerBtn" data-id="${c.id_contrat_travail}">
                                Renouveler
                            </button>`;
                    }

                    let btnRequalification = ""; 
                    if (c.type_contrat === "CDD") {                
                        btnRequalification = `
                            <button class="btn btn-success btn-sm requalificationBtn" data-id="${c.id_contrat_travail}">
                                basculer vers CDI
                            </button>`;                    
                    }
                    

                    let actions = `
                        ${btnRenouveler}
                        ${btnRequalification}
                        <a href="<?= Flight::base() ?>/public/uploads/data/document/${pathPDF}" 
                           target="_blank" 
                           class="btn btn-info btn-sm">
                            Voir contrat
                        </a>
                    `;

                    dateFIN = c.fin;
                    if(c.nouvelle_date_fin != null) {
                        dateFIN = c.nouvelle_date_fin;
                    }  

                    rows.push([
                        c.nom + " " + c.prenom,
                        c.type_contrat,
                        c.poste,
                        c.debut,
                        dateFIN,
                        actions
                    ]);
                });

                table.rows.add(rows).draw(false);
            });
    }

    loadContrats();

    $('#tableContrats').on('click', '.renouvelerBtn',function () {
        $('#idContratRenouvellement').val($(this).data("id"));
        $('#nouvelleDateFin').val('');
        $('#commentaireRenouvellement').val('');
        $('#dateRenouvellement').val('');

        new bootstrap.Modal(document.getElementById('modalRenouvellement')).show();
    });

    $('#tableContrats').on('click', '.requalificationBtn',function () {
        $('#idContratRequalification').val($(this).data("id"));
        $('#nouvelleDateFin').val('');
        $('#commentaireRenouvellement').val('');
        $('#dateRenouvellement').val('');

        new bootstrap.Modal(document.getElementById('modalRequalification')).show();
    });


    $('#formRenouvellement').submit(function (e) {
        e.preventDefault();

        let id = $('#idContratRenouvellement').val();
        let dateFin = $('#nouvelleDateFin').val();
        let commentaire = $('#commentaireRenouvellement').val();
        let dateRenouvellement = $('#dateRenouvellement').val();

        fetch(`<?= Flight::base() ?>/contratTravail/CDD/renouveller/${id}?nouvelle_date_fin=${dateFin}&date_renouvellement=${dateRenouvellement}&commentaire=${encodeURIComponent(commentaire)}`)
            .then(res => res.json())
            .then(rep => {
                alert(rep.message);
                if (rep.success) $('#modalRenouvellement').modal('hide');
                loadContrats();
            });
    });

    $('#formRequalification').submit(function (e) {
        e.preventDefault();

        let id = $('#idContratRequalification').val();
        let dateDebut = $('#debutContrat').val();
        let salaireBase = $('#salaireBase').val();
        let dateSignature = $('#dateSignature').val();

        fetch(`<?= Flight::base() ?>/contratTravail/CDD/toCDI/${id}?date_debut=${dateDebut}&salaire_base=${salaireBase}&date_signature=${dateSignature}`)
            .then(res => res.json())
            .then(rep => {
                alert(rep.message);
                if (rep.success) $('#modalRequalification').modal('hide');
                loadContrats();
            });
    });

});
</script>

</body>
</html>
