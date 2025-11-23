<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pointage du compte - BackOffice</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
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
                            <h3>Pointage du compte</h3>
                            <p class="text-subtitle text-muted">
                                Consultez vos informations de présence et effectuez vos pointages.
                            </p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/backOffice">Accueil</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Paramètres</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="row">
                        <div class="col-12 col-lg-8">
                            <!-- SECTION : Pointage -->
                            <div class="card">
                                <div class="card-header">
                                    <h5>Pointage</h5>
                                </div>
                                <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <button id="checkinBtn" class="btn icon icon-left btn-success" <?= $hasCheckedIn ? 'disabled' : '' ?>>
                                                    <i data-feather="check-circle"></i> Check-in
                                                </button>

                                                <button id="checkoutBtn" class="btn icon icon-left btn-warning" <?= $hasCheckedOut ? 'disabled' : '' ?>>
                                                    <i data-feather="alert-triangle"></i> Check-out
                                                </button>
                                                <div id="pointageStatus" class="mt-3"></div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <!-- SECTION : Retard / Justification -->
                            <div class="card mt-4 border-danger">
                                <div class="card-header">
                                    <h5 class="text-danger">
                                        <i class="bi bi-exclamation-triangle"></i> Informations concernant votre présence
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p>
                                        Si vous avez effectué un <strong>retard</strong> ou un <strong>pointage anormal</strong>,
                                        vous pouvez envoyer une justification à l’administration.
                                    </p>
                                    <button class="btn btn-outline-danger" id="disableAccount">
                                        <i class="bi bi-send"></i> Envoyer une justification de retard
                                    </button>
                                </div>
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
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkinBtn = document.getElementById('checkinBtn');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const pointageStatus = document.getElementById('pointageStatus');

            function showToast(message, type) {
                Toastify({
                    text: message,
                    duration: 3000,
                    close: true,
                    gravity: "top", // `top` or `bottom`
                    position: "right", // `left`, `center` or `right`
                    backgroundColor: type === 'success' ? "#4fbe87" : "#dc3545",
                }).showToast();
            }

            async function handlePointage(action) {
                const response = await fetch(`<?= Flight::base() ?>/backOffice/user/${action}`);
                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    if (action === 'checkin') {
                        checkinBtn.disabled = true;
                        checkoutBtn.disabled = false;
                    } else if (action === 'checkout') {
                        checkoutBtn.disabled = true;
                        checkinBtn.disabled = true; // Empêcher un nouveau check-in après le check-out pour le même jour
                    }
                } else {
                    showToast(data.message, 'error');
                }
            }

            if (checkinBtn) {
                checkinBtn.addEventListener('click', () => handlePointage('checkin'));
            }

            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', () => handlePointage('checkout'));
            }

            // Initial state based on PHP variables
            <?php if ($hasCheckedIn): ?>
                checkinBtn.disabled = true;
            <?php endif; ?>
            <?php if ($hasCheckedOut): ?>
                checkoutBtn.disabled = true;
                checkinBtn.disabled = true; // Double désactivation si check-out fait
            <?php endif; ?>
        });
    </script>
</body>

</html>