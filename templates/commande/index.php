<?php
$title = 'Mes commandes — Vite & Gourmand';
$pageCss = 'commande.css';
ob_start();
?>

<main id="main-content" class="container py-4">
    
    

    <?php if (empty($commandes)): ?>
        <div class="empty-state" role="status">
            <p>Vous n'avez pas encore de commande.</p>
            <a href="/menus" class="submit-btn">Voir nos menus</a>
        </div>
    
<?php else: ?>
    <div class="commandes-liste">
        <?php foreach ($commandes as $item):
            $commande     = $item['commande'];
            $menuTitre    = $item['menu_titre'];
            $menuImage    = $item['menu_image'];
            $peutModifier = $commande->estModifiable();
        ?>

            <!-- OVERLAY SUIVI -->
            <div class="overlay" id="overlay-<?= (int)$commande->getCommandeId() ?>"
                 aria-hidden="true"></div>

            <!-- PANNEAU SUIVI -->
            <nav class="follow-menu"
                 id="followMenu-<?= (int)$commande->getCommandeId() ?>"
                 aria-label="Suivi de la commande <?= htmlspecialchars($commande->getNumeroCommande()) ?>"
                 aria-hidden="true">
                <h2>SUIVI</h2>
                <p>Votre commande est actuellement</p>
                <div class="status-badge status-<?= htmlspecialchars(str_replace('_', '-', $commande->getStatut())) ?>">
                    <p><?= htmlspecialchars(strtoupper($commande->getStatutLibelle())) ?></p>
                </div>
            </nav>

            <!-- COMMANDE -->
            
            <div class="commande-block">

                <div class="card left-card">
                    <div class="card-header">
                        Commande <code><?= htmlspecialchars($commande->getNumeroCommande()) ?></code>
                    </div>
                    <div class="content">
                        <div class="dish-container">
                            <div class="dish-card">
                                <h2><?= htmlspecialchars($menuTitre) ?></h2>
                                <?php if (!empty($menuImage)): ?>
                                    <img src="/<?= htmlspecialchars($menuImage) ?>"
                                        alt="Photo du menu <?= htmlspecialchars($menuTitre) ?>">
                                <?php else: ?>
                                    <img src="/images/menus/default.jpg" alt="Photo non disponible">
                                <?php endif; ?>
                            </div>
                        
                    </div>
                </div>
            </div>

            <div class="card right-card">
                <div class="card-header">Informations</div>
                    <div class="form-content">
                        <form id="orderForm-<?= (int)$commande->getCommandeId() ?>"
                              method="POST"
                              action="/commande/modifier"
                              novalidate>

                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="commande_id"
                                   value="<?= (int)$commande->getCommandeId() ?>">
                            <div class="form-group">
                                <label for="adresse-<?= (int)$commande->getCommandeId() ?>">Adresse de livraison</label>
                                <input type="text"
                                    id="adresse-<?= (int)$commande->getCommandeId() ?>"
                                    name="adresse_livraison"
                                    value="<?= htmlspecialchars($commande->getAdresseLivraison()) ?>"
                                    readonly aria-readonly="true">
                            </div>

                            <div class="form-group">
                                <label for="ville-<?= (int)$commande->getCommandeId() ?>">Ville</label>
                                <input type="text"
                                    id="ville-<?= (int)$commande->getCommandeId() ?>"
                                    name="ville_livraison"
                                    value="<?= htmlspecialchars($commande->getVilleLivraison()) ?>"
                                    readonly aria-readonly="true">
                            </div>

                         
                            <div class="form-group">
                                <label for="personnes-<?= (int)$commande->getCommandeId() ?>">Nombre de personnes</label>
                                <input type="number"
                                    id="personnes-<?= (int)$commande->getCommandeId() ?>"
                                    value="<?= (int)$commande->getNombrePersonnes() ?>"
                                    readonly aria-readonly="true">
                            </div>

                            <div class="form-group">
                                <label for="date-<?= (int)$commande->getCommandeId() ?>">Date de prestation</label>
                                <input type="date"
                                       id="date-<?= (int)$commande->getCommandeId() ?>"
                                       name="date_prestation"
                                       aria-label="Date de prestation — format jour/mois/année"
                                       value="<?= htmlspecialchars($commande->getDatePrestation()) ?>"
                                       min="<?= date('Y-m-d', strtotime('+6 days')) ?>"
                                       <?= !$peutModifier ? 'readonly aria-readonly="true"' : '' ?>>
                            </div>

                            <div class="form-group">
                                <label for="heure-<?= (int)$commande->getCommandeId() ?>">Heure de livraison</label>
                                <input type="time"
                                       id="heure-<?= (int)$commande->getCommandeId() ?>"
                                       name="heure_livraison"
                                       aria-label="Heure de livraison — format heures:minutes"
                                       value="<?= htmlspecialchars(substr($commande->getHeureLivraison(), 0, 5)) ?>"
                                       min="09:00" max="20:00"
                                       <?= !$peutModifier ? 'readonly aria-readonly="true"' : '' ?>>
                            </div>

                            <div class="form-group">
                                <label for="prix-<?= (int)$commande->getCommandeId() ?>">Prix total</label>
                                <input type="text"
                                    id="prix-<?= (int)$commande->getCommandeId() ?>"
                                    value="<?= number_format($commande->getPrixTotal(), 2, ',', ' ') ?> €"
                                    readonly aria-readonly="true">
                            </div>

                            <!-- ACTIONS -->
                            <div class="button-container">
                                <?php if ($peutModifier): ?>
                                    <button type="submit" class="btn btn-modify"
                                            name="action" value="modifier">
                                        MODIFIER
                                    </button>
                                    <button type="submit" class="btn btn-cancel"
                                            name="action" value="annuler"
                                            onclick="return confirm('Confirmer l\'annulation ?')">
                                        ANNULER
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-disabled" disabled
                                            aria-disabled="true">
                                        MODIFICATION IMPOSSIBLE
                                    </button>
                                    <p class="info-modification">
                                        Votre commande a déjà été prise en charge.
                                    </p>
                                <?php endif; ?>

                                <button type="button" class="btn btn-follow"
                                        data-commande-id="<?= (int)$commande->getCommandeId() ?>"
                                        aria-controls="followMenu-<?= (int)$commande->getCommandeId() ?>"
                                        aria-expanded="false">
                                    SUIVRE
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>

        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<script src="/js/suivi.js"></script>

<?php
$content = ob_get_clean();
require_once ROOT_PATH . '/templates/layout/base.php';
?>