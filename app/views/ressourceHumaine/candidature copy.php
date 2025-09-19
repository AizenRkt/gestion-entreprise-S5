<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidature - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/styles/choices.css">
    <style>
        .step-section {
            display: none;
        }

        .step-section.active {
            display: block;
        }
    </style>
</head>

<body>
    <div id="app">
        <div id="main" class="layout-horizontal">
            <?= Flight::menuFrontOffice() ?>
            <div class="content-wrapper container">

                <?php
                if (isset($_GET['success']) && $_GET['success'] == '1') {
                    echo '<div id="success-msg" class="alert alert-success"><i class="bi bi-check-circle"></i> Votre CV a été postulé avec succès !</div>';
                }
                if (isset($_GET['error']) && $_GET['error'] == 'mail') {
                    echo '<div id="error-msg" class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i>Un CV existe déjà pour ce profil et cet email.</div>';
                }
                ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var msg = document.getElementById('success-msg');
                        if (msg) {
                            setTimeout(function() {
                                msg.style.display = 'none';
                                // Retirer le paramètre success=1 de l'URL
                                if (window.location.search.includes('success=1')) {
                                    const url = new URL(window.location);
                                    url.searchParams.delete('success');
                                    window.history.replaceState({}, document.title, url.pathname + url.search);
                                }
                            }, 4000); // 4 secondes
                        }
                        var err = document.getElementById('error-msg');
                        if (err) {
                            setTimeout(function() {
                                err.style.display = 'none';
                                // Retirer le paramètre error=mail de l'URL
                                if (window.location.search.includes('error=mail')) {
                                    const url = new URL(window.location);
                                    url.searchParams.delete('error');
                                    window.history.replaceState({}, document.title, url.pathname + url.search);
                                }
                            }, 4000); // 4 secondes
                        }
                    });
                </script>
                <div class="page-heading">
                    <h3>Déposez votre candidature</h3>
                    <p class="text-muted">Remplissez le formulaire étape par étape</p>
                </div>

                <section class="section">
                    <div class="row">
                        <div class="col-12">
                            <div class="card p-4">
                                <form action="<?= Flight::base() ?>/candidat/create" method="post" id="cvForm" enctype="multipart/form-data">
                                    <!-- ===== Étape 1 : Informations personnelles ===== -->
                                    <div id="step1" class="step-section active">

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Prénom</label>
                                                <input type="text" name="prenom" class="form-control" placeholder="Prénom" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Nom</label>
                                                <input type="text" name="nom" class="form-control" placeholder="Nom" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Email</label>
                                                <input type="email" name="email" class="form-control" placeholder="Email" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Téléphone</label>
                                                <input type="tel" name="telephone" class="form-control" placeholder="Téléphone" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Date de naissance</label>
                                                <input type="date" name="date_naissance" class="form-control" placeholder="Date de naissance" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Ville</label>
                                                <select name="ville" class="form-select" required>
                                                    <?php if (isset($villes) && is_array($villes)): ?>
                                                        <?php foreach ($villes as $ville): ?>
                                                            <option value="<?= htmlspecialchars($ville['id_ville']) ?>">
                                                                <?= htmlspecialchars($ville['nom']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option disabled>Aucune ville disponible</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Genre</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="genre" id="homme" value="Homme" required>
                                                    <label class="form-check-label" for="homme">Homme</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="genre" id="femme" value="Femme" required>
                                                    <label class="form-check-label" for="femme">Femme</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-primary" onclick="nextStep(1, 2)">Suivant</button>
                                        </div>
                                    </div>

                                    <!-- ===== Étape 2 : Parcours & compétences ===== -->
                                    <div id="step2" class="step-section">

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Diplômes</label>
                                            <?php if (isset($diplomes) && is_array($diplomes)): ?>
                                                <?php foreach ($diplomes as $diplome): ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="diplome[]" value="<?= htmlspecialchars($diplome['nom']) ?>" id="diplome<?= $diplome['id_diplome'] ?>">
                                                        <label class="form-check-label" for="diplome<?= $diplome['id_diplome'] ?>">
                                                            <?= htmlspecialchars($diplome['nom']) ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p>Aucun diplôme disponible.</p>
                                            <?php endif; ?>
                                        </div>


                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Compétences (mots-clés)</label>
                                            <select class="choices form-select" name="competences[]" multiple="multiple">
                                                <?php if (isset($competences) && is_array($competences)): ?>
                                                    <?php foreach ($competences as $competence): ?>
                                                        <option value="<?= htmlspecialchars($competence['nom']) ?>">
                                                            <?= htmlspecialchars($competence['nom']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option disabled>Aucune compétence disponible</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-light-secondary" onclick="prevStep(2, 1)">Précédent</button>
                                            <button type="button" class="btn btn-primary" onclick="nextStep(2, 3)">Suivant</button>
                                        </div>
                                    </div>

                                    <!-- ===== Étape 3 : Photo ===== -->
                                    <div id="step3" class="step-section">

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Téléchargez votre photo</label>
                                            <input type="file" name="photo" accept="image/*" class="form-control" required>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-light-secondary" onclick="prevStep(3, 2)">Précédent</button>
                                            <button type="submit" class="btn btn-success" id="submitBtn">Soumettre ma candidature</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const elements = document.querySelectorAll('.choices');
            elements.forEach(el => {
                new Choices(el, {
                    searchEnabled: true,
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: 'Sélectionnez...'
                });
            });

            // Coloration du bouton si un champ required n'est pas rempli
            const form = document.getElementById('cvForm');
            const submitBtn = document.getElementById('submitBtn');

            function updateButtonColor() {
                if (!form.checkValidity()) {
                    submitBtn.style.backgroundColor = '#d3d3d3'; // gris clair
                } else {
                    submitBtn.style.backgroundColor = '';
                }
            }

            form.addEventListener('input', updateButtonColor);
            // Initialisation au chargement
            updateButtonColor();
        });

        function nextStep(current, next) {
            document.getElementById('step' + current).classList.remove('active');
            document.getElementById('step' + next).classList.add('active');
        }

        function prevStep(current, prev) {
            document.getElementById('step' + current).classList.remove('active');
            document.getElementById('step' + prev).classList.add('active');
        }
    </script>
</body>

</html>