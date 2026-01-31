<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie bon de commande</title>
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
            <a href="<?= Flight::base() ?>/avis/achat/bc" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h3>Nouveau bon de commande</h3>
                <p class="text-subtitle text-muted">Création d'un BC fournisseur.</p>
            </div>
        </div>
        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= Flight::base() ?>/avis/achat/bc" method="POST" id="orderForm">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Demande d'achat</label>
                                        <select name="request_id" class="form-select" required>
                                            <option value="">Choisir une DA validée</option>
                                            <?php foreach (($requests ?? []) as $req): ?>
                                                <option value="<?= htmlspecialchars($req['id_demande_achat']) ?>">
                                                    <?= htmlspecialchars($req['numero'] ?? '') ?> — <?= htmlspecialchars($req['supplier_name'] ?? '') ?> (<?= htmlspecialchars($req['date_demande'] ?? '') ?>, TTC <?= htmlspecialchars(number_format((float) ($req['montant_ttc'] ?? 0), 2, '.', ' ')) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Numéro BC</label>
                                        <input type="text" name="bc_numero" class="form-control" placeholder="BC0001" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date</label>
                                        <input type="datetime-local" name="bc_date" class="form-control" value="<?= htmlspecialchars(date('Y-m-d\TH:i')) ?>" required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Dépôt</label>
                                        <select name="depot_id" class="form-select" required>
                                            <option value="">Choisir un dépôt</option>
                                            <?php foreach (($depots ?? []) as $depot): ?>
                                                <option value="<?= htmlspecialchars($depot['id_depot']) ?>"><?= htmlspecialchars($depot['nom'] ?? '') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Fournisseur</label>
                                        <select name="supplier_id" class="form-select" required>
                                            <option value="">Choisir un fournisseur</option>
                                            <?php foreach (($suppliers ?? []) as $supplier): ?>
                                                <option value="<?= htmlspecialchars($supplier['id_fournisseur']) ?>"><?= htmlspecialchars($supplier['nom'] ?? '') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Montant HT</label>
                                        <input type="number" step="0.01" min="0" name="montant_ht" class="form-control text-end" value="0" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">TVA (%)</label>
                                        <input type="number" step="0.01" min="0" name="tva_rate" class="form-control text-end" value="20" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Montant TTC</label>
                                        <input type="number" step="0.01" min="0" name="montant_ttc" class="form-control text-end" value="0" readonly>
                                        <input type="hidden" name="montant_tva" value="0">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <a class="btn btn-outline-secondary" href="<?= Flight::get('flight.base_url') ?>/avis/achat/bc">Annuler</a>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-light" id="recalcBtn">Recalculer</button>
                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    </div>
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
<script>
    const htInput = document.querySelector('input[name="montant_ht"]');
    const tvaRateInput = document.querySelector('input[name="tva_rate"]');
    const tvaAmountInput = document.querySelector('input[name="montant_tva"]');
    const ttcInput = document.querySelector('input[name="montant_ttc"]');

    function recalc() {
        const ht = parseFloat(htInput.value) || 0;
        const tvaRate = parseFloat(tvaRateInput.value) || 0;
        const tvaAmount = ht * tvaRate / 100;
        tvaAmountInput.value = tvaAmount.toFixed(2);
        ttcInput.value = (ht + tvaAmount).toFixed(2);
    }

    document.getElementById('recalcBtn').addEventListener('click', recalc);
    htInput.addEventListener('input', recalc);
    tvaRateInput.addEventListener('input', recalc);
    recalc();
</script>
</body>
</html>
