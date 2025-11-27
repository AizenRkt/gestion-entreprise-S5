<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Employés - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
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
                        <h3>Statistiques des Employés</h3>
                        <p class="text-subtitle text-muted">Visualisation des données des employés</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/employes">Employés</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Statistiques</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Répartition par Genre</h5>
                            </div>
                            <div class="card-body">
                                <div id="chart-genre"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Répartition par Service</h5>
                            </div>
                            <div class="card-body">
                                <div id="chart-service"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Répartition par Département</h5>
                            </div>
                            <div class="card-body">
                                <div id="chart-departement"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Répartition par Poste</h5>
                            </div>
                            <div class="card-body">
                                <div id="chart-poste"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Statut d'Activité</h5>
                            </div>
                            <div class="card-body">
                                <div id="chart-activite"></div>
                            </div>
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

<script src="<?= Flight::base() ?>/public/template/assets/extensions/dayjs/dayjs.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/apexcharts/apexcharts.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données PHP
    const statsGenre = <?= json_encode($statsGenre) ?>;
    const statsService = <?= json_encode($statsService) ?>;
    const statsDepartement = <?= json_encode($statsDepartement) ?>;
    const statsPoste = <?= json_encode($statsPoste) ?>;
    const statsActivite = <?= json_encode($statsActivite) ?>;

    // Chart Genre - Pie
    const optionsGenre = {
        series: statsGenre.map(item => parseInt(item.count)),
        chart: {
            type: 'pie',
            height: 300
        },
        labels: statsGenre.map(item => item.genre === 'M' ? 'Masculin' : 'Féminin'),
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    const chartGenre = new ApexCharts(document.querySelector("#chart-genre"), optionsGenre);
    chartGenre.render();

    // Chart Service - Bar
    const optionsService = {
        series: [{
            data: statsService.map(item => parseInt(item.count))
        }],
        chart: {
            type: 'bar',
            height: 300
        },
        plotOptions: {
            bar: {
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: statsService.map(item => item.service),
        }
    };
    const chartService = new ApexCharts(document.querySelector("#chart-service"), optionsService);
    chartService.render();

    // Chart Département - Area
    const optionsDepartement = {
        series: [{
            name: 'Employés',
            data: statsDepartement.map(item => parseInt(item.count))
        }],
        chart: {
            type: 'area',
            height: 300,
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        xaxis: {
            categories: statsDepartement.map(item => item.departement),
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
        },
    };
    const chartDepartement = new ApexCharts(document.querySelector("#chart-departement"), optionsDepartement);
    chartDepartement.render();

    // Chart Poste - Line
    const optionsPoste = {
        series: [{
            name: 'Employés',
            data: statsPoste.map(item => parseInt(item.count))
        }],
        chart: {
            height: 300,
            type: 'line',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        xaxis: {
            categories: statsPoste.map(item => item.poste),
        }
    };
    const chartPoste = new ApexCharts(document.querySelector("#chart-poste"), optionsPoste);
    chartPoste.render();

    // Chart Activité - Donut
    const optionsActivite = {
        series: statsActivite.map(item => parseInt(item.count)),
        chart: {
            type: 'donut',
            height: 300
        },
        labels: statsActivite.map(item => item.statut),
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };
    const chartActivite = new ApexCharts(document.querySelector("#chart-activite"), optionsActivite);
    chartActivite.render();
});
</script>

</body>
</html>