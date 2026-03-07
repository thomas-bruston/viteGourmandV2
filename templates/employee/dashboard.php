<?php
$title = 'Espace employé';
$pageCss = 'admin.css';
ob_start();
?>

<div class="menuContainer">

    <a href="/employe/menus" class="menu-btn">
         Gestion des menus
    </a>
    <a href="/employe/commandes" class="menu-btn">
         Gestion des commandes
    </a>
    <a href="/employe/avis" class="menu-btn">
         Gestion des avis
    </a>
    <a href="/employe/messages" class="menu-btn">
     Messages
    </a>

    <!-- Horaires -->
    <button class="menu-btn" id="timeBtn"
            aria-expanded="false"
            aria-controls="timeMenu">
        Gestion des horaires
    </button>

    <a href="/deconnexion" class="menu-btn">
         Déconnexion
    </a>

</div>

<!-- PANNEAU HORAIRES -->
<nav class="time-menu" id="timeMenu" aria-hidden="true" aria-label="Modifier les horaires">
    <h2>HORAIRES</h2>
    <form method="POST" action="/employe/horaires" novalidate>
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <div>
            <label for="horaires">Nouveaux horaires</label>
            <textarea id="horaires" name="horaires"
                      placeholder="Ex: Lundi-Vendredi 9h-18h"
                      rows="4" required
                      aria-required="true"><?= htmlspecialchars($horairesActuels ?? '') ?></textarea>
        </div>
        <button type="submit" class="valider-btn">VALIDER</button>
    </form>
</nav>

<div class="overlay" id="overlay" aria-hidden="true"></div>
<script src="/js/horaires.js"></script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/employee/layout/base.php';
?>