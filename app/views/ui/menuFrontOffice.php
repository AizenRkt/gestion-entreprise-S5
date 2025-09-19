<header class="mb-5">
    <div class="header-top">
        <div class="container">
            <div class="logo">
                <a href="index.html"><img src="<?= Flight::base() ?>/public/template/assets/compiled/svg/logo.svg" alt="Logo"></a>
            </div>
            <div class="header-top-right">

                <div class="dropdown">
                    <a href="#" id="topbarUserDropdown" class="user-dropdown d-flex align-items-center dropend dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar avatar-md2" >
                            <img src="<?= Flight::base() ?>/public/template/assets/compiled/jpg/1.jpg" alt="Avatar">
                        </div>
                        <div class="text">
                            <h6 class="user-dropdown-name">User</h6>
                            <p class="user-dropdown-status text-sm text-muted">Candidat</p>
                        </div>
                    </a>
                    <!-- <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="topbarUserDropdown">
                        <li><a class="dropdown-item" href="#">Mon compte</a></li>
                        <li><a class="dropdown-item" href="#">Paramètres</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="auth-login.html">déconnexion</a></li>
                    </ul> -->
                </div>

                <!-- Burger button responsive -->
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </div>
        </div>
    </div>
    <nav class="custom main-navbar"> 
        <div class="container">
            <ul>        
                <li class="menu-item">
                    <a href="<?= Flight::base() ?>/" class='menu-link'>
                        <span><i class="bi bi-house-fill"></i> Home</span>
                    </a>
                </li>
                
                <li class="menu-item">
                    <a href="<?= Flight::base() ?>/annonces" class='menu-link'>
                        <span><i class="bi bi-briefcase-fill"></i> Offre d'emploi</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="service.html" class='menu-link'>
                        <span><i class="bi bi-gear-fill"></i> Services</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="contact.html" class='menu-link'>
                        <span><i class="bi bi-envelope-fill"></i> Contact</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

</header>