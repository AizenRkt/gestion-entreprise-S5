<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'Absence - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg"
        type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet"
        href="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/styles/choices.css">
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
                            <h3>Demande d'absence</h3>
                            <p class="text-subtitle text-muted">Formulaire de demande d'absence</p>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Nouvelle demande d'absence</h5>
                        </div>
                        <div class="card-body">
                            <form id="demandeAbsenceForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type_absence">Type d'absence *</label>
                                            <select class="form-select" id="type_absence" name="id_type_absence"
                                                required>
                                                <option value="">Sélectionnez un type</option>
                                                <?php if (!empty($typesAbsence)): ?>
                                                    <?php foreach ($typesAbsence as $type): ?>
                                                        <option value="<?= $type['id_type_absence'] ?>">
                                                            <?= htmlspecialchars($type['nom']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="">Aucun type disponible</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_debut">Date de début *</label>
                                            <input type="date" class="form-control" id="date_debut" name="date_debut"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_fin">Date de fin *</label>
                                            <input type="date" class="form-control" id="date_fin" name="date_fin"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="motif">Motif *</label>
                                            <textarea class="form-control" id="motif" name="motif" rows="4"
                                                placeholder="Décrivez le motif de votre absence..." required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Soumettre la demande</button>
                                        <a href="<?= Flight::base() ?>/backOffice/absence"
                                            class="btn btn-secondary">Annuler</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
    <script
        src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

    <script>
        (function ($) {
            $('#demandeAbsenceForm').on('submit', function (e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.post('<?= Flight::base() ?>/absence/demande/submit', formData, function (resp) {
                    if (resp.success) {
                        alert('Demande soumise avec succès !');
                        window.location.href = '<?= Flight::base() ?>/backOffice/absence';
                    } else {
                        alert('Erreur: ' + resp.message);
                    }
                }, 'json').fail(function () {
                    alert('Erreur de communication avec le serveur');
                });
            });

            // Validation des dates
            $('#date_debut, #date_fin').on('change', function () {
                var dateDebut = new Date($('#date_debut').val());
                var dateFin = new Date($('#date_fin').val());

                if ($('#date_debut').val() && $('#date_fin').val() && dateFin < dateDebut) {
                    alert('La date de fin doit être après la date de début');
                    $('#date_fin').val('');
                }
            });
        })(jQuery);
    </script>
</body>

</html>