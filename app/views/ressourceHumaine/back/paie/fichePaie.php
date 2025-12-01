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
        /* --- Complete CSS for Fiche de Paie --- */
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
                                                <span class="info-value nom"></span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Fonction :</span>
                                                <span class="info-value fonction"></span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Date d'embauche :</span>
                                                <span class="info-value date_embauche"></span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Ancienneté :</span>
                                                <span class="info-value anciennete"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Classification :</span>
                                                <span class="info-value classification"></span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Salaire de base :</span>
                                                <span class="info-value montant-highlight"></span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Taux journaliers :</span>
                                                <span class="info-value taux_journalier"></span>
                                            </div>
                                            <div class="info-item mb-2">
                                                <span class="info-label">Taux horaires :</span>
                                                <span class="info-value taux_horaire"></span>
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

                                        <!-- Salaire Brut affiché -->
                                        <tr>
                                            <td>Salaire Brut</td>
                                            <td class="text-right" colspan="3">
                                                <span class="salaire-brut-display"></span>
                                            </td>
                                        </tr>

                                        <tr class="total-row">
                                            <td colspan="3"><strong>TOTAL IRSA</strong></td>
                                            <td class="text-right total-irsa"><strong>0</strong></td>
                                        </tr>

                                        <tr>
                                            <td>Autres indemnités</td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"></td>
                                        </tr>
                                        <tr class="net-payer">
                                            <td colspan="3"><strong>Net à payer</strong></td>
                                            <td class="text-right"><strong></strong></td>
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

<script>
document.addEventListener("DOMContentLoaded", () => {

    const parts = window.location.pathname.split("/").filter(Boolean);
    const employeId = parts[parts.length - 3];
    const mois = parts[parts.length - 2];
    const annee = parts[parts.length - 1];

    const tbody = document.querySelector(".table-paie tbody");
    const salaireDisplay = document.querySelector(".salaire-brut-display");

    let taux = [];
    let employe = {};
    let heuresSupp = [];
    let primes = [];

    // ============================
    // FETCH EMPLOYEE
    // ============================
    fetch(`<?= Flight::base() ?>/employe/${employeId}`)
        .then(res => res.json())
        .then(res => {
            if (!res.success || !res.data) return;

            employe = res.data;

            const dateEmbauche = new Date(employe.date_embauche);
            const now = new Date();
            let years = now.getFullYear() - dateEmbauche.getFullYear();
            let months = now.getMonth() - dateEmbauche.getMonth();
            let days = now.getDate() - dateEmbauche.getDate();
            if (days < 0) { months--; days += new Date(now.getFullYear(), now.getMonth(), 0).getDate(); }
            if (months < 0) { years--; months += 12; }
            const anciennete = `${years} an(s) ${months} mois et ${days} jour(s)`;

            const salaire_base = Number(employe.contrat.salaire_base);
            const taux_journalier = salaire_base / 30;
            const taux_horaire = taux_journalier / 9;

            // Fill UI
            document.querySelector(".info-value.nom").textContent = employe.nom + " " + employe.prenom;
            document.querySelector(".info-value.fonction").textContent = employe.titre_poste;
            document.querySelector(".info-value.date_embauche").textContent = dateEmbauche.toLocaleDateString('fr-FR');
            document.querySelector(".info-value.anciennete").textContent = anciennete;
            document.querySelector(".info-value.classification").textContent = employe.id_poste;
            document.querySelector(".info-value.montant-highlight").textContent = salaire_base.toLocaleString('fr-FR');
            document.querySelector(".info-value.taux_journalier").textContent = taux_journalier.toLocaleString('fr-FR');
            document.querySelector(".info-value.taux_horaire").textContent = taux_horaire.toLocaleString('fr-FR');

            salaireDisplay.textContent = salaire_base.toLocaleString('fr-FR');

            // Fetch heures supp
            return fetch(`<?= Flight::base() ?>/api/heures-supp/${employeId}/${mois}/${annee}`);
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) heuresSupp = res.data;
            return fetch(`<?= Flight::base() ?>/api/prime/${employeId}/${mois}/${annee}`);
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.data) primes = res.data;
            updatePaieTable(Number(salaireDisplay.textContent.replace(/\s/g, '')));
        });

    // ============================
    // FETCH TAUX ASSURANCE
    // ============================
    fetch("<?= Flight::base() ?>/api/tauxAssurance")
        .then(res => res.json())
        .then(data => {
            taux = data;
            if (salaireDisplay.textContent) updatePaieTable(Number(salaireDisplay.textContent.replace(/\s/g, '')));
        });

    // ============================
    // CALCUL RETENUES
    // ============================
    function calculerRetenues(salaire, rows) {
        let retenues = {};
        let irsaTotal = 0;

        rows.forEach(r => {
            const tauxNum = r.taux / 100;
            let montant = 0;

            if (r.minpay === null && r.maxpay === null) {
                montant = salaire * tauxNum;
            } else if (salaire >= r.minpay && (r.maxpay === null || salaire <= r.maxpay)) {
                montant = (salaire - r.minpay) * tauxNum;
                irsaTotal = montant;
            }

            if (montant > 0) retenues[r.nom] = { montant, taux: r.taux };
        });

        return {
            retenues,
            irsaTotal,
            salaireNet: salaire - Object.values(retenues).reduce((s, r) => s + r.montant, 0)
        };
    }

    // ============================
    // UPDATE TABLE UI
    // ============================
    function updatePaieTable(salaire) {
        const data = calculerRetenues(salaire, taux);

        tbody.querySelectorAll(".dynamic-row").forEach(r => r.remove());

        const sectionIndex = Array.from(tbody.rows).findIndex(r => r.classList.contains("section-header"));
        let insertIndex = sectionIndex + 1;

        // --- Retenues ---
        for (const [nom, info] of Object.entries(data.retenues)) {
            const row = tbody.insertRow(insertIndex++);
            row.classList.add("dynamic-row");
            row.innerHTML = `
                <td>${nom}</td>
                <td class="text-right">-</td>
                <td class="text-right">${info.taux} %</td>
                <td class="text-right">${info.montant.toLocaleString('fr-FR')}</td>
            `;
        }

        // --- Update TOTAL IRSA ---
        const totalIrsaCell = document.querySelector(".total-irsa strong");
        if (totalIrsaCell) totalIrsaCell.textContent = data.irsaTotal.toLocaleString('fr-FR');

        // --- Heures supplémentaires ---
        heuresSupp.forEach(hs => {
            const row = tbody.insertRow(insertIndex++);
            row.classList.add("dynamic-row");
            row.innerHTML = `
                <td>${hs.type_heure_supp}</td>
                <td class="text-right">${hs.nombre_heures}</td>
                <td class="text-right">${hs.taux} %</td>
                <td class="text-right">${hs.montant.toLocaleString('fr-FR')}</td>
            `;
        });

        // --- Primes ---
        primes.forEach(p => {
            const row = tbody.insertRow(insertIndex++);
            row.classList.add("dynamic-row");
            row.innerHTML = `
                <td>${p.nom}</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">${Number(p.montant).toLocaleString('fr-FR')}</td>
            `;
        });

        // --- Net à payer ---
        const totalHS = heuresSupp.reduce((s, hs) => s + Number(hs.montant), 0);
        const totalPrimes = primes.reduce((s, p) => s + Number(p.montant), 0);
        const netRow = document.querySelector(".net-payer td:last-child");
        if (netRow) netRow.textContent = (data.salaireNet + totalHS + totalPrimes).toLocaleString('fr-FR');
    }

});
</script>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/toastify.js"></script>

</body>
</html>
