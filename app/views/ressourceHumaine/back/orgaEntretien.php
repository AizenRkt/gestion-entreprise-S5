<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planifier un entretien - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <style>
        .alert {
            display: none;
        }
        .loading {
            display: none;
        }
    </style>
</head>

<body>
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
                        <h3>Planifier un entretien</h3>
                        <p class="text-subtitle text-muted">Le candidat sera notifié par mail</p>
                    </div>
                </div>
            </div>
            
            <!-- Messages d'alerte -->
            <div id="alertContainer">
                <div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert">
                    <span id="successMessage"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div id="errorAlert" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span id="errorMessage"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>

            <section class="section">
                <div class="row">
                    <!-- Formulaire entretien -->
                    <div class="col-12 col-lg-8">
                        <div class="card p-4">
                            <form id="entretienForm" method="post">

                                <!-- Sélection du candidat -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Candidat *</label>
                                    <select name="id_candidat" id="candidatSelect" class="form-select" required>
                                        <option value="">-- Sélectionner un candidat --</option>
                                        <?php if (isset($candidats) && is_array($candidats)): ?>
                                            <?php foreach ($candidats as $candidat): ?>
                                                <option value="<?= htmlspecialchars($candidat['id_candidat']) ?>">
                                                    <?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Date de l'entretien -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Date *</label>
                                    <input type="date" name="date_entretien" class="form-control" required min="<?= date('Y-m-d') ?>">
                                </div>

                                <!-- Heure de l'entretien -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Heure *</label>
                                    <input type="time" name="heure_entretien" class="form-control" required>
                                </div>

                                <!-- Durée -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Durée (minutes) *</label>
                                    <input type="number" name="duree_entretien" class="form-control" placeholder="Ex: 60" min="15" max="480" required>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">
                                        <span class="loading spinner-border spinner-border-sm me-2" role="status"></span>
                                        Planifier l'entretien
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>

                    <!-- Aperçu du candidat -->
                    <div class="col-12 col-lg-4">
                        <div class="card p-4">
                            <h5 class="fw-bold">Informations du candidat</h5>
                            <p class="text-muted">Détails du candidat sélectionné</p>

                            <!-- Aperçu du candidat -->
                            <div id="candidatPreview" style="display: none;">
                                <div class="text-center mb-3">
                                    <div class="avatar avatar-xl">
                                        <span id="candidatInitials" class="avatar-initial bg-primary text-white"></span>
                                    </div>
                                </div>
                                <h6 id="candidatNom" class="fw-bold text-center mb-2"></h6>
                                <div class="mb-2">
                                    <strong>Email:</strong>
                                    <span id="candidatEmail" class="text-muted"></span>
                                </div>
                                <div class="mb-2">
                                    <strong>Téléphone:</strong>
                                    <span id="candidatTelephone" class="text-muted"></span>
                                </div>
                            </div>

                            <div id="noSelection" class="text-center text-muted">
                                <i class="bi bi-person-circle fs-1 mb-3"></i>
                                <p>Sélectionnez un candidat pour voir ses informations</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('entretienForm');
    const candidatSelect = document.getElementById('candidatSelect');
    const preview = document.getElementById('candidatPreview');
    const noSelection = document.getElementById('noSelection');
    const loading = document.querySelector('.loading');

    // Gestion de la sélection du candidat
    candidatSelect.addEventListener('change', function() {
        const candidatId = this.value;
        
        if (candidatId) {
            // Récupérer les informations du candidat via AJAX
            fetch(`<?= Flight::base() ?>/entretien/candidat-info?id=${candidatId}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.error) {
                        updateCandidatPreview(data);
                        preview.style.display = 'block';
                        noSelection.style.display = 'none';
                    } else {
                        console.error('Erreur:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des données candidat:', error);
                });
        } else {
            preview.style.display = 'none';
            noSelection.style.display = 'block';
        }
    });

    // Fonction pour mettre à jour l'aperçu du candidat
    function updateCandidatPreview(candidat) {
        const initials = (candidat.prenom[0] + candidat.nom[0]).toUpperCase();
        document.getElementById('candidatInitials').textContent = initials;
        document.getElementById('candidatNom').textContent = candidat.prenom + ' ' + candidat.nom;
        document.getElementById('candidatEmail').textContent = candidat.email || 'Non renseigné';
        document.getElementById('candidatTelephone').textContent = candidat.telephone || 'Non renseigné';
    }

    // Gestion de la soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Afficher le loading
        loading.style.display = 'inline-block';
        
        const formData = new FormData(form);
        
        fetch('<?= Flight::base() ?>/entretien/creer', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.success) {
                showAlert('success', data.message);
                form.reset();
                preview.style.display = 'none';
                noSelection.style.display = 'block';
            } else {
                showAlert('error', data.error || 'Une erreur est survenue');
            }
        })
        .catch(error => {
            loading.style.display = 'none';
            console.error('Erreur:', error);
            showAlert('error', 'Erreur de communication avec le serveur');
        });
    });

    // Fonction pour afficher les alertes
    function showAlert(type, message) {
        const alertElement = document.getElementById(type + 'Alert');
        const messageElement = document.getElementById(type + 'Message');
        
        messageElement.textContent = message;
        alertElement.style.display = 'block';
        
        // Masquer l'alerte après 5 secondes
        setTimeout(() => {
            alertElement.style.display = 'none';
        }, 5000);
    }
});
</script>
</body>
</html>