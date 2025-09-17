<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une annonce - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/styles/choices.css">
    <style>
        .step-section { display: none; }
        .step-section.active { display: block; }
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
                        <h3>Créer une annonce</h3>
                        <p class="text-subtitle text-muted">Remplissez le formulaire suivant étape par étape</p>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="row">
                    <div class="col-12">
                    <div class="card p-4">
                        <form action="#" method="post" id="annonceForm">
                        <!-- ===== Étape 1 : Informations de base ===== -->
                        <div id="step1" class="step-section active">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Titre de l’annonce</label>
                                <input type="text" name="titre" class="form-control" placeholder="Ex: Développeur Full Stack" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Âge minimum</label>
                                    <input type="number" name="age_min" class="form-control" placeholder="Ex: 22" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Âge maximum</label>
                                    <input type="number" name="age_max" class="form-control" placeholder="Ex: 35" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Diplôme requis</label>
                                <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="diplome[]" value="Licence" id="licence">
                                <label class="form-check-label" for="licence">Licence</label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="diplome[]" value="Master" id="master">
                                <label class="form-check-label" for="master">Master</label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="diplome[]" value="Doctorat" id="doctorat">
                                <label class="form-check-label" for="doctorat">Doctorat</label>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                <label class="form-label fw-bold">Années d'expérience (minimum)</label>
                                <input type="number" name="experience" class="form-control" placeholder="Ex: 2" required>
                                </div>
                                <div class="col-md-6">
                                <label class="form-label fw-bold">Lieu (ville)</label>
                                <input type="text" name="lieu" class="form-control" placeholder="Ex: Antananarivo" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                <label class="form-label fw-bold">Date de début</label>
                                <input type="date" name="date_debut" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                <label class="form-label fw-bold">Date de fin</label>
                                <input type="date" name="date_fin" class="form-control" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="nextStep()">Suivant</button>
                            </div>
                        </div>

                        <!-- ===== Étape 2 : Description du poste ===== -->
                        <div id="step2" class="step-section">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Objectif du travail</label>
                                <textarea name="objectif" class="form-control" rows="3" placeholder="Décrivez ce qu'il faut faire..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Qualités du candidat</label>
                                <textarea name="qualites" class="form-control" rows="3" placeholder="Décrivez les qualités attendues..." required></textarea>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Profil</label>
                                <div class="form-group">
                                    <select class="choices form-select">
                                        <option value="square">Square</option>
                                        <option value="rectangle">Rectangle</option>
                                        <option value="rombo">Rombo</option>
                                        <option value="romboid">Romboid</option>
                                        <option value="trapeze">Trapeze</option>
                                        <option value="traible">Triangle</option>
                                        <option value="polygon">Polygon</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Compétence (mots-clés)</label>
                                <div class="form-group">
                                    <select class="choices form-select" multiple="multiple">
                                        <optgroup label="Figures">
                                            <option value="romboid">Romboid</option>
                                            <option value="trapeze" selected>Trapeze</option>
                                            <option value="triangle">Triangle</option>
                                            <option value="polygon">Polygon</option>
                                        </optgroup>
                                        <optgroup label="Colors">
                                            <option value="red">Red</option>
                                            <option value="green">Green</option>
                                            <option value="blue" selected>Blue</option>
                                            <option value="purple">Purple</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-light-secondary" onclick="prevStep()">Précédent</button>
                                <button type="submit" class="btn btn-success">Publier</button>
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

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const elements = document.querySelectorAll('.choices');
    elements.forEach(el => {
        new Choices(el, {
            searchEnabled: true,
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Sélectionnez des compétences'
        });
    });
});
</script>
<script>
  function nextStep() {
    document.getElementById('step1').classList.remove('active');
    document.getElementById('step2').classList.add('active');
  }
  function prevStep() {
    document.getElementById('step2').classList.remove('active');
    document.getElementById('step1').classList.add('active');
  }
</script>
</body>
</html>
