<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

$db = getDB();

if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$q = trim((string)($_GET['q'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];
if ($q !== '') {
	$where = 'WHERE c.nom LIKE :q OR c.slug LIKE :q';
	$params[':q'] = '%' . $q . '%';
}

$countStmt = $db->prepare("SELECT COUNT(*) FROM categories c $where");
foreach ($params as $key => $value) {
	$countStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$countStmt->execute();
$totalItems = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalItems / $perPage));
if ($page > $totalPages) {
	$page = $totalPages;
	$offset = ($page - 1) * $perPage;
}

$stmt = $db->prepare(
	"SELECT c.id, c.nom, c.slug, c.created_at, COUNT(a.id) AS total_articles
	 FROM categories c
	 LEFT JOIN articles a ON a.categorie_id = c.id
	 $where
	 GROUP BY c.id, c.nom, c.slug, c.created_at
	 ORDER BY c.nom ASC
	 LIMIT :limit OFFSET :offset"
);
foreach ($params as $key => $value) {
	$stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll();

function buildQuery(array $replace = []): string
{
	$query = $_GET;
	foreach ($replace as $k => $v) {
		if ($v === null) {
			unset($query[$k]);
		} else {
			$query[$k] = $v;
		}
	}
	return http_build_query($query);
}

require_once '../../includes/header.php';
?>

<div class="page-header">
	<h1>Gestion des categories</h1>
	<a href="create.php" class="btn-primary"><i class="fa-solid fa-plus"></i> Nouvelle categorie</a>
</div>

<?php if (isset($_GET['success'])): ?>
	<p class="success">Categorie <?= $_GET['success'] === 'deleted' ? 'supprimee' : 'enregistree' ?> avec succes.</p>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'linked'): ?>
	<div class="errors"><p class="error"><i class="fa-solid fa-circle-exclamation"></i> Impossible de supprimer cette categorie: des articles y sont lies.</p></div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'token'): ?>
	<div class="errors"><p class="error"><i class="fa-solid fa-circle-exclamation"></i> Requete invalide (token).</p></div>
<?php endif; ?>

<div class="filters-bar">
	<form method="get" class="search-form">
		<input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Rechercher par nom ou slug">
		<button type="submit" class="btn-secondary"><i class="fa-solid fa-magnifying-glass"></i> Rechercher</button>
		<?php if ($q !== ''): ?>
			<a href="list.php" class="btn-secondary">Reinitialiser</a>
		<?php endif; ?>
	</form>
	<p class="results-count"><?= $totalItems ?> resultat(s)</p>
</div>

<table class="data-table">
	<thead>
		<tr>
			<th>Nom</th>
			<th>Slug</th>
			<th>Articles</th>
			<th>Date</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	<?php if (empty($categories)): ?>
		<tr><td colspan="5" class="table-empty">Aucune categorie trouvee.</td></tr>
	<?php endif; ?>
	<?php foreach ($categories as $cat): ?>
		<tr>
			<td><?= htmlspecialchars($cat['nom']) ?></td>
			<td><?= htmlspecialchars($cat['slug']) ?></td>
			<td><?= (int)$cat['total_articles'] ?></td>
			<td><?= date('d/m/Y', strtotime($cat['created_at'])) ?></td>
			<td class="actions">
				<a href="edit.php?id=<?= (int)$cat['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Modifier</a>
				<form method="post" action="delete.php" class="inline-form" onsubmit="return confirm('Supprimer cette categorie ?');">
					<input type="hidden" name="id" value="<?= (int)$cat['id'] ?>">
					<input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
					<button type="submit" class="btn-delete"><i class="fa-solid fa-trash-can"></i> Supprimer</button>
				</form>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php if ($totalPages > 1): ?>
	<nav class="pagination" aria-label="Pagination categories">
		<a class="btn-secondary <?= $page <= 1 ? 'disabled' : '' ?>" href="?<?= htmlspecialchars(buildQuery(['page' => max(1, $page - 1)])) ?>">Precedent</a>
		<span class="page-current">Page <?= $page ?> / <?= $totalPages ?></span>
		<a class="btn-secondary <?= $page >= $totalPages ? 'disabled' : '' ?>" href="?<?= htmlspecialchars(buildQuery(['page' => min($totalPages, $page + 1)])) ?>">Suivant</a>
	</nav>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>

