<h2>Détail du candidat</h2>
<?php if (isset($candidat) && $candidat): ?>
    <ul>
        <li>Nom : <?= htmlspecialchars($candidat['nom'] ?? '') ?></li>
        <li>Prénom : <?= htmlspecialchars($candidat['prenom'] ?? '') ?></li>
        <li>Email : <?= htmlspecialchars($candidat['email'] ?? '') ?></li>
        <li>Téléphone : <?= htmlspecialchars($candidat['telephone'] ?? '') ?></li>
        <li>Genre : <?= htmlspecialchars($candidat['genre'] ?? '') ?></li>
        <!-- Ajoute d'autres champs si besoin -->
    </ul>
<?php else: ?>
    <p>Aucun candidat trouvé.</p>
<?php endif; ?>
