<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des QCM - Mazer</title>
    
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
</head>

<style>
.qcm-card {
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}
.qcm-card:hover {
    transform: scale(1.02);
    box-shadow: rgba(14, 63, 126, 0.06) 0px 0px 0px 1px,
                rgba(42, 51, 70, 0.03) 0px 1px 1px -0.5px,
                rgba(42, 51, 70, 0.04) 0px 2px 2px -1px,
                rgba(42, 51, 70, 0.04) 0px 3px 3px -1.5px,
                rgba(42, 51, 70, 0.03) 0px 5px 5px -2.5px,
                rgba(42, 51, 70, 0.03) 0px 10px 10px -5px,
                rgba(42, 51, 70, 0.03) 0px 24px 24px -8px;
}
</style>

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
                        <h3>Liste des QCM</h3>
                        <p class="text-subtitle text-muted">Voici les QCM que vous avez créés</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Mazer</a></li>
                                <li class="breadcrumb-item active" aria-current="page">QCM</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Gestion des QCM</h4>
                        <a href="<?= Flight::base() ?>/createQcm" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Créer un QCM
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- Barre de recherche -->
                        <form class="row g-2 mb-4">
                            <div class="col-auto">
                                <input type="text" class="form-control form-control-sm" placeholder="Titre du QCM">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Chercher</button>
                            </div>
                        </form>

                        <!-- Grille de QCM -->
                        <div class="row" id="qcmContainer"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>    
</div>

<!-- Modal réutilisable pour la suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" id="deleteModalBody">
                <!-- Le contenu sera injecté ici -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Conteneur pour les toasts -->
<div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script>
// Variables globales pour gérer le modal
let qcmToDelete = null;
let deleteModal = null;

function showToast(message, type = 'success') {
    const toastId = `toast${Date.now()}`;
    const html = `
        <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 show mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    document.getElementById('toastContainer').insertAdjacentHTML('beforeend', html);
    setTimeout(() => document.getElementById(toastId)?.remove(), 4000);
}

function showDeleteModal(qcm) {
    // Stocker les informations du QCM à supprimer
    qcmToDelete = qcm;
    
    // Mettre à jour le contenu du modal
    document.getElementById('deleteModalBody').innerHTML = 
        `Êtes-vous sûr de vouloir supprimer le QCM "<strong>${qcm.titre}</strong>" ?`;
    
    // Afficher le modal
    deleteModal.show();
}

function deleteQcm() {
    if (!qcmToDelete || !qcmToDelete.id_qcm) return;

    const id = qcmToDelete.id_qcm;

    fetch(`<?= Flight::base() ?>/qcm/${id}`, { method: 'DELETE' })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            showToast(data.message, 'success');

            // Retirer le QCM du DOM
            const qcmElement = document.querySelector(`[data-qcm-id="${id}"]`);
            if(qcmElement) {
                qcmElement.remove();
            }

            // Fermer le modal
            deleteModal.hide();
            qcmToDelete = null;

        } else {
            showToast("Erreur : " + data.message, 'danger');
        }
    })
    .catch(err => {
        console.error(err);
        showToast("Erreur lors de la suppression du QCM.", 'danger');
    });
}

// Récupération des QCM via AJAX
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("qcmContainer");
    
    // Initialiser le modal Bootstrap
    const modalElement = document.getElementById('deleteModal');
    deleteModal = new bootstrap.Modal(modalElement);
    
    // Gérer le clic sur le bouton de confirmation
    document.getElementById('confirmDeleteBtn').addEventListener('click', deleteQcm);
    
    fetch("<?= Flight::base() ?>/qcm/all")
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                container.innerHTML = ""; 
                data.data.forEach(qcm => {
                    const col = document.createElement("div");
                    col.className = "col-12 col-md-6 col-lg-4 col-xl-3 mb-4";
                    col.setAttribute('data-qcm-id', qcm.id_qcm);
                    col.innerHTML = `
                        <div class="card qcm-card">
                            <div class="card-body">
                                <h5 class="card-title">${qcm.titre}</h5>
                                <p class="card-text text-muted">Note max : ${qcm.note_max}</p>
                                <p class="card-text text-muted">Date de création : ${qcm.date_creation}</p>
                                <a href="<?= Flight::base() ?>/singleQcm?id=${qcm.id_qcm}" class="btn btn-outline-primary btn-sm">Voir</a>
                                <button class="btn btn-outline-danger btn-sm ms-2 delete-btn" data-qcm='${JSON.stringify(qcm)}'>
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    `;
                    container.appendChild(col);
                });

                // Ajouter les event listeners après avoir créé tous les éléments
                setTimeout(() => {
                    const deleteButtons = document.querySelectorAll('.delete-btn');
                    deleteButtons.forEach(btn => {
                        btn.addEventListener('click', function() {
                            const qcmData = JSON.parse(this.getAttribute('data-qcm'));
                            showDeleteModal(qcmData);
                        });
                    });
                }, 100);

            } else {
                container.innerHTML = `<p class="text-muted">Aucun QCM trouvé.</p>`;
            }
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = `<p class="text-danger">Erreur lors du chargement des QCM.</p>`;
        });
});
</script>

</body>
</html>