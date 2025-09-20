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
    .calendar-day { 
      min-height: 80px; 
      padding: .5rem; 
      border: 1px solid #dee2e6; 
      position: relative; 
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .calendar-day:hover {
      background-color: rgba(0,123,255,.1);
    }
    .calendar-day.day-with-interviews { 
      background: rgba(220,53,69,.15); 
      border-color: #dc3545; 
    }
    .calendar-day.day-free { 
      background: rgba(40,167,69,.15); 
      border-color: #28a745; 
    }
    .calendar-day.other-month {
      color: #ccc;
      background-color: #f8f9fa;
    }
    .day-number { 
      font-weight: bold; 
      margin-bottom: 0.25rem;
    }
    .interview-count {
      font-size: 0.75rem;
      background: rgba(220,53,69,.8);
      color: white;
      padding: 0.125rem 0.375rem;
      border-radius: 10px;
      display: inline-block;
    }
    .loading {
      text-align: center;
      color: #666;
    }
    .legend {
      margin-bottom: 1rem;
      display: flex;
      gap: 1rem;
      align-items: center;
    }
    .legend-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
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
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title">Planning Mensuel</h4>
          <div class="calendar-nav">
            <button class="btn btn-sm btn-outline-primary" onclick="changeMonth(-1)">
              <i class="bi bi-chevron-left"></i>
            </button>
            <span id="currentMonth" class="mx-3 fw-bold"></span>
            <button class="btn btn-sm btn-outline-primary" onclick="changeMonth(1)">
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <!-- Légende -->
          <div class="legend">
            <div class="legend-item">
              <span class="legend-color" style="background-color: rgba(220,53,69,.15); border: 1px solid #dc3545;"></span>
              <span>Jours avec entretiens</span>
            </div>
            <div class="legend-item">
              <span class="legend-color" style="background-color: rgba(40,167,69,.15); border: 1px solid #28a745;"></span>
              <span>Jours libres</span>
            </div>
          </div>

          <div class="calendar">
            <div class="calendar-header">
              <div class="day-name">Lun</div><div class="day-name">Mar</div><div class="day-name">Mer</div>
              <div class="day-name">Jeu</div><div class="day-name">Ven</div><div class="day-name">Sam</div><div class="day-name">Dim</div>
            </div>
            <div class="calendar-body" id="calendarBody">
              <div class="loading">Chargement...</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bouton pour organiser un nouvel entretien -->
      <div class="card mt-3">
        <div class="card-body text-center">
          <a href="/organiserEntretien" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Organiser un nouvel entretien
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let currentDate = new Date();
let entretiensByDate = {};

// Fonction pour charger les entretiens du mois
async function loadEntretiens(year, month) {
  try {
    const response = await fetch(`/api/entretiens/month?year=${year}&month=${month}`);
    const data = await response.json();
    entretiensByDate = data;
  } catch (error) {
    console.error('Erreur lors du chargement des entretiens:', error);
    entretiensByDate = {};
  }
}

function renderCalendar(date) {
  const year = date.getFullYear();
  const month = date.getMonth();
  
  document.getElementById("currentMonth").textContent =
    date.toLocaleString("fr-FR", { month: "long", year: "numeric" });

  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  const startDay = firstDay.getDay() === 0 ? 7 : firstDay.getDay(); // dimanche=7

  const calendarBody = document.getElementById("calendarBody");
  calendarBody.innerHTML = "";

  // Cases vides avant le 1er du mois
  for (let i = 1; i < startDay; i++) {
    const prevMonth = new Date(year, month - 1);
    const prevLastDay = new Date(year, month, 0).getDate();
    const prevDay = prevLastDay - (startDay - i - 1);
    
    const emptyDay = document.createElement("div");
    emptyDay.classList.add("calendar-day", "other-month");
    emptyDay.innerHTML = `<div class="day-number">${prevDay}</div>`;
    calendarBody.appendChild(emptyDay);
  }

  // Les jours du mois courant
  for (let d = 1; d <= lastDay.getDate(); d++) {
    const day = document.createElement("div");
    day.classList.add("calendar-day");
    
    const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(d).padStart(2, "0")}`;
    
    let dayContent = `<div class="day-number">${d}</div>`;
    
    if (entretiensByDate[dateStr]) {
      const entretien = entretiensByDate[dateStr];
      day.classList.add("day-with-interviews");
      dayContent += `<div class="interview-count">${entretien.count} entretien(s)</div>`;
      day.title = `Entretiens avec: ${entretien.candidats}`;
    } else {
      // Jour libre (sauf weekends)
      const dayOfWeek = new Date(year, month, d).getDay();
      if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Pas dimanche (0) ou samedi (6)
        day.classList.add("day-free");
        day.title = "Jour libre - Cliquez pour planifier un entretien";
      }
    }
    
    day.innerHTML = dayContent;
    
    // Ajouter un événement de clic pour planifier un entretien
    day.addEventListener('click', function() {
      if (!entretiensByDate[dateStr] && day.classList.contains('day-free')) {
        window.location.href = `/organiserEntretien?date=${dateStr}`;
      }
    });
    
    calendarBody.appendChild(day);
  }

  // Cases après le dernier jour du mois
  const totalCells = calendarBody.children.length;
  const remainingCells = 42 - totalCells; // 6 semaines * 7 jours = 42 cases
  
  for (let i = 1; i <= remainingCells && calendarBody.children.length < 42; i++) {
    const nextDay = document.createElement("div");
    nextDay.classList.add("calendar-day", "other-month");
    nextDay.innerHTML = `<div class="day-number">${i}</div>`;
    calendarBody.appendChild(nextDay);
  }
}

async function changeMonth(delta) {
  currentDate.setMonth(currentDate.getMonth() + delta);
  await loadEntretiens(currentDate.getFullYear(), currentDate.getMonth() + 1);
  renderCalendar(currentDate);
}

// Initialisation
async function init() {
  await loadEntretiens(currentDate.getFullYear(), currentDate.getMonth() + 1);
  renderCalendar(currentDate);
}

// Démarrer l'application
init();
</script>

</body>
</html>