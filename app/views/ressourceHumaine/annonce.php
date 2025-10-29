<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Annonces - Mazer Corporation</title>
  <link rel="shortcut icon" href="<?= Flight::base() ?>/public/template/assets/compiled/svg/favicon.svg" type="image/x-icon">
  <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/app.css">
  <link rel="stylesheet" href="<?= Flight::base() ?>/public/template/assets/compiled/css/iconly.css">

  <style>
    body {
      background-color: #f8fafc;
    }

    .page-heading h3 {
      color: #1e293b;
      font-weight: 700;
      font-size: 1.6rem;
      text-align: center;
      margin-bottom: 1.5rem;
    }

    /* Barre de recherche */
    .card form {
      display: flex;
      flex-wrap: wrap;
      gap: 0.8rem;
      justify-content: center;
    }

    .form-control,
    .form-select {
      border-radius: 0.6rem;
      padding: 0.7rem;
      font-size: 0.95rem;
      box-shadow: none;
      border: 1px solid #e2e8f0;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #6366f1;
      box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
    }

    .btn-primary {
      background-color: #4f46e5;
      border: none;
      padding: 0.7rem 1.2rem;
      font-weight: 600;
      border-radius: 0.6rem;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #4338ca;
      transform: translateY(-1px);
    }

    /* Carte annonce */
    .card-annonce {
      border: none;
      background-color: #ffffff;
      border-radius: 1rem;
      padding: 1.5rem;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }

    .card-annonce:hover {
      transform: translateY(-4px);
      box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
    }

    .card-annonce h5 {
      font-weight: 600;
      color: #111827;
    }

    .card-annonce p {
      color: #4b5563;
      font-size: 0.95rem;
    }

    .card-annonce .btn-outline-primary {
      border-color: #4f46e5;
      color: #4f46e5;
      font-weight: 600;
      border-radius: 0.5rem;
      transition: all 0.3s ease;
    }

    .card-annonce .btn-outline-primary:hover {
      background-color: #4f46e5;
      color: #fff;
      transform: translateY(-1px);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .card-annonce {
        flex-direction: column !important;
        align-items: flex-start !important;
        text-align: left;
      }

      .card-annonce div:last-child {
        width: 100%;
        margin-top: 0.8rem;
      }

      .btn-outline-primary {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <div id="app">
    <div id="main" class="layout-horizontal">
      <?= Flight::menuFrontOffice() ?>

      <div class="content-wrapper container py-4">
        <div class="page-heading">
          <h3>Voici les postes disponibles du moment</h3>
        </div>

        <!-- Barre de recherche -->
        <div class="card p-4 mb-4">
          <form method="get">
            <input type="text" name="keyword" class="form-control col-md-3" placeholder="Mot-clé...">
            <select class="form-select col-md-3" name="profil">
              <option value="">Profils</option>
              <?php foreach ($pfs as $p): ?>
                <option value="<?= $p['id_profil'] ?>"><?= $p['nom'] ?></option>
              <?php endforeach; ?>
            </select>
            <select class="form-select col-md-3" name="diplome">
              <option value="">Diplômes</option>
              <?php foreach ($diplomes as $d): ?>
                <option value="<?= $d['id_diplome'] ?>"><?= $d['nom'] ?></option>
              <?php endforeach; ?>
            </select>
            <select class="form-select col-md-3" name="ville">
              <option value="">Villes</option>
              <?php foreach ($villes as $v): ?>
                <option value="<?= $v['id_ville'] ?>"><?= $v['nom'] ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary col-md-2">Rechercher</button>
          </form>
        </div>

        <!-- Liste des annonces -->
        <section class="row">
          <div class="col-12">
            <?php if (!empty($annonces)): ?>
              <?php foreach ($annonces as $annonce): ?>
                <?php if ($annonce['valeur'] != 'retrait'): ?>
                  <div class="card-annonce mb-3 d-flex flex-row align-items-center justify-content-between">
                    <div class="flex-grow-1">
                      <h5><?= htmlspecialchars($annonce['titre']) ?></h5>
					  <h7 class="mb-1"><?= htmlspecialchars($annonce['profil']) ?></h7>
                      <p class="text-muted mb-1">
                        <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($annonce['ville']) ?> &nbsp;·&nbsp;
                        <i class="bi bi-mortarboard me-1"></i> Diplômes : <?= htmlspecialchars($annonce['diplomes']) ?> &nbsp;·&nbsp;
                        <i class="bi bi-calendar-event me-1"></i> Publié le <?= date('d/m/Y', strtotime($annonce['date_debut'])) ?>
                      </p>
                    </div>
                    <div>
                      <a href="<?= Flight::base() ?>/annoncePage?id=<?= $annonce['id_annonce'] ?>" class="btn btn-outline-primary">
                        Voir les détails
                      </a>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="alert alert-light text-center mt-4">
                <i class="bi bi-info-circle me-2"></i>Aucune annonce disponible pour le moment.
              </div>
            <?php endif; ?>
          </div>
        </section>
      </div>
    </div>
  </div>

  <script src="<?= Flight::base() ?>/public/template/assets/static/js/pages/horizontal-layout.js"></script>
  <script src="<?= Flight::base() ?>/public/template/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
  <script src="<?= Flight::base() ?>/public/template/assets/compiled/js/app.js"></script>
</body>

</html>