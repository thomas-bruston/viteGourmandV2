<?php
$title = 'Inscription — Vite & Gourmand';
$pageCss = 'inscript-inform.css';
ob_start();
?>

<div class="image-container">
    <img src="/images/ban.png" alt="Présentation d'un plat Vite et Gourmand">
</div>

<section class="form-container">
    <form class="contact-form" method="POST" action="/inscription" novalidate>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="container">
            <h1>S'INSCRIRE</h1>
        </div>

        <div>
            <div>
                <label for="nom">Nom <span aria-hidden="true">*</span></label>
                <input type="text" id="nom" name="nom" placeholder="Votre nom"
                       value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                       required autocomplete="family-name"
                       aria-required="true"
                       aria-describedby="<?= !empty($errors['nom']) ? 'nom-error' : '' ?>">
                <?php if (!empty($errors['nom'])): ?>
                    <span id="nom-error" class="field-error" role="alert"><?= htmlspecialchars($errors['nom']) ?></span>
                <?php endif; ?>
            </div>

            <div>
                <label for="prenom">Prénom <span aria-hidden="true">*</span></label>
                <input type="text" id="prenom" name="prenom" placeholder="Votre prénom"
                       value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                       required autocomplete="given-name"
                       aria-required="true"
                       aria-describedby="<?= !empty($errors['prenom']) ? 'prenom-error' : '' ?>">
                <?php if (!empty($errors['prenom'])): ?>
                    <span id="prenom-error" class="field-error" role="alert"><?= htmlspecialchars($errors['prenom']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label for="email">E-mail <span aria-hidden="true">*</span></label>
            <input type="email" id="email" name="email" placeholder="Votre E-mail"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   required autocomplete="email"
                   aria-required="true"
                   aria-describedby="<?= !empty($errors['email']) ? 'email-error' : '' ?>">
            <?php if (!empty($errors['email'])): ?>
                <span id="email-error" class="field-error" role="alert"><?= htmlspecialchars($errors['email']) ?></span>
            <?php endif; ?>
        </div>

        <div>
            <label for="telephone">Téléphone <span aria-hidden="true">*</span></label>
            <input type="tel" id="telephone" name="telephone" placeholder="06 12 34 56 78"
                   value="<?= htmlspecialchars($old['telephone'] ?? '') ?>"
                   required autocomplete="tel"
                   aria-required="true">
        </div>

        <div>
            <label for="adresse">Adresse <span aria-hidden="true">*</span></label>
            <input type="text" id="adresse" name="adresse" placeholder="Votre adresse"
                   value="<?= htmlspecialchars($old['adresse'] ?? '') ?>"
                   required autocomplete="street-address"
                   aria-required="true">
        </div>

        <div>
            <label for="code_postal">Code postal <span aria-hidden="true">*</span></label>
            <input type="text" id="code_postal" name="code_postal" placeholder="Votre code postal"
                   value="<?= htmlspecialchars($old['code_postal'] ?? '') ?>"
                   required autocomplete="postal-code"
                   aria-required="true"
                   pattern="[0-9]{5}" maxlength="5">
        </div>

        <div>
            <label for="ville">Ville <span aria-hidden="true">*</span></label>
            <input type="text" id="ville" name="ville" placeholder="Votre ville"
                   value="<?= htmlspecialchars($old['ville'] ?? '') ?>"
                   required autocomplete="address-level2"
                   aria-required="true">
        </div>

        <!-- MDP-->
        <div class="password-container">
            <label for="password">Mot de passe <span aria-hidden="true">*</span></label>
            <input type="password" id="password" name="mot_de_passe" placeholder="Mot de passe"
                   required autocomplete="new-password"
                   aria-required="true"
                   aria-describedby="password-rules">
            <button type="button" class="toggle-password"
                    onclick="togglePassword('password', 'toggleIcon1')"
                    aria-label="Afficher ou masquer le mot de passe">
                <i class="fa-solid fa-eye" id="toggleIcon1" aria-hidden="true"></i>
            </button>
        </div>

        <p id="password-rules" class="password-hint">
            Votre mot de passe doit contenir au moins 10 caractères, 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial.
        </p>

        <div class="password-container">
            <label for="confirm-password">Confirmer le mot de passe <span aria-hidden="true">*</span></label>
            <input type="password" id="confirm-password" name="mot_de_passe_confirm"
                   placeholder="Confirmation"
                   required autocomplete="new-password"
                   aria-required="true"
                   aria-describedby="<?= !empty($errors['password']) ? 'password-error' : '' ?>">
            <button type="button" class="toggle-password"
                    onclick="togglePassword('confirm-password', 'toggleIcon2')"
                    aria-label="Afficher ou masquer la confirmation">
                <i class="fa-solid fa-eye" id="toggleIcon2" aria-hidden="true"></i>
            </button>
        </div>

        <?php if (!empty($errors['password'])): ?>
            <span id="password-error" class="field-error" role="alert">
                <?= htmlspecialchars($errors['password']) ?>
            </span>
        <?php endif; ?>

        <div>
            <button type="submit" class="submit-btn">S'INSCRIRE</button>
        </div>

        <div class="links-container">
            <a href="/connexion">Déjà inscrit ?</a>
        </div>

    </form>
</section>

<script src="/js/password.js"></script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>