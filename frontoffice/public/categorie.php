<?php
require_once '../includes/db.php';

$db = getDB();
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

$pageTitle = $categorie['nom'] . ' - Guerre en Iran';
$metaDescription = 'Consultez les articles de la categorie ' . $categorie['nom'] . '.';

require_once '../includes/header.php';
?>

<main class="container article-page">
	<section class="card-bg shadow-sm rounded-lg p-8">
		<h1><?= htmlspecialchars($categorie['nom']) ?></h1>
		<p><?= count($articles) ?> article(s) publie(s) dans cette rubrique.</p>
	</section>

	<section class="articles-lies">
		<div class="grid-small">
			<?php foreach ($articles as $article): ?>
			<article class="article-card-small">
				<h3><a href="/article/<?= htmlspecialchars($article['slug']) ?>"><?= htmlspecialchars(strip_tags($article['titre'])) ?></a></h3>
				<p class="breadcrumb"><?= date('d/m/Y', strtotime($article['created_at'])) ?></p>
				<?php if (!empty($article['resume'])): ?>
				<p><?= htmlspecialchars($article['resume']) ?></p>
				<?php endif; ?>
			</article>
			<?php endforeach; ?>
		</div>
	</section>
</main>

<?php require_once '../includes/footer.php'; ?>

