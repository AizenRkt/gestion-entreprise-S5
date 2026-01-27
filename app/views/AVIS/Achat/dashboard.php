<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achats - Tableau de bord</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
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
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h3>Module Achats</h3>
                    <p class="text-subtitle text-muted">Suivi des fournisseurs, commandes, réceptions, factures et paiements</p>
                </div>
            </div>
        </div>
        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Fournisseurs</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="suppliersTable">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Adresse</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($suppliers ?? []) as $supplier): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($supplier['nom'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($supplier['email'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($supplier['telephone'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($supplier['adresse'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Bons de commande</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="ordersTable">
                                    <thead>
                                        <tr>
                                            <th>Numéro</th>
                                            <th>Date</th>
                                            <th>Fournisseur</th>
                                            <th>Dépôt</th>
                                            <th>Montant TTC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($orders ?? []) as $order): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($order['bc_numero'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($order['bc_date'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($order['supplier_name'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($order['depot_name'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($order['montant_ttc'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Réceptions</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="receptionsTable">
                                    <thead>
                                        <tr>
                                            <th>Numéro</th>
                                            <th>Date</th>
                                            <th>BC lié</th>
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

            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Factures</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="invoicesTable">
                                    <thead>
                                        <tr>
                                            <th>Numéro</th>
                                            <th>Date</th>
                                            <th>Réception</th>
                                            <th>Fournisseur</th>
                                            <th>Montant TTC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($invoices ?? []) as $invoice): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($invoice['facture_numero'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($invoice['facture_date'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($invoice['reception_numero'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($invoice['supplier_name'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($invoice['montant_ttc'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Paiements</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="paymentsTable">
                                    <thead>
                                        <tr>
                                            <th>Numéro</th>
                                            <th>Date</th>
                                            <th>Facture</th>
                                            <th>Montant total</th>
                                            <th>Référence</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($payments ?? []) as $payment): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($payment['paiement_numero'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($payment['paiement_date'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($payment['facture_numero'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($payment['montant_total'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($payment['reference_paiement'] ?? '') ?></td>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(function() {
        $('#suppliersTable').DataTable();
        $('#ordersTable').DataTable();
        $('#receptionsTable').DataTable();
        $('#invoicesTable').DataTable();
        $('#paymentsTable').DataTable();
    });
</script>
</body>
</html>
