<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi Clubs et Groupes</title>
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/styles/choices.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Styles existants + nouveaux styles -->
    <style>
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
            display: inline-block;
        }

        .day-full {
            background-color: #dc3545; /* Rouge pour jour plein */
        }

        .day-partial {
            background-color: #ffc107; /* Jaune pour heures disponibles */
        }

        .day-free {
            background-color: rgb(255, 255, 255); /* blanc */
            border: 1px solid #28a745; /* bordure verte fine */
        }

        /* Classes pour les jours du calendrier */
        .calendar-day.day-full {
            background-color: rgba(220, 53, 69, 0.2);
            border-color: #dc3545;
        }

        .calendar-day.day-partial {
            background-color: rgba(255, 193, 7, 0.2);
            border-color: #ffc107;
        }

        .calendar-day.day-free {
            background-color: rgba(40, 167, 69, 0.2);
            border-color: #28a745;
        }

        .calendar {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
        .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .day-name {
            padding: 0.75rem;
            text-align: center;
            font-weight: 600;
            border-right: 1px solid #dee2e6;
        }
        .day-name:last-child {
            border-right: none;
        }
        .calendar-body {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }
        .calendar-day {
            min-height: 80px;
            padding: 0.5rem;
            border-bottom: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            cursor: pointer;
            position: relative;
            transition: background-color 0.2s;
        }
        .calendar-day:hover {
            background-color: #f8f9fa;
        }
        .calendar-day:nth-child(7n) {
            border-right: none;
        }
        .calendar-day.selected {
            background-color: #e3f2fd;
            border-color: #2196f3;
        }

        .calendar-day.other-month {
            opacity: 0.5;
        }

        .availability-notification {
            position: absolute;
            top: 2px;
            right: 2px;
            background-color: #17a2b8;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            cursor: pointer;
        }
        .day-number {
            font-weight: 600;
        }
        .availability-notification {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        .time-slot {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
        }
        .slot-time {
            font-weight: 600;
            color: #495057;
        }
        .group-name {
            font-weight: 500;
        }
        .discipline {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .participants {
            font-size: 0.875rem;
            color: #28a745;
        }
        .available-time {
            padding: 0.25rem 0.5rem;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 0.25rem;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }

        /* Nouveaux styles pour les tabs */
        .nav-tabs .nav-link {
            color: #495057;
            border: 1px solid transparent;
            border-radius: 0.375rem 0.375rem 0 0;
        }
        .nav-tabs .nav-link.active {
            color: #007bff;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
        .tab-content {
            border: 1px solid #dee2e6;
            border-radius: 0 0.375rem 0.375rem 0.375rem;
            padding: 1rem;
            background-color: #fff;
        }

        /* Styles pour la liste des groupes */
        .group-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.375rem;
            transition: all 0.3s;
        }
        .group-card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transform: translateY(-1px);
        }
        .discipline-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .day-partial {
            background-color: #fff3cd; /* yellow */
        }

        .day-full {
            background-color: #f8d7da; /* red */
        }
        .calendar-day.day-free {
    background-color: rgba(40, 167, 69, 0.2); /* Light green for free days */
    border-color: #28a745;
}


    </style>
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
                <div class="d-flex justify-content-between align-items-center">
                    <h3>Planning des entretiens</h3>
                </div>
            </div>

            <div class="page-content">
                <div class="tab-content" id="clubTabsContent">
                    <!-- Onglet Planning -->
                    <div class="tab-pane fade show active" id="calendar-pane" role="tabpanel">
                        <section class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title">Planning Mensuel</h4>
                                        <div class="calendar-nav">
                                            <button class="btn btn-sm btn-outline-primary" onclick="changeMonth(-1)" title="Mois précédent">
                                                <i class="bi bi-chevron-left"></i>
                                            </button>
                                            <span id="currentMonth" class="mx-3 fw-bold">
                                            </span>
                                            <button class="btn btn-sm btn-outline-primary" onclick="changeMonth(1)" title="Mois suivant">
                                                <i class="bi bi-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <!-- Légende -->
                                        <div class="calendar-legend mb-4">
                                            <div class="d-flex gap-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="legend-color day-full me-2"></div>
                                                    <small>Jour plein</small>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="legend-color day-partial me-2"></div>
                                                    <small>Heures disponibles</small>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="legend-color day-free me-2"></div>
                                                    <small>Totalement libre</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Calendrier -->
                                            <div class="col-md-8">
                                                <div class="calendar">
                                                    <div class="calendar-header">
                                                        <div class="day-name">Lun</div>
                                                        <div class="day-name">Mar</div>
                                                        <div class="day-name">Mer</div>
                                                        <div class="day-name">Jeu</div>
                                                        <div class="day-name">Ven</div>
                                                        <div class="day-name">Sam</div>
                                                        <div class="day-name">Dim</div>
                                                    </div>
                                                    <div class="calendar-body" id="calendarBody">
                                                        <!-- Les jours seront générés par JavaScript -->
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Détails du jour sélectionné -->
                                            <div class="col-md-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Détails du jour</h5>
                                                        <div class="text-muted" id="selectedDate">Sélectionnez une date</div>
                                                    </div>

                                                    <div class="card-body" id="detailsContent">
                                                        <div class="occupied-slots">
                                                            <h6><i class="bi bi-clock me-1"></i> Créneaux occupés</h6>
                                                            <div id="occupiedSlots">
                                                                <!-- Chargé dynamiquement -->
                                                            </div>
                                                        </div>

                                                        <div class="available-slots mt-3" id="availableSlots" style="display: none;">
                                                            <h6><i class="bi bi-plus-circle me-1"></i> Créneaux disponibles</h6>
                                                            <div id="availableList">
                                                                <!-- Chargé dynamiquement -->
                                                            </div>
                                                        </div>

                                                        <div class="empty-day text-center" id="emptyDay" style="display: none;">
                                                            <i class="bi bi-calendar-x text-muted" style="font-size: 2rem;"></i>
                                                            <h6 class="mt-2">Aucune activité programmée</h6>
                                                            <p class="text-muted small">Cette journée est entièrement libre</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Onglet Liste des Groupes -->
                    <div class="tab-pane fade" id="groups-pane" role="tabpanel">
                        <section class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title">Liste des Clubs et Groupes</h4>
                                        <div class="d-flex gap-2">
                                            <input type="text" class="form-control" id="searchGroups" placeholder="Rechercher...">
                                            <select class="form-select" id="filterDiscipline">
                                                <option value="">Toutes disciplines</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div id="groupsList" class="row">
                                            <!-- La liste sera chargée dynamiquement -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout de groupe -->
    <div class="modal fade" id="addGroupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                        <i class="bi bi-plus-circle me-2"></i>Enregistrer un nouveau groupe
                    </button>
                </div>
                <form id="addGroupForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nom_responsable" class="form-label">Nom du responsable</label>
                            <input type="text" class="form-control" id="nom_responsable" name="nom_responsable" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact</label>
                            <input type="text" class="form-control" id="contact" name="contact" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de participants</label>
                            <input type="number" class="form-control" id="nombre" name="nombre" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="discipline" class="form-label">Discipline</label>
                            <input type="text" class="form-control" id="discipline" name="discipline" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Scripts -->
    <script src="<?= Flight::base() ?>/public/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>

</body>
</html>