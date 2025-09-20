<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur 400 - Mauvaise requête</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .error-container { max-width: 800px; margin: 0 auto; }
        .error-code { font-size: 72px; color: #dc3545; }
        .error-message { font-size: 24px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">400</h1>
        <h2 class="error-message">Mauvaise requête</h2>
        <p><?= $message ?? 'Les données envoyées sont incomplètes ou incorrectes.' ?></p>
        <p><a href="javascript:history.back()">Retour</a> | <a href="<?= Flight::base() ?>/">Accueil</a></p>
    </div>
</body>
</html>