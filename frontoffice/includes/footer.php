<footer class="site-footer" role="contentinfo">
    <div class="footer-accent-bar" aria-hidden="true"></div>
    <div class="container">
        <div class="footer-inner">

            <div class="footer-col">
                <p class="footer-logo-text">Guerre en Iran</p>
                <p class="footer-desc">
                    Couverture editoriale independante du conflit iranien.
                    Analyses factuelles, decryptages geopolitiques, suivi diplomatique.
                </p>
            </div>

            <div class="footer-col">
                <p class="footer-col-title">Navigation</p>
                <ul class="footer-links-list" role="list">
                    <li><a href="/">Accueil</a></li>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <li><a href="/categorie/<?= htmlspecialchars($cat['slug']) ?>"><?= htmlspecialchars($cat['nom']) ?></a></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <li><a href="/a-propos">A propos</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <p class="footer-col-title">Publication</p>
                <p class="footer-desc">
                    Redaction numerique independante.<br>
                    Contenu verifie et date.<br>
                    Pas de publicite, pas de sponsoring.
                </p>
            </div>

        </div>

        <div class="footer-bottom">
            <span>&#169; <?= date('Y') ?> Guerre en Iran — Redaction numerique</span>
            <span>Tous droits reserves</span>
        </div>
    </div>
</footer>

</body>
</html>