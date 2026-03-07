<?php
$title = htmlspecialchars($menu->getTitre()) . ' — Vite & Gourmand';
$metaDescription = htmlspecialchars($menu->getDescription() ?? 'Découvrez ce menu traiteur Vite & Gourmand.');
$pageCss = 'detailMenu.css';
ob_start();
?>

<!-- BARRE INFO MENU -->
<div class="secondMenu" role="complementary" aria-label="Informations rapides du menu">
    <p>Régime : <?= htmlspecialchars(implode(', ', $menu->getRegimes()) ?: 'Classique') ?></p>
    <p>
        <strong><?= number_format($menu->getPrixParPersonne(), 0) ?>€</strong>
        / pers — <?= (int)$menu->getNombrePersonneMinimum() ?> pers. minimum
    </p>
    <?php if (!empty($allergenes)): ?>
        <button id="allergieBtn" aria-expanded="false" aria-controls="allergieMenu">
            Liste des allergènes
        </button>
    <?php endif; ?>
</div>
    <p class="quantite-restante">
        <?php if ($menu->getQuantiteRestante() === 0): ?>
            <span class="epuise">Ce menu est epuisé</span>
        <?php else: ?>
            <span class="disponible"><?= (int)$menu->getQuantiteRestante() ?> menus disponibles</span>
        <?php endif; ?>
    </p>

<!-- MENU ALLERGÈNES -->
<?php if (!empty($allergenes)): ?>
    <div class="allergie-menu" id="allergieMenu" aria-hidden="true">
        <div class="allergie-header">
            <h2>Allergènes</h2>
        </div>
        <div class="allergenes">
            <ol>
                <?php foreach ($allergenes as $allergene): ?>
                    <li><?= htmlspecialchars($allergene) ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
<?php endif; ?>

<!-- TITRE MENU -->
<div class="theme">
    <p class="titre"><?= htmlspecialchars(implode(', ', $menu->getThemes())) ?></p>
</div>

<h3>"<?= htmlspecialchars($menu->getTitre()) ?>"</h3>
<p class="description"><?= htmlspecialchars($menu->getDescription() ?? '') ?></p>

<!-- PLATS -->
<?php if (!empty($plats)): ?>
    <div id="cardsContainer">
        <?php foreach ($plats as $plat): ?>
            <article class="card" aria-label="Plat : <?= htmlspecialchars($plat->getNom()) ?>">
                <div class="card-header">
                    <?= nl2br(htmlspecialchars($plat->getNom())) ?>
                  
                </div>
                <div class="image-container">
                    <?php if (!empty($plat->getImage())): ?>
                        <img src="/<?= htmlspecialchars($plat->getImage()) ?>"
                             alt="Photo du plat <?= htmlspecialchars($plat->getNom()) ?>"
                             loading="lazy">
                    <?php else: ?>
                        <img src="/images/plats/default.jpg" alt="Photo non disponible">
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- FOOTER MENU-->

<footer class="detail-footer">
    <div class="footer-left">
        <div class="footer-message">
            <p>Ce menu doit être commandé <strong>6 jours minimum</strong> avant la date de la prestation.</p>
        </div>
        <div class="footer-info">
            <p>Tous nos plats sont fabriqués à base de produits frais.<br>
            Ils doivent être conservés au réfrigérateur et peuvent être congelés.</p>
        </div>
    </div>
    <div class="footer-right">
        <?php if (empty($_SESSION['user'])): ?>
            <p class="auth-invite">Connectez-vous pour commander</p>
            <div class="auth-links">
                <a href="/connexion" class="btn-auth">Se connecter</a>
                <span class="separator">/</span>
                <a href="/inscription" class="btn-auth">S'inscrire</a>
            </div>
        <?php else: ?>
            <form method="GET" action="/commander">
                <input type="hidden" name="menu_id" value="<?= (int)$menu->getMenuId() ?>">
                <button type="submit" class="btn-commander">COMMANDER</button>
            </form>
        <?php endif; ?>
    </div>
</footer>

<div class="overlay" id="overlay" aria-hidden="true"></div>
<script src="/js/gestionMenus.js"></script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>