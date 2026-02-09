<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventes - Factures</title>
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
        .step.active { background: var(--primary); color: white; }
        .step.done { background: var(--success); color: white; }
        .step.pending { background: #e2e8f0; color: var(--gray); }
        .stat-mini { background: white; border-radius: 10px; padding: 1rem; border: 1px solid #e2e8f0; text-align: center; }
        .stat-mini .value { font-size: 1.5rem; font-weight: 700; }
        .stat-mini .label { font-size: 0.75rem; color: var(--gray); text-transform: uppercase; }
        .card-minimal { background: white; border-radius: 12px; border: 1px solid #e2e8f0; }
        .card-minimal .card-body { padding: 1.25rem; }
        .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray); font-weight: 600; }
        .table td { vertical-align: middle; font-size: 0.875rem; }
        .badge-status { font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 4px; }
        .btn-action { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        .modal-minimal .modal-content { border-radius: 12px; border: none; }
        .form-label { font-size: 0.8rem; font-weight: 500; color: #475569; }
        .info-box { background: #eff6ff; border-radius: 8px; padding: 1rem; border: 1px solid #bfdbfe; }
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
                    <h1 class="page-title">Factures</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/ventes" class="text-decoration-none">Ventes</a></li>
                            <li class="breadcrumb-item active">Factures</li>
                        </ol>
                    </nav>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFacture">
                    <i class="bi bi-receipt"></i> Nouvelle Facture
                </button>
            </div>

            <!-- Workflow mini -->
            <div class="workflow-mini mb-4">
                <span class="step done"><i class="bi bi-cart"></i> 1. Commande</span>
                <span>→</span>
                <span class="step done"><i class="bi bi-truck"></i> 2. Livraison</span>
                <span>→</span>
                <span class="step active"><i class="bi bi-receipt"></i> 3. Facture</span>
                <span>→</span>
                <span class="step pending"><i class="bi bi-cash"></i> 4. Encaissement</span>
            </div>

            <!-- Mini stats -->
            <?php
            $brouillons = array_filter($factures ?? [], fn($f) => ($f['statut'] ?? '') === 'BROUILLON');
            $enAttente = array_filter($factures ?? [], fn($f) => ($f['statut'] ?? '') === 'VALIDE');
            $payees = array_filter($factures ?? [], fn($f) => ($f['statut'] ?? '') === 'PAYEE');
            $totalCA = array_sum(array_map(fn($f) => $f['montant_ttc'] ?? 0, $payees));
            ?>
            <div class="row mb-4">
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-mini">
                        <div class="value text-secondary"><?= count($brouillons) ?></div>
                        <div class="label">Brouillons</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-mini">
                        <div class="value text-warning"><?= count($enAttente) ?></div>
                        <div class="label">En attente</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-mini">
                        <div class="value text-success"><?= count($payees) ?></div>
                        <div class="label">Payées</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-mini">
                        <div class="value text-primary"><?= number_format($totalCA / 1000000, 1) ?>M</div>
                        <div class="label">CA (Ar)</div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card-minimal">
                <div class="card-body">
                    <table class="table table-hover mb-0" id="facturesTable">
                        <thead>
                            <tr>
                                <th>N° Facture</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Livraison</th>
                                <th class="text-end">Montant TTC</th>
                                <th class="text-center">Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($factures ?? []) as $fac): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($fac['facture_numero'] ?? '') ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($fac['facture_date'] ?? 'now')) ?></td>
                                    <td><?= htmlspecialchars($fac['client_nom'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= Flight::base() ?>/ventes/livraisons?id=<?= $fac['id_livraison'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($fac['livraison_numero'] ?? '-') ?>
                                        </a>
                                    </td>
                                    <td class="text-end fw-bold"><?= number_format($fac['montant_ttc'] ?? 0, 0, ' ', ' ') ?> Ar</td>
                                    <td class="text-center">
                                        <?php 
                                        $statusClass = match($fac['statut'] ?? 'BROUILLON') { 
                                            'BROUILLON' => 'bg-secondary', 
                                            'VALIDE' => 'bg-warning', 
                                            'PAYEE' => 'bg-success', 
                                            'ANNULEE' => 'bg-danger',
                                            default => 'bg-light' 
                                        };
                                        $statusText = match($fac['statut'] ?? 'BROUILLON') { 
                                            'BROUILLON' => 'Brouillon', 
                                            'VALIDE' => 'En attente', 
                                            'PAYEE' => 'Payée', 
                                            'ANNULEE' => 'Annulée',
                                            default => $fac['statut'] 
                                        };
                                        ?>
                                        <span class="badge badge-status <?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-outline-info btn-action" onclick="viewFacture(<?= $fac['id_facture'] ?>)"><i class="bi bi-eye"></i></button>
                                        <button class="btn btn-outline-secondary btn-action" onclick="printFacture(<?= $fac['id_facture'] ?>)"><i class="bi bi-printer"></i></button>
                                        <?php if (($fac['statut'] ?? 'BROUILLON') === 'BROUILLON'): ?>
                                            <button class="btn btn-success btn-action" onclick="validerFacture(<?= $fac['id_facture'] ?>)"><i class="bi bi-check"></i></button>
                                        <?php elseif (($fac['statut'] ?? '') === 'VALIDE'): ?>
                                            <a href="<?= Flight::base() ?>/ventes/encaissements?fac=<?= $fac['id_facture'] ?>" class="btn btn-outline-success btn-action"><i class="bi bi-cash"></i></a>
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

<!-- Modal Nouvelle Facture -->
<div class="modal fade modal-minimal" id="modalFacture" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-receipt"></i> Nouvelle Facture</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formFacture">
                <div class="modal-body">
                    <!-- Sélection livraison -->
                    <div class="mb-4">
                        <label class="form-label">Livraison effectuée à facturer *</label>
                        <select class="form-select" id="facLivraison" name="id_livraison" required>
                            <option value="">-- Sélectionner une livraison --</option>
                            <?php foreach (($livraisonsTerminees ?? []) as $liv): ?>
                                <option value="<?= $liv['id_livraison'] ?>" 
                                        data-client="<?= htmlspecialchars($liv['client_nom'] ?? '') ?>"
                                        data-commande="<?= htmlspecialchars($liv['commande_numero'] ?? '') ?>"
                                        data-montant="<?= $liv['montant_ttc'] ?? 0 ?>">
                                    <?= htmlspecialchars($liv['livraison_numero']) ?> - <?= htmlspecialchars($liv['client_nom'] ?? '') ?> (<?= number_format($liv['montant_ttc'] ?? 0, 0, ' ', ' ') ?> Ar)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Infos livraison -->
                    <div class="info-box mb-4" id="infoLivraison" style="display: none;">
                        <div class="row">
                            <div class="col-4">
                                <small class="text-muted">Client</small>
                                <div id="infoClient" class="fw-bold">-</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Commande</small>
                                <div id="infoCommande" class="fw-bold">-</div>
                            </div>
                            <div class="col-4 text-end">
                                <small class="text-muted">Montant TTC</small>
                                <div id="infoMontant" class="fw-bold text-primary fs-5">0 Ar</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Date facture</label>
                            <input type="date" class="form-control" id="facDate" name="facture_date" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Échéance</label>
                            <input type="date" class="form-control" id="facEcheance" name="date_echeance" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer la facture</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script>
const BASE_URL = '<?= Flight::base() ?>';

$(document).ready(function() {
    $('#facturesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        order: [[0, 'desc']]
    });

    $('#facLivraison').on('change', function() {
        const selected = $(this).find(':selected');
        if (!selected.val()) {
            $('#infoLivraison').hide();
            return;
        }
        
        $('#infoClient').text(selected.data('client') || '-');
        $('#infoCommande').text(selected.data('commande') || '-');
        $('#infoMontant').text(parseInt(selected.data('montant') || 0).toLocaleString('fr-FR') + ' Ar');
        $('#infoLivraison').show();
    });
});

$('#formFacture').on('submit', function(e) {
    e.preventDefault();
    const livSelected = $('#facLivraison').find(':selected');
    
    $.ajax({
        url: `${BASE_URL}/ventes/factures`,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            id_livraison: parseInt($('#facLivraison').val()),
            facture_date: $('#facDate').val(),
            date_echeance: $('#facEcheance').val(),
            montant_ht: parseFloat(livSelected.data('montant') || 0) / 1.2,
            montant_tva: parseFloat(livSelected.data('montant') || 0) * 0.2 / 1.2,
            montant_ttc: parseFloat(livSelected.data('montant') || 0),
            cree_par: 1
        }),
        success: () => location.reload(),
        error: (xhr) => alert(xhr.responseJSON?.error || 'Erreur')
    });
});

function validerFacture(id) {
    if (!confirm('Valider cette facture ?')) return;
    $.ajax({ url: `${BASE_URL}/ventes/factures/${id}/valider`, type: 'PUT', success: () => location.reload() });
}

function viewFacture(id) { alert('Voir facture #' + id); }
function printFacture(id) { window.open(`${BASE_URL}/ventes/factures/${id}/pdf`, '_blank'); }
</script>
</body>
</html>
