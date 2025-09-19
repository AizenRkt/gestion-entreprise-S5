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
                        <h3>Planning des entretien</h3>
                        <p class="text-subtitle text-muted">Voici les entretien à venir</p>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="row">

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
