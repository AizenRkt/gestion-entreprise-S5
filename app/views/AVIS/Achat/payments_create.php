<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau paiement fournisseur</title>
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
            <a href="<?= Flight::base() ?>/avis/achat/paiements" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h3>Nouveau paiement fournisseur</h3>
                <p class="text-subtitle text-muted">Rattacher un règlement à une facture validée.</p>
            </div>
        </div>
        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= Flight::base() ?>/avis/achat/paiements" method="POST" id="paymentForm">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Numéro de paiement</label>
                                        <input type="text" name="paiement_numero" class="form-control" placeholder="PAY0001" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Date de paiement</label>
                                        <input type="datetime-local" name="paiement_date" class="form-control" value="<?= htmlspecialchars(date('Y-m-d\TH:i')) ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Facture</label>
                                        <select name="invoice_id" class="form-select" id="invoiceSelect" required>
                                            <option value="">Choisir une facture</option>
                                            <?php foreach (($invoices ?? []) as $invoice): ?>
                                                <option value="<?= htmlspecialchars($invoice['id_facture_fournisseur']) ?>" data-amount="<?= htmlspecialchars($invoice['montant_ttc'] ?? 0) ?>">
                                                    <?= htmlspecialchars(($invoice['facture_numero'] ?? '') . ' - ' . ($invoice['supplier_name'] ?? '')) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Mode de paiement</label>
                                        <select name="mode_id" class="form-select" required>
                                            <option value="">Choisir un mode</option>
                                            <?php foreach (($modes ?? []) as $mode): ?>
                                                <option value="<?= htmlspecialchars($mode['id_mode_paiement']) ?>"><?= htmlspecialchars($mode['libelle'] ?? '') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Montant payé</label>
                                        <input type="number" step="0.01" min="0" name="amount" class="form-control text-end" id="amountInput" value="0" required>
                                        <small class="text-muted">Par défaut, le TTC de la facture sélectionnée.</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Référence (optionnel)</label>
                                        <input type="text" name="reference" class="form-control" placeholder="Référence bancaire / chèque">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <a class="btn btn-outline-secondary" href="<?= Flight::get('flight.base_url') ?>/avis/achat/paiements">Annuler</a>
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
<script>
    const invoiceSelect = document.getElementById('invoiceSelect');
    const amountInput = document.getElementById('amountInput');

    function fillAmountFromInvoice() {
        const option = invoiceSelect.options[invoiceSelect.selectedIndex];
        if (!option) {
            return;
        }
        const value = parseFloat(option.dataset.amount || '0');
        if (!Number.isNaN(value) && value > 0) {
            amountInput.value = value.toFixed(2);
        }
    }

    invoiceSelect.addEventListener('change', fillAmountFromInvoice);
    fillAmountFromInvoice();
</script>
</body>
</html>
