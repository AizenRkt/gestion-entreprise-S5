<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>liste des employés - Mazer</title>
    
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">
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
                    <div class="card-header">
                        <h5 class="card-title">
                            Liste des employes 
                        </h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalRecrutement">
                            Recruter un employé
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Formulaire de filtrage -->
                        <form method="post" action="">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Genre</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="genre[]" value="M" id="genreM">
                                        <label class="form-check-label" for="genreM">Masculin</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="genre[]" value="F" id="genreF">
                                        <label class="form-check-label" for="genreF">Féminin</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Date d'embauche début</label>
                                    <input type="date" class="form-control" name="date_debut">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">Date d'embauche fin</label>
                                    <input type="date" class="form-control" name="date_fin">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Département</label>
                                    <select class="form-select" name="id_dept">
                                        <option value="">Tous</option>
                                        <?php if (isset($departements) && is_array($departements)): ?>
                                            <?php foreach ($departements as $dept): ?>
                                                <option value="<?= $dept['id_dept'] ?>"><?= htmlspecialchars($dept['nom']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Service</label>
                                    <select class="form-select" name="id_service">
                                        <option value="">Tous</option>
                                        <?php if (isset($services) && is_array($services)): ?>
                                            <?php foreach ($services as $serv): ?>
                                                <option value="<?= $serv['id_service'] ?>"><?= htmlspecialchars($serv['nom']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                                    <a href="" class="btn btn-secondary">Réinitialiser</a>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div class="table-responsive">
                            <table class="table" id="table1">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Genre</th>
                                        <th>Département</th>
                                        <th>Service</th>
                                        <th>Poste</th>
                                        <th>Date d'embauche</th>
                                        <!--<th>Fin de contrat</th>
                                        <th>Congés non pris</th>-->
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($employes) && is_array($employes)): ?>
                                        <?php foreach ($employes as $emp): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($emp['nom'] . ' ' . $emp['prenom']) ?></td>
                                                <td><?= htmlspecialchars($emp['email']) ?></td>
                                                <td><?= htmlspecialchars($emp['telephone']) ?></td>
                                                <td><?= htmlspecialchars($emp['genre']) ?></td>
                                                <td><?= htmlspecialchars($emp['dept_nom']) ?></td>
                                                <td><?= htmlspecialchars($emp['service_nom']) ?></td>
                                                <td><?= htmlspecialchars($emp['poste_titre']) ?></td>
                                                <td><?= htmlspecialchars($emp['date_embauche']) ?></td>
                                                    <!--<td>
                                                        <?php
                                                        $fin = $emp['contrat_fin'];
                                                        if ($fin) {
                                                            $now = new DateTime();
                                                            $end = new DateTime($fin);
                                                            if ($end > $now) {
                                                                $interval = $now->diff($end);
                                                                $days = $interval->days;
                                                                if ($days <= 30) {
                                                                    $text = $days . ' jour(s) restant(s)';
                                                                } elseif ($days <= 365) {
                                                                    $months = floor($days / 30);
                                                                    $text = $months . ' mois restant(s)';
                                                                } else {
                                                                    $years = floor($days / 365);
                                                                    $text = $years . ' an(s) restant(s)';
                                                                }
                                                                if ($days <= 30) $class = 'text-danger fw-bold';
                                                                elseif ($days <= 90) $class = 'text-warning';
                                                                elseif ($days <= 180) $class = 'text-info';
                                                                else $class = 'text-success';
                                                            } else {
                                                                $text = 'Expiré';
                                                                $class = 'text-danger fw-bold';
                                                            }
                                                        } else {
                                                            $text = 'CDI';
                                                            $class = 'text-success fw-bold';
                                                        }
                                                        ?>
                                                        <span class="<?= $class ?>"><?= $text ?></span>                              
                                                </td>-->
                                                <!--<td>
                                                        
                                                        <?php
                                                        $joursRestants = \app\models\ressourceHumaine\employe\EmployeModel::getCongesNonPris($emp['id_employe']);
                                                        if ($joursRestants > 0) {
                                                            $class = ($joursRestants < 5) ? 'text-danger fw-bold' : 'text-warning';
                                                            echo "<span class='$class'>$joursRestants jour(s) restant(s)</span>";
                                                        } else {
                                                            echo "<span class='text-success'>Tous pris</span>";
                                                        }
                                                        ?>
                                                        
                                                </td>-->
                                                <td>
                                                    <?php if (isset($emp['activite']) && $emp['activite'] == 1): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn icon btn-primary" data-bs-toggle="modal" data-bs-target="#editModal-<?= $emp['id_employe'] ?>"><i class="bi bi-pencil"></i></button>
                                                    <a href="<?= Flight::base() ?>/ficheEmploye?id=<?= $emp['id_employe'] ?>"><button type="button" class="btn icon btn-primary"><i class="bi bi-eye"></i></button></a>
                                                    <button type="button"
                                                        class="btn icon btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalChangePoste-<?= $emp['id_employe'] ?>">
                                                        <i class="bi bi-briefcase"></i> changer poste
                                                    </button>                                                        
                                                </td>
                                            </tr>

                                            <!-- Modal for editing employee -->
                                            <div class="modal fade" id="editModal-<?= $emp['id_employe'] ?>" tabindex="-1" aria-labelledby="editModalLabel-<?= $emp['id_employe'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editModalLabel-<?= $emp['id_employe'] ?>">Modifier l'employé</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="<?= Flight::base() ?>/employe/update" method="post">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id_employe" value="<?= $emp['id_employe'] ?>">
                                                                <div class="mb-3">
                                                                    <label for="nom" class="form-label">Nom</label>
                                                                    <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($emp['nom']) ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="prenom" class="form-label">Prénom</label>
                                                                    <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($emp['prenom']) ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="email" class="form-label">Email</label>
                                                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($emp['email']) ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="telephone" class="form-label">Téléphone</label>
                                                                    <input type="text" class="form-control" name="telephone" value="<?= htmlspecialchars($emp['telephone']) ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="id_poste" class="form-label">Poste</label>
                                                                    <select class="form-select" name="id_poste">
                                                                        <?php if (isset($postes) && is_array($postes)): ?>
                                                                            <?php foreach ($postes as $poste): ?>
                                                                                <option value="<?= $poste['id_poste'] ?>" <?= ($poste['id_poste'] == $emp['id_poste']) ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($poste['titre']) ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="genre" class="form-label">Genre</label>
                                                                    <select class="form-select" name="genre">
                                                                        <option value="M" <?= ($emp['genre'] == 'M') ? 'selected' : '' ?>>Masculin</option>
                                                                        <option value="F" <?= ($emp['genre'] == 'F') ? 'selected' : '' ?>>Féminin</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="date_embauche" class="form-label">Date d'embauche</label>
                                                                    <input type="date" class="form-control" name="date_embauche" value="<?= htmlspecialchars($emp['date_embauche']) ?>">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="activite" class="form-label">Statut</label>
                                                                    <select class="form-select" name="activite">
                                                                        <option value="1" <?= ($emp['activite'] == 1) ? 'selected' : '' ?>>Active</option>
                                                                        <option value="0" <?= ($emp['activite'] == 0) ? 'selected' : '' ?>>Inactive</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal : Changer de poste -->
                                            <div class="modal fade" id="modalChangePoste-<?= $emp['id_employe'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Changer le poste — <?= htmlspecialchars($emp['nom'] . ' ' . $emp['prenom']) ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <input type="hidden" id="empId-<?= $emp['id_employe'] ?>" value="<?= $emp['id_employe'] ?>">

                                                            <div class="mb-3">
                                                                <label class="form-label">Nouveau poste</label>
                                                                <select id="newPoste-<?= $emp['id_employe'] ?>" class="form-select">
                                                                    <option value="">--Sélectionner--</option>
                                                                    <?php foreach ($postes as $pst): ?>
                                                                        <option value="<?= $pst['id_poste'] ?>" <?= ($pst['id_poste'] == $emp['id_poste']) ? 'selected' : '' ?>>
                                                                            <?= htmlspecialchars($pst['titre']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>

                                                            <hr>

                                                            <div class="mb-3">
                                                                <label class="form-label">Type de contrat</label>
                                                                <select id="newContratType-<?= $emp['id_employe'] ?>" class="form-select">
                                                                    <option value="CDI">CDI</option>
                                                                    <option value="CDD">CDD</option>
                                                                </select>
                                                            </div>

                                                            <div class="row g-2">
                                                                <div class="col-6 mb-3">
                                                                    <label class="form-label">Date début</label>
                                                                    <input type="date" id="newDateDebut-<?= $emp['id_employe'] ?>" class="form-control">
                                                                </div>

                                                                <div class="col-6 mb-3">
                                                                    <label class="form-label">Date fin (si CDD)</label>
                                                                    <input type="date" id="newDateFin-<?= $emp['id_employe'] ?>" class="form-control">
                                                                </div>

                                                                <div class="col-6 mb-3">
                                                                    <label class="form-label">Salaire de base</label>
                                                                    <input type="number" id="newSalaire-<?= $emp['id_employe'] ?>" class="form-control" step="0.01" min="0">
                                                                </div>

                                                                <div class="col-6 mb-3">
                                                                    <label class="form-label">Date signature</label>
                                                                    <input type="date" id="newDateSignature-<?= $emp['id_employe'] ?>" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="form-text">Remplis la partie contrat si tu veux créer un contrat associé au changement de poste.</div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <button id="btnChangePoste-<?= $emp['id_employe'] ?>" class="btn btn-warning"
                                                                onclick="changerPoste(<?= $emp['id_employe'] ?>)">Valider</button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- modal recrutement -->
    <div class="modal fade" id="modalRecrutement" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">Recrutement d'un employé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Stepper -->
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="step-employe-tab" data-bs-toggle="pill"
                    data-bs-target="#step-employe" type="button" role="tab">
                    1. Infos Employé
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="step-contrat-tab" data-bs-toggle="pill"
                    data-bs-target="#step-contrat" type="button" role="tab">
                    2. Infos Contrat
                    </button>
                </li>
                </ul>

                <div class="tab-content">

                <!-- ÉTAPE 1 -->
                <div class="tab-pane fade show active" id="step-employe" role="tabpanel">
                    <div class="row">
                    <div class="col-md-6">
                        <label>Nom</label>
                        <input type="text" id="emp_nom" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Prénom</label>
                        <input type="text" id="emp_prenom" class="form-control">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Email</label>
                        <input type="email" id="emp_email" class="form-control">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Téléphone</label>
                        <input type="text" id="emp_tel" class="form-control">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Genre</label>
                        <select id="emp_genre" class="form-control">
                        <option value="">Choisir</option>
                        <option value="M">Homme</option>
                        <option value="F">Femme</option>
                        </select>
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Date de naissance</label>
                        <input type="date" id="emp_date_naissance" class="form-control">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Date d'embauche</label>
                        <input type="date" id="emp_date_embauche" class="form-control">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Poste</label>
                        <select id="emp_poste" name="id_poste" class="form-select">
                            <?php if (isset($postes) && is_array($postes)): ?>
                                    <option value="">--Sélectionner un poste--</option>
                                <?php foreach ($postes as $poste): ?>
                                    <option value="<?= $poste['id_poste'] ?>" <?= ($poste['id_poste'] == $emp['id_poste']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($poste['titre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    </div>

                    <button class="btn btn-primary mt-3" onclick="goToContratStep()">Suivant →</button>
                </div>

                <!-- ÉTAPE 2 -->
                <div class="tab-pane fade" id="step-contrat" role="tabpanel">
                    <div class="row">
                    <div class="col-md-6">
                        <label>Type contrat</label>
                        <select id="ct_type" class="form-control">
                        <option value="CDI">CDI</option>
                        <option value="CDD">CDD</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Salaire de base</label>
                        <input type="number" id="ct_salaire" class="form-control">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Date début</label>
                        <input type="date" id="ct_debut" class="form-control">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Date fin (si CDD)</label>
                        <input type="date" id="ct_fin" class="form-control">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Date signature</label>
                        <input type="date" id="ct_signature" class="form-control">
                    </div>
                    </div>

                    <button class="btn btn-secondary mt-3" onclick="goToEmployeeStep()">← Retour</button>
                    <button class="btn btn-success mt-3 float-end" onclick="submitRecrutement()">Valider le recrutement</button>

                </div>
                </div>

            </div>

            </div>
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

    <!-- recruter un nouveau employé -->
    <!-- navigation -->
    <script>
        function goToContratStep() {
            document.getElementById("step-contrat-tab").click();
        }

        function goToEmployeeStep() {
            document.getElementById("step-employe-tab").click();
        }
    </script>

    <!-- soummision form -->
    <script>
        function submitRecrutement() {

            const data = {
                nom: document.getElementById("emp_nom").value,
                prenom: document.getElementById("emp_prenom").value,
                email: document.getElementById("emp_email").value,
                telephone: document.getElementById("emp_tel").value,
                genre: document.getElementById("emp_genre").value,
                date_embauche: document.getElementById("emp_date_embauche").value,
                date_naissance: document.getElementById("emp_date_naissance").value,
                id_poste: document.getElementById("emp_poste").value,

                type_contrat: document.getElementById("ct_type").value,
                date_debut: document.getElementById("ct_debut").value,
                date_fin: document.getElementById("ct_fin").value,
                salaire_base: document.getElementById("ct_salaire").value,
                date_signature: document.getElementById("ct_signature").value
            };

            fetch("<?= Flight::base() ?>/employe/recruter", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    alert("Employé recruté avec succès !");
                    location.reload();
                } else {
                    alert("Erreur : " + res.message);
                }
            })
            .catch(err => alert("Erreur serveur"));
        }
    </script>

    <!-- changer le poste d'un employé -->
    <script>
        async function changerPoste(id) {
            const btn = document.getElementById(`btnChangePoste-${id}`);
            btn.disabled = true;
            btn.textContent = "En cours...";

            const payload = {
                id_poste: document.getElementById(`newPoste-${id}`).value || null,
                type_contrat: document.getElementById(`newContratType-${id}`).value || null,
                date_debut: document.getElementById(`newDateDebut-${id}`).value || null,
                date_fin: document.getElementById(`newDateFin-${id}`).value || null,
                salaire_base: document.getElementById(`newSalaire-${id}`).value || null,
                date_signature: document.getElementById(`newDateSignature-${id}`).value || null
            };

            if (!payload.id_poste) {
                alert("Vous devez choisir un poste.");
                btn.disabled = false;
                btn.textContent = "Valider";
                return;
            }

            try {
                const res = await fetch(`<?= Flight::base() ?>/employe/${id}/changerPoste`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!res.ok) {
                    alert(data.message || "Erreur serveur");
                    btn.disabled = false;
                    btn.textContent = "Valider";
                    return;
                }

                if (!data.success) {
                    alert(data.message || "Échec");
                    btn.disabled = false;
                    btn.textContent = "Valider";
                    return;
                }

                const modalEl = document.getElementById(`modalChangePoste-${id}`);
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                alert("Poste changé avec succès.");
                location.reload();
            } catch (err) {
                console.error(err);
                alert("Erreur réseau ou serveur.");
                btn.disabled = false;
                btn.textContent = "Valider";
            }
        }
    </script>


</html>