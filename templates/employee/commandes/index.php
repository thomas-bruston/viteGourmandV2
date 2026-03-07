<?php
use Entity\Commande;
$title = 'Gestion des commandes';
$pageCss = 'gestionCommandes.css';
ob_start();
?>

<div class="btn-container">
    <h2 class="main-btn">GESTION DES COMMANDES</h2>
</div>

<p class="warning-msg">
    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
    Contacter le client avant d'annuler sa commande.
</p>

<div class="container">
    <div class="left-card">
        <div class="card-header">
            <h2 class="card-title">Commandes</h2>
        </div>

        <div class="user-list">
            <?php if (empty($commandes)): ?>
                <p class="text-center py-4">Aucune commande.</p>
            <?php else: ?>
                <?php foreach ($commandes as $commande): ?>
                    <div class="user-item"
                         data-commande-id="<?= (int)$commande['commande_id'] ?>"
                         role="button" tabindex="0"
                         aria-label="Sélectionner la commande de <?= htmlspecialchars($commande['user_prenom'] . ' ' . $commande['user_nom']) ?>">

                        <div class="user-info">
                            <span class="user-name">
                                <strong class="client-name">
                                    <?= htmlspecialchars($commande['user_prenom'] . ' ' . $commande['user_nom']) ?>
                                </strong><br>
                                <strong>N° :</strong>
                                <code><?= htmlspecialchars($commande['numero_commande']) ?></code><br>
                                <strong>Menu :</strong> <?= htmlspecialchars($commande['menu_titre'] ?? '—') ?><br>
                                <strong>Personnes :</strong> <?= (int)$commande['nombre_personnes'] ?><br>
                                <strong>Total :</strong> <?= number_format($commande['prix_total'], 2, ',', ' ') ?> €<br>
                                <strong>Téléphone :</strong> <?= htmlspecialchars($commande['user_tel'] ?? '—') ?><br>
                                <strong>Adresse :</strong> <?= htmlspecialchars($commande['adresse_livraison']) ?>,
                                                           <?= htmlspecialchars($commande['ville_livraison']) ?><br>
                                <strong>Date :</strong>
                                <?= htmlspecialchars(date('d/m/Y', strtotime($commande['date_prestation']))) ?>
                                à <?= htmlspecialchars(substr($commande['heure_livraison'], 0, 5)) ?>
                            </span>
                        </div>

                        <!-- Motif annulation -->
                        <div class="motif-annulation-column">
                            <?php if (!empty($commande['motif_annulation'])): ?>
                                <div class="motif-annulation-box">
                                    <strong class="motif-title-small">Motif d'annulation :</strong>
                                    <p class="motif-text">
                                        <?= htmlspecialchars($commande['motif_annulation']) ?>
                                    </p>
                                </div>
                            <?php else: ?>
                                <div class="motif-empty">—</div>
                            <?php endif; ?>
                        </div>

                        <div class="status-badge status-<?= htmlspecialchars(str_replace(' ', '-', $commande['statut'])) ?>"
                             aria-label="Statut : <?= htmlspecialchars($commande['statut']) ?>">
                            <?= htmlspecialchars(ucfirst($commande['statut'])) ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- PANNEAU STATUTS -->
<div class="right-card" id="rightCard" aria-label="Changer le statut">
    <div class="right-card-header">
        <h3 class="right-card-title">Changer le statut</h3>
    </div>

  <?php foreach ([
    Commande::STATUT_ACCEPTEE                   => ['id' => 'status-acceptee',   'label' => 'Acceptée'],
    Commande::STATUT_EN_PREPARATION             => ['id' => 'status-preparation', 'label' => 'En préparation'],
    Commande::STATUT_EN_COURS_LIVRAISON         => ['id' => 'status-livraison',   'label' => 'En livraison'],
    Commande::STATUT_LIVREE                     => ['id' => 'status-livree',      'label' => 'Livrée'],
    Commande::STATUT_EN_ATTENTE_RETOUR_MATERIEL => ['id' => 'status-retour',      'label' => 'Retour matériel'],
    Commande::STATUT_TERMINEE                   => ['id' => 'status-terminee',    'label' => 'Terminée'],
] as $value => $info): ?>
    <a href="#"
       class="status-item"
       id="<?= $info['id'] ?>"
       data-status="<?= htmlspecialchars($value) ?>"
       role="button">
        <?= htmlspecialchars($info['label']) ?>
    </a>
<?php endforeach; ?>

<a href="#" class="status-item" id="status-annulee"
   data-status="<?= Commande::STATUT_ANNULEE ?>" role="button">
    Annulée
</a>
</div>

<!-- MOTIF ANNULATION -->
<div class="motif-container" id="motif-container" aria-hidden="true" aria-label="Saisir le motif d'annulation">
    <div class="motif-header">
        <h3 class="motif-title">Motif d'annulation</h3>
    </div>
    <textarea id="motif-annulation" class="motif-textarea"
              placeholder="Indiquez la raison de l'annulation et le moyen de contact"
              rows="6"
              aria-label="Motif d'annulation"></textarea>

    

    <input type="hidden" id="csrf-token-statut"
           value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

    <button id="btn-envoyer-annulation" class="btn-envoyer-annulation">Envoyer</button>
</div>

<div id="overlay" class="overlay" aria-hidden="true"></div>

<script src="/js/gestionStatuts.js"></script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/employee/layout/base.php';
?>