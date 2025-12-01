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
    <title>Fiche de Paie - BackOffice Mazer Entreprise</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    
    <style>
        .fiche-paie-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
        }
        
        .fiche-paie-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .fiche-paie-header h4 {
            margin: 0;
            font-weight: bold;
        }
        
        .fiche-paie-header h6 {
            margin: 5px 0 0 0;
            font-weight: 600;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }
        
        .info-label {
            font-weight: 600;
            min-width: 150px;
            color: #333;
        }
        
        .info-value {
            color: #000;
        }
        
        .info-highlight {
            background-color: #00bfff;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: 600;
        }
        
        .table-paie {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 0.9rem;
        }
        
        .table-paie th {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: left;
            border: 1px solid #dee2e6;
            font-weight: 600;
            color: #000;
        }
        
        .table-paie td {
            padding: 8px 10px;
            border: 1px solid #dee2e6;
        }
        
        .table-paie tr.section-header {
            background-color: #e9ecef;
            font-weight: 600;
        }
        
        .table-paie tr.section-header td {
            padding: 8px 10px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-row {
            background-color: #f8f9fa;
            font-weight: 700;
            font-size: 1rem;
        }
        
        .net-payer {
            background-color: #fff;
            border: 2px solid #000;
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .montant-highlight {
            background-color: #00bfff;
            color: white;
            padding: 3px 10px;
            border-radius: 3px;
            font-weight: 700;
        }
        
        .avantages-box {
            border: 1px solid #dee2e6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .avantages-box p {
            margin: 5px 0;
            font-size: 0.9rem;
        }
        
        .mode-paiement {
            font-style: italic;
            color: #0000ff;
            margin-top: 20px;
        }
        
        .watermark {
            position: relative;
        }
        
        .watermark::before {
            content: "Page 1";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 120px;
            color: rgba(200, 200, 200, 0.15);
            font-weight: bold;
            z-index: 0;
            pointer-events: none;
        }
        
        .content-wrapper {
            position: relative;
            z-index: 1;
        }
        
        @media print {
            .fiche-paie-container {
                box-shadow: none;
                padding: 20px;
            }
            
            body * {
                visibility: hidden;
            }
            
            .fiche-paie-container, .fiche-paie-container * {
                visibility: visible;
            }
            
            .fiche-paie-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
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
                        <h3>Fiche de Paie</h3>
                        <p class="text-subtitle text-muted">Bulletin de salaire détaillé</p>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-primary me-2" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i> Imprimer
                            </button>
                        </div>

                        <div class="fiche-paie-container watermark">
                            <div class="content-wrapper">
                                <!-- En-tête -->
                                <div class="fiche-paie-header">
                                    <h4>FICHE DE PAIE</h4>
                                    <h6>ARRETE AU 31/10/25</h6>
                                </div>

                                <!-- Informations employé -->
                                <div class="info-section">
                                    <div class="info-grid">
                                        <div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Nom et Prénoms :</span>
                                                <span class="info-value">RAZAFIARISON Laza</span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Fonction :</span>
                                                <span class="info-value">DPH</span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Date d'embauche :</span>
                                                <span class="info-value info-highlight">25/09/2010</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Ancienneté :</span>
                                                <span class="info-value">14 an(s) 7 mois et 12 jour(s)</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Classification :</span>
                                                <span class="info-value">HC</span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Salaire de base :</span>
                                                <span class="info-value montant-highlight">300 000.00</span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Taux journaliers :</span>
                                                <span class="info-value">10 000.00</span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Taux horaires :</span>
                                                <span class="info-value">1 073.00</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Indice :</span>
                                                <span class="info-value">1238.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tableau des éléments de paie -->
                                <table class="table-paie">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;">Désignations</th>
                                            <th class="text-right" style="width: 15%;">Nombre</th>
                                            <th class="text-right" style="width: 15%;">Taux</th>
                                            <th class="text-right" style="width: 20%;">Montant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="section-header">
                                            <td colspan="4">
                                                <i class="bi bi-wallet2 me-2"></i>A VALEUR
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Absences déductibles</td>
                                            <td class="text-right">1</td>
                                            <td class="text-right">10 000.00</td>
                                            <td class="text-right">300 000.00</td>
                                        </tr>
                                        <tr>
                                            <td>Absences déductibles</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">10 000.00</td>
                                        </tr>
                                        <tr>
                                            <td>Primes de rendement</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <td>Primes d'ancienneté</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <td>Heures supplémentaires majorées de 30%</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">2 350.00</td>
                                        </tr>
                                        <tr>
                                            <td>Heures supplémentaires majorées de 40%</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">2 423.00</td>
                                        </tr>
                                        <tr>
                                            <td>Heures supplémentaires majorées de 50%</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">2 637.00</td>
                                        </tr>
                                        <tr>
                                            <td>Heures supplémentaires majorées de 100%</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">3 452.00</td>
                                        </tr>
                                        <tr>
                                            <td>Majoration pour heures de nuit</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">515.00</td>
                                        </tr>
                                        <tr>
                                            <td>Primes diverses</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <td>Rappels sur période antérieure</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <td>Droits de congés</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">10 000.00</td>
                                        </tr>
                                        <tr>
                                            <td>Droits de préavis</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">10 000.00</td>
                                        </tr>
                                        <tr>
                                            <td>Indemnités de licenciement</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">10 000.00</td>
                                        </tr>
                                        <tr class="total-row">
                                            <td colspan="3"><strong>Salaire brut</strong></td>
                                            <td class="text-right"><strong>300 000.00</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Retenue CNaPS 1%</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">3 000.00</td>
                                        </tr>
                                        <tr>
                                            <td>Retenue sanitaire</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right">3 000.00</td>
                                        </tr>
                                        <tr>
                                            <td>Tranche IRSA DE 0 à 350 0000</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <td>Tranche IRSA DE 350 0001 à 400 000</td>
                                            <td class="text-right"></td>
                                            <td class="text-right">5%</td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <td>Tranche IRSA DE 400 0001 à 500 000</td>
                                            <td class="text-right"></td>
                                            <td class="text-right">10%</td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <td>Tranche IRSA DE 500 001 à 600 000</td>
                                            <td class="text-right"></td>
                                            <td class="text-right">15%</td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <td>Tranche IRSA DE 600 001 à 4000 000</td>
                                            <td class="text-right"></td>
                                            <td class="text-right">20%</td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr>
                                            <td>Tranche IRSA PLUS DE 4000 000</td>
                                            <td class="text-right"></td>
                                            <td class="text-right">25%</td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr class="total-row">
                                            <td colspan="3"><strong>TOTAL IRSA</strong></td>
                                            <td class="text-right"><strong></strong></td>
                                        </tr>
                                        <tr class="total-row">
                                            <td colspan="3"><strong>Total des retenues</strong></td>
                                            <td class="text-right"><strong>6 000.00</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Autres indemnités</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr class="net-payer">
                                            <td colspan="3"><strong>Net à payer</strong></td>
                                            <td class="text-right"><strong>294 000.00</strong></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Avantages en nature -->
                                <div class="avantages-box">
                                    <p><strong>Avantages en nature :</strong></p>
                                    <p><strong>Déductions IRSA :</strong></p>
                                    <p><strong>Montant imposable :</strong> 294 000.00</p>
                                </div>

                                <!-- Mode de paiement -->
                                <div class="mode-paiement">
                                    <p><strong>Mode de paiement : </strong><em>Virement/chèque</em></p>
                                </div>
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
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/toastify.js"></script>

</body>
</html>