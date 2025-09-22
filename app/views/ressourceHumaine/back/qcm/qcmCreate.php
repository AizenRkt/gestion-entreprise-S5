<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un QCM - Mazer</title>
    
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">

    <style>
        .autocomplete-list {
            position: absolute;
            z-index: 1000;
            background: white;
            border: 1px solid #ccc;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
        }
        .autocomplete-item {
            padding: 8px;
            cursor: pointer;
        }
        .autocomplete-item:hover {
            background: #f1f1f1;
        }

        /* Flashcards - Deux par deux */
        .flashcards-wrapper {
            margin-top: 20px;
            min-height: 270px;
        }
        
        .flashcard-container {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .flashcard {
            flex: 1;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
            min-height: 250px;
            position: relative;
        }
        
        .flashcard-empty {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-style: italic;
        }
        
        .flashcard-nav {
            text-align: center;
            margin-top: 15px;
        }
        
        .flashcard-nav.hidden {
            display: none;
        }
        
        @media (max-width: 768px) {
            .flashcard-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
<div id="app">
    <?= Flight::menuBackOffice() ?>
    <div id="main">
        <div class="page-heading">
            <h3>Créateur de QCM</h3>
        </div>

        <div class="page-content">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Nouveau QCM</h4>
                </div>

                <div class="card-body">
                    <div class="col-6 mb-3">
                        <label class="form-label">Titre du QCM</label>
                        <input type="text" class="form-control" placeholder="Ex: Test de logique">
                    </div>
                    
                    <hr>

                    <div class="row">
                        <div class="col-3 mb-3">
                            <label class="form-label">Barème global (points)</label>
                            <input type="number" class="form-control" value="1" min="1">
                        </div>

                        <div class="col-3 mb-3">
                            <label class="form-label">profil</label>
                            <select name="id_profil" class="form-select" id="basicSelect">
                                <?php foreach($profil as $x) {?>
                                    <option value="<?= $x['id_profil'] ?>"><?= $x['nom'] ?></option>
                                <?php } ?>
                            </select>

                        </div>
                    </div>
                    
                    <div class="mb-3 position-relative">
                        <label class="form-label">Rechercher une question existante</label>
                        <input type="text" id="searchQuestion" class="form-control" placeholder="Tapez un mot-clé...">
                        <div id="autocompleteList" class="autocomplete-list d-none"></div>
                        <button class="btn btn-primary mt-2" id="addSelectedQuestion">
                            <i class="bi bi-plus-circle"></i> Ajouter au QCM
                        </button>
                    </div>

                    <hr>

                    <!-- Zone flashcards - Deux par deux -->
                    <div class="flashcards-wrapper">
                        <div id="flashcards-display"></div>
                        
                        <div class="flashcard-nav hidden" id="flashcard-nav">
                            <button class="btn btn-outline-secondary" id="prev-page">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <span class="mx-3" id="page-info">Page 1</span>
                            <button class="btn btn-outline-secondary" id="next-page">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn btn-secondary me-2">Annuler</button>
                        <button class="btn btn-success">Enregistrer le QCM</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    

<script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script>
    let selectedQuestion = null;
    let questions = [];
    let currentPage = 0;
    const questionsPerPage = 2;

    // Autocomplete
    $("#searchQuestion").on("input", function() {
        const query = $(this).val().trim();
        if (query.length < 2) {
            $("#autocompleteList").addClass("d-none").empty();
            return;
        }

        $.getJSON("<?= Flight::base() ?>/question/search?q=" + encodeURIComponent(query), function(response) {
            const list = $("#autocompleteList");
            list.empty();

            if (response.success && response.data.length > 0) {
                response.data.forEach(q => {
                    const item = $(`<div class="autocomplete-item">${q.enonce}</div>`);
                    item.on("click", function() {
                        $("#searchQuestion").val(q.enonce);
                        selectedQuestion = q;
                        list.addClass("d-none").empty();
                    });
                    list.append(item);
                });
                list.removeClass("d-none");
            } else {
                list.addClass("d-none");
            }
        });
    });

    // Ajouter une question
    $("#addSelectedQuestion").on("click", function() {
        if (!selectedQuestion) {
            alert("Veuillez d'abord sélectionner une question !");
            return;
        }

        // On enregistre uniquement id_question + bareme (le reste sert à l’affichage)
        questions.push({
            id_question: selectedQuestion.id_question,
            enonce: selectedQuestion.enonce,
            reponses: selectedQuestion.reponses || [],
            bareme: 1
        });

        $("#searchQuestion").val("");
        selectedQuestion = null;

        updateFlashcardsDisplay();
        updateNavigation();
    });

    function createFlashcard(question, index) {
        return `
            <div class="flashcard">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Question ${index + 1}</h6>
                    <button class="btn btn-sm btn-danger remove-question" data-index="${index}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="mb-3">
                    <strong>${question.enonce}</strong>
                </div>
                <ul class="mb-3">
                    ${question.reponses.map(r => 
                        `<li class="mb-1">${r.reponse} ${r.est_correcte ? "<span class='badge bg-success ms-1'>Correct</span>" : ""}</li>`
                    ).join("")}
                </ul>
                <div class="mt-auto">
                    <label class="form-label">Barème (points)</label>
                    <input type="number" class="form-control bareme-question" value="${question.bareme}" min="1" data-index="${index}">
                </div>
            </div>
        `;
    }

    function updateFlashcardsDisplay() {
        const display = document.getElementById("flashcards-display");
        display.innerHTML = "";

        if (questions.length === 0) {
            display.innerHTML = `
                <div class="flashcard-container">
                    <div class="flashcard flashcard-empty">
                        <span>Aucune question ajoutée</span>
                    </div>
                    <div class="flashcard flashcard-empty">
                        <span>Ajoutez des questions pour commencer</span>
                    </div>
                </div>
            `;
            return;
        }

        const startIndex = currentPage * questionsPerPage;
        const endIndex = Math.min(startIndex + questionsPerPage, questions.length);
        
        const container = document.createElement("div");
        container.className = "flashcard-container";

        for (let i = startIndex; i < endIndex; i++) {
            container.innerHTML += createFlashcard(questions[i], i);
        }

        if ((endIndex - startIndex) === 1) {
            container.innerHTML += '<div class="flashcard flashcard-empty"><span>Emplacement libre</span></div>';
        }

        display.appendChild(container);

        // Suppression
        container.querySelectorAll(".remove-question").forEach(btn => {
            btn.addEventListener("click", function() {
                const index = parseInt(this.dataset.index);
                questions.splice(index, 1);
                const maxPage = Math.max(0, Math.ceil(questions.length / questionsPerPage) - 1);
                if (currentPage > maxPage) {
                    currentPage = maxPage;
                }
                updateFlashcardsDisplay();
                updateNavigation();
            });
        });

        // Mise à jour barème
        container.querySelectorAll(".bareme-question").forEach(input => {
            input.addEventListener("change", function() {
                const index = parseInt(this.dataset.index);
                questions[index].bareme = parseInt(this.value) || 1;
            });
        });
    }

    function updateNavigation() {
        const nav = document.getElementById("flashcard-nav");
        const prevBtn = document.getElementById("prev-page");
        const nextBtn = document.getElementById("next-page");
        const pageInfo = document.getElementById("page-info");

        if (questions.length === 0) {
            nav.classList.add("hidden");
            return;
        }

        nav.classList.remove("hidden");

        const totalPages = Math.ceil(questions.length / questionsPerPage);
        
        prevBtn.disabled = currentPage === 0;
        nextBtn.disabled = currentPage >= totalPages - 1;
        
        pageInfo.textContent = `Page ${currentPage + 1} sur ${totalPages}`;
    }

    // Navigation
    $("#prev-page").on("click", function() {
        if (currentPage > 0) {
            currentPage--;
            updateFlashcardsDisplay();
            updateNavigation();
        }
    });

    $("#next-page").on("click", function() {
        const totalPages = Math.ceil(questions.length / questionsPerPage);
        if (currentPage < totalPages - 1) {
            currentPage++;
            updateFlashcardsDisplay();
            updateNavigation();
        }
    });

    // Initialiser
    updateFlashcardsDisplay();
    updateNavigation();
</script>

<script>
    $(".btn-success").on("click", function() {
        const titre = $("input[placeholder^='Ex: Test']").val().trim();
        const id_profil = $("#basicSelect").val();
        const note_max = $("input[type='number']").first().val();

        const questionsData = questions.map(q => ({
            id_question: q.id_question,
            bareme: q.bareme
        }));

        if(!titre || questionsData.length === 0){
            alert("Veuillez remplir le titre et ajouter au moins une question");
            return;
        }

        $.ajax({
            url: "<?= Flight::base() ?>/qcm/create",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                id_profil: id_profil,
                titre: titre,
                note_max: note_max,
                questions: questionsData
            }),
            success: function(res){
                if(res.success){
                    alert("QCM créé avec succès !");
                    window.location.href = "<?= Flight::base() ?>/qcm/all"; // redirection vers liste QCM
                } else {
                    alert("Erreur : " + res.message);
                }
            },
            error: function(){
                alert("Erreur lors de l'envoi du QCM");
            }
        });
    });
</script>

</body>
</html>
