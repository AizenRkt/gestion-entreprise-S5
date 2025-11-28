<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Congé</title>
    
    <!-- Styles et icônes -->
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.css">
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
                        <h3>Demande de Congé</h3>
                        <p class="text-subtitle text-muted">Soumettez votre demande de congé</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/backOffice">Accueil</a></li>
                                <li class="breadcrumb-item active">Demande Congé</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="section">
                <?php if (!$canRequestConge): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Demande non autorisée</h4>
                            <p class="mb-0">Vous ne pouvez pas faire de demande de congé pendant votre première année de contrat.</p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Nouvelle demande de congé</h5>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> 
                                    <strong>Règles importantes :</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Demande à faire au moins <strong>15 jours</strong> avant la date de début</li>
                                        <li>Les week-ends ne sont pas comptabilisés</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="formDemandeConge">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Type de congé *</label>
                                            <select class="form-select" name="id_type_conge" required>
                                                <option value="">Sélectionnez un type</option>
                                                <?php if (!empty($typesConge)): ?>
                                                    <?php foreach ($typesConge as $type): ?>
                                                        <option value="<?= htmlspecialchars($type['id_type_conge']) ?>">
                                                            <?= htmlspecialchars($type['nom']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Jours ouvrés estimés</label>
                                            <input type="text" class="form-control" id="joursEstimes" readonly value="0 jour(s)">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date de début *</label>
                                            <input type="date" class="form-control" name="date_debut" id="date_debut" required 
                                                   min="<?= date('Y-m-d', strtotime('+15 days')) ?>">
                                            <div class="form-text">Minimum 15 jours à partir d'aujourd'hui</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date de fin *</label>
                                            <input type="date" class="form-control" name="date_fin" id="date_fin" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Motif (optionnel)</label>
                                        <textarea class="form-control" name="motif" rows="3" placeholder="Raison de votre demande de congé..."></textarea>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-send"></i> Soumettre la demande
                                        </button>
                                        <a href="<?= Flight::base() ?>/backOffice" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Annuler
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Colonne d'information -->
                    <div class="col-12 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Informations importantes</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-light-primary">
                                    <h6><i class="bi bi-clock"></i> Délai de préavis</h6>
                                    <p class="mb-0">Toute demande doit être faite <strong>au moins 15 jours</strong> avant le début du congé.</p>
                                </div>
                                <div class="alert alert-light-warning">
                                    <h6><i class="bi bi-calendar-x"></i> Période d'essai</h6>
                                    <p class="mb-0">Aucune demande de congé n'est autorisée pendant la première année de contrat.</p>
                                </div>
                                <div class="alert alert-light-info">
                                    <h6><i class="bi bi-calendar-check"></i> Calcul des jours</h6>
                                    <p class="mb-0">Les samedis et dimanches ne sont pas comptabilisés dans le calcul.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </section>
        </div>

        <!-- Footer -->
        <footer>
            <div class="footer clearfix mb-0 text-muted">
                <div class="float-start">
                    <p>2024 &copy; Votre Entreprise</p>
                </div>
                <div class="float-end">
                    <p>Gestion des Congés</p>
                </div>
            </div>
        </footer>
    </div>
</div>

<!-- Scripts -->
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formDemandeConge');
    if (!form) return; // Si le formulaire n'existe pas (demande non autorisée)
    
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const joursEstimes = document.getElementById('joursEstimes');

    // Fonction pour calculer les jours ouvrés via l'API
    function calculerJoursOuvresAPI(startDate, endDate) {
        if (!startDate || !endDate) return 0;
        
        return fetch('<?= Flight::base() ?>/conge/calcul-jours', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `date_debut=${startDate}&date_fin=${endDate}`
        })
        .then(response => response.json())
        .then(data => {
            return data.success ? data.nbJours : 0;
        })
        .catch(error => {
            console.error('Erreur calcul jours:', error);
            return 0;
        });
    }

    // Mettre à jour les jours estimés
    async function mettreAJourJoursEstimes() {
        if (dateDebut.value && dateFin.value) {
            const nbJours = await calculerJoursOuvresAPI(dateDebut.value, dateFin.value);
            joursEstimes.value = nbJours + ' jour(s)';
            
            // Changer la couleur selon le nombre de jours
            if (nbJours > 0) {
                joursEstimes.classList.remove('is-invalid');
                joursEstimes.classList.add('is-valid');
            } else {
                joursEstimes.classList.remove('is-valid');
                joursEstimes.classList.add('is-invalid');
            }
        }
    }

    // Événements pour le calcul automatique
    dateDebut.addEventListener('change', function() {
        if (dateDebut.value && dateFin.value) {
            mettreAJourJoursEstimes();
        }
        // Mettre la date de fin minimum égale à la date de début
        if (dateDebut.value) {
            dateFin.min = dateDebut.value;
        }
    });

    dateFin.addEventListener('change', function() {
        if (dateDebut.value && dateFin.value) {
            mettreAJourJoursEstimes();
        }
    });

    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation côté client
        if (!dateDebut.value || !dateFin.value) {
            Toastify({
                text: "Veuillez remplir toutes les dates",
                backgroundColor: "red",
                duration: 3000
            }).showToast();
            return;
        }

        if (new Date(dateDebut.value) > new Date(dateFin.value)) {
            Toastify({
                text: "La date de fin doit être après la date de début",
                backgroundColor: "red",
                duration: 3000
            }).showToast();
            return;
        }

        // Vérification délai de 15 jours côté client
        const dateDebutObj = new Date(dateDebut.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const diffTime = dateDebutObj - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 15) {
            Toastify({
                text: "La demande doit être faite au moins 15 jours avant la date de début",
                backgroundColor: "red",
                duration: 5000
            }).showToast();
            return;
        }

        const formData = new FormData(this);
        
        // Afficher un indicateur de chargement
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Envoi en cours...';
        submitBtn.disabled = true;
        
        fetch('<?= Flight::base() ?>/conge/demande', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Toastify({
                    text: data.message,
                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                    duration: 5000
                }).showToast();
                
                // Redirection après succès
                setTimeout(() => {
                    window.location.href = '<?= Flight::base() ?>/backOffice';
                }, 2000);
            } else {
                Toastify({
                    text: data.message || "Erreur lors de la soumission",
                    backgroundColor: "red",
                    duration: 5000
                }).showToast();
                
                // Afficher les erreurs détaillées si disponibles
                if (data.errors) {
                    data.errors.forEach(error => {
                        Toastify({
                            text: error,
                            backgroundColor: "orange",
                            duration: 5000
                        }).showToast();
                    });
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            Toastify({
                text: "Erreur de connexion. Veuillez réessayer.",
                backgroundColor: "red",
                duration: 5000
            }).showToast();
        })
        .finally(() => {
            // Restaurer le bouton
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Initialiser la date minimum (15 jours à partir d'aujourd'hui)
    const minDate = new Date();
    minDate.setDate(minDate.getDate() + 15);
    const minDateString = minDate.toISOString().split('T')[0];
    dateDebut.min = minDateString;
    
    // Si la date de début est définie, mettre à jour le min de la date de fin
    if (dateDebut.value) {
        dateFin.min = dateDebut.value;
    }
});
</script>
</body>
</html>