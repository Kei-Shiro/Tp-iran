<?php
require_once '../includes/db.php';

$db = getDB();
$pageTitle       = 'Guerre en Iran — Actualites et analyses';
$metaDescription = 'Dernieres actualites, analyses et decryptages sur la guerre en Iran.';

$articles = $db->query(
        "SELECT a.titre, a.slug, a.resume, a.image, a.alt_image, a.created_at,
            c.nom AS categorie, c.slug AS cat_slug
     FROM articles a
     JOIN categories c ON c.id = a.categorie_id
     WHERE a.statut = 'publie'
     ORDER BY a.created_at DESC"
)->fetchAll();

// Decoupage editorial (style site media)
$featured   = $articles[0] ?? null;
$topStories = array_slice($articles, 1, 4);
$latest     = array_slice($articles, 5);

require_once '../includes/header.php';
?>

    <main class="page-main" id="main-content">

        <!-- Hero banner -->
        <section class="hero" aria-label="Presentation du site">
            <div class="container">
                <p class="hero-eyebrow anim-in">Edition continue</p>
                <h1 class="hero-title anim-in anim-in-2">
                    Actualites<br>
                    et <em>analyses</em><br>
                    de reference
                </h1>
                <p class="hero-desc anim-in anim-in-3">
                    Suivi editorial du conflit iranien —
                    terrain, diplomatie, enjeux regionaux et strategiques.
                </p>
            </div>
        </section>

        <!-- Accueil "media" : A la une + flux + sidebar -->
        <section class="home" aria-label="Accueil editorial">
            <div class="container home-layout">

                <div class="home-main">

                    <div class="section-header">
                        <span class="section-title">Edition du jour</span>
                        <span class="section-count"><?= count($articles) ?> publication<?= count($articles) > 1 ? 's' : '' ?></span>
                        <span class="section-line" aria-hidden="true"></span>
                    </div>

                    <?php if (empty($articles)): ?>
                        <p style="color:var(--muted); font-family:var(--font-mono); font-size:.8rem;">
                            Aucun article publie pour le moment.
                        </p>
                    <?php else: ?>

                        <!-- A la une -->
                        <?php if (!empty($featured)): ?>
                            <article class="lead-story" aria-label="A la une">
                                <?php if (!empty($featured['image'])): ?>
                                    <a class="lead-story-media" href="/article/<?= htmlspecialchars($featured['slug']) ?>">
                                        <img src="/<?= htmlspecialchars($featured['image']) ?>"
                                             alt="<?= htmlspecialchars($featured['alt_image'] ?: strip_tags($featured['titre'])) ?>"
                                             loading="eager">
                                    </a>
                                <?php endif; ?>

                                <div class="lead-story-content">
                                    <a href="/categorie/<?= htmlspecialchars($featured['cat_slug']) ?>" class="card-cat">
                                        <?= htmlspecialchars($featured['categorie']) ?>
                                    </a>
                                    <h2 class="lead-story-title">
                                        <a href="/article/<?= htmlspecialchars($featured['slug']) ?>">
                                            <?= htmlspecialchars(strip_tags($featured['titre'])) ?>
                                        </a>
                                    </h2>
                                    <?php if (!empty($featured['resume'])): ?>
                                        <p class="lead-story-excerpt"><?= htmlspecialchars($featured['resume']) ?></p>
                                    <?php endif; ?>
                                    <p class="card-meta">
                                        <time datetime="<?= $featured['created_at'] ?>">
                                            <?= date('d/m/Y', strtotime($featured['created_at'])) ?>
                                        </time>
                                    </p>
                                </div>
                            </article>
                        <?php endif; ?>

                        <!-- Top stories -->
                        <?php if (!empty($topStories)): ?>
                            <section class="top-stories" aria-label="A la suite">
                                <div class="mini-header">
                                    <span class="mini-title">A la suite</span>
                                    <span class="mini-line" aria-hidden="true"></span>
                                </div>
                                <div class="top-stories-grid" role="list">
                                    <?php foreach ($topStories as $article): ?>
                                        <article class="top-story" role="listitem">
                                            <a href="/categorie/<?= htmlspecialchars($article['cat_slug']) ?>" class="card-cat">
                                                <?= htmlspecialchars($article['categorie']) ?>
                                            </a>
                                            <h3 class="top-story-title">
                                                <a href="/article/<?= htmlspecialchars($article['slug']) ?>">
                                                    <?= htmlspecialchars(strip_tags($article['titre'])) ?>
                                                </a>
                                            </h3>
                                            <p class="card-meta">
                                                <time datetime="<?= $article['created_at'] ?>">
                                                    <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                                                </time>
                                            </p>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endif; ?>

                        <!-- Dernieres publications -->
                        <section class="latest" aria-label="Dernieres publications">
                            <div class="mini-header">
                                <span class="mini-title">Dernieres publications</span>
                                <span class="mini-line" aria-hidden="true"></span>
                            </div>

                            <?php if (empty($latest)): ?>
                                <p style="color:var(--muted); font-family:var(--font-mono); font-size:.8rem;">
                                    Pas d'autres publications.
                                </p>
                            <?php else: ?>
                                <div class="news-grid" role="list">
                                    <?php foreach ($latest as $i => $article): ?>
                                        <div class="card-standard" role="listitem">
                                            <article class="news-card">
                                                <?php if (!empty($article['image'])): ?>
                                                    <div class="card-image-wrap">
                                                        <img src="/<?= htmlspecialchars($article['image']) ?>"
                                                             alt="<?= htmlspecialchars($article['alt_image'] ?: strip_tags($article['titre'])) ?>"
                                                             loading="lazy">
                                                    </div>
                                                <?php endif; ?>
                                                <div class="card-content">
                                                    <a href="/categorie/<?= htmlspecialchars($article['cat_slug']) ?>" class="card-cat">
                                                        <?= htmlspecialchars($article['categorie']) ?>
                                                    </a>
                                                    <h3 class="card-title">
                                                        <a href="/article/<?= htmlspecialchars($article['slug']) ?>">
                                                            <?= htmlspecialchars(strip_tags($article['titre'])) ?>
                                                        </a>
                                                    </h3>
                                                    <?php if (!empty($article['resume'])): ?>
                                                        <p class="card-excerpt"><?= htmlspecialchars($article['resume']) ?></p>
                                                    <?php endif; ?>
                                                    <p class="card-meta">
                                                        <time datetime="<?= $article['created_at'] ?>">
                                                            <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                                                        </time>
                                                    </p>
                                                </div>
                                            </article>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </section>

                    <?php endif; ?>
                </div>

                <aside class="home-sidebar" aria-label="Encadres">
                    <div class="sidebar-block">
                        <p class="sidebar-heading">Rubriques</p>
                        <ul class="sidebar-links" role="list">
                            <?php foreach (($categories ?? []) as $cat): ?>
                                <li>
                                    <a href="/categorie/<?= htmlspecialchars($cat['slug']) ?>">
                                        <?= htmlspecialchars($cat['nom']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li><a href="/a-propos">A propos</a></li>
                        </ul>
                    </div>

                    <?php if (!empty($articles)): ?>
                        <div class="sidebar-block">
                            <p class="sidebar-heading">En bref</p>
                            <ol class="sidebar-brief" role="list">
                                <?php foreach (array_slice($articles, 0, 6) as $a): ?>
                                    <li>
                                        <a href="/article/<?= htmlspecialchars($a['slug']) ?>" class="brief-title">
                                            <?= htmlspecialchars(strip_tags($a['titre'])) ?>
                                        </a>
                                        <time class="brief-date" datetime="<?= $a['created_at'] ?>">
                                            <?= date('d/m', strtotime($a['created_at'])) ?>
                                        </time>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    <?php endif; ?>
                </aside>

            </div>
        </section>

    </main>

<?php require_once '../includes/footer.php'; ?>