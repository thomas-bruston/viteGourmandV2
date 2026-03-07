<?php
$title = 'Accueil — Vite & Gourmand';
$metaDescription = 'Vite & Gourmand, traiteur bordelais depuis 2008. Commandez vos menus traiteur en ligne, livraison sur Bordeaux et alentours.';
$pageCss = 'index.css';
ob_start();
?>

<!-- Hero -->
<div class="image-container">
    <img src="/images/ban.png" alt="Présentation d'un plat Vite et Gourmand">
    <div class="text-main">Vite et Gourmand</div>
</div>

<!-- Présentation -->
<section aria-labelledby="titre-presentation">
    <div class="text-container">
        
        <div class="text-content">
            <p>Fondé en 2008 par trois amis traiteurs passionnés, notre établissement vous propose une cuisine de qualité.</p>
            <p>Nous avons à cœur de favoriser le travail de produits locaux, frais,</p>
            <p>et issus de circuits courts principalement français, en collaborant étroitement avec les producteurs de la région de Bordeaux.</p>
            <p>Nos plats sont non seulement délicieux mais aussi respectueux de l'environnement et de l'économie locale. En privilégiant les produits de saison, nous vous offrons des saveurs authentiques qui reflètent les richesses de notre région.</p>
            <p>Cette démarche éthique nous permet de soutenir les agriculteurs et artisans locaux tout en vous proposant des menus gourmands, créatifs, et diversifiés.</p>
        </div>
    </div>
</section>

<!-- Cards -->
<div class="card-container">

    <div class="card">
        <img src="/images/restaurant.png" alt="Vue de l'intérieur du restaurant Vite et Gourmand">
        <div class="card-text">
            Nous vous proposons des menus gourmands, des menus événements et des menus du bout du monde.
        </div>
    </div>

    <div class="card">
        <img src="/images/equipe.png" alt="L'équipe de Vite et Gourmand avec le chef Jean Parmentier">
        <div class="card-text">
            Notre équipe et son chef <span class="name">Jean Parmentier</span>
            vous propose de nouvelles cartes au fil des saisons.
        </div>
    </div>

    <!-- Avis clients -->
    <div class="card">
        <img src="/images/avis.png" alt="Plat présenté par Vite et Gourmand">
        <div class="card-text-avis" aria-label="Avis de nos clients">
            <?php if (!empty($avis)): ?>
            <?php foreach ($avis as $avisItem): ?>
                <blockquote>
                    <p><?= htmlspecialchars($avisItem->getCommentaire()) ?></p>
                    <footer>
                        — <?= htmlspecialchars($avisItem->getUtilisateurPrenom() ?: 'Un client') ?>
                        <span class="avis-note" aria-label="Note : <?= $avisItem->getNote() ?> sur 5">
                            <?= str_repeat('★', $avisItem->getNote()) . str_repeat('☆', 5 - $avisItem->getNote()) ?>
                        </span>
                    </footer>
                </blockquote>
            <?php endforeach; ?>
            <?php else: ?>
                <p>Soyez le premier à laisser un avis !</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>