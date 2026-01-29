<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie demande d'achat</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
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
        <div class="page-heading">
            <h3>Saisie demande d'achat</h3>
            <p class="text-subtitle text-muted">Enregistrer une demande d'achat et ses lignes.</p>
        </div>
        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= Flight::base() ?>/avis/achat/saisie" method="POST" id="purchaseForm">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Date</label>
                                        <input type="date" name="request_date" class="form-control" value="<?= htmlspecialchars(date('Y-m-d')) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fournisseur</label>
                                        <select name="supplier_id" class="form-select" required>
                                            <option value="">Choisir un fournisseur</option>
                                            <?php foreach (($suppliers ?? []) as $supplier): ?>
                                                <option value="<?= htmlspecialchars($supplier['id_fournisseur']) ?>"><?= htmlspecialchars($supplier['nom'] ?? '') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <div class="w-100">
                                            <label class="form-label">Remarque</label>
                                            <input type="text" name="remark" class="form-control" placeholder="Optionnel">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 p-3 bg-light rounded border">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <h6 class="mb-0">Total</h6>
                                        <div class="d-flex gap-3">
                                            <div><span class="text-muted">HT :</span> <strong id="totalHt">0</strong></div>
                                            <div><span class="text-muted">TVA :</span> <strong id="totalTva">0</strong></div>
                                            <div><span class="text-muted">TTC :</span> <strong id="totalTtc">0</strong></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Importer un fichier Excel (optionnel)</label>
                                    <input type="file" class="form-control" name="excel_file" accept=".xls,.xlsx" disabled>
                                    <small class="text-muted">Fonction à venir, saisissez les lignes ci-dessous.</small>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm align-middle" id="linesTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Produit</th>
                                                <th>Désignation</th>
                                                <th class="text-end">Quantité</th>
                                                <th class="text-end">Prix Unitaire</th>
                                                <th class="text-end">TVA</th>
                                                <th class="text-end">Quantité en stock</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="addLineBtn">Ajouter une ligne</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="addTenBtn">Ajouter dix lignes</button>
                                    <button type="button" class="btn btn-light btn-sm" id="resetBtn">Réinitialiser</button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-outline-secondary" href="<?= Flight::get('flight.base_url') ?>/avis/achat/demandes">Annuler</a>
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

<template id="line-template">
    <tr data-index="__INDEX__">
        <td style="min-width: 260px;">
            <div class="position-relative">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control form-control-sm product-search" placeholder="Rechercher un produit">
                </div>
                <div class="list-group suggestion-list d-none" style="max-height: 220px; overflow-y: auto;"></div>
            </div>
            <input type="hidden" class="article-id" name="lines[__INDEX__][article_id]">
            <input type="text" class="form-control form-control-sm product-code" name="lines[__INDEX__][product_code]" placeholder="Code produit">
        </td>
        <td><input type="text" class="form-control form-control-sm designation" name="lines[__INDEX__][designation]" placeholder="Désignation"></td>
        <td class="text-end"><input type="number" step="0.001" min="0" class="form-control form-control-sm text-end quantity" name="lines[__INDEX__][quantity]" value="0"></td>
        <td class="text-end"><input type="number" step="0.01" min="0" class="form-control form-control-sm text-end unit-price" name="lines[__INDEX__][unit_price]" value="0"></td>
        <td class="text-end"><input type="number" step="0.01" min="0" class="form-control form-control-sm text-end vat" name="lines[__INDEX__][vat]" value="0"></td>
        <td class="text-end"><input type="number" step="0.001" min="0" class="form-control form-control-sm text-end stock-qty" name="lines[__INDEX__][stock_quantity]" value="0" readonly></td>
        <td class="text-center">
            <button type="button" class="btn btn-link text-danger p-0 remove-line" title="Supprimer"><i class="bi bi-x-lg"></i></button>
        </td>
    </tr>
</template>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script>
    const articlesData = <?php echo json_encode($articles ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    const linesTableBody = document.querySelector('#linesTable tbody');
    const template = document.querySelector('#line-template').content;
    let lineIndex = 0;

    function addLine() {
        const clone = document.importNode(template, true);
        const html = clone.firstElementChild.outerHTML.replace(/__INDEX__/g, lineIndex.toString());
        const temp = document.createElement('tbody');
        temp.innerHTML = html;
        const row = temp.firstElementChild;
        bindRow(row);
        linesTableBody.appendChild(row);
        lineIndex += 1;
    }

    function addTenLines() {
        for (let i = 0; i < 10; i += 1) {
            addLine();
        }
    }

    function bindRow(row) {
        const searchInput = row.querySelector('.product-search');
        const suggestionList = row.querySelector('.suggestion-list');
        const articleIdInput = row.querySelector('.article-id');
        const codeInput = row.querySelector('.product-code');
        const designationInput = row.querySelector('.designation');
        const unitPriceInput = row.querySelector('.unit-price');
        const stockInput = row.querySelector('.stock-qty');

        const hideSuggestions = () => {
            suggestionList.classList.add('d-none');
        };

        const fillFromArticle = (article) => {
            articleIdInput.value = article.id_article || '';
            codeInput.value = article.code || '';
            designationInput.value = article.designation || '';
            unitPriceInput.value = article.prix_achat || 0;
            stockInput.value = article.quantite_stock || 0;
            searchInput.value = article.designation || article.code || '';
            hideSuggestions();
            recalcTotals();
        };

        const renderSuggestions = (term) => {
            const value = term.trim().toLowerCase();
            suggestionList.innerHTML = '';
            if (!value) {
                hideSuggestions();
                return;
            }
            const matches = articlesData.filter((a) => {
                const code = (a.code || '').toLowerCase();
                const des = (a.designation || '').toLowerCase();
                return code.includes(value) || des.includes(value);
            }).slice(0, 8);

            if (matches.length === 0) {
                const emptyItem = document.createElement('div');
                emptyItem.className = 'list-group-item list-group-item-action disabled';
                emptyItem.textContent = 'Aucun produit';
                suggestionList.appendChild(emptyItem);
            } else {
                matches.forEach((a) => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = `${a.code || ''} - ${a.designation || ''}`;
                    item.addEventListener('click', () => fillFromArticle(a));
                    suggestionList.appendChild(item);
                });
            }
            suggestionList.classList.remove('d-none');
        };

        searchInput.addEventListener('input', (e) => renderSuggestions(e.target.value));
        searchInput.addEventListener('focus', (e) => renderSuggestions(e.target.value));
        searchInput.addEventListener('blur', () => setTimeout(hideSuggestions, 150));

        row.querySelectorAll('.quantity, .unit-price, .vat').forEach((input) => {
            input.addEventListener('input', recalcTotals);
        });

        const removeBtn = row.querySelector('.remove-line');
        removeBtn.addEventListener('click', () => {
            row.remove();
            recalcTotals();
        });
    }

    function recalcTotals() {
        let totalHt = 0;
        let totalTva = 0;

        linesTableBody.querySelectorAll('tr').forEach((row) => {
            const qty = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = parseFloat(row.querySelector('.unit-price').value) || 0;
            const vat = parseFloat(row.querySelector('.vat').value) || 0;
            const lineHt = qty * price;
            const lineTva = lineHt * (vat / 100);
            totalHt += lineHt;
            totalTva += lineTva;
        });

        document.getElementById('totalHt').textContent = totalHt.toFixed(2);
        document.getElementById('totalTva').textContent = totalTva.toFixed(2);
        document.getElementById('totalTtc').textContent = (totalHt + totalTva).toFixed(2);
    }

    document.getElementById('addLineBtn').addEventListener('click', addLine);
    document.getElementById('addTenBtn').addEventListener('click', addTenLines);
    document.getElementById('resetBtn').addEventListener('click', () => {
        linesTableBody.innerHTML = '';
        lineIndex = 0;
        addTenLines();
        recalcTotals();
    });

    document.getElementById('purchaseForm').addEventListener('submit', (event) => {
        if (linesTableBody.querySelectorAll('tr').length === 0) {
            event.preventDefault();
            alert('Ajoutez au moins une ligne de produit.');
            return;
        }
        recalcTotals();
    });

    // Initialisation
    addTenLines();
    recalcTotals();
</script>
</body>
</html>
