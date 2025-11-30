<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'Heures Supplémentaires - Mazer</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
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
                            <h3>Demande d'heures supplémentaires</h3>
                            <p class="text-subtitle text-muted">Formulaire de demande d'heures supplémentaires</p>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Nouvelle demande d'heures supplémentaires</h5>
                        </div>
                        <div class="card-body">
                            <form id="demandeHeureSuppForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_debut" class="form-label">Date de début *</label>
                                            <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_fin" class="form-label">Date de fin *</label>
                                            <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="heure_debut" class="form-label">Heure de début *</label>
                                            <input type="time" class="form-control" id="heure_debut" name="heure_debut" 
                                                   min="17:00" max="22:00" required>
                                            <small class="form-text text-muted">Heures autorisées: 17h00 - 22h00</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="heure_fin" class="form-label">Heure de fin *</label>
                                            <input type="time" class="form-control" id="heure_fin" name="heure_fin" 
                                                   min="17:00" max="23:00" required>
                                            <small class="form-text text-muted">Heures autorisées: 17h00 - 23h00</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <h6>Informations importantes:</h6>
                                            <ul class="mb-0">
                                                <li>Les heures supplémentaires doivent être effectuées entre 17h et 23h</li>
                                                <li>La durée maximale par jour est de 4 heures</li>
                                                <li>Le nombre maximum d'heures par semaine est limité</li>
                                                <li>Toute demande est soumise à validation</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Soumettre la demande</button>
                                        <a href="<?= Flight::base() ?>/backOffice/heureSupp" class="btn btn-secondary">Annuler</a>
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
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

    <script>
    (function($){
        // Helper pour obtenir la date actuelle
        function getCurrentDate() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        // Initialiser les dates
        $('#date_debut').val(getCurrentDate());
        $('#date_fin').val(getCurrentDate());

        // Validation des dates
        $('#date_debut, #date_fin').on('change', function(){
            var dateDebut = new Date($('#date_debut').val());
            var dateFin = new Date($('#date_fin').val());
            
            if ($('#date_debut').val() && $('#date_fin').val() && dateFin < dateDebut) {
                alert('La date de fin doit être après la date de début');
                $('#date_fin').val($('#date_debut').val());
            }
        });

        // Validation des heures
        $('#heure_debut, #heure_fin').on('change', function(){
            var heureDebut = $('#heure_debut').val();
            var heureFin = $('#heure_fin').val();
            
            if (heureDebut && heureFin) {
                var debut = new Date('2000-01-01T' + heureDebut + ':00');
                var fin = new Date('2000-01-01T' + heureFin + ':00');
                
                if (fin <= debut) {
                    alert('L\'heure de fin doit être après l\'heure de début');
                    $('#heure_fin').val('');
                }

                // Calculer la durée
                var duree = (fin - debut) / (1000 * 60 * 60); // en heures
                if (duree > 4) {
                    alert('La durée maximale autorisée est de 4 heures par jour');
                    $('#heure_fin').val('');
                }
            }
        });

        // Soumission du formulaire
        $('#demandeHeureSuppForm').on('submit', function(e){
            e.preventDefault();
            
            var formData = $(this).serialize();
            
            $.post('<?= Flight::base() ?>/heureSupp/demande/submit', formData, function(resp){
                if (resp.success) {
                    alert('Demande soumise avec succès !');
                    window.location.href = '<?= Flight::base() ?>/backOffice/heureSupp';
                } else {
                    alert('Erreur: ' + resp.message);
                }
            }, 'json').fail(function(xhr, status, error){
                alert('Erreur de communication avec le serveur: ' + error);
            });
        });

    })(jQuery);
    </script>
</body>
</html>