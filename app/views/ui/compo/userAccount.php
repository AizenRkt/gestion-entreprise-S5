<li class="sidebar-title">Compte</li>

<li class="sidebar-item">
    <a href="<?= Flight::base() ?>/backOffice/user/pointage" class="sidebar-link">
        <i class="bi bi-fingerprint"></i>
        <span>Pointage</span>
    </a>
</li>

<li class="sidebar-item has-sub">
    <a href="#" class='sidebar-link'>
        <i class="bi bi-person-bounding-box"></i> 
        <span>Demande</span>
    </a>
    <ul class="submenu">
        <li class="submenu-item">
            <a href="<?= Flight::base() ?>/conge/demande" class="sidebar-link">
                <!-- <i class="bi bi-calendar2-check"></i> -->
                <span>congé</span>
            </a>
        </li>

        <li class="submenu-item">
            <a href="<?= Flight::base() ?>/absence/demande" class="sidebar-link">
                <!-- <i class="bi bi-person-x-fill"></i> -->
                <span>absence</span>
            </a>
        </li>

        <li class="submenu-item">
            <a href="<?= Flight::base() ?>/heureSupp/demande" class="sidebar-link">
                <!-- <i class="bi bi-alarm-fill"></i> -->
                <span>heureSup</span>
            </a>
        </li>                                               
    </ul>
</li>

<li class="sidebar-item">
    <a href="<?= Flight::base() ?>/auth/parametre" class="sidebar-link">
        <i class="bi bi-gear-fill"></i>
        <span>Paramètre</span>
    </a>
</li>

<li class="sidebar-item">
    <a href="<?= Flight::base() ?>/deconnexion" class="sidebar-link">
        <i class="bi bi-door-open-fill"></i>
        <span>Déconnexion</span>
    </a>
</li>
