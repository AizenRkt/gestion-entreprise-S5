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

                <li class="sidebar-title">Validation Stock</li>

                <li class="sidebar-item">
                    <a href="<?= Flight::base() ?>/stock/validation" class="sidebar-link">
                        <i class="bi bi-check2-circle"></i>
                        <span>Valider les mouvements</span>
                    </a>
                </li>

                <!-- partie compte -->
                <?= Flight::userAccount() ?>
            </ul>
        </div>
    </div>
</div>
