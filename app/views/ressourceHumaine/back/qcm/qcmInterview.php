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
  <div id="main" class="layout-horizontal">
    <?= Flight::menuFrontOffice() ?>
    <div class="content-wrapper container">
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
              <p class="text-subtitle text-muted">Répondez aux questions ci-dessous.</p>
            </div>
          </div>
        </div>

        <section class="section">
          <div class="row">
              <div id="qcmContainer" class="col-12"></div>
          </div>
          <div class="mt-4 text-end">
              <button id="submitQcmBtn" class="btn btn-success">Valider mes réponses</button>
          </div>
        </section>
      </div>
    </div>
  </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const qcmContainer = document.getElementById("qcmContainer");

    const urlParams = new URLSearchParams(window.location.search);
    const qcmId = urlParams.get('id');
    const idCandidat = urlParams.get('id_candidat');

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
                                <input class="form-check-input" type="radio" name="q${question.id_question}" value="${rep.id_reponse}" data-correct="${rep.est_correcte}" id="q${question.id_question}r${i+1}">
                                <label class="form-check-label" for="q${question.id_question}r${i+1}">${rep.texte}</label>
                            </div>
                        `).join("");

                        card.innerHTML = `
                            <div class="card-body position-relative">
                                <span class="badge bg-primary position-absolute top-0 end-0 m-3">${question.bareme} pts</span>
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

    // Validation du QCM
    document.getElementById("submitQcmBtn").addEventListener("click", () => {
        const questions = qcmContainer.querySelectorAll(".card");
        let scoreTotal = 0;

        questions.forEach(card => {
            const selected = card.querySelector("input[type=radio]:checked");
            if(selected && selected.dataset.correct == "1") {
                const bareme = parseFloat(card.querySelector(".badge").textContent);
                scoreTotal += bareme;
            }
        });

        // Envoi du score au serveur
        fetch('<?= Flight::base() ?>/scoringQcm', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_candidat: idCandidat,
                id_qcm: qcmId,
                score: scoreTotal
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert("Score enregistré : " + scoreTotal + " pts");
                window.location.href = "<?= Flight::base() ?>/";
            } else {
                alert("Erreur lors de l'enregistrement du score.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Erreur réseau.");
        });
    });
});
</script>
</body>
</html>
