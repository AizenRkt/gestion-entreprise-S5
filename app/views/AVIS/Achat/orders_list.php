<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des bons de commande</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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
        <div class="page-heading d-flex align-items-center gap-2">
            <a href="<?= Flight::base() ?>/avis/achat/dashboard" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h3>Liste des bons de commande</h3>
                <p class="text-subtitle text-muted">Suivi des BC fournisseurs.</p>
            </div>
        </div>
        <div class="page-content">
            <section class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Récapitulatif</h6>
                            <a class="btn btn-sm btn-primary" href="<?= Flight::base() ?>/avis/achat/bc/nouveau">+ Nouveau BC</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="ordersTable">
                                    <thead>
                                        <tr>
                                            <th>Numéro</th>
                                            <th>DA</th>
                                            <th>Date</th>
                                            <th>Fournisseur</th>
                                            <th>Dépôt</th>
                                            <th class="text-end">Montant TTC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($orders ?? []) as $order): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($order['bc_numero'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($order['request_number'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($order['bc_date'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($order['supplier_name'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($order['depot_name'] ?? '') ?></td>
                                                <td class="text-end"><?= htmlspecialchars(number_format((float) ($order['montant_ttc'] ?? 0), 2, '.', ' ')) ?></td>
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
        $('#ordersTable').DataTable();
    });
</script>
</body>
</html>
