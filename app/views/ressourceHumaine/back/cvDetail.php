<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail CV - Mazer</title>
    
    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            border-radius: 0.5rem;
            color: white;
            margin-bottom: 2rem;
        }
        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .info-label {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 0.25rem;
        }
        .info-value {
            color: #3a3b45;
            margin-bottom: 1rem;
        }
        .badge-custom {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            margin: 0.25rem;
            display: inline-block;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 1rem;
            border-bottom: 2px solid #667eea;
            padding-bottom: 0.5rem;
        }
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
        }
        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        .error-message {
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            color: #842029;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
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
                        <h3>Détail du CV</h3>
                        <p class="text-subtitle text-muted">Informations complètes du candidat</p>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="error-message" style="display: none;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span id="errorText"></span>
            </div>

            <!-- Content Container -->
            <div id="cvContent" style="display: none;">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <img id="profilePhoto" src="" alt="Photo" class="profile-photo">
                        </div>
                        <div class="col-md-9">
                            <h2 id="fullName" class="mb-2"></h2>
                            <h4 id="profil" class="mb-3 opacity-75"></h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><i class="fas fa-envelope me-2"></i><span id="email"></span></p>
                                    <p class="mb-1"><i class="fas fa-phone me-2"></i><span id="telephone"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><i class="fas fa-venus-mars me-2"></i><span id="genre"></span></p>
                                    <p class="mb-1"><i class="fas fa-calendar me-2"></i><span id="dateNaissance"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Informations Personnelles -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><i class="fas fa-user me-2"></i>Informations Personnelles</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Date de Candidature</div>
                                            <div class="info-value" id="dateCandidature"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Date de Soumission CV</div>
                                            <div class="info-value" id="dateSoumission"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Villes -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><i class="fas fa-map-marker-alt me-2"></i>Villes de Résidence</h4>
                                </div>
                                <div class="card-body">
                                    <div id="villesList"></div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Diplômes -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><i class="fas fa-graduation-cap me-2"></i>Diplômes</h4>
                                </div>
                                <div class="card-body">
                                    <div id="diplomesList"></div>
                                </div>
                            </div>
                        </section>

                        <!-- Compétences -->
                        <section class="section">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><i class="fas fa-lightbulb me-2"></i>Compétences</h4>
                                </div>
                                <div class="card-body">
                                    <div id="competencesList"></div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>

<script>
    const API_URL = '<?= Flight::base() ?>/candidat/detail/'; 
    const CANDIDATE_ID = <?= $_GET['id_candidat']?>; 

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('fr-FR', options);
    }

    function getGenreFullText(genre) {
        return genre === 'M' ? 'Masculin' : genre === 'F' ? 'Féminin' : genre;
    }

    function calculateAge(dateString) {
        const birthDate = new Date(dateString);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }

    async function loadCVData() {
        try {
            document.getElementById('loadingSpinner').style.display = 'flex';
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('cvContent').style.display = 'none';

            const response = await fetch(`${API_URL}${CANDIDATE_ID}`);
            
            if (!response.ok) {
                throw new Error('Erreur lors du chargement des données');
            }

            const data = await response.json();
            
            document.getElementById('loadingSpinner').style.display = 'none';
            
            document.getElementById('cvContent').style.display = 'block';
            
            populateData(data);
            
        } catch (error) {
            console.error('Erreur:', error);
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'block';
            document.getElementById('errorText').textContent = 'Impossible de charger les données du CV. Veuillez réessayer.';
        }
    }

    function populateData(data) {
        const cv = data.cvs[0];
        
        const photoPath = `<?= Flight::base() ?>/public/uploads/photos/${cv.photo}`;
        document.getElementById('profilePhoto').src = photoPath;
        document.getElementById('profilePhoto').onerror = function() {
            this.src = '<?= Flight::base() ?>/public/uploads/photos/default.jpg';
        };
        
        document.getElementById('fullName').textContent = `${data.prenom} ${data.nom}`;
        document.getElementById('profil').textContent = cv.profil;
        document.getElementById('email').textContent = data.email;
        document.getElementById('telephone').textContent = data.telephone;
        document.getElementById('genre').textContent = getGenreFullText(data.genre);
        
        const age = calculateAge(data.date_naissance);
        document.getElementById('dateNaissance').textContent = `${formatDate(data.date_naissance)} (${age} ans)`;
        
        document.getElementById('dateCandidature').textContent = formatDate(data.date_candidature);
        document.getElementById('dateSoumission').textContent = formatDate(cv.date_soumission);
        
        const villesHTML = cv.villes.map(ville => 
            `<span class="badge bg-primary badge-custom">
                <i class="fas fa-map-marker-alt me-1"></i>${ville.nom}
            </span>`
        ).join('');
        document.getElementById('villesList').innerHTML = villesHTML || '<p class="text-muted">Aucune ville renseignée</p>';
        
        const diplomesHTML = cv.diplomes.map(diplome => 
            `<div class="d-flex align-items-center mb-2">
                <i class="fas fa-certificate text-warning me-2"></i>
                <span>${diplome.nom}</span>
            </div>`
        ).join('');
        document.getElementById('diplomesList').innerHTML = diplomesHTML || '<p class="text-muted">Aucun diplôme renseigné</p>';
        
        const competencesHTML = cv.competences.map(competence => 
            `<span class="badge bg-success badge-custom">
                <i class="fas fa-check-circle me-1"></i>${competence.nom}
            </span>`
        ).join('');
        document.getElementById('competencesList').innerHTML = competencesHTML || '<p class="text-muted">Aucune compétence renseignée</p>';
    }

    document.addEventListener('DOMContentLoaded', function() {
        // loadTestData();
        
        loadCVData();
    });

    function loadTestData() {
        const testData = {
            "id_candidat": 7,
            "nom": "PeterBot",
            "prenom": "Fenitra",
            "email": "fenitra00@gmail.com",
            "telephone": "0348366414",
            "genre": "M",
            "date_naissance": "2000-04-09",
            "date_candidature": "2025-09-22",
            "cvs": [{
                "id_cv": 2,
                "profil": "Informaticien",
                "date_soumission": "2025-09-22",
                "photo": "photo_68d10bd0b108c2.73919324.jpg",
                "villes": [{"id_item": 1, "nom": "Antananarivo"}],
                "diplomes": [{"id_item": 1, "nom": "BEPC"}, {"id_item": 2, "nom": "BACC"}],
                "competences": [
                    {"id_item": 2, "nom": "Administration Systèmes"},
                    {"id_item": 4, "nom": "Analyse de Données"},
                    {"id_item": 13, "nom": "Analyse Financière"}
                ]
            }]
        };
        
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('cvContent').style.display = 'block';
        populateData(testData);
    }

</script>
</body>
</html>