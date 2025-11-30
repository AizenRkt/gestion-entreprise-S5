<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertes Employés - Mazer</title>
    
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
</head>

<body>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/initTheme.js"></script>

<div id="app">
    <?= Flight::menuBackOffice() ?>
    <div id="main">
        <section class="section">
            <div class="row">
                <!-- Alertes Employés -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Alertes Employés</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($alertes)): ?>
                                <ul class="list-group">
                                    <?php foreach ($alertes as $alerte): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong><?= htmlspecialchars($alerte['nom'] . ' ' . $alerte['prenom']) ?></strong>
                                                <div>
                                                    <?php if ($alerte['contrat'] !== null): ?>
                                                        <?php
                                                        $fin = $alerte['contrat'];
                                                        if ($fin === null) {
                                                            $text = 'CDI';
                                                            $class = 'badge bg-success';
                                                        } else {
                                                            $diff = (new \DateTime($fin))->diff(new \DateTime());
                                                            $jours = $diff->invert ? -$diff->days : $diff->days;
                                                            if ($jours <= 0) {
                                                                $text = 'Expiré il y a ' . abs($jours) . ' jour(s)';
                                                                $class = 'badge bg-danger';
                                                            } elseif ($jours <= 30) {
                                                                $text = $jours . ' jour(s)';
                                                                $class = 'badge bg-danger';
                                                            } elseif ($jours <= 90) {
                                                                $text = $jours . ' jour(s)';
                                                                $class = 'badge bg-warning';
                                                            } elseif ($jours <= 180) {
                                                                $text = $jours . ' jour(s)';
                                                                $class = 'badge bg-info';
                                                            } else {
                                                                $text = $jours . ' jour(s)';
                                                                $class = 'badge bg-success';
                                                            }
                                                        }
                                                        ?>
                                                        <span class="badge me-2 <?= $class ?>"><?= $text ?> (Contrat)</span>
                                                    <?php else: ?>
                                                        <span class="badge me-2 bg-success">CDI (Contrat)</span>
                                                    <?php endif; ?>
                                                    <?php if ($alerte['conge'] !== null): ?>
                                                        <?php
                                                        $jours = $alerte['conge'];
                                                        if ($jours <= 5) {
                                                            $class = 'badge bg-danger';
                                                        } else {
                                                            $class = 'badge bg-warning';
                                                        }
                                                        ?>
                                                        <span class="badge <?= $class ?>"><?= $jours ?> jour(s) (Congé)</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">Aucune alerte.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

</body>
</html>
