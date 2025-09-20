<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCM Créator - Mazer</title>
    
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    
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
                        <h3>Création de QCM</h3>
                        <p class="text-subtitle text-muted">Cherchez et ajoutez des questions à votre QCM.</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            Ajouter une question
                        </button>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Rechercher une question...">
                        <button class="btn btn-outline-primary" type="button">Chercher</button>
                    </div>
                </div>
            </div>

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
                                        <label for="questionPoints" class="form-label">Barème (points)</label>
                                        <input type="number" class="form-control" id="questionPoints" value="5">
                                    </div>
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
                        <h5 class="card-title">Questions ajoutées au QCM</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="table1">
                                <thead>
                                    <tr>
                                        <th>Énoncé</th>
                                        <th>Nombre de réponses</th>
                                        <th>Réponse correcte</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Quel est la capitale de Madagascar ?</td>
                                        <td>3</td>
                                        <td>A) Antananarivo</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                    </tr>
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
$(document).ready(function(){

    function updateAnswers() {
        const num = parseInt($('#numReponses').val()) || 2;
        const container = $('#answersContainer');
        const select = $('#correctAnswer');

        container.empty();
        select.empty();

        for(let i=1; i<=num; i++){
            const letter = String.fromCharCode(64+i); // A, B, C...
            container.append(`<input type="text" class="form-control mb-2 answerInput" placeholder="Réponse ${letter}" data-letter="${letter}">`);
            select.append(`<option value="${letter}">${letter}</option>`);
        }
    }

    updateAnswers();
    $('#numReponses').on('input', updateAnswers);

    $('#addQuestionBtn').on('click', function(){
        const question = $('#questionText').val();
        const num = $('#numReponses').val();
        const correct = $('#correctAnswer').val();

        const answers = [];
        $('.answerInput').each(function(){ answers.push($(this).val()); });

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
        $('#questionPoints').val('5');
        $('#numReponses').val('3');
        updateAnswers();
    });

});
</script>

</body>
</html>
