<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inventaires' ?></title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
</head>

<body>
<div id="app">
    <!-- Sidebar -->
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
                        <h3>Gestion des Inventaires</h3>
                        <p class="text-subtitle text-muted">Suivez et validez vos sessions d'inventaire par dépôt</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                                <li class="breadcrumb-item"><a href="#">Stock</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Inventaires</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Sessions d'inventaire</h4>
                        <a href="<?= Flight::base() ?>/stock/inventaires/saisie" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Nouvel Inventaire
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="inventaireTable">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Date</th>
                                        <th>Dépôt</th>
                                        <th>Commentaire</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="inventaireBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
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
const base = '<?= Flight::base() ?>';

async function fetchJSON(url) {
    const res = await fetch(url);
    return res.json();
}

function getStatutBadge(statut) {
    switch (statut) {
        case 'BROUILLON': return '<span class="badge bg-warning">Brouillon</span>';
        case 'VALIDE':   return '<span class="badge bg-success">Validé</span>';
        case 'ANNULE':   return '<span class="badge bg-danger">Annulé</span>';
        default:         return '<span class="badge bg-secondary">' + statut + '</span>';
    }
}

async function loadInventaires() {
    const res = await fetchJSON(base + '/api/stock/inventaires');
    const tbody = document.getElementById('inventaireBody');
    tbody.innerHTML = '';

    if (res.success) {
        res.data.forEach(i => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${i.inventaire_numero}</strong></td>
                <td>${i.date_inventaire}</td>
                <td>${i.depot_nom}</td>
                <td>${i.commentaire || '-'}</td>
                <td>${getStatutBadge(i.statut)}</td>
                <td>
                    <a href="${base}/stock/inventaires/saisie/${i.id_inventaire}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> ${i.statut === 'BROUILLON' ? 'Modifier' : 'Détails'}
                    </a>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
}

init = () => loadInventaires();
init();
</script>
</body>
</html>
