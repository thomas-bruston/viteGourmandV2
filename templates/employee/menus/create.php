<?php
$title = 'Nouveau menu — Vite & Gourmand';
$pageCss = 'detailMenuAdmin.css';
ob_start();
?>


    <div class="edit-form-content">
        <h2>Créer un menu</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="/employe/menu/nouveau"
              enctype="multipart/form-data"
              novalidate>

            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <!-- INFOS MENU -->

            <div class="form-group">
                <label for="theme">Thème</label>
                <select id="theme" name="theme_ids[]" required aria-required="true">
                    <?php foreach ($themes as $t): ?>
                        <option value="<?= (int)$t->getThemeId() ?>">
                            <?= htmlspecialchars($t->getLibelle()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre"
                       required aria-required="true">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="regime">Régime</label>
                    <select id="regime" name="regime_ids[]" required aria-required="true">
                        <?php foreach ($regimes as $r): ?>
                            <option value="<?= (int)$r->getRegimeId() ?>">
                                <?= htmlspecialchars($r->getLibelle()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="prix">Prix / pers. (€)</label>
                    <input type="number" id="prix" name="prix_par_personne"
                           placeholder="25"
                           min="0" step="0.01" required aria-required="true">
                </div>

                <div class="form-group">
                    <label for="personne">Personnes min.</label>
                    <input type="number" id="personne" name="nombre_personne_minimum"
                           placeholder="2"
                           min="1" required aria-required="true">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"
                          rows="3" required aria-required="true"></textarea>
            </div>

            <div class="form-group">
                <label for="image">Image du menu</label>
                <input type="file" id="image" name="menu_image"
                    accept="image/jpeg,image/png,image/webp"
                    required aria-required="true">
                    
            </div>

            <!-- PLATS -->
            <h3 class="section-title">Plats du menu</h3>

            <?php for ($i = 0; $i < 3; $i++):
                $labels = ['Entrée', 'Plat principal', 'Dessert'];
                $categorieIds = [1, 2, 3]; // entrée, plat, dessert
            ?>
            <div class="plat-section">
                <h4><?= $labels[$i] ?></h4>

                <input type="hidden" name="plats[<?= $i ?>][categorie_id]"
                       value="<?= $categorieIds[$i] ?>">

                <div class="form-group">
                    <label for="plat_nom_<?= $i ?>">Nom du plat</label>
                    <input type="text"
                           id="plat_nom_<?= $i ?>"
                           name="plats[<?= $i ?>][nom]"
                           required aria-required="true">
                </div>

                <div class="form-group">
                    <label for="plat_image_<?= $i ?>">Photo du plat</label>
                    <input type="file"
                        id="plat_image_<?= $i ?>"
                        name="plats[<?= $i ?>][image]"
                        accept="image/jpeg,image/png,image/webp"
                        required aria-required="true">
                </div>

                <div class="form-group">
                    <fieldset>
                        
                        <div class="allergenes-checkboxes">
                            <?php foreach ($tousAllergenes as $allergene): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox"
                                           name="plats[<?= $i ?>][allergenes][]"
                                           value="<?= (int)$allergene->getAllergeneId() ?>">
                                    <?= htmlspecialchars($allergene->getLibelle()) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                </div>
            </div>
            <?php endfor; ?>

            <!-- ALLERGENES MENU -->
            <h3 class="section-title">Allergènes du menu</h3>
            <div class="form-group">
                <fieldset>
                    <div class="allergenes-checkboxes">
                        <?php foreach ($tousAllergenes as $allergene): ?>
                            <label class="checkbox-label">
                                <input type="checkbox"
                                       name="allergenes[]"
                                       value="<?= (int)$allergene->getAllergeneId() ?>">
                                <?= htmlspecialchars($allergene->getLibelle()) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn-save">CRÉER LE MENU</button>
            </div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/employee/layout/base.php';
?>