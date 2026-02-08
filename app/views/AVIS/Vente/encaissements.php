<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventes - Encaissements</title>
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
        .step.active { background: var(--success); color: white; }
        .step.done { background: var(--success); color: white; }
        .step.pending { background: #e2e8f0; color: var(--gray); }
        .stat-mini { background: white; border-radius: 10px; padding: 1rem; border: 1px solid #e2e8f0; text-align: center; }
        .stat-mini .value { font-size: 1.5rem; font-weight: 700; }
        .stat-mini .label { font-size: 0.75rem; color: var(--gray); text-transform: uppercase; }
        .card-minimal { background: white; border-radius: 12px; border: 1px solid #e2e8f0; }
        .card-minimal .card-body { padding: 1.25rem; }
        .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray); font-weight: 600; }
        .table td { vertical-align: middle; font-size: 0.875rem; }
        .badge-mode { font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 4px; }
        .btn-action { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        .modal-minimal .modal-content { border-radius: 12px; border: none; }
        .form-label { font-size: 0.8rem; font-weight: 500; color: #475569; }
        .info-box { background: #f0fdf4; border-radius: 8px; padding: 1rem; border: 1px solid #bbf7d0; }
        .mode-card { border: 2px solid #e2e8f0; border-radius: 10px; padding: 1rem; cursor: pointer; text-align: center; transition: all 0.2s; }
        .mode-card:hover { border-color: var(--primary); background: #f8fafc; }
        .mode-card.selected { border-color: var(--success); background: #f0fdf4; }
        .mode-card i { font-size: 1.5rem; display: block; margin-bottom: 0.5rem; }
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
                    <h1 class="page-title">Encaissements</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/ventes" class="text-decoration-none">Ventes</a></li>
                            <li class="breadcrumb-item active">Encaissements</li>
                        </ol>
                    </nav>
                </div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEncaissement">
                    <i class="bi bi-cash"></i> Nouvel Encaissement
                </button>
            </div>

            <!-- Workflow mini (tout complété) -->
            <div class="workflow-mini mb-4">
                <span class="step done"><i class="bi bi-cart"></i> 1. Commande</span>
                <span>→</span>
                <span class="step done"><i class="bi bi-truck"></i> 2. Livraison</span>
                <span>→</span>
                <span class="step done"><i class="bi bi-receipt"></i> 3. Facture</span>
                <span>→</span>
                <span class="step active"><i class="bi bi-cash"></i> 4. Encaissement</span>
            </div>

            <!-- Mini stats -->
            <?php
            $totalEncaisse = array_sum(array_map(fn($e) => $e['montant'] ?? 0, $encaissements ?? []));
            $facturesEnAttente = count($facturesNonPayees ?? []);
            $montantAttente = array_sum(array_map(fn($f) => $f['montant_ttc'] ?? 0, $facturesNonPayees ?? []));
            ?>
            <div class="row mb-4">
                <div class="col-md-4 col-6 mb-3">
                    <div class="stat-mini">
                        <div class="value text-success"><?= number_format($totalEncaisse / 1000000, 1) ?>M</div>
                        <div class="label">Total encaissé (Ar)</div>
                    </div>
                </div>
                <div class="col-md-4 col-6 mb-3">
                    <div class="stat-mini">
                        <div class="value text-warning"><?= $facturesEnAttente ?></div>
                        <div class="label">Factures en attente</div>
                    </div>
                </div>
                <div class="col-md-4 col-12 mb-3">
                    <div class="stat-mini">
                        <div class="value text-danger"><?= number_format($montantAttente / 1000000, 1) ?>M</div>
                        <div class="label">Créances (Ar)</div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card-minimal">
                <div class="card-body">
                    <table class="table table-hover mb-0" id="encaissementsTable">
                        <thead>
                            <tr>
                                <th>N° Encaissement</th>
                                <th>Date</th>
                                <th>Facture</th>
                                <th>Client</th>
                                <th class="text-end">Montant</th>
                                <th class="text-center">Mode</th>
                                <th>Référence</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($encaissements ?? []) as $enc): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($enc['encaissement_numero'] ?? '') ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($enc['encaissement_date'] ?? 'now')) ?></td>
                                    <td>
                                        <a href="<?= Flight::base() ?>/ventes/factures?id=<?= $enc['id_facture'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($enc['facture_numero'] ?? '-') ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($enc['client_nom'] ?? '-') ?></td>
                                    <td class="text-end fw-bold text-success"><?= number_format($enc['montant'] ?? 0, 0, ' ', ' ') ?> Ar</td>
                                    <td class="text-center">
                                        <?php 
                                        $modeClass = match($enc['mode_paiement'] ?? 'ESPECE') { 
                                            'ESPECE' => 'bg-success', 
                                            'CHEQUE' => 'bg-info', 
                                            'VIREMENT' => 'bg-primary', 
                                            'MOBILE' => 'bg-warning',
                                            default => 'bg-secondary' 
                                        };
                                        $modeIcon = match($enc['mode_paiement'] ?? 'ESPECE') { 
                                            'ESPECE' => 'bi-cash', 
                                            'CHEQUE' => 'bi-card-text', 
                                            'VIREMENT' => 'bi-bank', 
                                            'MOBILE' => 'bi-phone',
                                            default => 'bi-currency-exchange' 
                                        };
                                        ?>
                                        <span class="badge badge-mode <?= $modeClass ?>"><i class="<?= $modeIcon ?>"></i> <?= $enc['mode_paiement'] ?? '-' ?></span>
                                    </td>
                                    <td class="text-muted"><?= htmlspecialchars($enc['reference_paiement'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvel Encaissement -->
<div class="modal fade modal-minimal" id="modalEncaissement" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-cash-coin"></i> Nouvel Encaissement</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEncaissement">
                <div class="modal-body">
                    <!-- Sélection facture -->
                    <div class="mb-4">
                        <label class="form-label">Facture en attente de paiement *</label>
                        <select class="form-select" id="encFacture" name="id_facture" required>
                            <option value="">-- Sélectionner une facture --</option>
                            <?php foreach (($facturesNonPayees ?? []) as $fac): ?>
                                <option value="<?= $fac['id_facture'] ?>" 
                                        data-client="<?= htmlspecialchars($fac['client_nom'] ?? '') ?>"
                                        data-montant="<?= $fac['montant_ttc'] ?? 0 ?>"
                                        data-reste="<?= ($fac['montant_ttc'] ?? 0) - ($fac['montant_paye'] ?? 0) ?>">
                                    <?= htmlspecialchars($fac['facture_numero']) ?> - <?= htmlspecialchars($fac['client_nom'] ?? '') ?> (<?= number_format($fac['montant_ttc'] ?? 0, 0, ' ', ' ') ?> Ar)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Infos facture -->
                    <div class="info-box mb-4" id="infoFacture" style="display: none;">
                        <div class="row">
                            <div class="col-4">
                                <small class="text-muted">Client</small>
                                <div id="infoClient" class="fw-bold">-</div>
                            </div>
                            <div class="col-4 text-center">
                                <small class="text-muted">Montant total</small>
                                <div id="infoMontant" class="fw-bold">0 Ar</div>
                            </div>
                            <div class="col-4 text-end">
                                <small class="text-muted">Reste à payer</small>
                                <div id="infoReste" class="fw-bold text-danger fs-5">0 Ar</div>
                            </div>
                        </div>
                    </div>

                    <!-- Mode de paiement -->
                    <div class="mb-4">
                        <label class="form-label">Mode de paiement *</label>
                        <div class="row g-3">
                            <div class="col-3">
                                <div class="mode-card" data-mode="ESPECE">
                                    <i class="bi bi-cash text-success"></i>
                                    <span class="small">Espèces</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mode-card" data-mode="CHEQUE">
                                    <i class="bi bi-card-text text-info"></i>
                                    <span class="small">Chèque</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mode-card" data-mode="VIREMENT">
                                    <i class="bi bi-bank text-primary"></i>
                                    <span class="small">Virement</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mode-card" data-mode="MOBILE">
                                    <i class="bi bi-phone text-warning"></i>
                                    <span class="small">Mobile</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="encMode" name="mode_paiement" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="encDate" name="encaissement_date" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Montant *</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="encMontant" name="montant" step="1" required>
                                <span class="input-group-text">Ar</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Référence</label>
                            <input type="text" class="form-control" id="encReference" name="reference_paiement" placeholder="N° chèque, réf...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
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
    $('#encaissementsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        order: [[0, 'desc']]
    });

    $('#encFacture').on('change', function() {
        const selected = $(this).find(':selected');
        if (!selected.val()) {
            $('#infoFacture').hide();
            return;
        }
        
        const reste = parseFloat(selected.data('reste') || 0);
        $('#infoClient').text(selected.data('client') || '-');
        $('#infoMontant').text(parseInt(selected.data('montant') || 0).toLocaleString('fr-FR') + ' Ar');
        $('#infoReste').text(parseInt(reste).toLocaleString('fr-FR') + ' Ar');
        $('#encMontant').val(Math.round(reste)).attr('max', reste);
        $('#infoFacture').show();
    });

    // Mode selection
    $('.mode-card').on('click', function() {
        $('.mode-card').removeClass('selected');
        $(this).addClass('selected');
        $('#encMode').val($(this).data('mode'));
    });
});

$('#formEncaissement').on('submit', function(e) {
    e.preventDefault();
    
    if (!$('#encMode').val()) {
        alert('Veuillez sélectionner un mode de paiement');
        return;
    }
    
    $.ajax({
        url: `${BASE_URL}/ventes/encaissements`,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            id_facture: parseInt($('#encFacture').val()),
            encaissement_date: $('#encDate').val(),
            montant: parseFloat($('#encMontant').val()),
            mode_paiement: $('#encMode').val(),
            reference_paiement: $('#encReference').val(),
            cree_par: 1
        }),
        success: () => location.reload(),
        error: (xhr) => alert(xhr.responseJSON?.error || 'Erreur')
    });
});
</script>
</body>
</html>
