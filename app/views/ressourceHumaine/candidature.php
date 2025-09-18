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
        .step-section { display: none; }
        .step-section.active { display: block; }
    </style>
</head>

<body>
<div id="app">
    <div id="main" class="layout-horizontal">
        <?= Flight::menuFrontOffice() ?>
        <div class="content-wrapper container">
            
            <div class="page-heading">
                <h3>Déposez votre candidature</h3>
                <p class="text-muted">Remplissez le formulaire étape par étape</p>
            </div>

            <section class="section">
                <div class="row">
                    <div class="col-12">
                        <div class="card p-4">
                            <form action="<?= Flight::base() ?>/annonce" method="get" id="cvForm" enctype="multipart/form-data">
                                <!-- ===== Étape 1 : Informations personnelles ===== -->
                                <div id="step1" class="step-section active">

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Prénom</label>
                                            <input type="text" name="prenom" class="form-control" placeholder="Ex: Jean" >
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Nom</label>
                                            <input type="text" name="nom" class="form-control" placeholder="Ex: Dupont" >
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Email</label>
                                            <input type="email" name="email" class="form-control" placeholder="Ex: jean.dupont@email.com" >
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Téléphone</label>
                                            <input type="tel" name="telephone" class="form-control" placeholder="Ex: +261 34 00 000 00" >
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Date de naissance</label>
                                            <input type="date" name="date_naissance" class="form-control" >
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Ville</label>
                                            <input type="text" name="ville" class="form-control" placeholder="Ex: Antananarivo" >
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Genre</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="genre" id="homme" value="Homme" checked>
                                                <label class="form-check-label" for="homme">Homme</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="genre" id="femme" value="Femme">
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

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Profil</label>
                                        <select class="choices form-select" name="profil">
                                            <option value="">Sélectionnez votre profil</option>
                                            <option value="developpeur">Développeur</option>
                                            <option value="designer">Designer</option>
                                            <option value="chef_projet">Chef de projet</option>
                                            <option value="marketing">Marketing</option>
                                            <option value="autre">Autre</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Compétences (mots-clés)</label>
                                        <select class="choices form-select" name="competences[]" multiple="multiple">
                                            <optgroup label="Techniques">
                                                <option value="php">PHP</option>
                                                <option value="java">Java</option>
                                                <option value="javascript">JavaScript</option>
                                                <option value="python">Python</option>
                                            </optgroup>
                                            <optgroup label="Soft skills">
                                                <option value="communication">Communication</option>
                                                <option value="gestion">Gestion de projet</option>
                                                <option value="teamwork">Travail en équipe</option>
                                                <option value="creativite">Créativité</option>
                                            </optgroup>
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
                                        <input type="file" name="photo" accept="image/*" class="form-control" >
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-light-secondary" onclick="prevStep(3, 2)">Précédent</button>
                                        <button type="submit" class="btn btn-success">Soumettre ma candidature</button>
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
    document.addEventListener("DOMContentLoaded", function () {
        const elements = document.querySelectorAll('.choices');
        elements.forEach(el => {
            new Choices(el, {
                searchEnabled: true,
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Sélectionnez...'
            });
        });
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
