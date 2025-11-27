<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une annonce - Mazer</title>

    <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/styles/choices.css">
    <style>
        .step-section { display: none; }
        .step-section.active { display: block; }
    </style>
</head>
<script>
document.addEventListener("DOMContentLoaded", function () {
  fetch("<?= Flight::base() ?>/api/diplomes")
    .then(res => res.json())
    .then(data => {
      let container = document.getElementById("diplomeContainer"); 
      data.forEach(d => {
        let div = document.createElement("div");
        div.classList.add("form-check");
        div.innerHTML = `
          <input class="form-check-input" type="checkbox" name="diplome[]" value="${d.nom}" id="diplome_${d.nom}">
          <label class="form-check-label" for="diplome_${d.nom}">${d.nom}</label>
        `;
        container.appendChild(div);
      });
    });


  fetch("<?= Flight::base() ?>/api/competences")
  .then(res => res.json())
  .then(data => {
    let select = document.getElementById("competence-select");

    data.forEach(c => {
      let option = document.createElement("option");
      option.value = c.nom;
      option.textContent = c.nom;
      select.appendChild(option);
    });

    new Choices(select, {
      searchEnabled: true,
      removeItemButton: true,
      allowHTML: true,
      placeholderValue: "Sélectionnez des compétences"
    });
  });
});

fetch("<?= Flight::base() ?>/api/profil")
  .then(res => res.json())
  .then(data => {
    let select = document.getElementById("profil-select");
    data.forEach(p => {
      let option = document.createElement("option");
      option.value = p.id_profil || p.nom;  
      option.textContent = p.nom;
      select.appendChild(option);
    });
  });

fetch("<?= Flight::base() ?>/api/ville")
  .then(res => res.json())
  .then(data => {
    let select = document.getElementById("ville-select");
    data.forEach(v => {
      let option = document.createElement("option");
      option.value = v.id_ville || v.nom;  
      option.textContent = v.nom;
      select.appendChild(option);
    });
  });
</script>

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
                        <h3>Créer une annonce</h3>
                        <p class="text-subtitle text-muted">Remplissez le formulaire suivant étape par étape</p>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="row">
                    <div class="col-12">
                    <div class="card p-4">
                        <form action="/annonce/create" method="post" id="annonceForm">
                        <div id="step1" class="step-section active">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Titre de l’annonce</label>
                                <input type="text" name="titre" class="form-control" placeholder="Ex: Développeur Full Stack" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Profil du candidat</label>
                                <select name="profil" id="profil-select" class="form-select" required>
                                    <option value="">-- Sélectionnez un profil --</option>
                                </select>
                            </div>


                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Âge minimum</label>
                                    <input type="number" name="age_min" class="form-control" placeholder="Ex: 22" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Âge maximum</label>
                                    <input type="number" name="age_max" class="form-control" placeholder="Ex: 35" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Diplôme requis</label>
                                <div id="diplomeContainer"></div>
                            </div>


                            <div class="row mb-3">
                                <div class="col-md-6">
                                <label class="form-label fw-bold">Années d'expérience (minimum)</label>
                                <input type="number" name="experience" class="form-control" placeholder="Ex: 2" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Lieu (ville)</label>
                                    <select name="lieu" id="ville-select" class="form-select" required>
                                        <option value="">-- Sélectionnez une ville --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                <label class="form-label fw-bold">Date de début</label>
                                <input type="date" name="date_debut" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                <label class="form-label fw-bold">Date de fin</label>
                                <input type="date" name="date_fin" class="form-control" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" onclick="nextStep()">Suivant</button>
                            </div>
                        </div>

                        <!-- ===== Étape 2 : Description du poste ===== -->
                        <div id="step2" class="step-section">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Objectif du travail</label>
                                <textarea name="objectif" class="form-control" rows="3" placeholder="Décrivez ce qu'il faut faire..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Qualités du candidat</label>
                                <textarea name="qualites" class="form-control" rows="3" placeholder="Décrivez les qualités attendues..." required></textarea>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Compétence (mots-clés)</label>
                               <select id="competence-select" class="form-select" multiple="multiple"></select>

                            </div>


                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-light-secondary" onclick="prevStep()">Précédent</button>
                                <button type="submit" class="btn btn-success">Publier</button>
                            </div>
                        </div>

                        </form>
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
<script src="<?= Flight::base() ?>/public/template/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
<script>
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
<script>
  function nextStep() {
    document.getElementById('step1').classList.remove('active');
    document.getElementById('step2').classList.add('active');
  }
  function prevStep() {
    document.getElementById('step2').classList.remove('active');
    document.getElementById('step1').classList.add('active');
  }
</script>
<script>
document.getElementById("annonceForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    let diplomes = [];
    document.querySelectorAll("input[name='diplome[]']:checked").forEach(cb => {
        diplomes.push(cb.value);
    });

    let competences = Array.from(document.getElementById("competence-select").selectedOptions).map(opt => opt.value);


    let data = {
        titre: formData.get("titre"),
        profil: formData.get("profil"),
        age_min: formData.get("age_min"),
        age_max: formData.get("age_max"),
        experience: formData.get("experience"),
        lieu: formData.get("lieu"),
        date_debut: formData.get("date_debut"),
        date_fin: formData.get("date_fin"),
        objectif: formData.get("objectif"),
        qualites: formData.get("qualites"),
        diplomes: diplomes,
        competences: competences
    };

    console.log("Payload envoyé :", data);

    let res = await fetch("<?= Flight::base() ?>/annonce/create", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    });

    let result = await res.json();
    alert(result.message);
});
</script>

</body>
</html>
