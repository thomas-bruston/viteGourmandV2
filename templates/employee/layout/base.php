<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/ressources/icons/toque.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/admin.css">
    <link rel="stylesheet" href="/css/adminHeader.css">
    <?php if (!empty($pageCss)): ?>
        <link rel="stylesheet" href="/css/<?= htmlspecialchars($pageCss) ?>">
    <?php endif; ?>
    <title><?= htmlspecialchars($title ?? 'Espace ' . ucfirst($_SESSION['user']['role'] ?? '')) ?></title>
</head>
<body>

    <!-- HEADER ADMIN + EMPLOYÉ -->
    <div class="admin-header">
        <?php if (($_SESSION['user']['role'] ?? '') === 'administrateur'): ?>
            <h1>Bienvenue dans l'espace administrateur</h1>
        <?php else: ?>
            <h1>Bienvenue dans l'espace employé</h1>
        <?php endif; ?>
    </div>

    <div class="btnContainer">
        <?php if (($_SESSION['user']['role'] ?? '') === 'administrateur'): ?>
            <a href="/admin/" class="header-btn">Accueil Admin</a>
                
        <?php else: ?>
            <a href="/employe/" class="header-btn">Accueil Employé</a>
                 
        <?php endif; ?>
    </div>

    <!-- Flash message -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="flash flash--<?= htmlspecialchars($_SESSION['flash']['type']) ?>"
             id="flashMsg" role="alert" aria-live="polite">
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

    <!-- Contenu -->
    <?= $content ?>

    <?php if (!empty($pageJs)): ?>
        <script src="/js/<?= htmlspecialchars($pageJs) ?>"></script>
    <?php endif; ?>

</body>
</html>