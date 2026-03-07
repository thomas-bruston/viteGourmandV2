<?php
$title = 'Nos menus — Vite & Gourmand';
$metaDescription = 'Découvrez tous nos menus traiteur bordelais. Filtrez par thème, régime, prix ou nombre de personnes.';
$pageCss = 'menus.css';
ob_start();
?>

<div class="container">
    <button class="filter-button" id="filterButton"
            aria-expanded="false"
            aria-controls="filtres-menu"
            aria-label="Ouvrir les filtres">
        Filtres
    </button>
</div>

<!-- MENU FILTRES -->
<div class="filtres-menu" id="filtres-menu" aria-hidden="true">

    <div class="filtres-section">
        <h3>Thème</h3>
            <?php foreach ($themes as $theme): ?>
            <button class="filter-btn"
                    data-filter="theme"
                    data-value="<?= htmlspecialchars($theme->getLibelle()) ?>"
                    aria-pressed="false">
                <?= htmlspecialchars($theme->getLibelle()) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="filtres-section">
        <h3>Régime alimentaire</h3>
            <?php foreach ($regimes as $regime): ?>
            <button class="filter-btn"
                    data-filter="regime"
                    data-value="<?= htmlspecialchars($regime->getLibelle()) ?>"
                    aria-pressed="false">
                <?= htmlspecialchars($regime->getLibelle()) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="filtres-section">
        <h3>Prix maximum par personne</h3>
        <div class="prix-range">
            <label for="prix-max">Jusqu'à <span id="prix-max-label">100</span>€</label>
            <input type="range" id="prix-max" name="prix_max"
                   min="0" max="100" step="5" value="100"
                   aria-valuemin="0" aria-valuemax="100" aria-valuenow="100">
        </div>
    </div>

    <div class="filtres-section">
        <h3>Nombre de personnes minimum</h3>
        <?php foreach ([2, 4, 6, 8, 10] as $nb): ?>
            <button class="filter-btn"
                    data-filter="personnes"
                    data-value="<?= $nb ?>"
                    aria-pressed="false">
                <?= $nb ?> pers.
            </button>
        <?php endforeach; ?>
    </div>

    <div class="filtres-actions">
        <button class="valider-btn" id="valider-btn">Valider</button>
        <button class="reset-btn" id="reset-btn">Réinitialiser</button>
    </div>

</div>

<!-- RÉSULTATS -->
<div id="cardsContainer" role="region" aria-live="polite" aria-label="Liste des menus">
    <?php require ROOT_PATH . '/templates/menus/cards.php'; ?>
</div>


<div class="menu_footer">
    <p>Chaque menu impose un nombre de personnes minimum. Tous nos prix sont indiqués par personne et TTC.</p>
</div>

<script src="/js/filterScript.js"></script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>