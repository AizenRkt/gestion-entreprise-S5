<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="<?= Flight::base() ?>/">
                        <img src="<?= Flight::base() ?>/public/template/assets/compiled/svg/logo.svg" alt="Logo" srcset="">
                    </a>
                </div>
                <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                    <!-- ...theme toggle icons... -->
                </div>
                <div class="sidebar-toggler  x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <?= Flight::userCard() ?>
                

                <li class="sidebar-title">Magasin - Entrée/Sortie</li>

                <li class="sidebar-item">
                    <a href="<?= Flight::base() ?>/stock/entree" class="sidebar-link">
                        <i class="bi bi-box-arrow-in-down"></i>
                        <span>Entrée Stock</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= Flight::base() ?>/stock/sortie" class="sidebar-link">
                        <i class="bi bi-box-arrow-up"></i>
                        <span>Sortie Stock</span>
                    </a>
                </li>

                <li class="sidebar-title">AVIS - Achats/Ventes</li>

                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-cart-fill"></i>
                        <span>Ventes</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="<?= Flight::base() ?>/ventes" class="submenu-link">Tableau de bord</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= Flight::base() ?>/ventes/clients" class="submenu-link">Clients</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= Flight::base() ?>/ventes/commandes" class="submenu-link">Commandes</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= Flight::base() ?>/ventes/livraisons" class="submenu-link">Livraisons</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= Flight::base() ?>/ventes/factures" class="submenu-link">Factures</a>
                        </li>
                        <li class="submenu-item">
                            <a href="<?= Flight::base() ?>/ventes/encaissements" class="submenu-link">Encaissements</a>
                        </li>
                    </ul>
                </li>


                <!-- partie compte -->
                <?= Flight::userAccount() ?>
            </ul>
        </div>
    </div>
</div>
