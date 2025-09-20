<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Planning des entretiens</title>

  <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
  <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
  <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
  <style>
    .legend-color {
      width: 16px; height: 16px;
      border-radius: 3px; display: inline-block;
    }
    .calendar { border: 1px solid #dee2e6; border-radius: .375rem; }
    .calendar-header { display: grid; grid-template-columns: repeat(7,1fr); background: #f8f9fa; border-bottom: 1px solid #dee2e6; }
    .day-name { padding: .75rem; text-align: center; font-weight: 600; }
    .calendar-body { display: grid; grid-template-columns: repeat(7,1fr); }
    .calendar-day { min-height: 80px; padding: .5rem; border: 1px solid #dee2e6; position: relative; cursor: pointer; transition: background-color 0.2s; }
    .calendar-day:hover { background-color: rgba(0,123,255,0.1); }
    .calendar-day.selected { background-color: rgba(0,123,255,0.3) !important; border-color: #007bff; }
    .calendar-day.has-interviews { background: rgba(40,167,69,.2); border-color: #28a745; }
    .calendar-day.has-many-interviews { background: rgba(255,193,7,.2); border-color: #ffc107; }
    .calendar-day.has-conflicts { background: rgba(220,53,69,.2); border-color: #dc3545; }
    .day-number { font-weight: bold; margin-bottom: .25rem; }
    .day-count { font-size: 0.75rem; color: #666; }
    .interview-item { 
      background: white; 
      border: 1px solid #dee2e6; 
      border-radius: .25rem; 
      padding: .75rem; 
      margin-bottom: .5rem; 
      box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
      position: relative;
    }
    .interview-time { font-weight: bold; color: #007bff; }
    .interview-candidate { font-size: 0.95rem; margin: .25rem 0; }
    .interview-duration { font-size: 0.85rem; color: #666; }
    .interview-evaluation {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: 0.5rem;
      padding: 0.25rem;
      border-radius: 0.25rem;
      font-size: 0.8rem;
      font-weight: 500;
    }
    .evaluation-recommande { background-color: #d4edda; color: #155724; }
    .evaluation-reserve { background-color: #fff3cd; color: #856404; }
    .evaluation-refuse { background-color: #f8d7da; color: #721c24; }
    .note-badge {
      position: absolute;
      top: 0.25rem;
      right: 0.25rem;
      background: #007bff;
      color: white;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: bold;
    }
    .btn-noter {
      background: #28a745;
      color: white;
      border: none;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.75rem;
      cursor: pointer;
      margin-top: 0.5rem;
    }
    .btn-noter:hover { background: #218838; }
    .btn-noter:disabled { 
      background: #6c757d; 
      cursor: not-allowed; 
    }
    .no-interviews { text-align: center; color: #666; padding: 2rem; }
    
    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border-radius: 8px;
      width: 80%;
      max-width: 500px;
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
    }
    .form-group {
      margin-bottom: 1rem;
    }
    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
    }
    .form-control {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #dee2e6;
      border-radius: 0.375rem;
      font-size: 0.875rem;
    }
    .form-control:focus {
      border-color: #007bff;
      outline: 0;
      box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 0.375rem;
      cursor: pointer;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0056b3;
      border-color: #004085;
    }
    .btn-secondary {
      background-color: #6c757d;
      border-color: #6c757d;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 0.375rem;
      cursor: pointer;
      border: none;
    }
    .btn-secondary:hover {
      background-color: #545b62;
      border-color: #4e555b;
    }
    .star-rating {
      display: flex;
      gap: 0.25rem;
      margin: 0.5rem 0;
    }
    .star {
      font-size: 1.5rem;
      color: #ddd;
      cursor: pointer;
      transition: color 0.2s;
    }
    .star.active, .star:hover {
      color: #ffc107;
    }
    .evaluation-options {
      display: flex;
      gap: 1rem;
      margin: 1rem 0;
    }
    .evaluation-option {
      flex: 1;
    }
    .evaluation-option input[type="radio"] {
      display: none;
    }
    .evaluation-option label {
      display: block;
      padding: 0.75rem;
      border: 2px solid #dee2e6;
      border-radius: 0.375rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s;
      font-weight: 500;
    }
    .evaluation-option input[type="radio"]:checked + label {
      border-color: #007bff;
      background-color: #e7f1ff;
      color: #007bff;
    }
    .evaluation-recommande label {
      border-color: #28a745;
      color: #28a745;
    }
    .evaluation-recommande input[type="radio"]:checked + label {
      background-color: #d4edda;
      border-color: #28a745;
      color: #155724;
    }
    .evaluation-reserve label {
      border-color: #ffc107;
      color: #856404;
    }
    .evaluation-reserve input[type="radio"]:checked + label {
      background-color: #fff3cd;
      border-color: #ffc107;
      color: #856404;
    }
    .evaluation-refuse label {
      border-color: #dc3545;
      color: #dc3545;
    }
    .evaluation-refuse input[type="radio"]:checked + label {
      background-color: #f8d7da;
      border-color: #dc3545;
      color: #721c24;
    }
  </style>
</head>

<body>
<div id="app">
  <?= Flight::menuBackOffice() ?>
  <div id="main">
    <header class="mb-3">
      <a href="#" class="burger-btn d-block d-xl-none"><i class="bi bi-justify fs-3"></i></a>
    </header>

    <div class="page-heading">
      <h3>Planning des entretiens</h3>
    </div>

    <div class="page-content">
      <div class="row">
        <!-- Calendrier -->
        <div class="col-md-7">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4 class="card-title">Planning Mensuel</h4>
              <div class="calendar-nav">
                <button class="btn btn-sm btn-outline-primary" onclick="changeMonth(-1)"><i class="bi bi-chevron-left"></i></button>
                <span id="currentMonth" class="mx-3 fw-bold"></span>
                <button class="btn btn-sm btn-outline-primary" onclick="changeMonth(1)"><i class="bi bi-chevron-right"></i></button>
              </div>
            </div>
            <div class="card-body">
              <!-- Légende -->
              <div class="mb-3 d-flex gap-3 flex-wrap">
                <small><span class="legend-color has-interviews"></span> Entretiens programmés</small>
                <small><span class="legend-color has-many-interviews"></span> Journée chargée (3+)</small>
                <small><span class="legend-color has-conflicts"></span> Conflits possibles</small>
              </div>
              
              <div class="calendar">
                <div class="calendar-header">
                  <div class="day-name">Lun</div><div class="day-name">Mar</div><div class="day-name">Mer</div>
                  <div class="day-name">Jeu</div><div class="day-name">Ven</div><div class="day-name">Sam</div><div class="day-name">Dim</div>
                </div>
                <div class="calendar-body" id="calendarBody"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Détails des entretiens -->
        <div class="col-md-5">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title" id="selectedDateTitle">Sélectionnez une date</h4>
            </div>
            <div class="card-body">
              <div id="interviewDetails">
                <p class="text-muted text-center">Cliquez sur une date dans le calendrier pour voir les entretiens programmés.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de notation -->
<div id="notationModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h5 id="modalTitle">Noter l'entretien</h5>
      <span class="close" onclick="closeModal()">&times;</span>
    </div>
    <div class="modal-body">
      <form id="notationForm">
        <input type="hidden" id="entretienId" name="id_entretien">
        
        <div class="form-group">
          <label class="form-label">Candidat</label>
          <p id="candidatInfo" class="form-control-plaintext"></p>
        </div>

        <div class="form-group">
          <label class="form-label">Note sur 10</label>
          <div class="star-rating" id="starRating">
            <span class="star" data-value="1">★</span>
            <span class="star" data-value="2">★</span>
            <span class="star" data-value="3">★</span>
            <span class="star" data-value="4">★</span>
            <span class="star" data-value="5">★</span>
            <span class="star" data-value="6">★</span>
            <span class="star" data-value="7">★</span>
            <span class="star" data-value="8">★</span>
            <span class="star" data-value="9">★</span>
            <span class="star" data-value="10">★</span>
          </div>
          <input type="number" id="noteInput" name="note" min="0" max="10" step="0.5" class="form-control" placeholder="Note sur 10" required>
        </div>

        <div class="form-group">
          <label class="form-label">Évaluation</label>
          <div class="evaluation-options">
            <div class="evaluation-option evaluation-recommande">
              <input type="radio" id="recommande" name="evaluation" value="recommande" required>
              <label for="recommande">Recommandé</label>
            </div>
            <div class="evaluation-option evaluation-reserve">
              <input type="radio" id="reserve" name="evaluation" value="reserve" required>
              <label for="reserve">Réservé</label>
            </div>
            <div class="evaluation-option evaluation-refuse">
              <input type="radio" id="refuse" name="evaluation" value="refuse" required>
              <label for="refuse">Refusé</label>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="commentaire" class="form-label">Commentaire (optionnel)</label>
          <textarea id="commentaire" name="commentaire" class="form-control" rows="3" placeholder="Commentaires sur l'entretien..."></textarea>
        </div>

        <div class="d-flex gap-2 justify-content-end">
          <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let current = new Date();
let interviewsData = {}; // Stockage des données d'entretiens
let selectedDate = null;
let currentNote = 0;

// Fonction pour charger les entretiens depuis le serveur
async function loadInterviews() {
  try {
    // Vous pouvez ajuster cette URL selon votre structure
    const response = await fetch('<?= Flight::base() ?>/entretien/api/planning');
    if (response.ok) {
      const data = await response.json();
      processInterviewsData(data);
      renderCalendar(current);
    }
  } catch (error) {
    console.error('Erreur lors du chargement des entretiens:', error);
    // Utiliser des données de test en cas d'erreur
    loadTestData();
  }
}

// Données de test (à supprimer quand l'API est prête)
function loadTestData() {
  interviewsData = {
    "2025-09-15": [
      { 
        id: 1, 
        time: "09:00", 
        candidate: "Jean Dupont", 
        duration: 60, 
        email: "jean.dupont@email.com",
        note_entretien: 8.5,
        evaluation: "recommande",
        commentaire: "Excellent candidat, très motivé"
      },
      { 
        id: 2, 
        time: "14:30", 
        candidate: "Marie Martin", 
        duration: 45, 
        email: "marie.martin@email.com",
        note_entretien: null,
        evaluation: null,
        commentaire: null
      }
    ],
    "2025-09-18": [
      { 
        id: 3, 
        time: "10:00", 
        candidate: "Pierre Durant", 
        duration: 60, 
        email: "pierre.durant@email.com",
        note_entretien: 6.0,
        evaluation: "reserve",
        commentaire: "Bon profil mais manque d'expérience"
      },
      { 
        id: 4, 
        time: "11:30", 
        candidate: "Sophie Bernard", 
        duration: 30, 
        email: "sophie.bernard@email.com",
        note_entretien: null,
        evaluation: null,
        commentaire: null
      },
      { 
        id: 5, 
        time: "15:00", 
        candidate: "Lucas Robert", 
        duration: 45, 
        email: "lucas.robert@email.com",
        note_entretien: 3.5,
        evaluation: "refuse",
        commentaire: "Ne correspond pas au profil recherché"
      }
    ],
    "2025-09-20": [
      { 
        id: 6, 
        time: "09:30", 
        candidate: "Emma Leroy", 
        duration: 60, 
        email: "emma.leroy@email.com",
        note_entretien: null,
        evaluation: null,
        commentaire: null
      }
    ]
  };
  renderCalendar(current);
}

// Traiter les données reçues du serveur
function processInterviewsData(data) {
  interviewsData = {};
  data.forEach(interview => {
    const dateKey = interview.date.split(' ')[0]; // Extraire juste la date
    if (!interviewsData[dateKey]) {
      interviewsData[dateKey] = [];
    }
    interviewsData[dateKey].push({
      id: interview.id_entretien,
      time: interview.date.split(' ')[1].substring(0, 5), // HH:MM
      candidate: `${interview.prenom} ${interview.nom}`,
      duration: interview.duree,
      email: interview.email,
      note_entretien: interview.note_entretien,
      evaluation: interview.evaluation,
      commentaire: interview.commentaire
    });
  });
}

// Déterminer la classe CSS selon le nombre d'entretiens
function getDayClass(interviews) {
  if (!interviews || interviews.length === 0) return '';
  if (interviews.length >= 3) return 'has-many-interviews';
  return 'has-interviews';
}

function renderCalendar(date) {
  const year = date.getFullYear();
  const month = date.getMonth();
  document.getElementById("currentMonth").textContent =
    date.toLocaleString("fr-FR", { month: "long", year: "numeric" });

  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month+1, 0);
  const startDay = firstDay.getDay() === 0 ? 7 : firstDay.getDay();

  const calendarBody = document.getElementById("calendarBody");
  calendarBody.innerHTML = "";

  // Cases vides avant le 1er
  for (let i=1; i<startDay; i++) {
    const empty = document.createElement("div");
    calendarBody.appendChild(empty);
  }

  // Les jours du mois
  for (let d=1; d<=lastDay.getDate(); d++) {
    const day = document.createElement("div");
    day.classList.add("calendar-day");
    const dateStr = `${year}-${String(month+1).padStart(2,"0")}-${String(d).padStart(2,"0")}`;

    const interviews = interviewsData[dateStr] || [];
    const dayClass = getDayClass(interviews);
    if (dayClass) {
      day.classList.add(dayClass);
    }

    let dayContent = `<div class="day-number">${d}</div>`;
    if (interviews.length > 0) {
      dayContent += `<div class="day-count">${interviews.length} entretien${interviews.length > 1 ? 's' : ''}</div>`;
    }

    day.innerHTML = dayContent;
    day.setAttribute('data-date', dateStr);
    day.addEventListener('click', () => selectDate(dateStr));

    calendarBody.appendChild(day);
  }
}

function selectDate(dateStr) {
  // Retirer la sélection précédente
  document.querySelectorAll('.calendar-day.selected').forEach(el => {
    el.classList.remove('selected');
  });

  // Ajouter la sélection à la nouvelle date
  const dayElement = document.querySelector(`[data-date="${dateStr}"]`);
  if (dayElement) {
    dayElement.classList.add('selected');
  }

  selectedDate = dateStr;
  displayInterviewDetails(dateStr);
}

function displayInterviewDetails(dateStr) {
  const interviews = interviewsData[dateStr] || [];
  const dateObj = new Date(dateStr + 'T00:00:00');
  const formattedDate = dateObj.toLocaleDateString("fr-FR", { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });

  document.getElementById("selectedDateTitle").textContent = formattedDate;

  const detailsContainer = document.getElementById("interviewDetails");
  
  if (interviews.length === 0) {
    detailsContainer.innerHTML = '<div class="no-interviews">Aucun entretien programmé pour cette date.</div>';
    return;
  }

  // Trier les entretiens par heure
  interviews.sort((a, b) => a.time.localeCompare(b.time));

  let html = '';
  interviews.forEach(interview => {
    const endTime = calculateEndTime(interview.time, interview.duration);
    const hasNote = interview.note_entretien !== null;
    
    html += `
      <div class="interview-item">
        ${hasNote ? `<div class="note-badge">${interview.note_entretien}</div>` : ''}
        
        <div class="interview-time">${interview.time} - ${endTime}</div>
        <div class="interview-candidate"><strong>${interview.candidate}</strong></div>
        <div class="interview-duration">Durée: ${interview.duration} minutes</div>
        <div class="interview-email"><small><i class="bi bi-envelope"></i> ${interview.email}</small></div>
        
        ${interview.evaluation ? `
          <div class="interview-evaluation evaluation-${interview.evaluation}">
            <i class="bi ${getEvaluationIcon(interview.evaluation)}"></i>
            ${getEvaluationText(interview.evaluation)}
            ${interview.note_entretien ? ` - ${interview.note_entretien}/10` : ''}
          </div>
        ` : ''}
        
        ${interview.commentaire ? `
          <div class="mt-2">
            <small class="text-muted"><strong>Commentaire:</strong> ${interview.commentaire}</small>
          </div>
        ` : ''}
        
        <button class="btn-noter" onclick="openNotationModal(${interview.id}, '${interview.candidate}', '${interview.email}')" 
                ${hasNote ? 'title="Modifier la notation"' : 'title="Noter cet entretien"'}>
          ${hasNote ? 'Modifier la note' : 'Noter'}
        </button>
      </div>
    `;
  });

  detailsContainer.innerHTML = html;
}

function getEvaluationIcon(evaluation) {
  switch(evaluation) {
    case 'recommande': return 'bi-check-circle-fill';
    case 'reserve': return 'bi-clock-fill';
    case 'refuse': return 'bi-x-circle-fill';
    default: return '';
  }
}

function getEvaluationText(evaluation) {
  switch(evaluation) {
    case 'recommande': return 'Recommandé';
    case 'reserve': return 'Réservé';
    case 'refuse': return 'Refusé';
    default: return '';
  }
}

function calculateEndTime(startTime, duration) {
  const [hours, minutes] = startTime.split(':').map(Number);
  const startMinutes = hours * 60 + minutes;
  const endMinutes = startMinutes + duration;
  const endHours = Math.floor(endMinutes / 60);
  const remainingMinutes = endMinutes % 60;
  return `${String(endHours).padStart(2, '0')}:${String(remainingMinutes).padStart(2, '0')}`;
}

function changeMonth(delta) {
  current.setMonth(current.getMonth() + delta);
  renderCalendar(current);
  
  // Réinitialiser la sélection
  selectedDate = null;
  document.getElementById("selectedDateTitle").textContent = "Sélectionnez une date";
  document.getElementById("interviewDetails").innerHTML = '<p class="text-muted text-center">Cliquez sur une date dans le calendrier pour voir les entretiens programmés.</p>';
}

// Gestion de la modal de notation
function openNotationModal(entretienId, candidatName, candidatEmail) {
  document.getElementById('entretienId').value = entretienId;
  document.getElementById('candidatInfo').textContent = `${candidatName} (${candidatEmail})`;
  
  // Récupérer les données existantes si disponibles
  const interviews = interviewsData[selectedDate] || [];
  const interview = interviews.find(i => i.id === entretienId);
  
  if (interview && interview.note_entretien !== null) {
    document.getElementById('noteInput').value = interview.note_entretien;
    updateStarRating(interview.note_entretien);
    
    if (interview.evaluation) {
      document.getElementById(interview.evaluation).checked = true;
    }
    
    if (interview.commentaire) {
      document.getElementById('commentaire').value = interview.commentaire;
    }
  } else {
    // Réinitialiser le formulaire
    document.getElementById('notationForm').reset();
    document.getElementById('entretienId').value = entretienId;
    updateStarRating(0);
  }
  
  document.getElementById('notationModal').style.display = 'block';
}

function closeModal() {
  document.getElementById('notationModal').style.display = 'none';
}

function updateStarRating(note) {
  currentNote = note;
  const stars = document.querySelectorAll('.star');
  stars.forEach((star, index) => {
    star.classList.toggle('active', index < note);
  });
}

// Gestion des étoiles
document.addEventListener('DOMContentLoaded', function() {
  const stars = document.querySelectorAll('.star');
  const noteInput = document.getElementById('noteInput');
  
  stars.forEach(star => {
    star.addEventListener('click', function() {
      const value = parseInt(this.dataset.value);
      currentNote = value;
      noteInput.value = value;
      updateStarRating(value);
    });
    
    star.addEventListener('mouseover', function() {
      const value = parseInt(this.dataset.value);
      updateStarRating(value);
    });
  });
  
  document.getElementById('starRating').addEventListener('mouseleave', function() {
    updateStarRating(currentNote);
  });
  
  // Synchroniser input et étoiles
  noteInput.addEventListener('input', function() {
    const value = parseFloat(this.value) || 0;
    if (value >= 0 && value <= 10) {
      currentNote = value;
      updateStarRating(value);
    }
  });
});

// Soumission du formulaire de notation
document.getElementById('notationForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  
  try {
    const response = await fetch('<?= Flight::base() ?>/entretien/noter', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      alert('Notation enregistrée avec succès!');
      closeModal();
      // Recharger les données et rafraîchir l'affichage
      await loadInterviews();
      if (selectedDate) {
        displayInterviewDetails(selectedDate);
      }
    } else {
      alert('Erreur: ' + result.message);
    }
  } catch (error) {
    console.error('Erreur lors de la notation:', error);
    alert('Une erreur est survenue lors de l\'enregistrement de la notation.');
  }
});

// Fermer la modal en cliquant à l'extérieur
window.addEventListener('click', function(event) {
  const modal = document.getElementById('notationModal');
  if (event.target === modal) {
    closeModal();
  }
});

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
  loadTestData(); // Remplacer par loadInterviews() quand l'API est prête
});
</script>

</body>
</html>