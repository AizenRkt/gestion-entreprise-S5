<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Jours Fériés - Mazer</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">
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
        
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Gestion des Jours Fériés</h3>
                        <p class="text-subtitle text-muted">Ajoutez, modifiez ou supprimez les jours fériés.</p>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire d'ajout -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ajouter un jour férié</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?= Flight::base() ?>/backOffice/jourFerie/create" method="post">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">Date</label>
                                        <input type="date" class="form-control" id="date" name="date" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <input type="text" class="form-control" id="description" name="description" placeholder="Ex: Fête du Travail" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="recurrence" class="form-label">Récurrence</label>
                                        <select class="form-select" id="recurrence" name="recurrence" required>
                                            <option value="annuel">Annuel</option>
                                            <option value="fixe">Fixe</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Ajouter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Liste des jours fériés -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Liste des jours fériés</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Récurrence</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($joursFeries) && !empty($joursFeries)): ?>
                                    <?php foreach ($joursFeries as $jourFerie): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($jourFerie['date']) ?></td>
                                        <td><?= htmlspecialchars($jourFerie['description']) ?></td>
                                        <td><?= htmlspecialchars(ucfirst($jourFerie['recurrence'])) ?></td>
                                        <td>
                                            <a href="<?= Flight::base() ?>/backOffice/jourFerie/delete/<?= $jourFerie['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr ?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Aucun jour férié trouvé.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
</body>
</html>