<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrats d'essai - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">

    <!-- datatables -->
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
</head>

<body>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/initTheme.js"></script>
<div id="app">
    <?= Flight::menuBackOffice() ?>
    <div id="main">
        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Candidats éligibles au contrat d'essai</h5>
                    <select id="annonceSelect" class="form-select w-auto">
                        <option value="">-- choisir une annonce --</option>
                        <?php foreach($annonce as $a): ?>
                            <option value="<?= $a['id_annonce'] ?>"><?= htmlspecialchars($a['titre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tableCandidats">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Date de candidature</th>
                                    <th>Note QCM</th>
                                    <th>Note Entretien /10</th>
                                    <th>Évaluation Entretien</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- vide au départ -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- ✅ MODAL CRÉATION CONTRAT -->
<div class="modal fade" id="modalContrat" tabindex="-1" aria-labelledby="modalContratLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formContrat" action="<?= Flight::base() ?>/contrat/creer" method="get">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalContratLabel">Établir un contrat d'essai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_candidat" id="idCandidatInput">

                    <div class="mb-3">
                        <label for="dateDebut" class="form-label">Date de début du contrat</label>
                        <input type="date" class="form-control" name="date_debut" id="dateDebut" required>
                    </div>

                    <div class="mb-3">
                        <label for="duree" class="form-label">Durée du contrat (en mois)</label>
                        <input type="number" class="form-control" name="duree_mois" id="duree" min="1" max="6" required>
                        <small class="text-muted">La durée maximale est de 6 mois.</small>
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
    let table = $('#tableCandidats').DataTable();

    // 🟦 Chargement des candidats selon l'annonce
    $('#annonceSelect').on('change', function () {
        let idAnnonce = $(this).val();
        table.clear().draw();

        if (!idAnnonce) {
            table.row.add(["Veuillez sélectionner une annonce", "", "", "", "", "", "", ""]).draw();
            return;
        }

        fetch(`<?= Flight::base() ?>/eligibleEssai/${idAnnonce}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let rows = [];
                    data.data.forEach(candidat => {
                        let noteQCM = candidat.note_qcm + '/' + candidat.note_max_qcm;

                        let boutonContrat = `
                            <button class="btn btn-primary btn-sm etablir-contrat"
                                    data-id="${candidat.id_candidat}"
                                    data-nom="${candidat.nom || ''}"
                                    data-prenom="${candidat.prenom || ''}">
                                <i class="bi bi-file-earmark-text"></i> Établir un contrat
                            </button>
                        `;

                        rows.push([
                            candidat.nom,
                            candidat.prenom,
                            candidat.email,
                            candidat.date_candidature,
                            noteQCM,
                            candidat.note_entretien,
                            candidat.evaluation_entretien,
                            boutonContrat
                        ]);
                    });
                    table.rows.add(rows).draw(false);
                } else {
                    table.row.add(["Aucun candidat trouvé", "", "", "", "", "", "", ""]).draw();
                }
            })
            .catch(err => {
                console.error(err);
                table.row.add(["Erreur lors du chargement", "", "", "", "", "", "", ""]).draw();
            });
    });

    // 🟩 Ouverture du modal
    $('#tableCandidats').on('click', '.etablir-contrat', function () {
        const bouton = $(this);
        const idCandidat = bouton.attr('data-id');
        const nom = bouton.attr('data-nom');
        const prenom = bouton.attr('data-prenom');

        // Remplir le champ caché du formulaire
        $('#idCandidatInput').val(idCandidat);

        // Modifier le titre du modal
        $('#modalContratLabel').text(`Établir un contrat pour ${prenom} ${nom}`);

        // Ouvrir le modal
        const modal = new bootstrap.Modal(document.getElementById('modalContrat'));
        modal.show();
    });
});
</script>

</body>
</html>
