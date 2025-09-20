<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrats d'essai - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/styles/choices.css">
    <style>
        .candidate-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .candidate-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .candidate-details h5 {
            margin: 0;
            color: #495057;
        }
        .candidate-details p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .contract-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .contract-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        .contract-checkbox input[type="checkbox"] {
            transform: scale(1.2);
        }
        .contract-checkbox label {
            font-size: 0.9rem;
            color: #495057;
        }
        .btn-contract {
            background: #435ebe;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-contract:hover {
            background: #364a98;
        }
        .btn-contract:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .interview-badge {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
        }
        .no-candidates {
            text-align: center;
            padding: 40px;
            color: #6c757d;
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
                        <h3>Gestion des Contrats d'Essai</h3>
                        <p class="text-subtitle text-muted">Candidats recommandés pour signature de contrat</p>
                    </div>
                </div>
            </div>
            
            <section class="section">
                <div class="row">
                    <div class="col-12">
                        <?php if (!empty($candidatsRecommandes)): ?>
                            <?php foreach ($candidatsRecommandes as $candidat): ?>
                                <div class="candidate-card">
                                    <div class="candidate-info">
                                        <div class="candidate-details">
                                            <h5><?= htmlspecialchars($candidat['prenom'] . ' ' . $candidat['nom']) ?></h5>
                                            <p><i class="bi bi-envelope"></i> <?= htmlspecialchars($candidat['email']) ?></p>
                                            <p><i class="bi bi-telephone"></i> <?= htmlspecialchars($candidat['telephone'] ?? 'Non renseigné') ?></p>
                                            <p><i class="bi bi-calendar"></i> Entretien: <?= date('d/m/Y à H:i', strtotime($candidat['date_entretien'])) ?></p>
                                            <p><i class="bi bi-star-fill"></i> Note: <?= $candidat['note_entretien'] ?>/10</p>
                                        </div>
                                        <div class="interview-status">
                                            <span class="interview-badge">Recommandé</span>
                                        </div>
                                    </div>
                                    
                                    <div class="contract-actions">
                                        <button class="btn-contract" onclick="generateContract(<?= $candidat['id_candidat'] ?>)" 
                                                id="btn-contract-<?= $candidat['id_candidat'] ?>" 
                                                <?= !empty($candidat['contrat_accepte']) ? 'disabled' : '' ?>>
                                            <i class="bi bi-file-earmark-pdf"></i> 
                                            <?= !empty($candidat['contrat_accepte']) ? 'Contrat signé' : 'Générer le contrat' ?>
                                        </button>
                                        
                                        <div class="contract-checkbox">
                                            <input type="checkbox" 
                                                   id="accept-<?= $candidat['id_candidat'] ?>" 
                                                   <?= !empty($candidat['contrat_accepte']) ? 'checked disabled' : '' ?>
                                                   onchange="toggleContractAcceptance(<?= $candidat['id_candidat'] ?>)">
                                            <label for="accept-<?= $candidat['id_candidat'] ?>">
                                                Lu, compris et accepté les termes et conditions du contrat d'essai
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-candidates">
                                <i class="bi bi-person-x" style="font-size: 3rem; color: #dee2e6;"></i>
                                <h4>Aucun candidat recommandé</h4>
                                <p>Il n'y a actuellement aucun candidat avec un statut "recommandé" suite à un entretien.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>

    </div>
</div>

<!-- Modal de confirmation -->
<div class="modal fade" id="contractModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de contrat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir générer le contrat d'essai pour ce candidat ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmGenerate">Générer</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>

<script>
let currentCandidatId = null;

function generateContract(candidatId) {
    currentCandidatId = candidatId;
    
    // Vérifier si le contrat a été accepté
    const checkbox = document.getElementById(`accept-${candidatId}`);
    if (!checkbox.checked) {
        alert('Le candidat doit d\'abord accepter les termes du contrat avant de pouvoir le générer.');
        return;
    }
    
    // Générer le PDF directement
    window.open(`<?= Flight::base() ?>/contrat/generate/${candidatId}`, '_blank');
}

function toggleContractAcceptance(candidatId) {
    const checkbox = document.getElementById(`accept-${candidatId}`);
    const button = document.getElementById(`btn-contract-${candidatId}`);
    
    if (checkbox.checked) {
        // Marquer comme accepté en base de données
        fetch('<?= Flight::base() ?>/contrat/accepter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_candidat=${candidatId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.disabled = false;
                showAlert('Contrat accepté avec succès', 'success');
            } else {
                checkbox.checked = false;
                showAlert('Erreur lors de l\'acceptation du contrat', 'error');
            }
        })
        .catch(error => {
            checkbox.checked = false;
            showAlert('Erreur de communication', 'error');
            console.error('Error:', error);
        });
    } else {
        button.disabled = true;
    }
}

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insérer l'alerte au début de la section
    const section = document.querySelector('.section');
    section.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Supprimer l'alerte après 3 secondes
    setTimeout(() => {
        const alert = section.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 3000);
}

// Initialisation des choix si nécessaire
document.addEventListener("DOMContentLoaded", function () {
    const elements = document.querySelectorAll('.choices');
    elements.forEach(el => {
        new Choices(el, {
            searchEnabled: true,
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Sélectionnez des compétences'
        });
    });
});
</script>
</body>
</html>