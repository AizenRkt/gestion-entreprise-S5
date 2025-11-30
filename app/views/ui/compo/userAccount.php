<!-- Ajout d'un style simple pour la notification -->
<style>
    .notification-dot {
        height: 8px;
        width: 8px;
        background-color: #dc3545; /* Rouge Bootstrap Danger */
        border-radius: 50%;
        display: inline-block;
        margin-left: 8px;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(0.9);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }
        100% {
            transform: scale(0.9);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
</style>

<li class="sidebar-title">Compte</li>

<li class="sidebar-item">
    <a href="<?= Flight::base() ?>/backOffice/user/pointage" class="sidebar-link">
        <i class="bi bi-person-check-fill"></i>
        <span>pointage</span>
        <?php if (Flight::checkinStatus() === 'checkin-needed'): ?>
            <span class="notification-dot" title="Check-in requis"></span>
        <?php endif; ?>
        <i class="bi bi-fingerprint"></i>
        <span>Pointage</span>
    </a>
</li>

<li class="sidebar-item">
    <a href="<?= Flight::base() ?>/auth/parametre" class="sidebar-link">
        <i class="bi bi-gear-fill"></i>
        <span>Paramètre</span>
    </a>
</li>

<li class="sidebar-item">
    <a href="<?= Flight::base() ?>/conge/demande" class="sidebar-link">
        <i class="bi bi-calendar2-check"></i>
        <span>Demander congé</span>
    </a>
</li>

<li class="sidebar-item">
    <a href="<?= Flight::base() ?>/absence/demande" class="sidebar-link">
        <i class="bi bi-person-x-fill"></i>
        <span>Demander absence</span>
    </a>
</li>

<li class="sidebar-item">
    <a href="<?= Flight::base() ?>/heureSupp/demande" class="sidebar-link">
        <i class="bi bi-alarm-fill"></i>
        <span>Demander heureSupp</span>
    </a>
</li>

<li class="sidebar-item">
    <a href="<?= Flight::base() ?>/deconnexion" class="sidebar-link">
        <i class="bi bi-door-open-fill"></i>
        <span>Déconnexion</span>
    </a>
</li>
