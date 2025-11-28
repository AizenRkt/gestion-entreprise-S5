<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Employé - BackOffice Mazer Entreprise</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/toastify-js/src/toastify.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/custom/module1-RH/employe/ficheEmploye.css">
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
                        <h3>Fiche Employé</h3>
                        <p class="text-subtitle text-muted">Consultation et gestion des informations employé</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active" aria-current="page">Fiche Employé</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- En-tête employé -->
            <section class="section">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="avatar avatar-xl bg-light-primary rounded-circle mb-3 mb-md-0">
                                    <i class="bi bi-person-fill fs-1 text-primary"></i>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h4 class="mb-2">Rakoto Jean</h4>
                                <p class="text-muted mb-2"><i class="bi bi-briefcase me-2"></i>Développeur Full Stack</p>
                                <p class="text-muted mb-2"><i class="bi bi-building me-2"></i>Service IT</p>
                                <span class="badge bg-light-success">Actif</span>
                            </div>
                            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                <button class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-printer"></i> Imprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Onglets -->
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="employeeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="infos-tab" data-bs-toggle="tab" data-bs-target="#infos" type="button" role="tab" aria-controls="infos" aria-selected="true">
                                    <i class="bi bi-person-badge me-2"></i>Informations Personnelles
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="poste-tab" data-bs-toggle="tab" data-bs-target="#poste" type="button" role="tab" aria-controls="poste" aria-selected="false">
                                    <i class="bi bi-briefcase me-2"></i>Poste
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="false">
                                    <i class="bi bi-file-earmark-text me-2"></i>Documents
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="employeeTabsContent" style="margin-top: 20px;">
                            
                            <!-- Onglet Informations Personnelles -->
                            <div class="tab-pane fade show active" id="infos" role="tabpanel" aria-labelledby="infos-tab">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h6 class="text-primary mb-3">Identité</h6>
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted" style="width: 40%;">Nom</td>
                                                    <td class="fw-semibold">Rakoto</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Prénom</td>
                                                    <td class="fw-semibold">Jean</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Date de naissance</td>
                                                    <td class="fw-semibold">15/03/1990</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Sexe</td>
                                                    <td class="fw-semibold">Masculin</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Nationalité</td>
                                                    <td class="fw-semibold">Malgache</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>

                                    <div class="col-12 col-md-6">
                                        <h6 class="text-primary mb-3">Contact</h6>
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted" style="width: 40%;">Email professionnel</td>
                                                    <td class="fw-semibold">jean.rakoto@entreprise.mg</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td class="text-muted">Téléphone</td>
                                                    <td class="fw-semibold">+261 32 00 000 00</td>
                                                </tr>
                                                
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>

                            <!-- Onglet Poste -->
                            <div class="tab-pane fade" id="poste" role="tabpanel" aria-labelledby="poste-tab">
                                <div class="row">
                                    <div class="col-12 col-lg-6 mb-4">
                                        <h6 class="text-primary mb-3">Poste actuel</h6>
                                        <div class="card bg-light-primary">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-3">
                                                        <div class="avatar avatar-lg bg-primary">
                                                            <i class="bi bi-briefcase-fill text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-2">Développeur Full Stack</h5>
                                                        <p class="mb-1"><i class="bi bi-building me-2"></i><strong>Service :</strong> IT - Développement</p>
                                                        <p class="mb-1"><i class="bi bi-calendar-check me-2"></i><strong>Date d'embauche :</strong> 01/06/2022</p>
                                                        <p class="mb-1"><i class="bi bi-person-badge me-2"></i><strong>Type de contrat :</strong> CDI</p>
                                                        <p class="mb-1"><i class="bi bi-cash me-2"></i><strong>Salaire base :</strong> 2 500 000 Ar</p>
                                                        <span class="badge bg-success mt-2">En poste</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="text-primary mb-3 mt-4">Responsabilités</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Développement d'applications web</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Maintenance des systèmes existants</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Coordination avec l'équipe technique</li>
                                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Documentation des projets</li>
                                        </ul>

                                        <h6 class="text-primary mb-3 mt-4">Compétences</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-light-primary">PHP</span>
                                            <span class="badge bg-light-primary">JavaScript</span>
                                            <span class="badge bg-light-primary">MySQL</span>
                                            <span class="badge bg-light-primary">HTML/CSS</span>
                                            <span class="badge bg-light-primary">React</span>
                                            <span class="badge bg-light-primary">Git</span>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-primary mb-0">Historique des postes</h6>
                                        </div>

                                        <div class="timeline">
                                            <div class="timeline-item mb-4">
                                                <div class="timeline-point bg-secondary"></div>
                                                <div class="timeline-content ms-3">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="mb-1">Développeur Junior</h6>
                                                                <span class="badge bg-secondary">Terminé</span>
                                                            </div>
                                                            <p class="text-muted small mb-2">Service IT - Support</p>
                                                            <p class="text-muted small mb-2">
                                                                <i class="bi bi-calendar3 me-1"></i>Du 15/01/2020 au 31/05/2022
                                                            </p>
                                                            <p class="text-muted small mb-0">
                                                                <i class="bi bi-briefcase me-1"></i>CDD - 1 800 000 Ar
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="timeline-item mb-4">
                                                <div class="timeline-point bg-secondary"></div>
                                                <div class="timeline-content ms-3">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="mb-1">Stagiaire Développeur</h6>
                                                                <span class="badge bg-secondary">Terminé</span>
                                                            </div>
                                                            <p class="text-muted small mb-2">Service IT</p>
                                                            <p class="text-muted small mb-2">
                                                                <i class="bi bi-calendar3 me-1"></i>Du 01/09/2019 au 31/12/2019
                                                            </p>
                                                            <p class="text-muted small mb-0">
                                                                <i class="bi bi-briefcase me-1"></i>Stage - 500 000 Ar
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Onglet Documents -->
                            <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-primary mb-0">Liste des documents</h6>
                                    <button class="btn btn-primary btn-sm">
                                        <i class="bi bi-upload me-1"></i>Ajouter un document
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Type de document</th>
                                                <th>Nom du fichier</th>
                                                <th>Date d'ajout</th>
                                                <th>Taille</th>
                                                <th>Statut</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="documentsTableBody">
                                            
                                        </tbody>
                                    </table>
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

<?= $id = (int) $_GET['id']; ?>
<script>
    async function loadDocumentsEmploye(idEmploye) {
        const tbody = document.getElementById("documentsTableBody");
        tbody.innerHTML = `<tr><td colspan="6" class="text-center">Chargement...</td></tr>`;

        try {
            const res = await fetch(`<?= Flight::base() ?>/employe/${idEmploye}/documents`);
            const json = await res.json();

            if (!json.success || json.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">
                    Aucun document trouvé
                </td></tr>`;
                return;
            }

            let html = "";

            json.data.forEach(doc => {
                let icon = "bi bi-file-earmark";
                if (doc.pathScan?.endsWith(".pdf")) icon = "bi bi-file-earmark-pdf text-danger";
                if (doc.pathScan?.endsWith(".jpg") || doc.pathScan?.endsWith(".png")) icon = "bi bi-file-earmark-image text-primary";

                let badge = `<span class="badge bg-light-secondary">Inconnu</span>`;
                if (doc.statut_actuel === "valide") badge = `<span class="badge bg-light-success">Validé</span>`;
                if (doc.statut_actuel === "expire") badge = `<span class="badge bg-light-danger">Expiré</span>`;
                if (doc.statut_actuel === "annule") badge = `<span class="badge bg-light-dark">Annulé</span>`;

                // Lien fichier
                const fileLink = doc.pathScan 
                    ? `public/uploads/data/document/${doc.pathScan}` 
                    : null;

                html += `
                    <tr>
                        <td>
                            <i class="${icon} me-2"></i>
                            <span class="fw-semibold">${doc.type_document}</span>
                        </td>

                        <td>${doc.pathScan ?? "—"}</td>

                        <td>${doc.dateUpload}</td>

                        <td>${doc.size ? doc.size + " KB" : "—"}</td>

                        <td>${badge}</td>

                        <td class="text-center">
                            ${fileLink ? `
                            <a class="btn btn-sm btn-outline-primary me-1" href="${fileLink}" download title="Télécharger">
                                <i class="bi bi-download"></i>
                            </a>

                            <a class="btn btn-sm btn-outline-info me-1" href="<?= Flight::base() ?>/${fileLink}" target="_blank" title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            ` : `
                            <button class="btn btn-sm btn-outline-secondary me-1" disabled title="Aucun fichier">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                            `}

                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="deleteDocument(${doc.id_document})"
                                    title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;

        } catch (error) {
            console.error(error);
            tbody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">
                Erreur de chargement
            </td></tr>`;
        }
    }
    loadDocumentsEmploye(<?= $id ?>);

</script>