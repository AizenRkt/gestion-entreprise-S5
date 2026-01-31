<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des demandes d'achat</title>
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
        <div class="page-heading">
            <h3>Liste des demandes d'achat</h3>
            <p class="text-subtitle text-muted">Consultez, filtrez et ouvrez le détail des demandes.</p>
        </div>
        <div class="page-content">
            <section class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Récapitulatif</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div><span class="text-muted">Total demandes</span></div>
                                <div class="fs-4 fw-bold"><?= htmlspecialchars((string) ($count ?? 0)) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Demandes</h6>
                            <a class="btn btn-sm btn-primary" href="<?= Flight::base() ?>/avis/achat/saisie">+ Nouvelle demande</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="requestsTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Fournisseur</th>
                                            <th>Remarque</th>
                                            <th>Statut</th>
                                            <th class="text-end">Montant TTC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($requests ?? []) as $request): ?>
                                            <tr>
                                                <td><a href="<?= Flight::base() ?>/avis/achat/demandes/<?= htmlspecialchars($request['id_demande_achat']) ?>" class="fw-bold"><?= htmlspecialchars($request['numero'] ?? ('DMDA' . $request['id_demande_achat'])) ?></a></td>
                                                <td><?= htmlspecialchars($request['date_demande'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($request['supplier_name'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($request['remarque'] ?? '') ?></td>
                                                <td>
                                                    <?php $statut = $request['statut'] ?? 'CREE'; ?>
                                                    <span class="badge bg-<?= $statut === 'VISEE' ? 'success' : 'secondary' ?>"><?= $statut === 'VISEE' ? 'Visé(e)' : 'Créé(e)' ?></span>
                                                </td>
                                                <td class="text-end"><?= htmlspecialchars(number_format((float) ($request['montant_ttc'] ?? 0), 2, '.', ' ')) ?></td>
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
        $('#requestsTable').DataTable();
    });
</script>
</body>
</html>
