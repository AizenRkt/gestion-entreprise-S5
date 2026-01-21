<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>default</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
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
            <h3>Dashboard KPI – Direction Générale</h3>
            <p class="text-subtitle text-muted">Vue synthétique de la performance globale</p>
        </div>

        <div class="page-content">

            <!-- KPI CARDS -->
            <section class="row">
                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Chiffre d’Affaires</h6>
                            <h4 class="text-success">1 250 000 €</h4>
                            <small class="text-muted">+8% vs M-1</small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Marge Brute</h6>
                            <h4>420 000 €</h4>
                            <small class="text-muted">33,6 %</small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Valeur Stock</h6>
                            <h4 class="text-warning">310 000 €</h4>
                            <small class="text-muted">-5% vs M-1</small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Rotation Stock</h6>
                            <h4>4,2</h4>
                            <small class="text-muted">objectif ≥ 5</small>
                        </div>
                    </div>
                </div>
            </section>

            <!-- GRAPHIQUES -->
            <section class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Évolution CA & Marge (12 mois)</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-ca"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Valeur Stock par Site</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="stockSiteChart"></canvas>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SURSTOCK / INVENTAIRE -->
            <section class="row">
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Top 5 Surstocks / Obsolescence</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="surstockChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Écarts Inventaire</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Dépôt</th>
                                    <th>Valeur (€)</th>
                                    <th>%</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Central</td>
                                    <td>-4 200</td>
                                    <td class="text-danger">-1,4%</td>
                                </tr>
                                <tr>
                                    <td>Nord</td>
                                    <td>+1 100</td>
                                    <td class="text-success">+0,3%</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/toastify.js"></script>

<!-- charts -->
<script src="<?= Flight::base() ?>/public/template/assets/extensions/dayjs/dayjs.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/apexcharts/apexcharts.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/ui-apexchart.js"></script>

<script src="<?= Flight::base() ?>/public/template/assets/extensions/chart.js/chart.umd.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/ui-chartjs.js"></script>

<!-- scripts -->
<script>
/* ApexCharts – CA */
new ApexCharts(document.querySelector("#chart-ca"), {
    chart: { type: 'line', height: 300 },
    series: [
        { name: "CA", data: [80,90,95,100,110,120,130,125,135,140,145,150] },
        { name: "Marge", data: [25,30,32,33,35,36,38,37,39,40,42,44] }
    ],
    xaxis: {
        categories: ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc']
    }
}).render();

/* ChartJS – Stock par site */
new Chart(document.getElementById('stockSiteChart'), {
    type: 'doughnut',
    data: {
        labels: ['Central', 'Nord', 'Sud'],
        datasets: [{
            data: [180000, 80000, 50000]
        }]
    }
});

/* ChartJS – Surstock */
new Chart(document.getElementById('surstockChart'), {
    type: 'bar',
    data: {
        labels: ['Article A','Article B','Article C','Article D','Article E'],
        datasets: [{
            label: 'Valeur immobilisée (€)',
            data: [42000, 38000, 29000, 21000, 18000]
        }]
    }
});
</script>



</body>
</html>

