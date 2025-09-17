<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test Calendrier - Mazer</title>

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
    .calendar-day { min-height: 80px; padding: .5rem; border: 1px solid #dee2e6; position: relative; }
    .calendar-day.day-full { background: rgba(220,53,69,.2); border-color: #dc3545; }
    .calendar-day.day-partial { background: rgba(255,193,7,.2); border-color: #ffc107; }
    .calendar-day.day-free { background: rgba(40,167,69,.2); border-color: #28a745; }
    .day-number { font-weight: bold; }
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
      <h3>Test Calendrier</h3>
    </div>

    <div class="page-content">
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
  </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Données de test pour voir si les couleurs fonctionnent
const events = {
  "2025-09-10": "full",
  "2025-09-12": "partial",
  "2025-09-15": "free"
};

let current = new Date();

function renderCalendar(date) {
  const year = date.getFullYear();
  const month = date.getMonth();
  document.getElementById("currentMonth").textContent =
    date.toLocaleString("fr-FR", { month: "long", year: "numeric" });

  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month+1, 0);
  const startDay = firstDay.getDay() === 0 ? 7 : firstDay.getDay(); // dimanche=7

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

    if (events[dateStr]) {
      day.classList.add(`day-${events[dateStr]}`);
    }

    day.innerHTML = `<div class="day-number">${d}</div>`;
    calendarBody.appendChild(day);
  }
}

function changeMonth(delta) {
  current.setMonth(current.getMonth() + delta);
  renderCalendar(current);
}

renderCalendar(current);
</script>

</body>
</html>
