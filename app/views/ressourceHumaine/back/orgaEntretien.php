<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planifier un entretien - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg"
        type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
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
                            <h3>Planifier un entretien</h3>
                            <p class="text-subtitle text-muted">Le candidat sera notifié par mail</p>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="row">
                        <!-- Formulaire entretien -->
                        <div class="col-12 col-lg-8">
                            <div class="card p-4">
                                <form action="<?= Flight::base() ?>/organiserEntretien" method="post">
                                    <!-- Date de l'entretien -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Date</label>
                                        <input type="date" name="date_entretien" class="form-control" required>
                                    </div>

                                    <!-- Heure de l'entretien -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Heure</label>
                                        <input type="time" name="heure_entretien" class="form-control" required>
                                    </div>

                                    <!-- Durée -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Durée (minutes)</label>
                                        <input type="number" name="duree_entretien" class="form-control"
                                            placeholder="Ex: 60" required>
                                    </div>

                                    <!-- ID candidat choisi (caché rempli par JS) -->
                                    <input type="hidden" name="candidat_id" id="candidatIdHidden">

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success">Planifier</button>
                                    </div>

                                </form>
                            </div>
                        </div>

                        <!-- Sélection du candidat -->
                        <div class="col-12 col-lg-4">
                            <div class="card p-4">
                                <h5 class="fw-bold">Choisir un candidat</h5>
                                <p class="text-muted">Sélectionnez le candidat à notifier</p>

                                <!-- Sélection du candidat -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Candidat</label>
                                    <?php
                                    // Récupérer la liste des candidats depuis la base de données
                                    $db = Flight::db();
                                    $stmt = $db->query("SELECT id_candidat, nom, prenom, email FROM candidat ORDER BY nom, prenom");
                                    $candidats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>

                                    <select class="form-select" id="candidatSelect" name="candidat_id">
                                        <option value="">-- Sélectionner --</option>
                                        <?php foreach ($candidats as $candidat): ?>
                                            <option value="<?= $candidat['id_candidat'] ?>">
                                                <?= $candidat['nom'] ?>     <?= $candidat['prenom'] ?>
                                                (<?= $candidat['email'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Aperçu du candidat -->
                                <div id="candidatPreview" style="display: none; margin-top: 20px;">
                                    <div class="text-center mb-3">
                                        <img id="candidatPhoto" src="" alt="Photo candidat" class="rounded-circle"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                    <h6 id="candidatNom" class="fw-bold text-center"></h6>
                                    <p id="candidatEmail" class="text-center text-muted"></p>
                                    <p id="candidatCompetences" class="text-center"></p>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="button" class="btn btn-primary" id="confirmerBtn">Confirmer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </div>

    <script src="<?= Flight::base() ?>/public/template/assets/static/js/components/dark.js"></script>
    <script
        src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
    <script>
        const candidats = {
            1: {
                nom: "Jean Dupont",
                email: "jean.dupont@email.com",
                photo: "https://randomuser.me/api/portraits/men/32.jpg",
                competences: "PHP, JavaScript, SQL"
            },
            2: {
                nom: "Marie Martin",
                email: "marie.martin@email.com",
                photo: "https://randomuser.me/api/portraits/women/44.jpg",
                competences: "Python, Gestion de projet, Communication"
            },
            3: {
                nom: "Paul Durand",
                email: "paul.durand@email.com",
                photo: "https://randomuser.me/api/portraits/men/65.jpg",
                competences: "Java, C#, Leadership"
            }
        };

        const select = document.getElementById('candidatSelect');
        const preview = document.getElementById('candidatPreview');
        const photo = document.getElementById('candidatPhoto');
        const nom = document.getElementById('candidatNom');
        const email = document.getElementById('candidatEmail');
        const competences = document.getElementById('candidatCompetences');
        const candidatIdHidden = document.getElementById('candidatIdHidden');
        const confirmerBtn = document.getElementById('confirmerBtn');

        select.addEventListener('change', function () {
            const value = this.value;
            if (value && candidats[value]) {
                const c = candidats[value];
                photo.src = c.photo;
                nom.textContent = c.nom;
                email.textContent = c.email;
                competences.textContent = c.competences;
                preview.style.display = 'block';
                candidatIdHidden.value = value; // Remplir input hidden
            } else {
                preview.style.display = 'none';
                candidatIdHidden.value = "";
            }
        });

        confirmerBtn.addEventListener('click', function () {
            if (candidatIdHidden.value === "") {
                alert("Veuillez sélectionner un candidat !");
            } else {
                alert("Candidat confirmé : " + nom.textContent);
            }
        });
    </script>
</body>

</html>