<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une annonce - Mazer</title>
    
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">

    <!-- datatables -->
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
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
                        <h3>Détail de l'annonce</h3>
                        <p class="text-subtitle text-muted">Informations complètes sur l'offre d'emploi</p>
                    </div>
                    
                </div>
            </div>
            <?php foreach($annonces as $annonce): ?>
            <section class="section">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="btn-group" role="group">
                            <a href="<?= Flight::base() ?>/annonceListe">
                                <button type="button" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Retour
                                </button>
                            </a>
                            <?php if ((($annonce['valeur'])==null)or(($annonce['valeur'])=='renouvellement')) {?> 
                                <a href="<?= Flight::base() ?>/annonceretrait2?id=<?= $annonce['id_annonce'] ?>"><button type="button" class="btn btn-outline-danger">
                                Retirer
                                </button></a>
                            <?php } else { ?>
                                <a href="<?= Flight::base() ?>/annoncerenouvellement2?id=<?= $annonce['id_annonce'] ?>"><button type="button" class="btn btn-outline-success">
                                Renouveller
                                </button></a>
                            <?php } ?>
                            
                            
                        </div>
                    </div>
                </div>
            </section>
            <?php endforeach; ?>
        </div>

        <section class="section">
            <?php foreach($annonces as $annonce): ?>
            <div class="row">
                <!-- Informations principales -->
                <div class="col-lg-8 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="card-title"><?= htmlspecialchars($annonce['titre']) ?></h1>
                                <?php if ($annonce['valeur'] != null): ?>
                                    <span class="badge bg-light-secondary"><?= htmlspecialchars($annonce['valeur']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-light-success">Actif</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-muted mt-2">
                                <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($annonce['ville']) ?> •
                                <i class="bi bi-calendar"></i> <?= date('d/m/Y', strtotime($annonce['date_debut'])) ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Objectif -->
                            <div class="divider">
                                <div class="divider-text">
                                    <i class="bi bi-check-square"></i> Objectif 
                                </div>
                            </div>
                            <div class="alert alert-light-primary">
                                <h4 class="alert-heading">
                                    <i class="bi bi-check2-square"></i> Objectif de la recherche
                                </h4>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($annonce['objectif'])) ?></p>
                            </div>

                            <!-- Compétences -->
                            <div class="divider">
                                <div class="divider-text">
                                    <i class="bi bi-gear"></i> Compétences requises
                                </div>
                            </div>
                            <div class="alert alert-light-success">
                                <h4 class="alert-heading">
                                    <i class="bi bi-lightbulb"></i> Compétences techniques
                                </h4>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($annonce['competences'])) ?></p>
                            </div>

                            <!-- Diplômes -->
                            <div class="divider">
                                <div class="divider-text">
                                    <i class="bi bi-mortarboard"></i> Diplômes nécessaires
                                </div>
                            </div>
                            <div class="alert alert-light-warning">
                                <h4 class="alert-heading">
                                    <i class="bi bi-award"></i> Formation requise
                                </h4>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($annonce['diplomes'])) ?></p>
                            </div>

                            <!-- Qualités -->
                            <div class="divider">
                                <div class="divider-text">
                                    <i class="bi bi-star"></i> Qualités recherchées
                                </div>
                            </div>
                            <div class="alert alert-light-danger">
                                <h4 class="alert-heading">
                                    <i class="bi bi-person-check"></i> Profil idéal
                                </h4>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($annonce['qualite'])) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-lg-3 col-md-6">
                            
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            
                        </div>
                        <div class="col-6 col-lg-3 col-md-6">
                            
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4 col-md-12">
                    <!-- Informations clés -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Informations clés</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-light-primary me-3">
                                                <i class="bi bi-calendar2-range"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Période</h6>
                                                <p class="text-muted mb-0">
                                                    <?= date('d/m/Y', strtotime($annonce['date_debut'])) ?> - 
                                                    <?= date('d/m/Y', strtotime($annonce['date_fin'])) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-light-success me-3">
                                                <i class="bi bi-geo-alt"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Localisation</h6>
                                                <p class="text-muted mb-0"><?= htmlspecialchars($annonce['ville']) ?></p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-light-warning me-3">
                                                <i class="bi bi-flag"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Statut</h6>
                                                <p class="text-muted mb-0">
                                                    <?php if ($annonce['valeur'] != null): ?>
                                                        <?= htmlspecialchars($annonce['valeur']) ?>
                                                    <?php else: ?>
                                                        Actif
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Historique</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-point timeline-point-primary"></div>
                                        <div class="timeline-event">
                                            <div class="timeline-heading">
                                                <h6 class="timeline-title">Annonce créée</h6>
                                            </div>
                                            <p><?= date('d/m/Y', strtotime($annonce['date_debut'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-point timeline-point-success"></div>
                                        <div class="timeline-event">
                                            <div class="timeline-heading">
                                                <h6 class="timeline-title">Annonce publiée</h6>
                                            </div>
                                            <p><?= date('d/m/Y', strtotime($annonce['date_debut'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="timeline-item">
                                        <div class="timeline-point timeline-point-warning"></div>
                                        <div class="timeline-event">
                                            <div class="timeline-heading">
                                                <h6 class="timeline-title">Fin prévue</h6>
                                            </div>
                                            <p><?= date('d/m/Y', strtotime($annonce['date_fin'])) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </section>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/simple-datatables.js"></script>

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