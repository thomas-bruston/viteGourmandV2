<?php
$title = 'Messages';
$pageCss = 'messages.css';
ob_start();
?>

<div class="btn-container">
    <h2 class="main-btn">Messages clients</h2>
</div>

<div class="tableContainer">
    <table>
        <caption class="caption">Tableau des messages</caption>
        
        <tbody>
            <?php if (empty($messages)): ?>
                <tr>
                    <td colspan="5" class="text-center py-4">Aucun message.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($messages as $m): ?>
                    <tr class="tableContent">
                        <th scope="row" class="tableContentItem">
                            <?= htmlspecialchars($m->getPrenom() . ' ' . $m->getNom()) ?>
                        </th>
                        <td class="tableContentItem">
                            <a href="mailto:<?= htmlspecialchars($m->getEmail()) ?>">
                                <?= htmlspecialchars($m->getEmail()) ?>
                            </a>
                        </td>
                        <td class="tableContentItem">
                            <?= nl2br(htmlspecialchars($m->getMessage())) ?>
                        </td>
                        <td class="tableContentItem">
                            <?= htmlspecialchars(date('d/m/Y H:i', strtotime($m->getDateEnvoi() ?? ''))) ?>
                        </td>
                        <td>
                            <form method="POST"
                                  action="/employe/messages/supprimer"
                                  onsubmit="return confirm('Supprimer ce message ?')">
                                <input type="hidden" name="csrf_token"
                                       value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <input type="hidden" name="contact_id"
                                       value="<?= (int)$m->getContactId() ?>">
                                <button type="submit" class="btn-delete"
                                        aria-label="Supprimer le message de <?= htmlspecialchars($m->getEmail()) ?>">
                                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                </button>
                            </form>
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