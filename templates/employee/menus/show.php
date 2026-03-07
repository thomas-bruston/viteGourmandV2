<?php
$title = 'Détail menu — ' . htmlspecialchars($menu->getTitre());
$pageCss = 'detailMenuAdmin.css';
ob_start();
?>

<!-- VUE DÉTAIL -->
<div id="contentView">

    <div class="secondMenu">
        <p>Régime : <?= htmlspecialchars(implode(', ', $menu->getRegimes()) ?: 'Classique') ?></p>
        <p>
            <?= number_format($menu->getPrixParPersonne(), 0) ?>€
            (<?= (int)$menu->getNombrePersonneMinimum() ?> pers. min.)
        </p>
        <?php if (!empty($allergenes)): ?>
            <button id="allergieBtn" aria-expanded="false" aria-controls="allergieMenu">
                Liste des allergènes
            </button>
        <?php endif; ?>
    </div>

    <?php if (!empty($allergenes)): ?>
        <div class="allergie-menu" id="allergieMenu" aria-hidden="true">
            <div class="allergie-header">
                <h2>Allergènes</h2>
            </div>
            <div class="allergenes">
                <ol>
                    <?php foreach ($allergenes as $allergene): ?>
                        <li><?= htmlspecialchars($allergene) ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    <?php endif; ?>

    <div class="theme">
        <p class="titre"><?= htmlspecialchars(implode(', ', $menu->getThemes())) ?></p>
    </div>

    <h3 class="menu-titre">"<?= htmlspecialchars($menu->getTitre()) ?>"</h3>
    <p class="description"><?= htmlspecialchars($menu->getDescription()) ?></p>

    <!-- PLATS -->
    <div id="cardsContainer">
        <?php foreach ($plats as $plat): ?>
            <article class="card" aria-label="Plat : <?= htmlspecialchars($plat->getNom()) ?>">
                <div class="card-header">
                    <?= nl2br(htmlspecialchars($plat->getNom())) ?>
                </div>
                <div class="image-container">
                    <?php if (!empty($plat->getImage())): ?>
                        <img src="/<?= htmlspecialchars($plat->getImage()) ?>"
                             alt="Photo du plat <?= htmlspecialchars($plat->getNom()) ?>"
                             loading="lazy">
                    <?php else: ?>
                        <img src="/images/plats/default.jpg" alt="Photo non disponible">
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<!-- FORMULAIRE MODIFICATION -->
<div class="edit-form-content">

    <h2>Modifier le menu</h2>

    <?php if (!empty($error)): ?>
        <div class="error-message" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST"
          action="/employe/menu/modifier"
          enctype="multipart/form-data"
          novalidate>

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        <input type="hidden" name="menu_id"
               value="<?= (int)$menu->getMenuId() ?>">

        <div class="form-group">
            <label for="theme">Thème</label>
            <select id="theme" name="theme_ids[]" required aria-required="true">
                <?php foreach ($themes as $t): ?>
                    <option value="<?= (int)$t->getThemeId() ?>"
                        <?= in_array($t->getThemeId(), $themeIds) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t->getLibelle()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="titre">Titre</label>
            <input type="text" id="titre" name="titre"
                   value="<?= htmlspecialchars($menu->getTitre()) ?>"
                   required aria-required="true">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="regime">Régime</label>
                <select id="regime" name="regime_ids[]" required aria-required="true">
                    <?php foreach ($regimes as $r): ?>
                        <option value="<?= (int)$r->getRegimeId() ?>"
                            <?= in_array($r->getRegimeId(), $regimeIds) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r->getLibelle()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="prix">Prix / pers. (€)</label>
                <input type="number" id="prix" name="prix_par_personne"
                       value="<?= htmlspecialchars($menu->getPrixParPersonne()) ?>"
                       min="0" step="0.01" required aria-required="true">
            </div>

            <div class="form-group">
                <label for="personne">Personnes min.</label>
                <input type="number" id="personne" name="nombre_personne_minimum"
                       value="<?= (int)$menu->getNombrePersonneMinimum() ?>"
                       min="1" required aria-required="true">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"
                      rows="3" required aria-required="true"><?= htmlspecialchars($menu->getDescription()) ?></textarea>
        </div>

        <div class="form-group">
            <label>Changer l'image du menu</label>
            <input type="file" name="menu_image"
                   accept="image/jpeg,image/png,image/webp">
        </div>

        <!-- Modification des plats -->
        <div class="form-group">
            <label>Plats du menu</label>
            <div class="plates-images-grid">
                <?php foreach ($plats as $plat): ?>
                    <div class="plate-image-item">
                        <div class="current-plate-image">
                            <?php if (!empty($plat->getImage())): ?>
                                <img src="/<?= htmlspecialchars($plat->getImage()) ?>"
                                     alt="<?= htmlspecialchars($plat->getNom()) ?>">
                            <?php endif; ?>
                        </div>

                        <input type="hidden"
                               name="plats[<?= (int)$plat->getPlatId() ?>][plat_id]"
                               value="<?= (int)$plat->getPlatId() ?>">

                        <div class="form-group">
                            <label>Nom du plat</label>
                            <input type="text"
                                   name="plats[<?= (int)$plat->getPlatId() ?>][nom]"
                                   value="<?= htmlspecialchars($plat->getNom()) ?>">
                        </div>

                        <div class="form-group">
                            <label>Nouvelle photo</label>
                            <input type="file"
                                   name="plat_photos[<?= (int)$plat->getPlatId() ?>]"
                                   accept="image/jpeg,image/png,image/webp">
                        </div>

                        <!-- Bouton supprimer plat -->
                        <form method="POST" action="/employe/plat/supprimer"
                              onsubmit="return confirm('Supprimer ce plat ?')">
                            <input type="hidden" name="csrf_token"
                                   value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                            <input type="hidden" name="plat_id"
                                   value="<?= (int)$plat->getPlatId() ?>">
                            <input type="hidden" name="menu_id"
                                   value="<?= (int)$menu->getMenuId() ?>">
                            <button type="submit" class="btn-supprimer">
                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Allergènes -->
        <div class="form-group">
            <fieldset>
                <legend>Allergènes</legend>
                <div class="allergenes-checkboxes">
                    <?php foreach ($tousAllergenes as $allergene): ?>
                        <label class="checkbox-label">
                            <input type="checkbox"
                                   name="allergenes[]"
                                   value="<?= (int)$allergene->getAllergeneId() ?>"
                                   <?= in_array($allergene->getAllergeneId(), $allergeneIds) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($allergene->getLibelle()) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-save">Enregistrer</button>
        </div>
        <div class="footer-btn" style = margin-top:10px>
        <form method="POST"
              action="/employe/menu/supprimer"
              onsubmit="return confirm('Supprimer ce menu définitivement ?')">
            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <input type="hidden" name="menu_id" value="<?= (int)$menu->getMenuId() ?>">
            <button type="submit" class="btn-supprimer">SUPPRIMER CE MENU</button>
        </form>
    </div>

    </form>
</div>

<!-- FOOTER -->
<footer class="detail-footer-admin">
    
</footer>

<script src="/js/allergie.js"></script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/employee/layout/base.php';
?>