<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Globales des Employés</title>
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
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
                            <h3>Statistiques Globales des Employés</h3>
                            <p class="text-subtitle text-muted">Vue d'ensemble des métriques RH<?php if ($selectedYear) { echo ' jusqu\'au ' . ($selectedMonth ? date('F Y', strtotime("$selectedYear-$selectedMonth-01")) : "31 décembre $selectedYear"); } else { echo ' pour l\'année ' . $data['annee']; } ?></p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= Flight::base() ?>/">Accueil</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Statistiques</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="get" action="">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="month" class="form-label">Mois</label>
                                        <select name="month" id="month" class="form-control">
                                            <option value="" <?php echo (!$selectedMonth) ? 'selected' : ''; ?>>Tous</option>
                                            <?php
                                            $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                                            for ($i = 1; $i <= 12; $i++):
                                            ?>
                                            <option value="<?php echo $i; ?>" <?php echo ($selectedMonth == $i) ? 'selected' : ''; ?>><?php echo $months[$i-1]; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="year" class="form-label">Année</label>
                                        <input type="number" name="year" id="year" class="form-control" value="<?php echo isset($selectedYear) ? $selectedYear : date('Y'); ?>" min="2000" max="2030">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <a href="?" class="btn btn-secondary me-2">Réinitialiser</a>
                                        <button type="submit" class="btn btn-primary">Filtrer</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Taux de Turnover</h5>
                                    <h2 class="text-primary"><?php echo $data['taux_turnover']; ?>%</h2>
                                    <p>Pourcentage d'employés ayant démissioné</p>
                                    <div id="turnoverChart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Taux d'Absentéisme</h5>
                                    <h2 class="text-warning"><?php echo $data['taux_absenteisme']; ?>%</h2>
                                    <p>Pourcentage de jours d'absence</p>
                                    <div id="absenteismeChart"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Ancienneté Moyenne</h5>
                                    <h2 class="text-success"><?php echo $data['anciennete_moyenne']; ?> ans</h2>
                                    <p>Durée moyenne en entreprise</p>
                                    <div id="ancienneteChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <footer>
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2025 &copy; Gestion Entreprise</p>
                    </div>
                    <div class="float-end">
                        <p>Propulsé par <span class="text-danger">Flight PHP</span></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="<?= Flight::base() ?>/public/template/assets/extensions/dayjs/dayjs.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/apexcharts/apexcharts.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    
    
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

    <script>
        // Graphique Turnover - Bar vertical (Comparison)
        var turnoverOptions = {
            series: [{
                name: 'Turnover',
                data: [<?php echo $data['taux_turnover']; ?>]
            }],
            chart: {
                type: 'bar',
                height: 250
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '40%',
                    endingShape: 'rounded'
                }
            },
            colors: ['#dc3545'],
            xaxis: {
                categories: ['<?php echo $data['annee']; ?>']
            },
            yaxis: {
                title: {
                    text: '%'
                },
                min: 0,
                max: 100
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) { return val + '%'; }
            }
        };
        var turnoverChart = new ApexCharts(document.querySelector("#turnoverChart"), turnoverOptions);
        turnoverChart.render();

        // Graphique Absentéisme - Bar vertical (Comparison)
        var absenteismeOptions = {
            series: [{
                name: 'Absentéisme',
                data: [<?php echo $data['taux_absenteisme']; ?>]
            }],
            chart: {
                type: 'bar',
                height: 250
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '40%',
                    endingShape: 'rounded'
                }
            },
            colors: ['#ffc107'],
            xaxis: {
                categories: ['<?php echo $data['annee']; ?>']
            },
            yaxis: {
                title: {
                    text: '%'
                },
                min: 0,
                max: 100
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) { return val + '%'; }
            }
        };
        var absenteismeChart = new ApexCharts(document.querySelector("#absenteismeChart"), absenteismeOptions);
        absenteismeChart.render();

        // Graphique Ancienneté - Dot plot (Scatter)
        var ancienneteOptions = {
            series: [{
                name: 'Ancienneté',
                data: [{x: 'Moyenne', y: <?php echo $data['anciennete_moyenne']; ?>}]
            }],
            chart: {
                type: 'scatter',
                height: 250
            },
            colors: ['#28a745'],
            xaxis: {
                categories: ['Moyenne'],
                title: {
                    text: 'Ancienneté'
                }
            },
            yaxis: {
                title: {
                    text: 'Années'
                },
                min: 0
            },
            markers: {
                size: 10
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    // Pour scatter, la valeur est dans opts.w.config.series[seriesIndex].data[dataPointIndex].y
                    var y = opts.w.config.series[opts.seriesIndex].data[opts.dataPointIndex].y;
                    return y + ' ans';
                }
            }
        };
        var ancienneteChart = new ApexCharts(document.querySelector("#ancienneteChart"), ancienneteOptions);
        ancienneteChart.render();
    </script>
</body>
</html>
