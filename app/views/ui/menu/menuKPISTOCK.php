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

                <li class="sidebar-title">Stock & KPI</li>

                <li class="sidebar-item">
                    <a href="<?= Flight::base() ?>/kpi/directions" class="sidebar-link">
                        <i class="bi bi-bar-chart-fill"></i>
                        <span>Dashboard Stock</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= Flight::base() ?>/kpi/achats" class="sidebar-link">
                        <i class="bi bi-box-seam"></i>
                        <span>Achats</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= Flight::base() ?>/kpi/stock" class="sidebar-link">
                        <i class="bi bi-graph-up"></i>
                        <span>Stock</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= Flight::base() ?>/kpi/ventes" class="sidebar-link">
                        <i class="bi bi-graph-up"></i>
                        <span>Vente</span>
                    </a>
                </li>

                <!-- partie compte -->
                <?= Flight::userAccount() ?>
            </ul>
        </div>
    </div>
</div>
