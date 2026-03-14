<?php
$title = 'Laisser un avis — Vite & Gourmand';
$pageCss = 'connexion.css';
ob_start();
?>

<div class="image-container">
    <img src="/images/ban.png" alt="Présentation d'un plat Vite et Gourmand">
</div>

<section class="form-container" aria-labelledby="titre-avis">
    <form class="contact-form" method="POST" action="/avis/nouveau" novalidate>

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <input type="hidden" name="commande_id"
               value="<?= (int)($commande['commande_id'] ?? 0) ?>">

        <div class="container">
            <h1 id="titre-avis">LAISSER UN AVIS</h1>
        </div>

        <!-- Info commande -->
        <?php if (!empty($commande)): ?>
            <div class="avis-commande-info">
                <p>
                    Vous laissez un avis pour le menu
                    <strong><?= htmlspecialchars($commande['menu_titre'] ?? '') ?></strong>
                    du <?= htmlspecialchars(date('d/m/Y', strtotime($commande['date_prestation']))) ?>.
                </p>
            </div>
        <?php endif; ?>

        <div class="prevention">
            <p>Dans le cadre de notre politique de respect, nous vous demandons de rester
                courtois lors de la rédaction de votre avis, afin de garantir le respect
                envers nos employés et nos utilisateurs.
                Tout avis ne respectant pas ces conditions sera supprimé. Merci.
            </p>
        </div>

     <div class="note-container">
    <p id="label-note">Votre note <span aria-hidden="true">*</span></p>
    <div class="stars-container" 
         id="stars-container" 
         role="radiogroup" 
         aria-labelledby="label-note">
        <?php for ($i = 1; $i <= 5; $i++): ?>
        <span class="star" 
              data-value="<?= $i ?>"
              role="radio"
              tabindex="0"
              aria-checked="<?= ($old['note'] ?? 5) == $i ? 'true' : 'false' ?>"
              aria-label="<?= $i ?> étoile<?= $i > 1 ? 's' : '' ?> sur 5">★</span>
        <?php endfor; ?>
    </div>
    <input type="hidden" name="note" id="note-value" value="<?= $old['note'] ?? 5 ?>">
</div>

        <!-- Commentaire -->
        <div>
            <label for="description">Votre commentaire <span aria-hidden="true">*</span></label>
            <textarea id="description" name="description"
                      placeholder="Partagez votre expérience..."
                      required aria-required="true"
                      minlength="10" maxlength="1000"
                      aria-describedby="description-hint <?= !empty($errors['description']) ? 'description-error' : '' ?>"
                      rows="5"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
            <span id="description-hint" class="field-hint">
                Entre 10 et 1000 caractères.
            </span>
            <?php if (!empty($errors['description'])): ?>
                <span id="description-error" class="field-error" role="alert">
                    <?= htmlspecialchars($errors['description']) ?>
                </span>
            <?php endif; ?>
        </div>

        <button type="submit" class="submit-btn">DÉPOSER MON AVIS</button>

    </form>
</section>
<script src= "/js/stars.js"> </script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>