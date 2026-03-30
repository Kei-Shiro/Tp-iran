<?php
require_once '../includes/db.php';

$db = getDB();
$pageTitle = 'Guerre en Iran - Actualites et analyses';
$metaDescription = 'Dernieres actualites, analyses et decryptages sur la guerre en Iran.';

$articles = $db->query(
	"SELECT a.titre, a.slug, a.resume, a.image, a.alt_image, a.created_at, c.nom AS categorie, c.slug AS cat_slug
	 FROM articles a
	 JOIN categories c ON c.id = a.categorie_id
	 WHERE a.statut = 'publie'
	 ORDER BY a.created_at DESC"
)->fetchAll();

require_once '../includes/header.php';
?>

<main class="container article-page">
	<section class="card-bg shadow-sm rounded-lg p-8">
		<h1>Actualites</h1>
		<p>Suivi editorialisé des evenements, avec un angle factuel et contextualise.</p>
	</section>

	<section class="articles-lies">
		<h2>Derniers articles</h2>
		<div class="grid-small">
			<?php foreach ($articles as $article): ?>
			<article class="article-card-small">
				<?php if (!empty($article['image'])): ?>
					<img src="/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['alt_image'] ?: strip_tags($article['titre'])) ?>" loading="lazy">
				<?php endif; ?>
				<h3><a href="/article/<?= htmlspecialchars($article['slug']) ?>"><?= htmlspecialchars(strip_tags($article['titre'])) ?></a></h3>
				<p class="breadcrumb">
					<a href="/categorie/<?= htmlspecialchars($article['cat_slug']) ?>"><?= htmlspecialchars($article['categorie']) ?></a>
					- <?= date('d/m/Y', strtotime($article['created_at'])) ?>
				</p>
				<?php if (!empty($article['resume'])): ?>
				<p><?= htmlspecialchars($article['resume']) ?></p>
				<?php endif; ?>
			</article>
			<?php endforeach; ?>
		</div>
	</section>
</main>

<?php require_once '../includes/footer.php'; ?>

