<?php
$title = 'Gestion des avis';
$pageCss = 'gestionAvis.css';
ob_start();
?>

<div class="btn-container">
    <h2 class="main-btn">Gestion des avis</h2>
</div>

<div class="tableContainer">
    <table>
        <caption>Tableau des avis utilisateurs</caption>
        <tbody>
            <?php if (empty($avis)): ?>
                <tr>
                    <td colspan="5" class="text-center py-4">Aucun avis.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($avis as $a): ?>
                    <tr>
                        <th scope="row">
                            <?= htmlspecialchars($a->getUtilisateurPrenom() . ' ' . $a->getUtilisateurNom()) ?>
                        </th>
                        <td>
                            <?= str_repeat('★', $a->getNote()) . str_repeat('☆', 5 - $a->getNote()) ?>
                        </td>
                        <td><?= nl2br(htmlspecialchars($a->getCommentaire())) ?></td>
                        <td>
                            <?php if ($a->getStatut() === 'valide'): ?>
                                <span class="badge-valid">Validé</span>
                            <?php elseif ($a->getStatut() === 'refuse'): ?>
                                <span class="badge-refuse">Refusé</span>
                            <?php else: ?>
                                <span class="badge-attente">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <?php if ($a->getStatut() === 'en_attente'): ?>
                                <form method="POST" action="/employe/avis/valider" style="display:inline">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="avis_id" value="<?= (int)$a->getAvisId() ?>">
                                    <button type="submit" class="btn-validate"
                                            aria-label="Valider l'avis">
                                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                                    </button>
                                </form>

                                <form method="POST" action="/employe/avis/refuser" style="display:inline">
                                    <input type="hidden" name="csrf_token"
                                           value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <input type="hidden" name="avis_id" value="<?= (int)$a->getAvisId() ?>">
                                    <button type="submit" class="btn-delete"
                                            aria-label="Refuser l'avis">
                                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/employee/layout/base.php';
?>