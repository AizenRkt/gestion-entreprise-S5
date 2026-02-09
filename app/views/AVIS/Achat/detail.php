<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de demande d'achat</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
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
            <a href="<?= Flight::base() ?>/avis/achat/demandes" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h3>Fiche de demande d'achat</h3>
                <p class="text-subtitle text-muted">Suivi et validation de la demande.</p>
            </div>
        </div>
        <div class="page-content">
            <section class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <p class="text-muted mb-0">ID</p>
                                    <h6><?= htmlspecialchars($request['numero'] ?? '') ?></h6>
                                </div>
                                <div class="col-md-3">
                                    <p class="text-muted mb-0">Date de la demande</p>
                                    <h6><?= htmlspecialchars($request['date_demande'] ?? '') ?></h6>
                                </div>
                                <div class="col-md-3">
                                    <p class="text-muted mb-0">Fournisseur</p>
                                    <h6><?= htmlspecialchars($request['supplier_name'] ?? '') ?></h6>
                                </div>
                                <div class="col-md-3">
                                    <p class="text-muted mb-0">Remarque</p>
                                    <h6><?= htmlspecialchars($request['remarque'] ?? '-') ?></h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-3">
                                <div>
                                    <?php $statut = $request['statut'] ?? 'CREE'; ?>
                                    <span class="badge bg-<?= $statut === 'VISEE' ? 'success' : 'secondary' ?>"><?= $statut === 'VISEE' ? 'Visé(e)' : 'Créé(e)' ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= Flight::base() ?>/avis/achat/saisie" class="btn btn-outline-secondary btn-sm">Modifier</a>
                                    <?php 
                                    $userRole = $_SESSION['user']['role'] ?? '';
                                    if (($request['statut'] ?? '') !== 'VISEE' && $userRole === 'ROLE_RESPONSABLE_ACHATS'): 
                                    ?>
                                        <form method="POST" action="<?= Flight::base() ?>/avis/achat/demandes/<?= htmlspecialchars($request['id_demande_achat']) ?>/valider" class="d-inline">
                                            <button type="submit" class="btn btn-primary btn-sm">Valider</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Détails</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Id Produit</th>
                                            <th>Désignation</th>
                                            <th class="text-end">Quantité</th>
                                            <th class="text-end">Prix unitaire</th>
                                            <th class="text-end">TVA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (($request['lines'] ?? []) as $line): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($line['code_article'] ?: ($line['article_code_db'] ?? '')) ?></td>
                                                <td><?= htmlspecialchars($line['designation'] ?? '') ?></td>
                                                <td class="text-end"><?= htmlspecialchars($line['quantite'] ?? '') ?></td>
                                                <td class="text-end"><?= htmlspecialchars(number_format((float) ($line['prix_unitaire'] ?? 0), 2, '.', ' ')) ?></td>
                                                <td class="text-end"><?= htmlspecialchars(number_format((float) ($line['tva'] ?? 0), 2, '.', ' ')) ?></td>
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
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

</div>
</body>
</html>
