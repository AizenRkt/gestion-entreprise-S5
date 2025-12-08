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
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                            <label class="form-label fw-bold">Département</label>
                            <select class="form-select" id="filterCategorie">
                                <option value="">Toutes les départements</option>
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
                            <h3 class="mb-0" id="statEmployes">0</h3>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon green">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <h6 class="text-muted mb-1">Masse Salariale Brute</h6>
                            <h3 class="mb-0" id="statBrut">0</h3>
                            <small class="text-muted">Ar</small>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon orange">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <h6 class="text-muted mb-1">Total Retenues</h6>
                            <h3 class="mb-0" id="statRetenues">0</h3>
                            <small class="text-muted">Ar</small>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card">
                            <div class="stats-icon blue">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <h6 class="text-muted mb-1">Net à Payer Total</h6>
                            <h3 class="mb-0" id="statNet">0</h3>
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
                            <h5 class="card-title mb-3 mb-md-0">Liste des Paies</h5>
                            <div class="export-buttons">
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
                                        <th rowspan="2" class="col-sticky-left">Date</th>
                                        <th rowspan="2">Matricule</th>
                                        <th rowspan="2">Nom et Prénoms</th>
                                        <th rowspan="2">Date d'embauche</th>
                                        <th rowspan="2">Ancienneté</th>
                                        <th rowspan="2">Fonction</th>
                                        <th rowspan="2">Salaire de Base</th>
                                        <th colspan="2">Taux</th>
                                        <th rowspan="2">Heures Supplémentaires</th>
                                        <th rowspan="2">Primes</th>
                                        <th rowspan="2">Avances</th>
                                        <th rowspan="2">Salaire Net</th>
                                    </tr>
                                    <tr>
                                        <th>T.H (HR)</th>
                                        <th>T.J (JRS)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>


            <!-- Résumé des charges -->
<section class="section">
    <div class="row">
        <!-- Charges Patronales -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Charges Sociales Patronales</h5>
                </div>
                <div class="card-body" style="margin-top: 50px">
                    <div id="chargesPatronales" class="charges-list">
                        <!-- Les charges patronales seront ajoutées dynamiquement ici -->
                    </div>
                    <div class="d-flex justify-content-between bg-light p-3 rounded mt-2">
                        <strong><i class="bi bi-calculator me-2"></i>Total Charges Patronales</strong>
                        <strong id="totalPatronales" class="text-primary fs-5">0,00 Ar</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charges Salariales -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Charges Sociales Salariales</h5>
                </div>
                <div class="card-body" style="margin-top: 50px">
                    <div id="chargesSalariales" class="charges-list">
                        <!-- Les charges salariales seront ajoutées dynamiquement ici -->
                    </div>
                    <div class="d-flex justify-content-between bg-light p-3 rounded mt-2">
                        <strong><i class="bi bi-calculator me-2"></i>Total Charges Salariales</strong>
                        <strong id="totalSalariales" class="text-success fs-5">0,00 Ar</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total général des charges -->
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between bg-secondary text-white p-3 rounded mt-3">
                <strong><i class="bi bi-calculator me-2"></i>Total Charges</strong>
                <strong id="totalCharges" class="fs-5">0,00 Ar</strong>
            </div>
        </div>
    </div>
</section>

<!-- Graphiques par Catégorie -->
<section class="section">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="bi bi-bar-chart-line me-2"></i>Répartition par Catégorie</h5>
                </div>
                <div class="card-body">
                    <div id="repartitionCategories" class="row text-center">
                        <!-- Les graphiques par catégorie seront ajoutés dynamiquement ici -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


            <!-- Notes et Observations -->
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
                                <p id="periodeReference" class="text-muted">-</p>
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
<div id="debugInfo" style="margin-bottom:10px; color:blue;"></div>

<script>
async function updateStats(employes, mois, annee) {
    let totalEmployes = 0;
    let masseBrute = 0;
    let totalRetenues = 0;
    let netTotal = 0;

    for (const emp of employes) {
        if (!emp.contrat || !emp.contrat.salaire_base) continue;

        totalEmployes++;
        const salaire_base = Number(emp.contrat.salaire_base);
        
        // Fetch retenues, primes, avances, heures supp as in updateTable()
        const [heuresSupp, primes, avances, tauxAssurance, tauxHeureSup] = await Promise.all([
            fetch(`<?= Flight::base() ?>/api/heures-supp/${emp.id_employe}/${mois}/${annee}`).then(r => r.json()).then(r => r.success ? r.data : []),
            fetch(`<?= Flight::base() ?>/api/prime/${emp.id_employe}/${mois}/${annee}`).then(r => r.json()).then(r => r.success ? r.data : []),
            fetch(`<?= Flight::base() ?>/api/avance/${emp.id_employe}/${mois}/${annee}`).then(r => r.json()).then(r => r.success ? r.data : []),
            fetch("<?= Flight::base() ?>/api/tauxAssurance").then(r => r.json()),
            fetch("<?= Flight::base() ?>/api/tauxHeureSup").then(r => r.json())
        ]);

        function calculerRetenues(salaire, rows) {
            let retenues = {};
            rows.forEach(r => {
                const tauxNum = r.taux / 100;
                let montant = 0;
                if (r.minpay === null && r.maxpay === null) {
                    montant = salaire * tauxNum;
                } else if (salaire >= r.minpay && (r.maxpay === null || salaire <= r.maxpay)) {
                    montant = (salaire - r.minpay) * tauxNum;
                }
                if (montant > 0) retenues[r.nom] = { montant, taux: r.taux };
            });
            return {
                retenues,
                salaireNet: salaire - Object.values(retenues).reduce((s, r) => s + r.montant, 0)
            };
        }

        function calculMontantHeuresSuppParType(nombreHeures, tauxHeureSupData, tauxHoraire) {
            const result = [];
            let restant = nombreHeures;
            for (const range of tauxHeureSupData) {
                if (restant <= 0) break;
                const start = range.heure_debut;
                const end = range.heure_fin;
                const heuresRange = Math.min(restant, end - start + 1);
                const montant = heuresRange * tauxHoraire * (range.taux / 100);
                result.push({ type: range.type_heuresup, heures: heuresRange, taux: range.taux, montant });
                restant -= heuresRange;
            }
            return result;
        }

        const taux_journalier = salaire_base / 30;
        const taux_horaire = taux_journalier / 9;
        const retenuesData = calculerRetenues(salaire_base, tauxAssurance);

        let totalHS = 0;
        heuresSupp.forEach(hs => {
            const hsParType = calculMontantHeuresSuppParType(Number(hs.total_heures_supp || 0), tauxHeureSup, taux_horaire);
            hsParType.forEach(item => totalHS += item.montant);
        });

        const totalPrimes = primes.reduce((sum, p) => sum + Number(p.montant || 0), 0);
        const totalAvances = avances.reduce((sum, a) => sum + Number(a.montant || 0), 0);

        const netAPayer = retenuesData.salaireNet + totalHS + totalPrimes - totalAvances;

        masseBrute += salaire_base;
        totalRetenues += (salaire_base - retenuesData.salaireNet);
        netTotal += netAPayer;
    }

    // Update DOM
    document.getElementById("statEmployes").textContent = totalEmployes;
    document.getElementById("statBrut").textContent = masseBrute.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById("statRetenues").textContent = totalRetenues.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById("statNet").textContent = netTotal.toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2});
}
</script>
<script>
function updateCharges(patronales, salariales, repartition, periode) {
    // --- Patronales ---
    const chargesPat = document.getElementById("chargesPatronales");
    chargesPat.innerHTML = "";
    let totalPat = 0;
    patronales.forEach(item => {
        const div = document.createElement("div");
        div.className = "d-flex justify-content-between mb-3 pb-2 border-bottom";
        div.innerHTML = `<span><i class="${item.icon} me-2 text-primary"></i>${item.nom} ${item.taux ? item.taux + '%' : ''}</span>
                         <strong class="text-primary">${item.montant.toLocaleString('fr-FR')} Ar</strong>`;
        chargesPat.appendChild(div);
        totalPat += item.montant;
    });
    document.getElementById("totalPatronales").textContent = totalPat.toLocaleString('fr-FR') + " Ar";

    // --- Salariales ---
    const chargesSal = document.getElementById("chargesSalariales");
    chargesSal.innerHTML = "";
    let totalSal = 0;
    salariales.forEach(item => {
        const div = document.createElement("div");
        div.className = "d-flex justify-content-between mb-3 pb-2 border-bottom";
        div.innerHTML = `<span><i class="${item.icon} me-2 text-success"></i>${item.nom} ${item.taux ? item.taux + '%' : ''}</span>
                         <strong class="text-success">${item.montant.toLocaleString('fr-FR')} Ar</strong>`;
        chargesSal.appendChild(div);
        totalSal += item.montant;
    });
    document.getElementById("totalSalariales").textContent = totalSal.toLocaleString('fr-FR') + " Ar";

    // --- Répartition par catégorie ---
    const repartDiv = document.getElementById("repartitionCategories");
    repartDiv.innerHTML = "";
    repartition.forEach(cat => {
        const col = document.createElement("div");
        col.className = "col-md-4 mb-3";
        col.innerHTML = `
            <div class="p-4 border rounded">
                <div class="mb-3">
                    <span class="badge-cat badge-cat-${cat.code} fs-5">${cat.code}</span>
                </div>
                <h6 class="text-muted">Salaire Brut</h6>
                <h4 class="text-primary">${cat.montant.toLocaleString('fr-FR')} Ar</h4>
                <small class="text-muted">${cat.nbEmployes} employé${cat.nbEmployes > 1 ? 's' : ''}</small>
            </div>`;
        repartDiv.appendChild(col);
    });

    // --- Période ---
    document.getElementById("periodeReference").textContent = periode;
}
</script>
<script>
async function filtrerTable() {
    const periode = document.getElementById('filterPeriode').value; // e.g., "2025-10"
    const service = document.getElementById('filterService').value || "Tous";
    const categorie = document.getElementById('filterCategorie').value || "Tous";

    if (!periode) {
        alert("Veuillez sélectionner une période.");
        return;
    }

    const [year, month] = periode.split('-');
    const encodedService = encodeURIComponent(service);
    const encodedCategorie = encodeURIComponent(categorie);

    const apiUrl = `<?= Flight::base() ?>/api/empfilter/${encodedService}/${encodedCategorie}/${month}/${year}`;

    try {
        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error("Erreur API: " + response.status);

        const data = await response.json();
        if (data.success) {
            await updateTable(data.data, month, year);
        } else {
            alert("Aucun employé trouvé pour ces filtres.");
            await updateTable([], month, year);
        }
    } catch (err) {
        console.error(err);
        alert("Impossible de charger les données.");
    }
}

async function updateTable(employes, mois, annee) {
    const tbody = document.querySelector("#tablePaie tbody");
    tbody.innerHTML = "";

    // --- Initialize totals ---
    let totalPatronales = 0;
    let totalSalariales = 0;

    if (!employes || employes.length === 0) {
        const tr = document.createElement("tr");
        tr.innerHTML = `<td colspan="31" class="text-center">Aucun employé trouvé pour ces filtres.</td>`;
        tbody.appendChild(tr);

        // Update totals to 0
        document.getElementById("totalPatronales").textContent = "0,00 Ar";
        document.getElementById("totalSalariales").textContent = "0,00 Ar";
        document.getElementById("totalCharges").textContent = "0,00 Ar";

        return;
    }

    for (const emp of employes) {
        if (!emp.contrat || !emp.contrat.salaire_base) continue;

        const [heuresSupp, primes, avances, tauxAssurance, tauxHeureSup] = await Promise.all([
            fetch(`<?= Flight::base() ?>/api/heures-supp/${emp.id_employe}/${mois}/${annee}`)
                .then(r => r.json()).then(r => r.success ? r.data : []),
            fetch(`<?= Flight::base() ?>/api/prime/${emp.id_employe}/${mois}/${annee}`)
                .then(r => r.json()).then(r => r.success ? r.data : []),
            fetch(`<?= Flight::base() ?>/api/avance/${emp.id_employe}/${mois}/${annee}`)
                .then(r => r.json()).then(r => r.success ? r.data : []),
            fetch("<?= Flight::base() ?>/api/tauxAssurance").then(r => r.json()),
            fetch("<?= Flight::base() ?>/api/tauxHeureSup").then(r => r.json())
        ]);

        const salaire_base = Number(emp.contrat.salaire_base);
        const taux_journalier = salaire_base / 30;
        const taux_horaire = taux_journalier / 9;

        // --- Charges calculation ---
        const tauxPatronales = 0.10; // 10% patronales
        const tauxSalariales = 0.05; // 5% salariales
        const chargePat = salaire_base * tauxPatronales;
        const chargeSal = salaire_base * tauxSalariales;

        totalPatronales += chargePat;
        totalSalariales += chargeSal;

        // --- Retenues ---
        function calculerRetenues(salaire, rows) {
            let retenues = {};
            rows.forEach(r => {
                const tauxNum = r.taux / 100;
                let montant = 0;
                if (r.minpay === null && r.maxpay === null) {
                    montant = salaire * tauxNum;
                } else if (salaire >= r.minpay && (r.maxpay === null || salaire <= r.maxpay)) {
                    montant = (salaire - r.minpay) * tauxNum;
                }
                if (montant > 0) retenues[r.nom] = { montant, taux: r.taux };
            });
            const totalRetenues = Object.values(retenues).reduce((s, r) => s + r.montant, 0);
            return {
                retenues,
                salaireNet: salaire - totalRetenues
            };
        }

        // --- Heures sup ---
        function calculMontantHeuresSuppParType(nombreHeures, tauxHeureSupData, tauxHoraire) {
            const result = [];
            let restant = nombreHeures;
            for (const range of tauxHeureSupData) {
                if (restant <= 0) break;
                const start = range.heure_debut;
                const end = range.heure_fin;
                const heuresRange = Math.min(restant, end - start + 1);
                const montant = heuresRange * tauxHoraire * (range.taux / 100);
                result.push({ type: range.type_heuresup, heures: heuresRange, taux: range.taux, montant });
                restant -= heuresRange;
            }
            return result;
        }

        const retenuesData = calculerRetenues(salaire_base, tauxAssurance);

        let totalHS = 0;
        heuresSupp.forEach(hs => {
            const hsParType = calculMontantHeuresSuppParType(Number(hs.total_heures_supp || 0), tauxHeureSup, taux_horaire);
            hsParType.forEach(item => totalHS += item.montant);
        });

        const totalPrimes = primes.reduce((sum, p) => sum + Number(p.montant || 0), 0);
        const totalAvances = avances.reduce((sum, a) => sum + Number(a.montant || 0), 0);
        const netAPayer = retenuesData.salaireNet + totalHS + totalPrimes - totalAvances;

        const tr = document.createElement("tr");
        tr.dataset.categorie = emp.nom_departement || "-";

        const nameLink = `<a href="<?= Flight::base() ?>/paie/fichePaie/${emp.id_employe}/${mois}/${annee}" target="_blank">${emp.nom} ${emp.prenom}</a>`;

        tr.innerHTML = `
            <td class="col-sticky-left">${emp.date_embauche || "-"}</td>
            <td>${emp.id_employe || "-"}</td>
            <td>${nameLink}</td>
            <td class="text-center">${emp.date_embauche || "-"}</td>
            <td class="text-center">-</td>
            <td>${emp.titre_poste || "-"}</td>
            <td class="text-right montant-positif">${salaire_base.toLocaleString('fr-FR')}</td>
            <td class="text-right">${Object.values(retenuesData.retenues).reduce((s, r) => s + r.montant, 0).toLocaleString('fr-FR')}</td>
            <td class="text-right">${totalHS.toLocaleString('fr-FR')}</td>
            <td class="text-right">${totalPrimes.toLocaleString('fr-FR')}</td>
            <td class="text-right">${totalAvances.toLocaleString('fr-FR')}</td>
            <td class="text-right montant-positif">${netAPayer.toLocaleString('fr-FR')}</td>
        `;

        tbody.appendChild(tr);
    }

    // --- Update charges totals in DOM ---
    document.getElementById("totalPatronales").textContent = totalPatronales.toLocaleString('fr-FR', {minimumFractionDigits:2}) + " Ar";
    document.getElementById("totalSalariales").textContent = totalSalariales.toLocaleString('fr-FR', {minimumFractionDigits:2}) + " Ar";
    document.getElementById("totalCharges").textContent = (totalPatronales + totalSalariales).toLocaleString('fr-FR', {minimumFractionDigits:2}) + " Ar";

    // --- Update employee stats ---
    updateStats(employes, mois, annee);
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectService = document.getElementById('filterService');
    const selectDept = document.getElementById('filterCategorie');
    let services = []; // Will store all services for filtering

    // Load departments
    fetch('<?= Flight::base() ?>/api/dept')
        .then(res => res.json())
        .then(data => {
            if(data && Array.isArray(data)) {
                data.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.nom;
                    option.textContent = dept.nom;
                    selectDept.appendChild(option);
                });
            }
        });

    // Load services
    fetch('<?= Flight::base() ?>/api/service')
        .then(res => res.json())
        .then(data => {
            if(data && Array.isArray(data)) {
                services = data; // save for filtering
            }
        });

    // Filter services when department changes
    selectDept.addEventListener('change', function() {
        const deptId = parseInt(this.value);
        selectService.innerHTML = '<option value="">Tous les services</option>'; // reset
        services.forEach(s => {
            if(!deptId || s.id_dept === deptId) {
                const option = document.createElement('option');
                option.value = s.nom;
                option.textContent = s.nom;
                selectService.appendChild(option);
            }
        });
    });
});
</script>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/toastify.js"></script>
