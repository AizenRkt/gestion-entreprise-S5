<!DOCTYPE html>
<html lang="fr">

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

    <style>
        .toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 1100; }
    </style>
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
                        <h3>Création de questions</h3>
                        <p class="text-subtitle text-muted">Cherchez et ajoutez des questions dans la base</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            Ajouter une question
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Ajouter Question -->
            <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="addQuestionLabel">Ajouter une question</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addQuestionForm">
                                <div class="mb-3">
                                    <label for="questionText" class="form-label">Énoncé de la question</label>
                                    <input type="text" class="form-control" id="questionText" placeholder="Tapez la question" required>
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
                            <button type="button" class="btn btn-primary" id="addQuestionBtn">Ajouter la question</button>
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
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<!-- Toast container -->
<div id="toastContainer" class="toast-container"></div>

<!-- Scripts -->
<script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>

<script>
function showToast(message, type='success'){
    const toastId = `toast${Date.now()}`;
    const html = `
        <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    $('#toastContainer').append(html);
    setTimeout(() => $(`#${toastId}`).remove(), 4000);
}

$(document).ready(function(){

    function updateAnswers() {
        const num = parseInt($('#numReponses').val()) || 2;
        const container = $('#answersContainer');
        const select = $('#correctAnswer');
        container.empty();
        select.empty();
        for(let i=1;i<=num;i++){
            const letter = String.fromCharCode(64+i); // A,B,C
            container.append(`<input type="text" class="form-control mb-2 answerInput" placeholder="Réponse ${letter}" data-letter="${letter}">`);
            select.append(`<option value="${letter}">${letter}</option>`);
        }
    }
    updateAnswers();
    $('#numReponses').on('input', updateAnswers);

    $.getJSON("<?= Flight::base() ?>/question/all", function(response){
        if(response.success){
            const tbody = $('#table1 tbody');
            tbody.empty();
            response.data.forEach(q => {
                const nbReponses = q.reponse.length;
                const correctes = q.reponse.filter(r => r.est_correcte).map(r => r.reponse).join(", ") || '-';
                tbody.append(`
                    <tr>
                        <td>${q.enonce}</td>
                        <td>${nbReponses}</td>
                        <td>${correctes}</td>
                        <td>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${q.id_question}">Supprimer</button>
                        </td>
                    </tr>
                `);
            });
        } else {
            showToast(response.message ?? "Erreur lors du chargement des questions.", 'danger');
        }
    });

    // Ajouter une question
    $('#addQuestionBtn').on('click', function(){
        const question = $('#questionText').val();
        const correct = $('#correctAnswer').val();
        const answers = [];
        $('.answerInput').each(function(i){
            answers.push({texte: $(this).val(), est_correcte: (String.fromCharCode(65+i) === correct)});
        });
        $.ajax({
            url: "<?= Flight::base() ?>/question/add",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({enonce: question, reponses: answers}),
            success: function(response){
                if(response.success){
                    showToast("Question ajoutée !", 'success');
                    location.reload();
                } else {
                    showToast(response.message ?? "Erreur lors de l'ajout.", 'danger');
                }
            }
        });
    });

    // Supprimer une question (délégué)
    $('#table1').on('click', '.delete-btn', function(){
        const btn = $(this);
        const id = btn.data('id');
        if(!id) return;
        if(!confirm("Voulez-vous vraiment supprimer cette question ?")) return;

        $.ajax({
            url: `<?= Flight::base() ?>/question/${id}`,
            method: 'DELETE',
            contentType: 'application/json',
            success: function(response){
                if(response.success){
                    btn.closest('tr').remove();
                    showToast(response.message ?? "Question supprimée !", 'success');
                } else {
                    showToast(response.message ?? "Erreur lors de la suppression.", 'danger');
                }
            },
            error: function(err){
                console.error(err);
                showToast("Erreur réseau lors de la suppression.", 'danger');
            }
        });
    });
});
</script>

</body>
</html>
