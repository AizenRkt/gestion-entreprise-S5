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
          <form action="#" method="post">
            
            <!-- Question 1 -->
            <div class="card mb-3 question-step" id="question1">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">1. Quel est le langage utilisé pour structurer une page web ?</h5>
                <span class="badge bg-primary">5 pts</span>
              </div>
              <div class="card-body">
                <div class="form-check">
                  <input class="form-check-input" type="radio" id="q1r1" name="q1" value="html">
                  <label class="form-check-label" for="q1r1">HTML</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" id="q1r2" name="q1" value="css">
                  <label class="form-check-label" for="q1r2">CSS</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" id="q1r3" name="q1" value="php">
                  <label class="form-check-label" for="q1r3">PHP</label>
                </div>
              </div>
              <div class="card-footer text-end">
                <button type="button" class="btn btn-primary next-btn">Question suivante</button>
              </div>
            </div>

            <!-- Question 2 -->
            <div class="card mb-3 question-step d-none" id="question2">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">2. Quel langage est orienté objet ?</h5>
                <span class="badge bg-primary">8 pts</span>
              </div>
              <div class="card-body">
                <div class="form-check">
                  <input class="form-check-input" type="radio" id="q2r1" name="q2" value="java">
                  <label class="form-check-label" for="q2r1">Java</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" id="q2r2" name="q2" value="python">
                  <label class="form-check-label" for="q2r2">Python</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" id="q2r3" name="q2" value="html">
                  <label class="form-check-label" for="q2r3">HTML</label>
                </div>
              </div>
              <div class="card-footer text-end">
                <button type="submit" class="btn btn-success">Valider mes réponses</button>
              </div>
            </div>

          </form>
        </section>
      </div>
    </div>
  </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script>
    document.querySelectorAll(".next-btn").forEach(btn => {
        btn.addEventListener("click", function() {
            const current = this.closest(".question-step");
            const next = current.nextElementSibling;
            if (next && next.classList.contains("question-step")) {
                current.classList.add("d-none");
                next.classList.remove("d-none");
            }
        });
    });
</script>

</body>
</html>
