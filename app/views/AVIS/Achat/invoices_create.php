<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle facture fournisseur</title>
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
            <a href="<?= Flight::base() ?>/avis/achat/factures" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h3>Nouvelle facture fournisseur</h3>
                <p class="text-subtitle text-muted">Rattacher la facture à une réception.</p>
            </div>
        </div>
        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= Flight::base() ?>/avis/achat/factures" method="POST" id="invoiceForm">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Numéro de facture</label>
                                        <input type="text" name="facture_numero" class="form-control" placeholder="FAC0001" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Date de facture</label>
                                        <input type="datetime-local" name="facture_date" class="form-control" value="<?= htmlspecialchars(date('Y-m-d\TH:i')) ?>" required>
                                    </div>
                                    <div class="col-md-4">
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
                                    <div class="col-md-6">
                                        <label class="form-label">Réception liée</label>
                                        <select name="reception_id" class="form-select" required>
                                            <option value="">Choisir une réception</option>
                                            <?php foreach (($receptions ?? []) as $reception): ?>
                                                <option value="<?= htmlspecialchars($reception['id_reception_fournisseur']) ?>"><?= htmlspecialchars(($reception['reception_numero'] ?? '') . ' - ' . ($reception['supplier_name'] ?? '')) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Montant HT</label>
                                        <input type="number" step="0.01" min="0" name="montant_ht" class="form-control text-end" value="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">TVA (%)</label>
                                        <input type="number" step="0.01" min="0" name="tva_rate" class="form-control text-end" value="20" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Montant TTC</label>
                                        <input type="number" step="0.01" min="0" name="montant_ttc" class="form-control text-end" value="0" readonly>
                                        <input type="hidden" name="montant_tva" value="0">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <a class="btn btn-outline-secondary" href="<?= Flight::get('flight.base_url') ?>/avis/achat/factures">Annuler</a>
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
