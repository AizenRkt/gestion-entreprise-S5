<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCM - Mazer</title>
    
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
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
                        <h3>QCM : Examen Test</h3>
                        <p class="text-subtitle text-muted">Voici un aperçu de ce QCM.</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">QCM</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="row">
                    <!-- Barre de recherche -->
                    <div class="d-flex mb-4">
                        <input type="text" id="searchInput" class="form-control form-control-sm w-25 me-2" placeholder="Rechercher une question">
                        <button id="searchBtn" class="btn btn-primary btn-sm">Chercher</button>
                    </div>

                    <!-- Conteneur QCM dynamique -->
                    <div id="qcmContainer"></div>
                </div>
            </section>
        </div>
    </div>
</div>    
</body>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const qcmContainer = document.getElementById("qcmContainer");

    const urlParams = new URLSearchParams(window.location.search);
    const qcmId = urlParams.get('id');

    function loadQcm(id) {
        fetch(`<?= Flight::base() ?>/qcm/${id}`)
            .then(res => res.json())
            .then(response => {
                if(response.success) {
                    qcmContainer.innerHTML = "";
                    response.data.forEach((question, index) => {
                        const card = document.createElement("div");
                        card.className = "card mb-4 shadow-sm";

                        let reponsesHTML = question.reponses.map((rep, i) => `
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="q${question.id_question}" id="q${question.id_question}r${i+1}">
                                <label class="form-check-label" for="q${question.id_question}r${i+1}">${rep.texte}</label>
                            </div>
                        `).join("");

                        card.innerHTML = `
                            <div class="card-body position-relative">
                                <span class="badge bg-primary position-absolute top-0 end-0 m-3">8 pts</span>
                                <h5 class="card-title">Question ${index + 1} : ${question.enonce}</h5>
                                ${reponsesHTML}
                            </div>
                        `;
                        qcmContainer.appendChild(card);
                    });
                } else {
                    qcmContainer.innerHTML = `<p class="text-muted">QCM introuvable.</p>`;
                }
            })
            .catch(err => {
                console.error(err);
                qcmContainer.innerHTML = `<p class="text-danger">Erreur lors du chargement du QCM.</p>`;
            });
    }

    loadQcm(qcmId);

    // Recherche simple
    document.getElementById("searchBtn").addEventListener("click", () => {
        const term = document.getElementById("searchInput").value.toLowerCase();
        Array.from(qcmContainer.children).forEach(card => {
            const title = card.querySelector(".card-title").textContent.toLowerCase();
            card.style.display = title.includes(term) ? "block" : "none";
        });
    });
});
</script>
</html>
