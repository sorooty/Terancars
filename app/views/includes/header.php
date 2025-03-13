<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="<?= SITE_URL ?>/home">TeranCars</a>

            <!-- Bouton du menu burger -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Liens du menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/catalogue">Catalogue</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/about">À propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL ?>/contact">Contact</a></li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm" href="<?= SITE_URL ?>/auth/login">Se connecter</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

</header>

<!-- Ajout du script pour la navigation mobile -->
<script src="/DaCar/public/assets/js/nav.js"></script>