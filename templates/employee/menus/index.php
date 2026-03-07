<?php
$title = 'Gestion des menus';
$pageCss = 'gestionMenus.css';
ob_start();
?>

<div>
    <a href="/employe/menu/nouveau" class="btn-save">NOUVEAU MENU</a>
</div>

<div id="cardsContainer">
    
    
        <?php foreach ($menus as $menu): ?>
            <article class="card"
                     aria-label="Menu <?= htmlspecialchars($menu->getTitre()) ?>">

                <div class="card-header">
                    <span class="badge-category">
                        <?= htmlspecialchars(implode(', ', $menu->getThemes())) ?>
                    </span>
                    <h2 class="dish-title">"<?= htmlspecialchars($menu->getTitre()) ?>"</h2>
                    <div class="persons">
                        <span>
                            <?= (int)$menu->getNombrePersonneMinimum() ?>
                            personne<?= $menu->getNombrePersonneMinimum() > 1 ? 's' : '' ?> min.
                        </span>
                    </div>
                    <div class="details-badge">
                        <a href="/employe/menu/modifier?id=<?= (int)$menu->getMenuId() ?>"
                           aria-label="Gérer le menu <?= htmlspecialchars($menu->getTitre()) ?>">
                            <i class="fa-solid fa-circle-plus" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="image-container">
                    <?php if (!empty($menu->getImage())): ?>
                        <img src="/<?= htmlspecialchars($menu->getImage()) ?>"
                             alt="Photo du menu <?= htmlspecialchars($menu->getTitre()) ?>"
                             class="dish-image" loading="lazy">
                    <?php else: ?>
                        <img src="/images/menus/default.jpg"
                             alt="Photo non disponible" class="dish-image">
                    <?php endif; ?>
                    <div class="price-badge"><?= number_format($menu->getPrixParPersonne(), 0) ?>€</div>
                </div>

                <div class="card-footer">
                    <div class="card-footer">
                        <span class="badge-diet">
                            Régime : <?= htmlspecialchars(implode(', ', $menu->getRegimes()) ?: 'Classique') ?>
                        </span>
                        <span class="quantite <?= $menu->getQuantiteRestante() === 0 ? 'epuise' : '' ?>">
                            <?= $menu->getQuantiteRestante() === 0 ? 'Épuisé' : $menu->getQuantiteRestante() . ' menus' ?>
                        </span>
                    </div>                  
                </div>

            </article>
        <?php endforeach; ?>

</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/employee/layout/base.php';
?>