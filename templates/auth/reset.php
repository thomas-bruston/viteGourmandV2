<?php
$title = 'Nouveau mot de passe — Vite & Gourmand';
$pageCss = 'inscript-inform.css';
ob_start();
?>

<div class="image-container">
    <img src="/images/ban.png" alt="Présentation d'un plat Vite et Gourmand">
</div>

<section class="form-container">
    <form class="contact-form" method="POST" action="/reinitialisation-mot-de-passe" novalidate>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="container">
            <h1>NOUVEAU MOT DE PASSE</h1>
        </div>

        <?php if (($step ?? 1) === 1): ?>
        

            <p>Entrez le code à 6 chiffres reçu par email ainsi que votre adresse e-mail.</p>

            <input type="hidden" name="step" value="1">

            <div>
                <label for="email">Votre E-mail</label>
                <input type="email" id="email" name="email"
                       placeholder="Votre e-mail"
                       value="<?= htmlspecialchars($_SESSION['reset_email'] ?? $old['email'] ?? '') ?>"
                       required autocomplete="email"
                       aria-required="true">
            </div>

            <div>
                <label for="code">Code de vérification</label>
                <input type="text" id="code" name="code"
                       placeholder="123456"
                       maxlength="6" pattern="[0-9]{6}"
                       required
                       aria-required="true"
                       aria-describedby="<?= !empty($errors['code']) ? 'code-error' : '' ?>"
                       inputmode="numeric">
                <?php if (!empty($errors['code'])): ?>
                    <span id="code-error" class="field-error" role="alert">
                        <?= htmlspecialchars($errors['code']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <button type="submit" class="submit-btn">VÉRIFIER</button>

        <?php else: ?>
       

            <input type="hidden" name="step" value="2">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId ?? '') ?>">

            <div class="password-container">
                <label for="password">Nouveau mot de passe <span aria-hidden="true">*</span></label>
                <input type="password" id="password" name="password"
                       placeholder="Votre nouveau mot de passe"
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
                10 caractères minimum — 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial.
            </p>

            <div class="password-container">
                <label for="confirm-password">Confirmer le mot de passe <span aria-hidden="true">*</span></label>
                <input type="password" id="confirm-password" name="confirm-password"
                       placeholder="Confirmez votre nouveau mot de passe"
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

            <button type="submit" class="submit-btn">VALIDER</button>

        <?php endif; ?>

    </form>
</section>

<script src="/js/password.js"></script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>