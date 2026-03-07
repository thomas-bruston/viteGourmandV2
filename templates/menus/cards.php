
    <?php foreach ($menus as $menu): ?>
        <article class="card"
                 aria-label="Menu <?= htmlspecialchars($menu->getTitre()) ?>">

            <div class="card-header">
                <span class="badge-category">
                    <?= htmlspecialchars(implode(', ', $menu->getThemes())) ?>
                </span>
                <h2 class="dish-title">"<?= htmlspecialchars($menu->getTitre()) ?>"</h2>
                <div class="persons">
                    <span><?= (int)$menu->getNombrePersonneMinimum() ?> personnes minimum</span>
                </div>
                <div class="details-badge">
                    <a href="/menus/detail?id=<?= (int)$menu->getMenuId() ?>"
                    aria-label="Voir le menu <?= htmlspecialchars($menu->getTitre()) ?>">
                        <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="image-container">
                <?php if (!empty($menu->getImage())): ?>
                    <img src="/<?= htmlspecialchars($menu->getImage()) ?>"
                         alt="Photo du menu <?= htmlspecialchars($menu->getTitre()) ?>"
                         class="dish-image"
                         loading="lazy">
                <?php else: ?>
                    <img src="/images/menus/default.jpg"
                         alt="Photo non disponible"
                         class="dish-image">
                <?php endif; ?>
                <div class="price-badge">
                    <?= number_format($menu->getPrixParPersonne(), 0) ?>€
                </div>
            </div>

            <div class="card-footer">
                <span class="badge-diet">
                    Régime : <?= htmlspecialchars(implode(', ', $menu->getRegimes())) ?>
                </span>
            </div>

        </article>
    <?php endforeach; ?>
