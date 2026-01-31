<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réceptions fournisseurs</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
</head>
<body>
<div id="app">
    <?php Flight::render('AVIS/Achat/menu'); ?>
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
        <div class="page-heading d-flex align-items-center gap-2">
            <a href="<?= Flight::base() ?>/avis/achat/dashboard" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h3>Réceptions fournisseurs</h3>
                <p class="text-subtitle text-muted">Enregistrements des livraisons liées aux BC.</p>
            </div>
        </div>
        <div class="page-content">
            <section class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Liste</h6>
                            <a class="btn btn-sm btn-primary" href="<?= Flight::base() ?>/avis/achat/receptions/nouveau">+ Nouvelle réception</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="receptionsTable">
                                    <thead>
                                        <tr>
                                            <th>Numéro</th>
                                            <th>Date</th>
                                            <th>BC</th>
                                            <th>Fournisseur</th>
                                            <th>Dépôt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($receptions ?? []) as $reception): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($reception['reception_numero'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($reception['reception_date'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($reception['bc_numero'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($reception['supplier_name'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($reception['depot_name'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(function() {
        $('#receptionsTable').DataTable();
    });
</script>
</body>
</html>
