<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Vite & Gourmand — Traiteur bordelais, commandez vos menus en ligne.') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Italiana&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="/ressources/icons/toque.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/css/main.css">

    <!-- CSS spécifique à la page (optionnel) necessaire ????--> 
    <?php if (!empty($pageCss)): ?>
        <link rel="stylesheet" href="/css/<?= htmlspecialchars($pageCss) ?>">
    <?php endif; ?>

    <title><?= htmlspecialchars($title ?? 'Vite & Gourmand') ?></title>
</head>
<body>

    <?php require_once ROOT_PATH . '/templates/layout/header.php'; ?>

    <!-- Flash message -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="flash flash--<?= htmlspecialchars($_SESSION['flash']['type']) ?>" 
             id="flashMsg" 
             role="alert" 
             aria-live="polite">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
        <script>
            setTimeout(() => {
                const el = document.getElementById('flashMsg');
                if (el) el.style.display = 'none';
            }, 3000);
        </script>
    <?php endif; ?>

    <!-- Contenu de la page -->
    <?= $content ?>

    <?php require_once ROOT_PATH . '/templates/layout/footer.php'; ?>

 
    <script src="/js/main.js"></script>

    <!-- JS spécifique à la page (optionnel) necessaire ???? -->
    <?php if (!empty($pageJs)): ?>
        <script src="/js/<?= htmlspecialchars($pageJs) ?>"></script>
    <?php endif; ?>

</body>
</html>