<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventes - Livraisons</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        :root { --primary: #2563eb; --success: #10b981; --warning: #f59e0b; --danger: #ef4444; --gray: #6b7280; }
        body { background: #f8fafc; }
        .page-title { font-size: 1.5rem; font-weight: 600; color: #1e293b; }
        .breadcrumb { font-size: 0.875rem; }
        .workflow-mini { background: white; border-radius: 10px; padding: 1rem; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap; font-size: 0.8rem; }
        .step { padding: 0.35rem 0.75rem; border-radius: 20px; }
        .step.active { background: var(--warning); color: white; }
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
        .ligne-item { background: #f8fafc; border-radius: 8px; padding: 0.75rem 1rem; margin-bottom: 0.5rem; border: 1px solid #e2e8f0; }
        .info-box { background: #f0fdf4; border-radius: 8px; padding: 1rem; border: 1px solid #bbf7d0; }
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
                    <h1 class="page-title">Livraisons</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/ventes" class="text-decoration-none">Ventes</a></li>
                            <li class="breadcrumb-item active">Livraisons</li>
                        </ol>
                    </nav>
                </div>
                <button class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalLivraison">
                    <i class="bi bi-truck"></i> Nouvelle Livraison
                </button>
            </div>

            <!-- Workflow mini -->
            <div class="workflow-mini mb-4">
                <span class="step done"><i class="bi bi-cart"></i> 1. Commande</span>
                <span>→</span>
                <span class="step active"><i class="bi bi-truck"></i> 2. Livraison</span>
                <span>→</span>
                <span class="step pending"><i class="bi bi-receipt"></i> 3. Facture</span>
                <span>→</span>
                <span class="step pending"><i class="bi bi-cash"></i> 4. Encaissement</span>
            </div>

            <!-- Table -->
            <div class="card-minimal">
                <div class="card-body">
                    <table class="table table-hover mb-0" id="livraisonsTable">
                        <thead>
                            <tr>
                                <th>N° Livraison</th>
                                <th>Date</th>
                                <th>Commande</th>
                                <th>Client</th>
                                <th class="text-center">Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($livraisons ?? []) as $liv): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($liv['livraison_numero'] ?? '') ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($liv['livraison_date'] ?? 'now')) ?></td>
                                    <td>
                                        <a href="<?= Flight::base() ?>/ventes/commandes?id=<?= $liv['id_commande_client'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($liv['commande_numero'] ?? '-') ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($liv['client_nom'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <?php 
                                        $statusClass = match($liv['statut'] ?? 'EN_COURS') { 'EN_COURS' => 'bg-warning', 'LIVRE' => 'bg-success', default => 'bg-secondary' };
                                        $statusText = match($liv['statut'] ?? 'EN_COURS') { 'EN_COURS' => 'En cours', 'LIVRE' => 'Livré', default => $liv['statut'] };
                                        ?>
                                        <span class="badge badge-status <?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-outline-info btn-action" onclick="viewLivraison(<?= $liv['id_livraison'] ?>)"><i class="bi bi-eye"></i></button>
                                        <?php if (($liv['statut'] ?? 'EN_COURS') === 'EN_COURS'): ?>
                                            <button class="btn btn-success btn-action" onclick="validerLivraison(<?= $liv['id_livraison'] ?>)"><i class="bi bi-check"></i> Livré</button>
                                        <?php elseif (($liv['statut'] ?? '') === 'LIVRE'): ?>
                                            <a href="<?= Flight::base() ?>/ventes/factures?liv=<?= $liv['id_livraison'] ?>" class="btn btn-outline-primary btn-action"><i class="bi bi-receipt"></i></a>
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

<!-- Modal Nouvelle Livraison -->
<div class="modal fade modal-minimal" id="modalLivraison" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-truck"></i> Nouvelle Livraison</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formLivraison">
                <div class="modal-body">
                    <!-- Sélection commande -->
                    <div class="mb-4">
                        <label class="form-label">Commande validée à livrer *</label>
                        <select class="form-select" id="livCommande" name="id_commande_client" required>
                            <option value="">-- Sélectionner une commande --</option>
                            <?php foreach (($commandesValidees ?? []) as $cmd): ?>
                                <option value="<?= $cmd['id_commande_client'] ?>" 
                                        data-client="<?= htmlspecialchars($cmd['client_nom'] ?? '') ?>"
                                        data-montant="<?= number_format($cmd['montant_ttc'] ?? 0, 0, ' ', ' ') ?>"
                                        data-lignes='<?= json_encode($cmd['lignes'] ?? []) ?>'>
                                    <?= htmlspecialchars($cmd['commande_numero']) ?> - <?= htmlspecialchars($cmd['client_nom'] ?? '') ?> (<?= number_format($cmd['montant_ttc'] ?? 0, 0, ' ', ' ') ?> Ar)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Infos commande -->
                    <div class="info-box mb-4" id="infoCommande" style="display: none;">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Client</small>
                                <div id="infoClient" class="fw-bold">-</div>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Montant TTC</small>
                                <div id="infoMontant" class="fw-bold text-success">0 Ar</div>
                            </div>
                        </div>
                    </div>

                    <!-- Lignes à livrer -->
                    <div id="lignesSection" style="display: none;">
                        <h6 class="mb-3">Articles à livrer</h6>
                        <div id="lignesLivraison"></div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Date livraison</label>
                            <input type="date" class="form-control" id="livDate" name="livraison_date" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning text-white">Créer la livraison</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
const BASE_URL = '<?= Flight::base() ?>';

$(document).ready(function() {
    $('#livraisonsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        order: [[0, 'desc']]
    });

    $('#livCommande').on('change', function() {
        const selected = $(this).find(':selected');
        if (!selected.val()) {
            $('#infoCommande, #lignesSection').hide();
            return;
        }
        
        $('#infoClient').text(selected.data('client') || '-');
        $('#infoMontant').text((selected.data('montant') || '0') + ' Ar');
        $('#infoCommande').show();
        
        const lignes = selected.data('lignes') || [];
        let html = '';
        lignes.forEach((l, i) => {
            html += `
                <div class="ligne-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${l.designation || 'Article'}</strong>
                            <small class="text-muted d-block">Commandé: ${l.quantite || 0} - Déjà livré: ${l.quantite_livree || 0}</small>
                        </div>
                        <div style="width: 120px;">
                            <input type="number" class="form-control form-control-sm" 
                                   name="lignes[${i}][quantite_livree]" 
                                   value="${(l.quantite || 0) - (l.quantite_livree || 0)}" 
                                   min="0" max="${(l.quantite || 0) - (l.quantite_livree || 0)}" step="0.001">
                            <input type="hidden" name="lignes[${i}][id_ligne]" value="${l.id_ligne_commande}">
                        </div>
                    </div>
                </div>`;
        });
        $('#lignesLivraison').html(html);
        $('#lignesSection').show();
    });
});

$('#formLivraison').on('submit', function(e) {
    e.preventDefault();
    const lignes = [];
    $('[name^="lignes["]').each(function() {
        const match = $(this).attr('name').match(/lignes\[(\d+)\]\[(\w+)\]/);
        if (match) {
            const idx = parseInt(match[1]);
            const key = match[2];
            lignes[idx] = lignes[idx] || {};
            lignes[idx][key] = $(this).val();
        }
    });
    
    $.ajax({
        url: `${BASE_URL}/ventes/livraisons`,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            id_commande_client: parseInt($('#livCommande').val()),
            livraison_date: $('#livDate').val(),
            lignes: lignes.filter(l => l && parseFloat(l.quantite_livree) > 0),
            cree_par: 1
        }),
        success: () => location.reload(),
        error: (xhr) => alert(xhr.responseJSON?.error || 'Erreur')
    });
});

function validerLivraison(id) {
    if (!confirm('Confirmer la livraison ?')) return;
    $.ajax({ url: `${BASE_URL}/ventes/livraisons/${id}/livrer`, type: 'PUT', success: () => location.reload() });
}

function viewLivraison(id) { alert('Voir livraison #' + id); }
</script>
</body>
</html>
