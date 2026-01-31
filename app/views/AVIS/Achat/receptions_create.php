<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle réception</title>
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
            <a href="<?= Flight::get('flight.base_url') ?>/avis/achat/receptions" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h3>Nouvelle réception</h3>
                <p class="text-subtitle text-muted">Lien obligatoire avec un bon de commande.</p>
            </div>
        </div>
        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= Flight::base() ?>/avis/achat/receptions" method="POST">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Numéro de réception</label>
                                        <input type="text" name="reception_numero" class="form-control" placeholder="RC0001" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Date de réception</label>
                                        <input type="datetime-local" name="reception_date" class="form-control" value="<?= htmlspecialchars(date('Y-m-d\TH:i')) ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Dépôt</label>
                                        <select name="depot_id" class="form-select" required>
                                            <option value="">Choisir un dépôt</option>
                                            <?php foreach (($depots ?? []) as $depot): ?>
                                                <option value="<?= htmlspecialchars($depot['id_depot']) ?>"><?= htmlspecialchars($depot['nom'] ?? '') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Fournisseur</label>
                                        <select name="supplier_id" class="form-select" required>
                                            <option value="">Choisir un fournisseur</option>
                                            <?php foreach (($suppliers ?? []) as $supplier): ?>
                                                <option value="<?= htmlspecialchars($supplier['id_fournisseur']) ?>"><?= htmlspecialchars($supplier['nom'] ?? '') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Bon de commande</label>
                                        <select name="order_id" class="form-select" required>
                                            <option value="">Choisir un BC</option>
                                            <?php foreach (($orders ?? []) as $order): ?>
                                                <option value="<?= htmlspecialchars($order['id_bon_commande_fournisseur']) ?>"><?= htmlspecialchars(($order['bc_numero'] ?? '') . ' - ' . ($order['supplier_name'] ?? '')) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Assurez-vous que le BC sélectionné correspond au fournisseur.</small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <a class="btn btn-outline-secondary" href="<?= Flight::get('flight.base_url') ?>/avis/achat/receptions">Annuler</a>
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </form>
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
</body>
</html>
