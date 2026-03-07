<?php
$title = 'Gestion des employés';
$pageCss = 'gestionEmployes.css';
ob_start();
?>

<div class="btn-container">
    <h2 class="main-btn">Gestion des employés</h2>
</div>

<div class="employee-list">
    <?php if (empty($employes)): ?>
        <p class="text-center py-4">Aucun employé.</p>
    <?php else: ?>
        <?php foreach ($employes as $emp): ?>
            <div class="employee-item">
                <span class="employee-name">
                    <?= htmlspecialchars($emp->getPrenom() . ' ' . $emp->getNom()) ?>
                </span>
                <?= htmlspecialchars($emp->getEmail()) ?><br>
                <?= htmlspecialchars($emp->getTelephone() ?? '—') ?><br>
                <?= htmlspecialchars($emp->getAdresse() ?? '—') ?>

                <form method="POST"
                      action="/admin/employe/desactiver"
                      style="display:inline"
                      onsubmit="return confirm('Supprimer cet employé ?')">
                    <input type="hidden" name="csrf_token"
                           value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="employe_id"
                           value="<?= (int)$emp->getUtilisateurId() ?>">
                    <button type="submit" class="btn-delete"
                            aria-label="Supprimer <?= htmlspecialchars($emp->getPrenom() . ' ' . $emp->getNom()) ?>">
                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="add-container">
    <a href="/admin/employe/nouveau" class="btn-add">
        <i class="fa-solid fa-plus" aria-hidden="true"></i> AJOUTER
    </a>
</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/employee/layout/base.php';
?>