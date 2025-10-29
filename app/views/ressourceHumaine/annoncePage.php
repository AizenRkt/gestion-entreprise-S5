<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Page Annonce - Mazer Corporation</title>
  <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
  <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
  <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">

  <style>
    /* --- Amélioration du style Mazer --- */
    body {
      background-color: #f9fafb;
    }

    .page-heading h2 {
      color: #2c3e50;
    }

    .page-heading p {
      font-size: 0.95rem;
    }

    .card {
      border: none;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
      border-radius: 1rem;
      background-color: #fff;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    .card h5, .card h6 {
      color: #0d6efd;
      font-weight: 600;
    }

    .card p {
      line-height: 1.6;
      color: #444;
    }

    /* Sidebar Entreprise */
    .company-card {
      background: linear-gradient(135deg, #f3f6ff, #ffffff);
      border: 1px solid #e6e9f0;
    }

    .company-card img {
      width: 60px;
      height: 60px;
      object-fit: contain;
    }

    .company-card h6 {
      font-weight: 700;
      color: #1e293b;
    }

    .company-card small {
      color: #64748b;
    }

    .company-card p {
      color: #475569;
      font-size: 0.95rem;
    }

    .btn-primary {
      background-color: #4f46e5;
      border: none;
      transition: all 0.3s ease;
      font-weight: 600;
      padding: 0.7rem 1.2rem;
      border-radius: 0.6rem;
    }

    .btn-primary:hover {
      background-color: #4338ca;
      transform: translateY(-1px);
    }

    /* Responsive ajustements */
    @media (max-width: 991px) {
      .page-heading {
        text-align: center;
      }

      .company-card {
        margin-top: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <div id="app">
    <div id="main" class="layout-horizontal">
      <?= Flight::menuFrontOffice() ?>

      <div class="content-wrapper container py-4">
        <?php foreach($annonces as $annonce): ?>
          <!-- En-tête de l'annonce -->
          <div class="page-heading mb-4 text-center text-md-start">
            <h2 class="fw-bold"><?= htmlspecialchars($annonce['titre']) ?></h2>
            <p class="text-muted">
              <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($annonce['ville']) ?> &nbsp;·&nbsp;
              <i class="bi bi-calendar-event me-1"></i> <?= date('d/m/Y', strtotime($annonce['date_debut'])) ?> &nbsp;·&nbsp;
              <?= htmlspecialchars($annonce['experience']) ?> ans d'expérience &nbsp;·&nbsp;
              <?= htmlspecialchars($annonce['age_min']) ?> à <?= htmlspecialchars($annonce['age_max']) ?> ans
            </p>
          </div>

          <div class="row g-4">
            <!-- Détails de l’annonce -->
            <div class="col-lg-8 col-md-12">
              <div class="card p-4">
                <h5 class="mb-3"><i class="bi bi-lightbulb me-2"></i>Compétences requises</h5>
                <p><?= htmlspecialchars($annonce['competences']) ?></p>

                <h6 class="mt-4"><i class="bi bi-mortarboard me-2"></i>Diplômes nécessaires</h6>
                <p><?= htmlspecialchars($annonce['diplomes']) ?></p>

                <h6 class="mt-4"><i class="bi bi-person-check me-2"></i>Qualités recherchées</h6>
                <p><?= htmlspecialchars($annonce['qualite']) ?></p>

                <h6 class="mt-4"><i class="bi bi-flag me-2"></i>Objectif du poste</h6>
                <p><?= htmlspecialchars($annonce['objectif']) ?></p>
              </div>
            </div>

            <!-- Sidebar entreprise -->
            <div class="col-lg-4 col-md-12">
              <div class="card company-card p-4">
                <div class="d-flex align-items-center mb-3">
                  <img src="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" alt="Mazer Entreprise" class="me-3">
                  <div>
                    <h6 class="mb-0">Mazer Entreprise</h6>
                    <small>Technologie & Innovation</small>
                  </div>
                </div>
                <p>
                  Mazer est un acteur majeur dans le développement de solutions digitales innovantes.  
                  Nous aidons nos clients à transformer leurs idées en produits performants et durables.
                </p>
                <a href="<?= Flight::base() ?>/candidature?id_annonce=<?= $annonce['id_annonce'] ?>" class="btn btn-primary w-100 mt-2">
                  <i class="bi bi-send me-2"></i> Postuler maintenant
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/horizontal-layout.js"></script>
  <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
  <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
</body>

</html>

