<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Filtre - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/styles/choices.css">

    <!-- datatables -->
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
</head>

<body>

    <script src="<?= Flight::base() ?>/public/template/assets/static/js/initTheme.js"></script>
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
                            <h3>Filtrer des cv</h3>
                            <p class="text-subtitle text-muted">Cherchez et filtrer des cv dans la base</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first text-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                                Trouver une cv
                            </button>
                        </div>
                    </div>
                </div>
                <section class="basic-choices">
                    <form method="post" action="<?= Flight::base() ?>/backOffice/candidat/filter">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Choix</h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold">Genre</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="genre" id="homme" value="Homme">
                                                        <label class="form-check-label" for="homme">Homme</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="genre" id="femme" value="Femme">
                                                        <label class="form-check-label" for="femme">Femme</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="ageMin">Âge minimum</label>
                                                        <input type="number" class="form-control" id="ageMin" name="age_min" placeholder="min" min="18" max="65">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="ageMax">Âge maximum</label>
                                                        <input type="number" class="form-control" id="ageMax" name="age_max" placeholder="max" min="18" max="65">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-4">
                                                    <h6>Diplomes</h6>
                                                    <div class="form-group">
                                                        <select class="choices form-select" name="diplome">
                                                            <option value="" disabled selected hidden>Choisissez un diplôme</option>
                                                            <option value="" disabled>Aucun diplôme</option>
                                                            <?php if (isset($diplomes) && is_array($diplomes)): ?>
                                                                <?php foreach ($diplomes as $diplome): ?>
                                                                    <option value="<?= htmlspecialchars($diplome['id_diplome']) ?>">
                                                                        <?= htmlspecialchars($diplome['nom']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <option disabled>Aucun diplôme disponible</option>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-4">
                                                    <h6>Competences</h6>
                                                    <div class="form-group">
                                                        <select class="choices form-select" name="competences[]" multiple="multiple">
                                                            <?php if (isset($competences) && is_array($competences)): ?>
                                                                <?php foreach ($competences as $competence): ?>
                                                                    <option value="<?= htmlspecialchars($competence['id_competence']) ?>">
                                                                        <?= htmlspecialchars($competence['nom']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <option disabled>Aucune compétence disponible</option>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="col-md-2 mb-4">
                                                    <h6>Situation</h6>
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="d-inline-block me-2 mb-1">
                                                            <div class="form-check">
                                                                <div class="checkbox">
                                                                    <input type="checkbox" class="form-check-input" id="statutEligible" name="statut" value="eligible" <?= (!empty($filters['statut']) && $filters['statut'] === 'eligible') ? 'checked' : '' ?>>
                                                                    <label for="statutEligible">eligible</label>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="d-inline-block me-2 mb-1">
                                                            <div class="form-check">
                                                                <div class="checkbox">
                                                                    <input type="checkbox" class="form-check-input" id="statutSousContrat" name="statut" value="sous-contrat" <?= (!empty($filters['statut']) && $filters['statut'] === 'sous-contrat') ? 'checked' : '' ?>>
                                                                    <label for="statutSousContrat">sous-contrat</label>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>


                                                </div>
                                                <div class="col-md-2 mb-4">
                                                    <h6>Ville</h6>
                                                    <fieldset class="form-group">
                                                        <select class="form-select" id="basicSelect" name="ville">
                                                            <option value="" disabled selected hidden>Choisissez une ville</option>
                                                            <option value="" disabled>Aucune ville</option>
                                                            <?php if (isset($villes) && is_array($villes)): ?>
                                                                <?php foreach ($villes as $ville): ?>
                                                                    <option value="<?= htmlspecialchars($ville['id_ville']) ?>">
                                                                        <?= htmlspecialchars($ville['nom']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <option disabled>Aucune ville disponible</option>
                                                            <?php endif; ?>
                                                        </select>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Date de candidature , avant:</label>
                                                    <input type="date" name="date_naissance" class="form-control" placeholder="Date de naissance">
                                                </div>
                                                <div class="col-md-4">
                                                    <h6>Profil</h6>
                                                    <div class="form-group">
                                                        <select class="choices form-select multiple-remove" name="profils[]" multiple="multiple">
                                                            <?php if (isset($profils) && is_array($profils)): ?>
                                                                <?php foreach ($profils as $profil): ?>
                                                                    <option value="<?= htmlspecialchars($profil['id_profil']) ?>">
                                                                        <?= htmlspecialchars($profil['nom']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <option disabled>Aucun profil disponible</option>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="col-sm-12 d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary me-1 mb-1">Valider</button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                    </form>
                </section>


                <!-- Tableau des candidats -->
                <section class="section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Tous les cv</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table" id="table1">
                                    <thead>
                                        <tr>
                                            <th>Photo</th>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Age</th>
                                            <th>Email</th>
                                            <th>Genre</th>
                                            <th>Date candidature</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($candidats) && is_array($candidats)): ?>
                                            <?php foreach ($candidats as $candidat): ?>
                                                <tr>
                                                    <?php
                                                    // Calculer l'âge à partir de la date de naissance
                                                    $age = '-';
                                                    if (!empty($candidat['date_naissance'])) {
                                                        $dob = new DateTime($candidat['date_naissance']);
                                                        $now = new DateTime();
                                                        $age = $now->diff($dob)->y;
                                                    }
                                                    ?>
                                                    <td>
                                                        <?php if (!empty($photos[$candidat['id_candidat']])): ?>
                                                            <div class="avatar bg-warning me-3">
                                                                <img src="<?= Flight::base() ?>/public/uploads/photos/<?= htmlspecialchars($photos[$candidat['id_candidat']]) ?>" alt="" srcset="">
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($candidat['nom']) ?></td>
                                                    <td><?= htmlspecialchars($candidat['prenom']) ?></td>
                                                    <td><?= htmlspecialchars($age) ?></td>
                                                    <td><?= htmlspecialchars($candidat['email']) ?></td>
                                                    <td><?= htmlspecialchars($candidat['genre']) ?></td>
                                                    <td><?= htmlspecialchars($candidat['date_candidature']) ?></td>
                                                    <td>
                                                        <?php
                                                        $statut = isset($statuts[$candidat['id_candidat']]) ? $statuts[$candidat['id_candidat']] : 'Archivé';
                                                        $badgeClass = 'bg-secondary';
                                                        if ($statut === 'Sous-contrat') $badgeClass = 'bg-success';
                                                        elseif ($statut === 'Révision') $badgeClass = 'bg-primary';
                                                        elseif ($statut === 'Refusé') $badgeClass = 'bg-danger';
                                                        ?>
                                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statut) ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8">Aucun candidat trouvé.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <!-- JQuery doit être chargé avant ton script -->
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/simple-datatables.js"></script>


    <script src="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/form-element-select.js"></script>

    <script>
    $(document).ready(function() {
        // Rendre les cases 'eligible' et 'sous-contrat' mutuellement exclusives
        $('#statutEligible').on('change', function() {
            if ($(this).is(':checked')) {
                $('#statutSousContrat').prop('checked', false);
            }
        });
        $('#statutSousContrat').on('change', function() {
            if ($(this).is(':checked')) {
                $('#statutEligible').prop('checked', false);
            }
        });
    });
    </script>


</body>

</html>