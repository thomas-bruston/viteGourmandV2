<footer class="footer-container">

    <div class="footer-link">
        <a href="/cgv">CGV</a>
    </div>

    <div class="footer-time">
        <?php if (!empty($horaires)): ?>
            <?= nl2br(htmlspecialchars($horaires)) ?>
        <?php else: ?>
            <span>Horaires non disponibles</span>
        <?php endif; ?>
    </div>

    <div class="footer-link">
        <a href="/mentions">Mentions légales</a>
    </div>

</footer>