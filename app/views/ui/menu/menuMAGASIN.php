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

                <li class="sidebar-title">AVIS</li>

                <!-- Ventes -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-cart-fill"></i>
                        <span>Ventes</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item"><a href="<?= Flight::base() ?>/ventes/clients" class="submenu-link">Clients</a></li>
                        <li class="submenu-item"><a href="<?= Flight::base() ?>/ventes/commandes" class="submenu-link">Commandes</a></li>
                        <li class="submenu-item"><a href="<?= Flight::base() ?>/ventes/livraisons" class="submenu-link">Livraisons</a></li>
                        <li class="submenu-item"><a href="<?= Flight::base() ?>/ventes/factures" class="submenu-link">Factures</a></li>
                        <li class="submenu-item"><a href="<?= Flight::base() ?>/ventes/encaissements" class="submenu-link">Encaissements</a></li>
                    </ul>
                </li>

                <!-- Achats -->
                <li class="sidebar-item has-sub">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-cart-check"></i>
                        <span>Achats</span>
                    </a>
                    <ul class="submenu">

                        <li class="submenu-item has-sub">
                            <a href="#" class="submenu-link">Demandes d'achat</a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="<?= Flight::base() ?>/avis/achat/saisie" class="submenu-link">Saisie</a></li>
                                <li class="submenu-item"><a href="<?= Flight::base() ?>/avis/achat/demandes" class="submenu-link">Liste</a></li>
                            </ul>
                        </li>

                        <li class="submenu-item has-sub">
                            <a href="#" class="submenu-link">Bons de commande</a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="<?= Flight::base() ?>/avis/achat/bc/nouveau" class="submenu-link">Saisie</a></li>
                                <li class="submenu-item"><a href="<?= Flight::base() ?>/avis/achat/bc" class="submenu-link">Liste</a></li>
                            </ul>
                        </li>

                        <li class="submenu-item has-sub">
                            <a href="#" class="submenu-link">Réceptions</a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="<?= Flight::base() ?>/avis/achat/receptions/nouveau" class="submenu-link">Saisie</a></li>
                                <li class="submenu-item"><a href="<?= Flight::base() ?>/avis/achat/receptions" class="submenu-link">Liste</a></li>
                            </ul>
                        </li>

                        <li class="submenu-item has-sub">
                            <a href="#" class="submenu-link">Factures fournisseurs</a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="<?= Flight::base() ?>/avis/achat/factures/nouveau" class="submenu-link">Saisie</a></li>
                                <li class="submenu-item"><a href="<?= Flight::base() ?>/avis/achat/factures" class="submenu-link">Liste</a></li>
                            </ul>
                        </li>

                        <li class="submenu-item has-sub">
                            <a href="#" class="submenu-link">Paiements fournisseurs</a>
                            <ul class="submenu">
                                <li class="submenu-item"><a href="<?= Flight::base() ?>/avis/achat/paiements/nouveau" class="submenu-link">Saisie</a></li>
                                <li class="submenu-item"><a href="<?= Flight::base() ?>/avis/achat/paiements" class="submenu-link">Liste</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>


                <!-- partie compte -->
                <?= Flight::userAccount() ?>
            </ul>
        </div>
    </div>
</div>
