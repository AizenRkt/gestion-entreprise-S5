<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventes - Clients</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        :root { --primary: #2563eb; --success: #10b981; --warning: #f59e0b; --danger: #ef4444; --gray: #6b7280; }
        body { background: #f8fafc; }
        .page-title { font-size: 1.5rem; font-weight: 600; color: #1e293b; }
        .breadcrumb { font-size: 0.875rem; }
        .stat-mini { background: white; border-radius: 10px; padding: 1rem; border: 1px solid #e2e8f0; text-align: center; }
        .stat-mini-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; }
        .stat-mini-label { font-size: 0.75rem; color: var(--gray); }
        .card-minimal { background: white; border-radius: 12px; border: 1px solid #e2e8f0; }
        .card-minimal .card-body { padding: 1.25rem; }
        .table th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray); font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        .table td { vertical-align: middle; font-size: 0.875rem; }
        .avatar-sm { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem; }
        .badge-type { font-size: 0.7rem; padding: 0.25rem 0.5rem; border-radius: 4px; }
        .btn-action { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
        .modal-minimal .modal-content { border-radius: 12px; border: none; }
        .modal-minimal .modal-header { border-bottom: 1px solid #f1f5f9; }
        .modal-minimal .modal-footer { border-top: 1px solid #f1f5f9; }
        .form-label { font-size: 0.8rem; font-weight: 500; color: #475569; }
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
                    <h1 class="page-title">Clients</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/ventes" class="text-decoration-none">Ventes</a></li>
                            <li class="breadcrumb-item active">Clients</li>
                        </ol>
                    </nav>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalClient">
                    <i class="bi bi-plus"></i> Nouveau
                </button>
            </div>

            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-mini">
                        <div class="stat-mini-value"><?= count($clients ?? []) ?></div>
                        <div class="stat-mini-label">Total</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-mini">
                        <div class="stat-mini-value text-info"><?= count(array_filter($clients ?? [], fn($c) => ($c['id_type'] ?? 0) == 1)) ?></div>
                        <div class="stat-mini-label">Particuliers</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-mini">
                        <div class="stat-mini-value text-success"><?= count(array_filter($clients ?? [], fn($c) => ($c['id_type'] ?? 0) == 2)) ?></div>
                        <div class="stat-mini-label">Professionnels</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-mini">
                        <div class="stat-mini-value text-warning"><?= count(array_filter($clients ?? [], fn($c) => ($c['id_type'] ?? 0) == 3)) ?></div>
                        <div class="stat-mini-label">Grossistes</div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card-minimal">
                <div class="card-body">
                    <table class="table table-hover mb-0" id="clientsTable">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>Type</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (($clients ?? []) as $client): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-primary text-white"><?= strtoupper(substr($client['nom'] ?? 'C', 0, 1)) ?></div>
                                            <div>
                                                <div class="fw-medium"><?= htmlspecialchars($client['nom'] ?? '') ?></div>
                                                <small class="text-muted">#<?= $client['id_client'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-telephone me-1 text-muted"></i><?= htmlspecialchars($client['telephone'] ?? '-') ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($client['email'] ?? '') ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                        $typeColors = [1 => 'bg-info', 2 => 'bg-success', 3 => 'bg-warning', 4 => 'bg-secondary'];
                                        $color = $typeColors[$client['id_type'] ?? 0] ?? 'bg-light text-dark';
                                        ?>
                                        <span class="badge badge-type <?= $color ?>"><?= htmlspecialchars($client['type_libelle'] ?? 'N/A') ?></span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-outline-primary btn-action" onclick="editClient(<?= $client['id_client'] ?>)"><i class="bi bi-pencil"></i></button>
                                        <a href="<?= Flight::base() ?>/ventes/commandes?client=<?= $client['id_client'] ?>" class="btn btn-outline-success btn-action"><i class="bi bi-cart"></i></a>
                                        <button class="btn btn-outline-danger btn-action" onclick="deleteClient(<?= $client['id_client'] ?>)"><i class="bi bi-trash"></i></button>
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

<!-- Modal Client -->
<div class="modal fade modal-minimal" id="modalClient" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modalClientTitle">Nouveau client</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formClient">
                <div class="modal-body">
                    <input type="hidden" id="clientId" name="id_client">
                    <div class="mb-3">
                        <label class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="clientNom" name="nom" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="clientTel" name="telephone">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="clientEmail" name="email">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" id="clientType" name="id_type">
                            <option value="">-- Sélectionner --</option>
                            <?php foreach (($types ?? []) as $type): ?>
                                <option value="<?= $type['id_client_type'] ?>"><?= htmlspecialchars($type['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adresse</label>
                        <textarea class="form-control" id="clientAdresse" name="adresse" rows="2"></textarea>
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
    $('#clientsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json' },
        order: [[0, 'asc']], pageLength: 10
    });
});

$('#formClient').on('submit', function(e) {
    e.preventDefault();
    const id = $('#clientId').val();
    $.ajax({
        url: id ? `${BASE_URL}/ventes/clients/${id}` : `${BASE_URL}/ventes/clients`,
        type: id ? 'PUT' : 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            nom: $('#clientNom').val(),
            telephone: $('#clientTel').val(),
            email: $('#clientEmail').val(),
            adresse: $('#clientAdresse').val(),
            id_type: $('#clientType').val() || null
        }),
        success: () => location.reload(),
        error: (xhr) => alert(xhr.responseJSON?.error || 'Erreur')
    });
});

function editClient(id) {
    $.get(`${BASE_URL}/ventes/clients/${id}`, function(c) {
        $('#modalClientTitle').text('Modifier client');
        $('#clientId').val(c.id_client);
        $('#clientNom').val(c.nom);
        $('#clientTel').val(c.telephone);
        $('#clientEmail').val(c.email);
        $('#clientAdresse').val(c.adresse);
        $('#clientType').val(c.id_type);
        $('#modalClient').modal('show');
    });
}

function deleteClient(id) {
    if (confirm('Supprimer ce client ?')) {
        $.ajax({ url: `${BASE_URL}/ventes/clients/${id}`, type: 'DELETE', success: () => location.reload() });
    }
}

$('#modalClient').on('hidden.bs.modal', () => { $('#formClient')[0].reset(); $('#clientId').val(''); $('#modalClientTitle').text('Nouveau client'); });
</script>
</body>
</html>
