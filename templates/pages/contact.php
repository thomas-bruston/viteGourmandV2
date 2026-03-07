<?php
$title = 'Contact — Vite & Gourmand';
$pageCss = 'connexion.css';
ob_start();
?>

<div class="image-container">
    <img src="/images/ban.png" alt="Présentation d'un plat Vite et Gourmand">
</div>

<section class="form-container" aria-labelledby="titre-contact">
    <form class="contact-form" method="POST" action="/contact" novalidate>

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($csrf_token ?? '') ?>">

        <div class="container">
            <h1 id="titre-contact">NOUS CONTACTER</h1>
        </div>

        <div>
            <label for="nom">Nom <span aria-hidden="true">*</span></label>
            <input type="text" id="nom" name="nom"
                   placeholder="Votre nom"
                   required aria-required="true">
        </div>

        <div>
            <label for="prenom">Prénom <span aria-hidden="true">*</span></label>
            <input type="text" id="prenom" name="prenom"
                   placeholder="Votre prénom"
                   required aria-required="true">
        </div>

        <div>
            <label for="email">E-mail <span aria-hidden="true">*</span></label>
            <input type="email" id="email" name="email"
                   placeholder="Votre e-mail"
                   required aria-required="true">
        </div>

        <div>
            <label for="titre">Sujet <span aria-hidden="true">*</span></label>
            <input type="text" id="titre" name="titre"
                   placeholder="Sujet de votre message"
                   required aria-required="true">
        </div>

        <div>
            <label for="message">Message <span aria-hidden="true">*</span></label>
            <textarea id="message" name="message"
                      placeholder="Votre message"
                      rows="5"
                      required aria-required="true"></textarea>
        </div>

        <button type="submit" class="submit-btn">ENVOYER</button>

    </form>
</section>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>