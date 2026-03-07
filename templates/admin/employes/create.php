<?php
$title = 'Ajouter un employé';
$pageCss = 'ajoutEmployes.css';
ob_start();
?>

<div class="btn-container">
    <h2 class="main-btn">Ajouter un employé</h2>
</div>

<section class="form-container" aria-labelledby="titre-ajout">
    <form class="contact-form" method="POST" action="/admin/employe/nouveau" novalidate>

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="container">
            <h1 id="titre-ajout">CRÉER UN COMPTE EMPLOYÉ</h1>
        </div>

        <p class="info-msg">
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            L'employé recevra un email avec son identifiant de connexion.
            Communiquez-lui son mot de passe directement.
        </p>

        <div>
            <div>
                <label for="nom">Nom <span aria-hidden="true">*</span></label>
                <input type="text" id="nom" name="nom"
                       placeholder="Son nom"
                       value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                       required aria-required="true"
                       autocomplete="off">
                <?php if (!empty($errors['nom'])): ?>
                    <span class="field-error" role="alert">
                        <?= htmlspecialchars($errors['nom']) ?>
                    </span>
                <?php endif; ?>
            </div>

            <div>
                <label for="prenom">Prénom <span aria-hidden="true">*</span></label>
                <input type="text" id="prenom" name="prenom"
                       placeholder="Son prénom"
                       value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                       required aria-required="true"
                       autocomplete="off">
                <?php if (!empty($errors['prenom'])): ?>
                    <span class="field-error" role="alert">
                        <?= htmlspecialchars($errors['prenom']) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label for="email">E-mail <span aria-hidden="true">*</span></label>
            <input type="email" id="email" name="email"
                   placeholder="Son adresse email"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                   required aria-required="true"
                   autocomplete="off">
            <?php if (!empty($errors['email'])): ?>
                <span class="field-error" role="alert">
                    <?= htmlspecialchars($errors['email']) ?>
                </span>
            <?php endif; ?>
        </div>

        <div>
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone"
                   placeholder="06 12 34 56 78"
                   value="<?= htmlspecialchars($old['telephone'] ?? '') ?>"
                   autocomplete="off">
        </div>

        <div>
            <label for="adresse">Adresse</label>
            <input type="text" id="adresse" name="adresse"
                   placeholder="Adresse complète"
                   value="<?= htmlspecialchars($old['adresse'] ?? '') ?>"
                   autocomplete="off">
        </div>

       
        <div class="password-container">
            <label for="password">Mot de passe <span aria-hidden="true">*</span></label>
            <input type="password" id="password" name="password"
                   placeholder="Mot de passe"
                   required aria-required="true"
                   autocomplete="new-password"
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
                   placeholder="Confirmation"
                   required aria-required="true"
                   autocomplete="new-password"
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

        <button type="submit" class="submit-btn">CRÉER</button>

    </form>
</section>

<script src="/js/password.js"></script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/employee/layout/base.php';
?>