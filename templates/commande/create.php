<?php
$title = 'Commander — Vite & Gourmand';
$pageCss = 'panier.css';
ob_start();
?>

<div class="container">

    <!-- MENU -->
    <div class="card left-card">
        <div class="header">Votre commande</div>
        <div class="content">
            <div class="dish-container">
                <div class="dish-card">
                    <h1 class="dish-title"><?= htmlspecialchars($menu->getTitre()) ?></h1>

                    <?php if (!empty($menu->getImage())): ?>
                        <img src="/<?= htmlspecialchars($menu->getImage()) ?>"
                             alt="Photo du menu <?= htmlspecialchars($menu->getTitre()) ?>">
                    <?php else: ?>
                        <img src="/images/menus/default.jpg" alt="Photo non disponible">
                    <?php endif; ?>

                    <p class="menu-info">
                        <?= (int)$menu->getNombrePersonneMinimum() ?> personnes minimum
                        — <?= number_format($menu->getPrixParPersonne(), 2, ',', ' ') ?>€ / pers.
                    </p>
                </div>

                <!-- Nb personnes -->
                <form method="GET" action="/commander" id="form-personnes" novalidate>
                    <input type="hidden" name="menu_id"
                           value="<?= (int)$menu->getMenuId() ?>">

                    <div class="input-group">
                        <label for="nb_personnes">Nombre de personnes</label>
                        <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
                        <input type="number"
                               id="nb_personnes"
                               name="nombre_personnes"
                               min="<?= (int)$menu->getNombrePersonneMinimum() ?>"
                               value="<?= (int)($nombrePersonnes ?? $menu->getNombrePersonneMinimum()) ?>"
                               required
                               aria-required="true"
                               aria-describedby="personnes-hint"
                               data-prix="<?= htmlspecialchars($menu->getPrixParPersonne()) ?>"
                               data-min="<?= (int)$menu->getNombrePersonneMinimum() ?>">
                    </div>
                    <p id="personnes-hint" class="field-hint">
                        Minimum <?= (int)$menu->getNombrePersonneMinimum() ?> personnes.
                    </p>

                    <button type="submit" class="btn-valider-calcul">Valider</button>
                </form>

                <p class="info-livraison">
                    Les frais de livraison sont calculés automatiquement selon votre adresse.
                </p>
            </div>
        </div>
    </div>

    <!-- RÉCAP PRIX -->
    <div class="card right-card">
        <div class="header">Total</div>

        <div class="form-group">
            <label>Sous-total menu :</label>
            <span class="price" id="sous_total">
                <?= number_format($prixMenu ?? 0, 2, ',', ' ') ?> €
            </span>
        </div>

        <div class="form-group">
            <label>Frais de livraison :</label>
            <span class="price" id="frais_livraison">
                <?= number_format($prixLivraison ?? 0, 2, ',', ' ') ?> €
            </span>
        </div>

        <hr>

        <div class="form-group total-group">
            <label><strong>Total :</strong></label>
            <span class="price total-price" id="total">
                <strong><?= number_format($prixTotal ?? 0, 2, ',', ' ') ?> €</strong>
            </span>
        </div>

        <div class="payment">
            <p>Le paiement s'effectue au moment de la livraison. Nous acceptons :</p>
            <img src="/images/moyens-paiement.png"
                 alt="Moyens de paiement acceptés : carte bleue, Visa, American Express">
        </div>
    </div>
</div>

<!-- FORMULAIRE LIVRAISON -->
<section class="form-container" aria-labelledby="titre-livraison">
    <form action="/commander" method="POST" class="contact-form" novalidate>

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <input type="hidden" name="menu_id"
               value="<?= (int)$menu->getMenuId() ?>">
        <input type="hidden" name="nombre_personnes"
               value="<?= (int)($nombrePersonnes ?? $menu->getNombrePersonneMinimum()) ?>">

        <h2 id="titre-livraison">VOS INFORMATIONS POUR LA LIVRAISON</h2>

        <div>
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom"
                   value="<?= htmlspecialchars($utilisateur?->getNom() ?? '') ?>"
                   readonly aria-readonly="true">
        </div>

        <div>
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom"
                   value="<?= htmlspecialchars($utilisateur?->getPrenom() ?? '') ?>"
                   readonly aria-readonly="true">
        </div>

        <div>
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($utilisateur?->getEmail() ?? '') ?>"
                   readonly aria-readonly="true">
        </div>

        <div>
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone"
                   value="<?= htmlspecialchars($utilisateur?->getTelephone() ?? '') ?>"
                   required aria-required="true">
        </div>

        <div>
            <label for="adresse_livraison">Adresse de livraison <span aria-hidden="true">*</span></label>
            <input type="text" id="adresse_livraison" name="adresse_livraison"
                   value="<?= htmlspecialchars($utilisateur?->getAdresse() ?? '') ?>"
                   required aria-required="true">
        </div>

        <div>
            <label for="code_postal_livraison">Code postal <span aria-hidden="true">*</span></label>
            <input type="text" id="code_postal_livraison" name="code_postal_livraison"
                   value="<?= htmlspecialchars($utilisateur?->getCodePostal() ?? '') ?>"
                   required aria-required="true">
        </div>

        <div>
            <label for="ville_livraison">Ville <span aria-hidden="true">*</span></label>
            <input type="text" id="ville_livraison" name="ville_livraison"
                   value="<?= htmlspecialchars($utilisateur?->getVille() ?? '') ?>"
                   required aria-required="true">
        </div>

        <div>
            <label for="date_prestation">Date de prestation <span aria-hidden="true">*</span></label>
            <input type="date" id="date_prestation" name="date_prestation"
                   min="<?= date('Y-m-d', strtotime('+6 days')) ?>"
                   required aria-required="true">
            <span class="field-hint">Minimum 6 jours à l'avance.</span>
        </div>

        <div>
            <label for="heure_livraison">Heure de livraison <span aria-hidden="true">*</span></label>
            <input type="time" id="heure_livraison" name="heure_livraison"
                   min="09:00" max="20:00"
                   required aria-required="true">
            <span class="field-hint">Entre 09h00 et 20h00.</span>
        </div>

        <div class="form-group-check">
            <input type="checkbox" id="pret_materiel" name="pret_materiel" value="1">
            <label for="pret_materiel">Souhaitez-vous du matériel (plats, équipements) ?</label>
            <p class="field-hint">
                Le matériel doit être restitué sous 10 jours ouvrés.
                Des frais de 600€ s'appliquent en cas de non-restitution.
            </p>
        </div>

        <button type="submit" class="submit-btn">VALIDER MA COMMANDE</button>
        
            <input type="hidden" name="distance_km" id="distance_km" value="0">
            <input type="hidden" name="prix_livraison_calcule" id="prix_livraison_calcule" value="5.00">
    </form>
  
</section>
<script src="/js/calculLivraison.js"></script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>