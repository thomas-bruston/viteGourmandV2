<?php
$title = 'Mes informations — Vite & Gourmand';
$pageCss = 'inscript-inform.css';
ob_start();
?>

<div class="image-container">
    <img src="/images/ban.png" alt="Présentation d'un plat Vite et Gourmand">

    <section class="form-container" aria-labelledby="titre-infos">
        <form class="contact-form" method="POST" action="/mes-informations" novalidate>

            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="container">
                <h1 id="titre-infos">MODIFIER VOS INFORMATIONS</h1>
            </div>

            <div>
                <div>
                    <label for="nom">Nom <span aria-hidden="true">*</span></label>
                    <input type="text" id="nom" name="nom"
                           placeholder="Votre nom"
                           value="<?= htmlspecialchars($user['nom'] ?? '') ?>"
                           required aria-required="true"
                           autocomplete="family-name">
                    <?php if (!empty($errors['nom'])): ?>
                        <span class="field-error" role="alert"><?= htmlspecialchars($errors['nom']) ?></span>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="prenom">Prénom <span aria-hidden="true">*</span></label>
                    <input type="text" id="prenom" name="prenom"
                           placeholder="Votre prénom"
                           value="<?= htmlspecialchars($user['prenom'] ?? '') ?>"
                           required aria-required="true"
                           autocomplete="given-name">
                    <?php if (!empty($errors['prenom'])): ?>
                        <span class="field-error" role="alert"><?= htmlspecialchars($errors['prenom']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label for="email">E-mail <span aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email"
                       placeholder="Votre E-mail"
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                       required aria-required="true"
                       autocomplete="email">
                <?php if (!empty($errors['email'])): ?>
                    <span class="field-error" role="alert"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div>
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone"
                       placeholder="06 12 34 56 78"
                       value="<?= htmlspecialchars($user['telephone'] ?? '') ?>"
                       autocomplete="tel">
            </div>

            <div>
                <label for="adresse">Adresse</label>
                <input type="text" id="adresse" name="adresse"
                       placeholder="Votre adresse complète"
                       value="<?= htmlspecialchars($user['adresse'] ?? '') ?>"
                       autocomplete="street-address">
            </div>

            <div>
                <label for="code_postal">Code postal</label>
                <input type="text" id="code_postal" name="code_postal"
                       placeholder="33000"
                       value="<?= htmlspecialchars($user['adresse_postale'] ?? '') ?>"
                       autocomplete="postal-code"
                       pattern="[0-9]{5}" maxlength="5">
            </div>

            <div>
                <label for="ville">Ville</label>
                <input type="text" id="ville" name="ville"
                       placeholder="Votre ville"
                       value="<?= htmlspecialchars($user['ville'] ?? '') ?>"
                       autocomplete="address-level2">
            </div>

            <button type="submit" class="submit-btn">MODIFIER</button>

        </form>
    </section>
</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>