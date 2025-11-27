<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compétences globales - Sanda</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/style.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/table-datatable.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .chart-container { width: 90%; max-width: 1200px; margin: 20px auto; }
    </style>
</head>
<body>
<div id="app">
    <?= Flight::menuBackOffice() ?>

    <div id="main">
        <!-- Chart Section -->
        <section class="section">
            <div class="card p-4">
                <div class="chart-container">
                    <canvas id="overallSkillsChart"></canvas>
                </div>
            </div>
        </section>

        <!-- Employee Skills Table Section -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Employés et Formations Recommandées</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="employeesTable">
                            <thead>
                                <tr>
                                    <th>Nom de l'employé</th>
                                    <th>Compétence possede</th>
                                    <th>poste relate</th>
                                    <th>formation suggere</th>
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
    fetch("<?= Flight::base() ?>/api/employees-skills")
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('employeesTableBody');
            tbody.innerHTML = ''; // clear existing rows

            data.forEach(emp => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${emp.employe_name}</td>
                    <td>${emp.skills_owned || '-'}</td>
                    <td>${emp.related_posts || '-'}</td>
                    <td>${emp.suggested_formations || 'Aucune'}</td>
                `;
                tbody.appendChild(row);
            });

            // Initialize DataTable *after* data is loaded
            const dataTable = new simpleDatatables.DataTable("#employeesTable", {
                searchable: true,
                fixedHeight: false,
                perPageSelect: [5, 10, 15, 20],
                perPage: 5
            });
        })
        .catch(err => console.error('Erreur API employés:', err));
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Fetch skills overview data for the chart
    fetch("<?= Flight::base() ?>/api/skills-overview")
        .then(res => res.json())
        .then(data => {
            const labels = data.map(item => item.competence);
            const counts = data.map(item => parseInt(item.count));

            const ctx = document.getElementById('overallSkillsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Nombre d'employés par compétence",
                        data: counts,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: true, position: 'top' } },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: "Nombre d'employés" } },
                        x: { title: { display: true, text: "Compétences" } }
                    }
                }
            });
        })
        .catch(err => console.error('Erreur API compétences globales:', err));
});
</script>

<script src="<?= Flight::base() ?>/public/template/assets/extensions/jquery/jquery.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/simple-datatables/umd/simple-datatables.js"></script>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

</body>
</html>
