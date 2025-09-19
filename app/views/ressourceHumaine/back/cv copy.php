<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCM Créator - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/styles/choices.css">

    <!-- datatables -->
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
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
                            <h3>Filtrer des candidats</h3>
                            <p class="text-subtitle text-muted">Cherchez et filtrer des cv dans la base</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first text-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                                Trouver une question
                            </button>
                        </div>
                    </div>
                </div>
                <section class="basic-choices">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Choices</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2">
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
                                            
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label for="ageMin">Âge minimum</label>
                                                    <input type="number" class="form-control" id="ageMin" name="age_min" placeholder="Âge min (ex: 18)" min="18" max="65" required>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label for="ageMax">Âge maximum</label>
                                                    <input type="number" class="form-control" id="ageMax" name="age_max" placeholder="Âge max (ex: 65)" min="18" max="65" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <h6>Diplomes</h6>
                                                <div class="form-group">
                                                    <select class="choices form-select" name="diplome">
                                                        <?php if (isset($diplomes) && is_array($diplomes)): ?>
                                                            <?php foreach ($diplomes as $diplome): ?>
                                                                <option value="<?= htmlspecialchars($diplome['id_diplome']) ?>">
                                                                    <?= htmlspecialchars($diplome['nom']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <option disabled>Aucun diplôme disponible</option>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <h6>Competences</h6>
                                                <div class="form-group">
                                                    <select class="choices form-select" name="competences[]" multiple="multiple">
                                                        <?php if (isset($competences) && is_array($competences)): ?>
                                                            <?php foreach ($competences as $competence): ?>
                                                                <option value="<?= htmlspecialchars($competence['id_competence']) ?>">
                                                                    <?= htmlspecialchars($competence['nom']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <option disabled>Aucune compétence disponible</option>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-4">
                                                <h6>Ville</h6>
                                                <fieldset class="form-group">
                                                    <select class="form-select" id="basicSelect" name="ville">
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
                                                </fieldset>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Date de candidature , avant:</label>
                                                <input type="date" name="date_naissance" class="form-control" placeholder="Date de naissance" required>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>Profil</h6>
                                                <div class="form-group">
                                                    <select class="choices form-select multiple-remove" name="profils[]" multiple="multiple">
                                                        <?php if (isset($profils) && is_array($profils)): ?>
                                                            <?php foreach ($profils as $profil): ?>
                                                                <option value="<?= htmlspecialchars($profil['id_profil']) ?>">
                                                                    <?= htmlspecialchars($profil['nom']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <option disabled>Aucun profil disponible</option>
                                                        <?php endif; ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Modal Ajouter Question -->
                <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="addQuestionLabel">Ajouter une question</h4>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="questionText" class="form-label">Énoncé de la question</label>
                                        <input type="text" class="form-control" id="questionText" placeholder="Tapez la question">
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="numReponses" class="form-label">Nombre de réponses</label>
                                            <input type="number" class="form-control" id="numReponses" value="3" min="2">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="correctAnswer" class="form-label">Réponse correcte</label>
                                            <select class="form-select" id="correctAnswer"></select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Réponses possibles</label>
                                        <div id="answersContainer"></div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="button" class="btn btn-primary" id="addQuestionBtn" data-bs-dismiss="modal">Ajouter la question</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des questions -->
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Candidats trouvées selon filtre</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Prenom</th>
                                            <th>Age</th>
                                            <th>Profil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <!-- JQuery doit être chargé avant ton script -->
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/simple-datatables.js"></script>

    <script>
        $(document).ready(function() {

            // Charger les questions depuis l’API
            $.getJSON("<?= Flight::base() ?>/question/all", function(response) {
                if (response.success) {
                    const questions = response.data;
                    const tbody = $('#table1 tbody');
                    tbody.empty(); // vider le tableau

                    questions.forEach(q => {
                        const nbReponses = q.reponse.length;

                        // trouver la/les réponses correctes
                        const correctes = q.reponse
                            .filter(r => r.est_correcte)
                            .map(r => r.reponse)
                            .join(", ");

                        tbody.append(`
                    <tr>
                        <td>${q.enonce}</td>
                        <td>${nbReponses}</td>
                        <td>${correctes || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${q.id_question}">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                `);
                    });
                } else {
                    alert("Erreur : " + response.message);
                }
            });

            // Fonction ajout via modal (garde ton code)
            function updateAnswers() {
                const num = parseInt($('#numReponses').val()) || 2;
                const container = $('#answersContainer');
                const select = $('#correctAnswer');

                container.empty();
                select.empty();

                for (let i = 1; i <= num; i++) {
                    const letter = String.fromCharCode(64 + i); // A, B, C...
                    container.append(`<input type="text" class="form-control mb-2 answerInput" placeholder="Réponse ${letter}" data-letter="${letter}">`);
                    select.append(`<option value="${letter}">${letter}</option>`);
                }
            }

            updateAnswers();
            $('#numReponses').on('input', updateAnswers);

            $('#addQuestionBtn').on('click', function() {
                const question = $('#questionText').val();
                const num = $('#numReponses').val();
                const correct = $('#correctAnswer').val();

                const answers = [];
                $('.answerInput').each(function() {
                    answers.push($(this).val());
                });

                $('#table1 tbody').append(`
            <tr>
                <td>${question}</td>
                <td>${num}</td>
                <td>${correct}) ${answers[correct.charCodeAt(0)-65]}</td>
                <td><span class="badge bg-success">Active</span></td>
            </tr>
        `);

                // Réinitialisation modal
                $('#questionText').val('');
                $('#numReponses').val('3');
                updateAnswers();
            });

        });
    </script>

    <script src="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/form-element-select.js"></script>


</body>

</html>