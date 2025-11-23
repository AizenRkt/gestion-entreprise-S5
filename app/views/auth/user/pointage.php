<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pointage du compte - BackOffice</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
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
                                Effectuez vos pointages et consultez votre historique de présence.
                            </p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/backOffice">Accueil</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Pointage</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Section des actions de pointage -->
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Actions de Pointage</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <button id="checkinBtn" class="btn icon icon-left btn-success" <?= $hasCheckedIn ? 'disabled' : '' ?> >
                                        <i data-feather="log-in"></i> Check-in
                                    </button>
                                    <button id="checkoutBtn" class="btn icon icon-left btn-warning" <?= !$hasCheckedIn || $hasCheckedOut ? 'disabled' : '' ?> >
                                        <i data-feather="log-out"></i> Check-out
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section de l'historique -->
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                Mon Historique de Pointage
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped" id="pointageTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Heure d'arrivée</th>
                                        <th>Heure de départ</th>
                                        <th>Durée Totale</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Les données seront chargées ici par JavaScript -->
                                </tbody>
                            </table>
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
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkinBtn = document.getElementById('checkinBtn');
            const checkoutBtn = document.getElementById('checkoutBtn');
            let dataTable;

            function showToast(message, type = 'success') {
                Toastify({
                    text: message,
                    duration: 4000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: type === 'success' ? "#4fbe87" : "#dc3545",
                }).showToast();
            }

            function formatTime(datetimeString) {
                if (!datetimeString) return 'N/A';
                const date = new Date(datetimeString);
                return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }

            async function loadDatatable() {
                try {
                    const response = await fetch(`<?= Flight::base() ?>/backOffice/user/pointage/historique`);
                    const result = await response.json();

                    if (!result.success) {
                        showToast(result.message, 'error');
                        return;
                    }

                    if (dataTable) {
                        dataTable.destroy();
                    }

                    const table = document.getElementById('pointageTable');
                    dataTable = new simpleDatatables.DataTable(table);
                    
                    const rows = result.data.map(item => {
                        let statusBadge;
                        if (item.retard_min > 0) {
                            statusBadge = `<span class="badge bg-danger">${item.retard_min} min de retard</span>`;
                        } else {
                            statusBadge = '<span class="badge bg-success">À l\'heure</span>';
                        }

                        return [
                            new Date(item.date_pointage).toLocaleDateString('fr-FR'),
                            formatTime(item.datetime_checkin),
                            formatTime(item.datetime_checkout),
                            item.duree_work || 'En cours',
                            statusBadge
                        ];
                    });

                    dataTable.insert({ data: rows });
                    
                } catch (error) {
                    showToast('Erreur de chargement de l\'historique.', 'error');
                    console.error('Fetch error:', error);
                }
            }

            async function handlePointage(action) {
                try {
                    const response = await fetch(`<?= Flight::base() ?>/backOffice/user/${action}`);
                    const data = await response.json();

                    if (data.success) {
                        showToast(data.message, 'success');
                        if (action === 'checkin') {
                            checkinBtn.disabled = true;
                            checkoutBtn.disabled = false;
                        } else if (action === 'checkout') {
                            checkoutBtn.disabled = true;
                        }
                        loadDatatable(); // Recharger les données
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (error) {
                    showToast('Une erreur est survenue.', 'error');
                }
            }

            if (checkinBtn) {
                checkinBtn.addEventListener('click', () => handlePointage('checkin'));
            }
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', () => handlePointage('checkout'));
            }

            // Charger les données au démarrage
            loadDatatable();
        });
    </script>
</body>
</html>
datatables.js"></script>
</html>
</html>
