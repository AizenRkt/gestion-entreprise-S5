<?php
if (isset($_GET['mssg'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Toastify({
                text: '" . addslashes($_GET['mssg']) . "',
                duration: 4000,
                gravity: 'top',
                position: 'right',
                backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)',
                close: true
            }).showToast();
        });
    </script>";
    unset($_GET['mssg']);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>État de Paie - BackOffice Mazer Entreprise</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    
    <style>
        .table-responsive-custom {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .table-etat-paie {
            width: 100%;
            min-width: 2400px;
            border-collapse: collapse;
            font-size: 0.75rem;
            background: white;
            margin: 0;
        }
        
        .table-etat-paie thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .table-etat-paie thead tr:first-child th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #5a67d8;
            white-space: nowrap;
            font-size: 0.7rem;
        }
        
        .table-etat-paie thead tr:nth-child(2) th {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
            font-weight: 600;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #5a67d8;
            white-space: nowrap;
            font-size: 0.65rem;
        }
        
        .table-etat-paie tbody td {
            padding: 10px 8px;
            border: 1px solid #e0e0e0;
            white-space: nowrap;
            background: white;
        }
        
        .table-etat-paie tbody tr:hover {
            background-color: #f8f9ff;
        }
        
        .table-etat-paie tbody tr.total-row {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            font-weight: 700;
            color: white;
        }
        
        .table-etat-paie tbody tr.total-row td {
            background: transparent;
            border-color: #e91e63;
        }
        
        .col-sticky-left {
            position: sticky;
            left: 0;
            z-index: 5;
            background: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        thead .col-sticky-left {
            z-index: 15;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .badge-cat {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.65rem;
        }
        
        .badge-cat-1A {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-cat-4A {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        
        .badge-cat-HC {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        .montant-positif {
            color: #2e7d32;
            font-weight: 600;
        }
        
        .montant-negatif {
            color: #c62828;
            font-weight: 600;
        }
        
        .filter-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: white;
        }
        
        .filter-section .form-control,
        .filter-section .form-select {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .stats-icon.purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stats-icon.green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }
        
        .stats-icon.orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .stats-icon.blue {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .scroll-hint {
            text-align: center;
            color: #666;
            font-size: 0.85rem;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .scroll-hint i {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateX(0);
            }
            40% {
                transform: translateX(-10px);
            }
            60% {
                transform: translateX(-5px);
            }
        }
        
        .export-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .table-etat-paie {
                font-size: 0.65rem;
            }
            
            .table-etat-paie thead tr:first-child th,
            .table-etat-paie thead tr:nth-child(2) th {
                padding: 8px 4px;
                font-size: 0.6rem;
            }
            
            .table-etat-paie tbody td {
                padding: 8px 4px;
            }
            
            .stats-card {
                margin-bottom: 15px;
            }
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
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>État de Paie de l'Entreprise</h3>
                        <p class="text-subtitle text-muted">Récapitulatif des salaires et charges</p>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <section class="section">
                <div class="filter-section">
                    <div class="row align-items-end">
                        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Période</label>
                            <input type="month" class="form-control" value="2025-10" id="filterPeriode">
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Service</label>
                            <select class="form-select" id="filterService">
                                <option value="">Tous les services</option>
                                <option value="admin">Administration</option>
                                <option value="rh">Ressources Humaines</option>
                                <option value="tech">Technique</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Département</label>
                            <select class="form-select" id="filterCategorie">
                                <option value="">Toutes les département</option>
                                <option value="1A">Administratif</option>
                                <option value="4A">Recherche</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <button class="btn btn-light w-100" onclick="filtrerTable()">
                                <i class="bi bi-search me-2"></i>Rechercher
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Statistiques -->
            <section class="section">
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon purple">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h6 class="text-muted mb-1">Total Employés</h6>
                            <h3 class="mb-0" id="statEmployes">3</h3>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon green">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <h6 class="text-muted mb-1">Masse Salariale Brute</h6>
                            <h3 class="mb-0" id="statBrut">3 373 404.30</h3>
                            <small class="text-muted">Ar</small>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon orange">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <h6 class="text-muted mb-1">Total Retenues</h6>
                            <h3 class="mb-0" id="statRetenues">126 532.44</h3>
                            <small class="text-muted">Ar</small>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon blue">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <h6 class="text-muted mb-1">Net à Payer Total</h6>
                            <h3 class="mb-0" id="statNet">3 362 191.22</h3>
                            <small class="text-muted">Ar</small>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tableau -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="card-title mb-3 mb-md-0">Liste des Paies - Octobre 2025</h5>
                            <div class="export-buttons">
                                <button class="btn btn-sm btn-success" onclick="exporterExcel()">
                                    <i class="bi bi-file-earmark-excel me-1"></i>Excel
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="exporterPDF()">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                                </button>
                                <button class="btn btn-sm btn-primary" onclick="window.print()">
                                    <i class="bi bi-printer me-1"></i>Imprimer
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="scroll-hint">
                            <i class="bi bi-arrow-left-right"></i>
                            <strong>Astuce:</strong> Faites glisser horizontalement pour voir toutes les colonnes
                        </div>
                        
                        <div class="table-responsive-custom">
                            <table class="table-etat-paie" id="tablePaie">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="col-sticky-left" style="min-width: 120px;">DATE</th>
                                        <th rowspan="2" style="min-width: 80px;">MATR.</th>
                                        <th rowspan="2" style="min-width: 100px;">N° MALTS</th>
                                        <th rowspan="2" style="min-width: 100px;">N° C.MALTS</th>
                                        <th rowspan="2" style="min-width: 150px;">NOM et Prénoms</th>
                                        <th rowspan="2" style="min-width: 100px;">Date d'embauche</th>
                                        <th rowspan="2" style="min-width: 100px;">ANCIENNETÉ</th>
                                        <th rowspan="2" style="min-width: 60px;">CAT</th>
                                        <th rowspan="2" style="min-width: 120px;">Fonction</th>
                                        <th rowspan="2" style="min-width: 120px;">SALAIRE DE BASE</th>
                                        <th colspan="2" style="min-width: 100px;">Taux</th>
                                        <th rowspan="2" style="min-width: 100px;">SALAIRE DE BASE H.SUP</th>
                                        <th rowspan="2" style="min-width: 100px;">INDEMNITÉ</th>
                                        <th rowspan="2" style="min-width: 80px;">RAPPEL</th>
                                        <th rowspan="2" style="min-width: 80px;">AUTRES A.V</th>
                                        <th rowspan="2" style="min-width: 120px;">NB SUP.MAJ.</th>
                                        <th rowspan="2" style="min-width: 120px;">Salaire Brut</th>
                                        <th rowspan="2" style="min-width: 100px;">CNaPS 1%</th>
                                        <th rowspan="2" style="min-width: 100px;">CNaPS 8%</th>
                                        <th rowspan="2" style="min-width: 100px;">OSTIE 1%</th>
                                        <th rowspan="2" style="min-width: 100px;">OSTIE 5%</th>
                                        <th rowspan="2" style="min-width: 100px;">Banastra Sanitaire</th>
                                        <th rowspan="2" style="min-width: 100px;">Impôt 0%</th>
                                        <th rowspan="2" style="min-width: 80px;">IR 5%</th>
                                        <th rowspan="2" style="min-width: 100px;">Montant IRSA IR</th>
                                        <th rowspan="2" style="min-width: 80px;">SGI NET</th>
                                        <th rowspan="2" style="min-width: 100px;">% IR BTB</th>
                                        <th rowspan="2" style="min-width: 120px;">TOTAL RETENUES</th>
                                        <th rowspan="2" style="min-width: 120px;">SALAIRE NET</th>
                                        <th rowspan="2" style="min-width: 100px;">AVANCE</th>
                                        <th rowspan="2" style="min-width: 120px;">NET A PAYER</th>
                                        <th rowspan="2" style="min-width: 120px;">AUTRES INDEMNITÉS</th>
                                        <th rowspan="2" style="min-width: 100px;">NET ISO.MAJ.</th>
                                    </tr>
                                    <tr>
                                        <th style="min-width: 80px;">T.H (HR)</th>
                                        <th style="min-width: 80px;">T.J (JRS)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-categorie="1A">
                                        <td class="col-sticky-left">31-oct-25</td>
                                        <td>1102</td>
                                        <td>779 912 432 497</td>
                                        <td></td>
                                        <td><strong>RAMAROSON Feno</strong></td>
                                        <td class="text-center">07/02/2023</td>
                                        <td class="text-center">1A</td>
                                        <td class="text-center"><span class="badge-cat badge-cat-1A">1A</span></td>
                                        <td>Secrétaire</td>
                                        <td class="text-right montant-positif">315 000.00</td>
                                        <td class="text-right">1 890.00</td>
                                        <td class="text-right">10 500.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right montant-positif">315 000.00</td>
                                        <td class="text-right">3 150.00</td>
                                        <td class="text-right">25 200.00</td>
                                        <td class="text-right">3 150.00</td>
                                        <td class="text-right">15 750.00</td>
                                        <td class="text-right">3 000.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right montant-negatif">50 250.00</td>
                                        <td class="text-right montant-positif">264 750.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right montant-positif">264 750.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                    </tr>
                                    <tr data-categorie="4A">
                                        <td class="col-sticky-left">31-oct-25</td>
                                        <td>1404</td>
                                        <td>779 912 404 404</td>
                                        <td></td>
                                        <td><strong>DEMARAY GILBERT</strong></td>
                                        <td class="text-center">01/07/2014</td>
                                        <td class="text-center">4A</td>
                                        <td class="text-center"><span class="badge-cat badge-cat-4A">4A</span></td>
                                        <td>Courrier Magazine</td>
                                        <td class="text-right montant-positif">210 000.00</td>
                                        <td class="text-right">1 260.00</td>
                                        <td class="text-right">7 000.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">23 604.30</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right montant-positif">233 604.30</td>
                                        <td class="text-right">2 336.04</td>
                                        <td class="text-right">18 688.34</td>
                                        <td class="text-right">2 336.04</td>
                                        <td class="text-right">11 680.22</td>
                                        <td class="text-right">3 000.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right montant-negatif">38 040.64</td>
                                        <td class="text-right montant-positif">195 563.66</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right montant-positif">195 563.66</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                    </tr>
                                    <tr data-categorie="HC">
                                        <td class="col-sticky-left">31-oct-25</td>
                                        <td>1104</td>
                                        <td>779 916 002 888</td>
                                        <td></td>
                                        <td><strong>MAHINTSET ALEXANDER</strong></td>
                                        <td class="text-center">01/12/2009</td>
                                        <td class="text-center">HC</td>
                                        <td class="text-center"><span class="badge-cat badge-cat-HC">HC</span></td>
                                        <td>Responsable Administratif et Financier</td>
                                        <td class="text-right montant-positif">2 500 000.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">324 800.00</td>
                                        <td class="text-right">500 000.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right montant-positif">2 824 800.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">3 000.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">35 736.00</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right montant-negatif">38 241.80</td>
                                        <td class="text-right montant-positif">2 901 877.56</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right montant-positif">2 901 877.56</td>
                                        <td class="text-right">-</td>
                                        <td class="text-right">264 000.00</td>
                                    </tr>
                                    <tr class="total-row">
                                        <td class="col-sticky-left" colspan="9"><strong>TOTAL GÉNÉRAL</strong></td>
                                        <td class="text-right"><strong>3 025 000.00</strong></td>
                                        <td class="text-right"><strong>3 150.00</strong></td>
                                        <td class="text-right"><strong>17 500.00</strong></td>
                                        <td class="text-right"><strong>324 800.00</strong></td>
                                        <td class="text-right"><strong>523 604.30</strong></td>
                                        <td class="text-right"><strong>-</strong></td>
                                        <td class="text-right"><strong>-</strong></td>
                                        <td class="text-right"><strong>-</strong></td>
                                        <td class="text-right"><strong>3 373 404.30</strong></td>
                                        <td class="text-right"><strong>5 486.04</strong></td>
                                        <td class="text-right"><strong>43 888.34</strong></td>
                                        <td class="text-right"><strong>5 486.04</strong></td>
                                        <td class="text-right"><strong>27 430.22</strong></td>
                                        <td class="text-right"><strong>9 000.00</strong></td>
                                        <td class="text-right"><strong>-</strong></td>
                                        <td class="text-right"><strong>-</strong></td>
                                        <td class="text-right"><strong>35 736.00</strong></td>
                                        <td class="text-right"><strong>-</strong></td>
                                        <td class="text-right"><strong>-</strong></td>
                                        <td class="text-right"><strong>126 532.44</strong></td>
                                        <td class="text-right"><strong>3 362 191.22</strong></td>
                                        <td class="text-right"><strong>-</strong></td>
                                        <td class="text-right"><strong>3 362 191.22</strong></td>
                                        <td class="text-right"><strong>-</strong></td>
                                        <td class="text-right"><strong>264 000.00</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Résumé des charges -->
            <section class="section">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Charges Sociales Patronales</h5>
                            </div>
                            <div class="card-body" style="margin-top: 50px">
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span><i class="bi bi-shield-check me-2 text-primary"></i>CNaPS 8%</span>
                                    <strong class="text-primary">43 888.34 Ar</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span><i class="bi bi-heart-pulse me-2 text-primary"></i>OSTIE 5%</span>
                                    <strong class="text-primary">27 430.22 Ar</strong>
                                </div>
                                <div class="d-flex justify-content-between bg-light p-3 rounded">
                                    <strong><i class="bi bi-calculator me-2"></i>Total Charges Patronales</strong>
                                    <strong class="text-primary fs-5">71 318.56 Ar</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Charges Sociales Salariales</h5>
                            </div>
                            <div class="card-body" style="margin-top: 50px">
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span><i class="bi bi-shield-check me-2 text-success"></i>CNaPS 1%</span>
                                    <strong class="text-success">5 486.04 Ar</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span><i class="bi bi-heart-pulse me-2 text-success"></i>OSTIE 1%</span>
                                    <strong class="text-success">5 486.04 Ar</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span><i class="bi bi-hospital me-2 text-success"></i>Sanitaire</span>
                                    <strong class="text-success">9 000.00 Ar</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                                    <span><i class="bi bi-receipt-cutoff me-2 text-success"></i>IRSA</span>
                                    <strong class="text-success">35 736.00 Ar</strong>
                                </div>
                                <div class="d-flex justify-content-between bg-light p-3 rounded">
                                    <strong><i class="bi bi-calculator me-2"></i>Total Charges Salariales</strong>
                                    <strong class="text-success fs-5">55 708.08 Ar</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Graphiques -->
            <section class="section">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title"><i class="bi bi-bar-chart-line me-2"></i>Répartition par Catégorie</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4 mb-3">
                                        <div class="p-4 border rounded">
                                            <div class="mb-3">
                                                <span class="badge-cat badge-cat-1A fs-5">1A</span>
                                            </div>
                                            <h6 class="text-muted">Salaire Brut</h6>
                                            <h4 class="text-primary">315 000.00 Ar</h4>
                                            <small class="text-muted">1 employé</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="p-4 border rounded">
                                            <div class="mb-3">
                                                <span class="badge-cat badge-cat-4A fs-5">4A</span>
                                            </div>
                                            <h6 class="text-muted">Salaire Brut</h6>
                                            <h4 class="text-primary">233 604.30 Ar</h4>
                                            <small class="text-muted">1 employé</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="p-4 border rounded">
                                            <div class="mb-3">
                                                <span class="badge-cat badge-cat-HC fs-5">HC</span>
                                            </div>
                                            <h6 class="text-muted">Salaire Brut</h6>
                                            <h4 class="text-primary">2 824 800.00 Ar</h4>
                                            <small class="text-muted">1 employé</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Notes et observations -->
            <section class="section">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Notes et Observations</h5>
                    </div>
                    <div class="card-body" style="margin-top: 50px">
                        <div class="alert alert-light-info mb-3">
                            <div class="d-flex">
                                <i class="bi bi-lightbulb fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Informations Importantes</h6>
                                    <ul class="mb-0">
                                        <li>Les calculs sont effectués selon la législation du travail en vigueur</li>
                                        <li>Les taux de cotisations sociales appliqués sont conformes aux barèmes officiels</li>
                                        <li>L'IRSA est calculé selon le barème progressif en vigueur</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3"><i class="bi bi-calendar-check me-2 text-primary"></i>Période de référence</h6>
                                <p class="text-muted">Octobre 2025 - Du 01/10/2025 au 31/10/2025</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Date d'édition</h6>
                                <p class="text-muted">Généré le <?= date('d/m/Y à H:i') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>    
</body>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/toastify.js"></script>
