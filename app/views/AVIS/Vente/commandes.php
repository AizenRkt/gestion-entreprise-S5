<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventes - Commandes</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        :root { --primary: #2563eb; --success: #10b981; --warning: #f59e0b; --danger: #ef4444; --gray: #6b7280; }
        body { background: #f8fafc; }
        .page-title { font-size: 1.5rem; font-weight: 600; color: #1e293b; }
        .breadcrumb { font-size: 0.875rem; }
        .workflow-mini { background: white; border-radius: 10px; padding: 1rem; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap; font-size: 0.8rem; }
        .step { padding: 0.35rem 0.75rem; border-radius: 20px; }
        .step.active { background: var(--primary); color: white; }
        .step.done { background: var(--success); color: white; }
        .step.pending { background: #e2e8f0; color: var(--gray); }
        .card-minimal { background: white; border-radius: 12px; border: 1px solid #e2e8f0; }
        .card-minimal .card-body { padding: 1.25rem; }
        .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray); font-weight: 600; }
        .table td { vertical-align: middle; font-size: 0.875rem; }
        .badge-status { font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 4px; }
        .btn-action { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        .modal-minimal .modal-content { border-radius: 12px; border: none; }
        .form-label { font-size: 0.8rem; font-weight: 500; color: #475569; }
        .ligne-article { background: #f8fafc; border-radius: 8px; padding: 1rem; margin-bottom: 0.75rem; border: 1px solid #e2e8f0; }
        .total-box { background: #f0fdf4; border-radius: 8px; padding: 1rem; }
        .select2-container { width: 100% !important; }
    </style>
</head>
<body>
<div id="app">
    <?= Flight::menuBackOffice() ?>
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none"><i class="bi bi-justify fs-3"></i></a>
        </header>
        
        <div class="page-content">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="page-title">Commandes</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/ventes" class="text-decoration-none">Ventes</a></li>
                            <li class="breadcrumb-item active">Commandes</li>
                        </ol>
                    </nav>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCommande">
                    <i class="bi bi-plus"></i> Nouvelle
                </button>
            </div>

            <!-- Workflow mini -->
            <div class="workflow-mini mb-4">
                <span class="step active"><i class="bi bi-cart"></i> 1. Commande</span>
                <span>→</span>
                <span class="step pending"><i class="bi bi-truck"></i> 2. Livraison</span>
                <span>→</span>
                <span class="step pending"><i class="bi bi-receipt"></i> 3. Facture</span>
                <span>→</span>
                <span class="step pending"><i class="bi bi-cash"></i> 4. Encaissement</span>
            </div>

            <!-- Table -->
            <div class="card-minimal">
                <div class="card-body">
                    <table class="table table-hover mb-0" id="commandesTable">
                        <thead>
                            <tr>
                                <th>N° Commande</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Dépôt</th>
                                <th class="text-end">Montant TTC</th>
                                <th class="text-center">Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($commandes ?? []) as $cmd): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($cmd['commande_numero'] ?? '') ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($cmd['commande_date'] ?? 'now')) ?></td>
                                    <td><?= htmlspecialchars($cmd['client_nom'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($cmd['depot_nom'] ?? '-') ?></td>
                                    <td class="text-end fw-bold"><?= number_format($cmd['montant_ttc'] ?? 0, 0, ' ', ' ') ?> Ar</td>
                                    <td class="text-center">
                                        <?php 
                                        $statusClass = match($cmd['statut'] ?? 'BROUILLON') { 'BROUILLON' => 'bg-secondary', 'VALIDE' => 'bg-success', 'CLOTURE' => 'bg-dark', default => 'bg-light' };
                                        ?>
                                        <span class="badge badge-status <?= $statusClass ?>"><?= $cmd['statut'] ?? 'BROUILLON' ?></span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-outline-info btn-action" onclick="viewCommande(<?= $cmd['id_commande_client'] ?>)"><i class="bi bi-eye"></i></button>
                                        <?php if (($cmd['statut'] ?? 'BROUILLON') === 'BROUILLON'): ?>
                                            <button class="btn btn-success btn-action" onclick="validerCommande(<?= $cmd['id_commande_client'] ?>)"><i class="bi bi-check"></i></button>
                                        <?php elseif (($cmd['statut'] ?? '') === 'VALIDE'): ?>
                                            <a href="<?= Flight::base() ?>/ventes/livraisons?cmd=<?= $cmd['id_commande_client'] ?>" class="btn btn-outline-warning btn-action"><i class="bi bi-truck"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvelle Commande -->
<div class="modal fade modal-minimal" id="modalCommande" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-cart-plus"></i> Nouvelle Commande</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCommande">
                <div class="modal-body">
                    <!-- Infos générales -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Client *</label>
                            <select class="form-select" id="cmdClient" name="id_client" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach (($clients ?? []) as $c): ?>
                                    <option value="<?= $c['id_client'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Dépôt *</label>
                            <select class="form-select" id="cmdDepot" name="id_depot" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach (($depots ?? []) as $d): ?>
                                    <option value="<?= $d['id_depot'] ?>"><?= htmlspecialchars($d['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="cmdDate" name="commande_date" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <!-- Articles -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Articles</h6>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="ajouterLigne()"><i class="bi bi-plus"></i> Ajouter</button>
                    </div>
                    
                    <div id="lignesContainer"></div>
                    <div id="noLignesMsg" class="text-center text-muted py-4">
                        <i class="bi bi-cart fs-3 d-block mb-2"></i>
                        Cliquez sur "Ajouter" pour ajouter des articles
                    </div>

                    <!-- Total -->
                    <div class="total-box mt-3">
                        <div class="d-flex justify-content-between">
                            <span>Total HT:</span>
                            <strong><span id="totalHT">0</span> Ar</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>TVA (20%):</span>
                            <span><span id="totalTVA">0</span> Ar</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fs-5">
                            <strong>Total TTC:</strong>
                            <strong class="text-success"><span id="totalTTC">0</span> Ar</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template ligne -->
<template id="tplLigne">
    <div class="ligne-article" data-idx="__IDX__">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Article *</label>
                <select class="form-select article-select" name="lignes[__IDX__][id_article]" required>
                    <option value="">-- Rechercher --</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Qté *</label>
                <input type="number" class="form-control qte-input" name="lignes[__IDX__][quantite]" min="0.001" step="0.001" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Prix unit.</label>
                <input type="number" class="form-control prix-input" name="lignes[__IDX__][prix_unitaire]" step="0.01" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label">Remise %</label>
                <input type="number" class="form-control remise-input" name="lignes[__IDX__][remise_pourcent]" min="0" max="30" value="0">
            </div>
            <div class="col-md-1">
                <label class="form-label">Total</label>
                <input type="text" class="form-control total-input" readonly>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger w-100" onclick="supprimerLigne(this)"><i class="bi bi-trash"></i></button>
            </div>
        </div>
    </div>
</template>

<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script>
const BASE_URL = '<?= Flight::base() ?>';
let idx = 0;
const articles = <?= json_encode($articles ?? []) ?>;

$(document).ready(function() {
    $('#commandesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        order: [[0, 'desc']]
    });
    $('#cmdClient').select2({ theme: 'bootstrap-5', dropdownParent: $('#modalCommande'), placeholder: 'Rechercher...' });
});

function ajouterLigne() {
    const html = document.getElementById('tplLigne').innerHTML.replace(/__IDX__/g, idx);
    $('#lignesContainer').append(html);
    $('#noLignesMsg').hide();
    
    const $select = $(`.ligne-article[data-idx="${idx}"] .article-select`);
    $select.select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalCommande'),
        placeholder: 'Rechercher un article...',
        data: articles.map(a => ({ id: a.id_article, text: `${a.code} - ${a.designation}`, prix: a.prix_vente, stock: a.stock_disponible }))
    });
    
    $select.on('select2:select', function(e) {
        const ligne = $(this).closest('.ligne-article');
        ligne.find('.prix-input').val(e.params.data.prix || 0);
        calculerLigne(ligne);
    });
    
    $(`.ligne-article[data-idx="${idx}"] .qte-input, .ligne-article[data-idx="${idx}"] .remise-input`).on('input', function() {
        calculerLigne($(this).closest('.ligne-article'));
    });
    
    idx++;
}

function supprimerLigne(btn) {
    $(btn).closest('.ligne-article').remove();
    calculerTotaux();
    if ($('#lignesContainer').children().length === 0) $('#noLignesMsg').show();
}

function calculerLigne(ligne) {
    const qte = parseFloat(ligne.find('.qte-input').val()) || 0;
    const prix = parseFloat(ligne.find('.prix-input').val()) || 0;
    const remise = parseFloat(ligne.find('.remise-input').val()) || 0;
    const total = qte * prix * (1 - remise / 100);
    ligne.find('.total-input').val(Math.round(total).toLocaleString('fr-FR'));
    calculerTotaux();
}

function calculerTotaux() {
    let ht = 0;
    $('.ligne-article').each(function() {
        const qte = parseFloat($(this).find('.qte-input').val()) || 0;
        const prix = parseFloat($(this).find('.prix-input').val()) || 0;
        const remise = parseFloat($(this).find('.remise-input').val()) || 0;
        ht += qte * prix * (1 - remise / 100);
    });
    const tva = ht * 0.2;
    $('#totalHT').text(Math.round(ht).toLocaleString('fr-FR'));
    $('#totalTVA').text(Math.round(tva).toLocaleString('fr-FR'));
    $('#totalTTC').text(Math.round(ht + tva).toLocaleString('fr-FR'));
}

$('#formCommande').on('submit', function(e) {
    e.preventDefault();
    const lignes = [];
    $('.ligne-article').each(function() {
        lignes.push({
            id_article: parseInt($(this).find('.article-select').val()),
            quantite: parseFloat($(this).find('.qte-input').val()),
            prix_unitaire: parseFloat($(this).find('.prix-input').val()),
            remise_pourcent: parseFloat($(this).find('.remise-input').val()) || 0
        });
    });
    
    $.ajax({
        url: `${BASE_URL}/ventes/commandes`,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            id_client: parseInt($('#cmdClient').val()),
            id_depot: parseInt($('#cmdDepot').val()),
            commande_date: $('#cmdDate').val(),
            lignes: lignes,
            cree_par: 1
        }),
        success: () => location.reload(),
        error: (xhr) => alert(xhr.responseJSON?.error || 'Erreur')
    });
});

function validerCommande(id) {
    if (!confirm('Valider cette commande ?')) return;
    $.ajax({ url: `${BASE_URL}/ventes/commandes/${id}/valider`, type: 'PUT', success: () => location.reload() });
}

function viewCommande(id) { alert('Voir commande #' + id); }
</script>
</body>
</html>
