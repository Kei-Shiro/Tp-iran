<?php
require_once '../includes/db.php';

$db   = getDB();
$slug = $_GET['slug'] ?? '';

if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
    http_response_code(404);
    include '404.php';
    exit;
}

$stmt = $db->prepare('SELECT id, nom, slug FROM categories WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$categorie = $stmt->fetch();

if (!$categorie) {
    http_response_code(404);
    include '404.php';
    exit;
}

$stmtArticles = $db->prepare(
        "SELECT titre, slug, resume, created_at
     FROM articles
     WHERE categorie_id = ? AND statut = 'publie'
     ORDER BY created_at DESC"
);
$stmtArticles->execute([$categorie['id']]);
$articles = $stmtArticles->fetchAll();

$pageTitle       = $categorie['nom'] . ' — Guerre en Iran';
$metaDescription = 'Consultez les articles de la categorie ' . $categorie['nom'] . ' sur Guerre en Iran.';

require_once '../includes/header.php';
?>

    <main class="page-main" id="main-content">
        <div class="container">

            <!-- En-tete de categorie -->
            <header class="cat-header">
                <p class="cat-label">Rubrique</p>
                <h1 class="cat-title"><?= htmlspecialchars($categorie['nom']) ?></h1>
                <p class="cat-count">
                    <?= count($articles) ?> article<?= count($articles) > 1 ? 's' : '' ?> publie<?= count($articles) > 1 ? 's' : '' ?>
                </p>
            </header>

            <!-- Liste des articles -->
            <?php if (empty($articles)): ?>
                <p style="color:var(--muted); font-family:var(--font-mono); font-size:.8rem; padding: 40px 0;">
                    Aucun article publie dans cette rubrique.
                </p>
            <?php else: ?>
                <section class="articles-list" aria-label="Articles de la rubrique <?= htmlspecialchars($categorie['nom']) ?>">
                    <?php foreach ($articles as $article): ?>
                        <article class="list-article">
                            <div>
                                <h2 class="list-article-title">
                                    <a href="/article/<?= htmlspecialchars($article['slug']) ?>">
                                        <?= htmlspecialchars(strip_tags($article['titre'])) ?>
                                    </a>
                                </h2>
                                <?php if (!empty($article['resume'])): ?>
                                    <p class="list-article-excerpt"><?= htmlspecialchars($article['resume']) ?></p>
                                <?php endif; ?>
                            </div>
                            <time class="list-article-date" datetime="<?= $article['created_at'] ?>">
                                <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                            </time>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

        </div>
    </main>

<?php require_once '../includes/footer.php'; ?>