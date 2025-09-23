<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning des entretiens</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">

    <!-- FullCalendar -->
    <link href="<?= Flight::base() ?>/public/plugin/fullcalendar-6.1.19/dist/index.global.js" rel="stylesheet">
    <script src="<?= Flight::base() ?>/public/plugin/fullcalendar-6.1.19/dist/index.global.min.js"></script>

</head>
<style>
    /* Modal personnalisé style Mazer */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background: rgba(0, 0, 0, 0.6);
    }

    .custom-modal-content {
        background: #fff;
        margin: 5% auto;
        padding: 0;
        border-radius: 12px;
        width: 600px;
        max-width: 90%;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .2);
        max-height: 90vh;
        overflow-y: auto;
    }

    .custom-modal-header {
        font-size: 18px;
        font-weight: bold;
        padding: 20px;
        border-bottom: 1px solid #ddd;
        background: #f8f9fa;
        border-radius: 12px 12px 0 0;
    }

    .custom-modal-body {
        padding: 20px;
    }

    .custom-modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #ddd;
        text-align: right;
        background: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: #435ebe;
        box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
    }

    .evaluation-options {
        display: flex;
        gap: 15px;
        margin-top: 0.5rem;
    }

    .evaluation-option {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .evaluation-option input[type="radio"] {
        margin: 0;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
    }

    .info-value {
        color: #495057;
    }

    .score-display {
        font-size: 18px;
        font-weight: bold;
        color: #435ebe;
    }

    .evaluation-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .evaluation-recommande {
        background: #d4edda;
        color: #155724;
    }

    .evaluation-refuse {
        background: #f8d7da;
        color: #721c24;
    }

    .evaluation-reserve {
        background: #fff3cd;
        color: #856404;
    }
</style>

<body>
    <div id="app">
        <?= Flight::menuBackOffice() ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none"><i class="bi bi-justify fs-3"></i></a>
            </header>
            <div class="page-content">
                <div class="row">
                    <!-- Colonne calendrier -->
                    <div class="col-lg-8 col-md-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h4>Planning des entretiens</h4>
                            </div>
                            <div class="card-body">
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne liste des entretiens -->
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Entretiens du jour</h4>
                            </div>
                            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                                <ul class="list-group" id="entretiens-list">
                                    <li class="list-group-item text-muted">Cliquez sur une date pour voir les entretiens</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal personnalisé -->
            <div id="customModal" class="custom-modal">
                <div class="custom-modal-content">
                    <div class="custom-modal-header" id="modalTitle">Détails de l'entretien</div>
                    <div class="custom-modal-body" id="modalBody"></div>
                    <div class="custom-modal-footer">
                        <button class="btn btn-secondary" onclick="closeModal()">Fermer</button>
                        <button class="btn btn-primary d-none" id="saveBtn" onclick="saveEvaluation()">Enregistrer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script>
    let currentInterview = null;

    function closeModal() {
        document.getElementById('customModal').style.display = 'none';
        currentInterview = null;
    }

    function showModal(interview) {
        currentInterview = interview;
        const modal = document.getElementById('customModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        const saveBtn = document.getElementById('saveBtn');

        modalTitle.textContent = `${interview.extendedProps.prenom} ${interview.extendedProps.nom}`;

        // Vérifier si l'évaluation existe
        const hasEvaluation = interview.extendedProps.score !== null || 
                            interview.extendedProps.evaluation !== null || 
                            interview.extendedProps.commentaire !== null;

        if (hasEvaluation) {
            // Mode affichage
            modalBody.innerHTML = `
                <div class="info-row">
                    <span class="info-label">Email :</span>
                    <span class="info-value">${interview.extendedProps.email}</span>
                </div>
                ${interview.extendedProps.telephone ? `
                <div class="info-row">
                    <span class="info-label">Téléphone :</span>
                    <span class="info-value">${interview.extendedProps.telephone}</span>
                </div>
                ` : ''}
                <div class="info-row">
                    <span class="info-label">Date :</span>
                    <span class="info-value">${new Date(interview.start).toLocaleString()}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Durée :</span>
                    <span class="info-value">${interview.extendedProps.duration} minutes</span>
                </div>
                ${interview.extendedProps.score ? `
                <div class="info-row">
                    <span class="info-label">Score :</span>
                    <span class="info-value score-display">${interview.extendedProps.score}/10</span>
                </div>
                ` : ''}
                ${interview.extendedProps.evaluation ? `
                <div class="info-row">
                    <span class="info-label">Évaluation :</span>
                    <span class="info-value">
                        <span class="evaluation-badge evaluation-${interview.extendedProps.evaluation}">
                            ${interview.extendedProps.evaluation}
                        </span>
                    </span>
                </div>
                ` : ''}
                ${interview.extendedProps.commentaire ? `
                <div class="info-row">
                    <span class="info-label">Commentaire :</span>
                    <span class="info-value">${interview.extendedProps.commentaire}</span>
                </div>
                ` : ''}
            `;
            saveBtn.classList.add('d-none');
        } else {
            // Mode édition
            modalBody.innerHTML = `
                <div class="info-row">
                    <span class="info-label">Email :</span>
                    <span class="info-value">${interview.extendedProps.email}</span>
                </div>
                ${interview.extendedProps.telephone ? `
                <div class="info-row">
                    <span class="info-label">Téléphone :</span>
                    <span class="info-value">${interview.extendedProps.telephone}</span>
                </div>
                ` : ''}
                <div class="info-row">
                    <span class="info-label">Date :</span>
                    <span class="info-value">${new Date(interview.start).toLocaleString()}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Durée :</span>
                    <span class="info-value">${interview.extendedProps.duration} minutes</span>
                </div>
                <hr>
                <div class="form-group">
                    <label class="form-label" for="scoreInput">Note (sur 10) :</label>
                    <input type="number" id="scoreInput" class="form-control" min="0" max="10" step="0.5" placeholder="Ex: 7.5">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Évaluation :</label>
                    <div class="evaluation-options">
                        <input type="hidden" id="idCandidat" name="idCandidat" value="${interview.extendedProps.id_candidat}">
                        <input type="hidden" id="idEntretien" name="idEntretien" value="${interview.id}">
                        <div class="evaluation-option">
                            <input type="radio" id="recommande" name="evaluation" value="recommande">
                            <label for="recommande">Recommandé</label>
                        </div>
                        <div class="evaluation-option">
                            <input type="radio" id="refuse" name="evaluation" value="refuse">
                            <label for="refuse">Refusé</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="commentaireInput">Commentaire :</label>
                    <textarea id="commentaireInput" class="form-control" rows="4" placeholder="Vos observations sur ce candidat..."></textarea>
                </div>
            `;
            saveBtn.classList.remove('d-none');
        }

        modal.style.display = 'block';
    }

    const BASE_URL = "<?= Flight::base() ?>";

    async function saveEvaluation() {
        if (!currentInterview) return;

        const id_candidat = document.getElementById('idCandidat').value;
        const id_entretien = document.getElementById('idEntretien').value;
        const score = document.getElementById('scoreInput').value;
        const evaluation = document.querySelector('input[name="evaluation"]:checked')?.value;
        const commentaire = document.getElementById('commentaireInput').value;

        if (!score && !evaluation && !commentaire.trim()) {
            alert('Veuillez remplir au moins un champ pour sauvegarder l\'évaluation.');
            return;
        }

        try {
            const response = await fetch(`${BASE_URL}/entretien/scoring`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_candidat: id_candidat,
                    id_entretien: id_entretien,
                    score: score ? parseFloat(score) : null,
                    evaluation: evaluation || null,
                    commentaire: commentaire.trim() || null
                })
            });

            if (response.ok) {
                alert('Évaluation sauvegardée avec succès !');
                
                // Mettre à jour les données locales
                currentInterview.extendedProps.score = score ? parseFloat(score) : null;
                currentInterview.extendedProps.evaluation = evaluation || null;
                currentInterview.extendedProps.commentaire = commentaire.trim() || null;
                
                closeModal();
                
                // Recharger la vue si nécessaire
                location.reload();
            } else {
                throw new Error('Erreur lors de la sauvegarde');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de la sauvegarde de l\'évaluation');
        }
    }

    // modal fermeture
    document.getElementById('customModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // load data
    document.addEventListener('DOMContentLoaded', async function () {
        const calendarEl = document.getElementById('calendar');
        const entretiensList = document.getElementById('entretiens-list');

        async function loadEvents() {
            try {
                const response = await fetch('<?= Flight::base() ?>/entretien/api/planning');
                const data = await response.json();

                return data.map(interview => ({
                    id: interview.id_entretien,
                    title: `${interview.prenom} ${interview.nom}`,
                    start: interview.date, 
                    extendedProps: {
                        id_candidat: interview.id_candidat,
                        prenom: interview.prenom,
                        nom: interview.nom,
                        email: interview.email,
                        telephone: interview.telephone,
                        duration: interview.duree,
                        score: interview.score,
                        evaluation: interview.evaluation, 
                        commentaire: interview.commentaire
                    }
                }));
            } catch (error) {
                console.error('Erreur lors du chargement des entretiens:', error);
                return [];
            }
        }

        const events = await loadEvents();

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap5',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            events: events,
            dateClick: function (info) {
                const selectedDate = info.dateStr;
                const filtered = events.filter(e => e.start.startsWith(selectedDate));

                entretiensList.innerHTML = "";

                if (filtered.length === 0) {
                    entretiensList.innerHTML = `<li class="list-group-item text-muted">Aucun entretien prévu ce jour-là</li>`;
                } else {
                    filtered.forEach(e => {
                        const li = document.createElement("li");
                        li.className = "list-group-item d-flex justify-content-between align-items-center";
                        li.innerHTML = `
                            <div>
                                <div><b>Nom :</b> ${e.extendedProps.prenom} ${e.extendedProps.nom}</div>
                                <div><b>Email :</b> ${e.extendedProps.email}</div>
                                ${e.extendedProps.telephone ? `<div><b>Tél :</b> ${e.extendedProps.telephone}</div>` : ""}
                                <div><b>Date :</b> ${new Date(e.start).toLocaleString()}</div>
                                <small>Candidat - ${e.extendedProps.duration} min</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary">Voir</button>
                        `;
                        li.querySelector("button").addEventListener("click", () => {
                            showModal(e);
                        });
                        entretiensList.appendChild(li);
                    });
                }
            }
        });

        calendar.render();
    });
</script>

</html>