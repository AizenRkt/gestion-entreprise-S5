<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compétences globales</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" >


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .chart-container { width: 90%; max-width: 1200px; margin: 20px auto; }
    </style>
</head>
<body>
<div id="app">
    <?= Flight::menuBackOffice() ?>

    <div id="main">
        <section class="section">
            <div class="card p-4">
                <div class="chart-container">
                    <canvas id="overallSkillsChart"></canvas>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Employés et Formations Recommandées</h5>
                </div>
                <div class="card-body">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold">filtre</label>
                        <select id="competence-select" class="form-select choices" multiple></select>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="employeesTable">
                            <thead>
                                <tr>
                                    <th>Nom de l'employé</th>
                                    <th>Poste actuel</th>
                                    <th>Compétence possede</th>
                                    <th>Poste possible</th>
                                    <th>Suggestion de formation</th>
                                </tr>
                            </thead>
                            <tbody id="employeesTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let allEmployees = [];

    fetch("<?= Flight::base() ?>/api/employees-skills")
        .then(res => res.json())
        .then(data => {
            allEmployees = data; 
            renderTable(allEmployees);
        });

   const competenceSelect = document.getElementById("competence-select");
competenceSelect.addEventListener("change", function () {

    const selected = Array.from(competenceSelect.selectedOptions).map(o => o.value);

    if (selected.length === 0) {
        renderTable(allEmployees);
        return;
    }

    const filtered = allEmployees.filter(emp =>
        selected.every(skill => emp.skills_owned?.includes(skill))
    );

    renderTable(filtered);
});


    function renderTable(list) {
        const tbody = document.getElementById('employeesTableBody');
        tbody.innerHTML = '';

        list.forEach(emp => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${emp.employe_name}</td>
                <td>${emp.current_poste}</td>
                <td>${emp.skills_owned || '-'}</td>
                <td>${emp.related_posts || '-'}</td>
                <td>${emp.missing_skills_current_post || '-'}</td>
            `;
            tbody.appendChild(row);
        });
    }

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("<?= Flight::base() ?>/api/skills-overview")
        .then(res => res.json())
        .then(data => {
            const labels = data.map(item => item.competence);
            const counts = data.map(item => parseInt(item.count));
            const ctx = document.getElementById('overallSkillsChart').getContext('2d');

            function generateColors(counting) {
                const colors = [];
                for (let i = 0; i < counting; i++) {
                    const hue = Math.floor(Math.random() * 360);
                    colors.push(`hsl(${hue}, 70%, 70%)`);
                }
                return colors;
            }

            colors = generateColors(data.length);
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Nombre d'employés par compétence",
                        data: counts,
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
            });
        })
        .catch(err => console.error('Erreur API compétences globales:', err));
});

fetch("<?= Flight::base() ?>/api/competences")
  .then(res => res.json())
  .then(data => {
    let select = document.getElementById("competence-select");

    data.forEach(c => {
      let option = document.createElement("option");
      option.value = c.nom;
      option.textContent = c.nom;
      select.appendChild(option);
    });

    new Choices(select, {
      searchEnabled: true,
      removeItemButton: true,
      allowHTML: true,
      placeholderValue: "Sélectionnez des compétences"
    });
  });
</script>

<script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

</body>
</html>
