<?php
$title = 'Mot de passe oublié — Vite & Gourmand';
$pageCss = 'reinitMDP.css';
ob_start();
?>

<div class="image-container">
    <img src="/images/ban.png" alt="Présentation d'un plat Vite et Gourmand">
</div>

<section class="form-container">
    <form class="contact-form" method="POST" action="/mot-de-passe-oublie" novalidate>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="container">
            <h1>RÉINITIALISER VOTRE MOT DE PASSE</h1>
        </div>

        <p>Entrez votre e-mail pour recevoir un code de vérification à 6 chiffres.</p>

        <div>
            <label for="email">Votre E-mail</label>
            <input type="email" id="email" name="email"
                   placeholder="Votre e-mail"
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

        <button type="submit" class="submit-btn">ENVOYER LE CODE</button>

        <div class="links-container">
            <a href="/connexion">← Retour à la connexion</a>
        </div>

    </form>
</section>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>