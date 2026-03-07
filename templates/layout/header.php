<header>
    <!-- NAVBAR -->
    <div class="navbar">
        <!-- Burger -->
        <button class="burger" id="burgerBtn"
                aria-label="Ouvrir le menu de navigation"
                aria-expanded="false"
                aria-controls="sideMenu">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>

        <!-- Liens gauche -->
        <div>
            <a href="/" class="header-btn">Accueil</a>
        </div>
        <div>
            <a href="/menus" class="header-btn">Menus</a>
        </div>

        <!-- Logo -->
        <div class="logo">
            
                <img src="/images/logo.png" alt="Vite et Gourmand">
            </a>
        </div>

        <!-- Liens droite -->
        <div>
            <a href="/contact" class="header-btn">Contact</a>
        </div>
        <div>
            <a href="/connexion" class="header-btn">Connexion</a>
        </div>

        <button class="user-icon" id="userBtn"
                aria-label="Mon compte"
                aria-expanded="false"
                aria-controls="userMenu">
            <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
        </button>
    </div>
</header>

<nav class="side-menu" id="sideMenu" aria-label="Menu principal mobile" aria-hidden="true">
    <a href="/"><i class="fa-regular fa-house" aria-hidden="true"></i>Accueil</a>
    <a href="/menus"><i class="fa-solid fa-utensils" aria-hidden="true"></i>Menus</a>
    <a href="/contact"><i class="fa-solid fa-message" aria-hidden="true"></i>Contact</a>

    <?php if (empty($_SESSION['user_id'])): ?>
        <a href="/connexion"><i class="fa-solid fa-user" aria-hidden="true"></i>Connexion</a>
    <?php else: ?>

        <a href="/deconnexion"><i class="fa-solid fa-power-off" aria-hidden="true"></i>Déconnexion</a>
    <?php endif; ?>
</nav>

<!-- USER MENU -->
<nav class="user-menu" id="userMenu" aria-label="Menu utilisateur" aria-hidden="true">
    <?php if (empty($_SESSION['user'])): ?>
        <a href="/connexion"><i class="fa-solid fa-user" aria-hidden="true"></i>Connexion</a>
        <a href="/inscription"><i class="fa-solid fa-user-plus" aria-hidden="true"></i>S'inscrire</a>
    <?php else: ?>
        <a href="/mes-commandes"><i class="fa-solid fa-basket-shopping" aria-hidden="true"></i>Mes commandes</a>
        <a href="/avis/nouveau"><i class="fa-solid fa-star" aria-hidden="true"></i>Laisser un avis</a>
        <a href="/mes-informations"><i class="fa-solid fa-user-pen" aria-hidden="true"></i>Mes informations</a>
        <?php if (in_array($_SESSION['user']['role'] ?? '', ['employe', 'administrateur'])): ?>
            <a href="/employe/commandes"><i class="fa-solid fa-briefcase" aria-hidden="true"></i>Espace employé</a>
        <?php endif; ?>
        <?php if (($_SESSION['user']['role'] ?? '') === 'administrateur'): ?>
            <a href="/admin/statistiques"><i class="fa-solid fa-chart-bar" aria-hidden="true"></i>Espace admin</a>
        <?php endif; ?>
        <a href="/deconnexion"><i class="fa-solid fa-power-off" aria-hidden="true"></i>Déconnexion</a>
    <?php endif; ?>
</nav>

<!-- OVERLAY -->
<div class="overlay" id="overlay" aria-hidden="true"></div>

<script src="/js/header.js"></script>