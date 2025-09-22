<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat QCM - Mazer</title>
    
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">

    <!-- datatables -->
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
</head>

<body>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/initTheme.js"></script>
<div id="app">
    <?= Flight::menuBackOffice() ?>
    <div id="main">
        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Résultats QCM</h5>
                    <form method="get" action="">
                        <select name="id_qcm" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Choisir un QCM --</option>
                            <?php foreach($qcm as $q): ?>
                                <option value="<?= $q['id_qcm'] ?>" 
                                    <?= (isset($_GET['id_qcm']) && $_GET['id_qcm'] == $q['id_qcm']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($q['titre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <?php if (!empty($candidat)): ?>
                        <div class="table-responsive">
                            <table class="table" id="table1">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Score</th>
                                        <th>Note max</th>
                                        <th>Moyenne</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($candidat as $c): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($c['nom']) ?></td>
                                            <td><?= htmlspecialchars($c['prenom']) ?></td>
                                            <td><?= $c['valeur'] ?></td>
                                            <td><?= $c['note_max'] ?></td>
                                            <td><?= $c['moyenne'] ?></td>
                                            <td>
                                                <a href="<?= Flight::base() ?>/organiserEntretien?id_candidat=<?= $c['id_candidat'] ?>" class="btn btn-sm btn-primary">
                                                    Organiser entretien
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Aucun résultat pour ce QCM.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>    
</body>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/simple-datatables.js"></script>

</html>
