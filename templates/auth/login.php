<?php
$title = 'Connexion — Vite & Gourmand';
$pageCss = 'connexion.css';
ob_start();
?>

<div class="image-container">
    <img src="/images/ban.png" alt="Présentation d'un plat Vite et Gourmand">
</div>

<section class="form-container">
    <form class="contact-form" method="POST" action="/connexion" novalidate>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="container">
            <h1>SE CONNECTER</h1>
        </div>

        <div>
            <label for="email">Votre E-mail</label>
            <input type="email" id="email" name="email" placeholder="E-mail"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   required autocomplete="email"
                   aria-required="true"
                   aria-describedby="<?= !empty($errors['email']) ? 'email-error' : '' ?>">
            <?php if (!empty($errors['email'])): ?>
                <span id="email-error" class="field-error" role="alert">
                    <?= htmlspecialchars($errors['email']) ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="password-container">
            <label for="password">Votre mot de passe</label>
            <input type="password" id="password" name="password" placeholder="Mot de passe"
                   required autocomplete="current-password"
                   aria-required="true">
            <button type="button" class="toggle-password"
                    onclick="togglePassword('password', 'toggleIcon1')"
                    aria-label="Afficher ou masquer le mot de passe">
                <i class="fa-solid fa-eye" id="toggleIcon1" aria-hidden="true"></i>
            </button>
        </div>

        <div class="links-container">
            <div class="password-link">
                <a href="/mot-de-passe-oublie">Mot de passe oublié ?</a>
            </div>
            <div class="inscription-link">
                <a href="/inscription">Pas encore inscrit ?</a>
            </div>
        </div>

        <div>
            <button type="submit" class="submit-btn">CONNEXION</button>
        </div>

    </form>
</section>

<script src="/js/password.js"></script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>