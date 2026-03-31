<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

$db = getDB();
$errors = [];
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
	header('Location: list.php?error=notfound');
	exit;
}

$stmt = $db->prepare('SELECT id, nom, slug FROM categories WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
	header('Location: list.php?error=notfound');
	exit;
}

$nom = $category['nom'];
$slug = $category['slug'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nom = trim($_POST['nom'] ?? '');
	$slug = trim($_POST['slug'] ?? '');

	if ($nom === '') {
		$errors[] = 'Le nom est obligatoire.';
	}

	if (mb_strlen($nom, 'UTF-8') > 120) {
		$errors[] = 'Le nom doit faire moins de 120 caractères.';
	}

	if ($slug === '') {
		$slug = generateSlug($nom);
	} else {
		$slug = generateSlug($slug);
	}

	if ($slug === '') {
		$errors[] = 'Le slug est invalide.';
	}

	if (empty($errors)) {
		$check = $db->prepare('SELECT COUNT(*) FROM categories WHERE slug = ? AND id != ?');
		$check->execute([$slug, $id]);
		if ((int)$check->fetchColumn() > 0) {
			$errors[] = 'Ce slug existe déjà.';
		}
	}

	if (empty($errors)) {
		$update = $db->prepare('UPDATE categories SET nom = ?, slug = ? WHERE id = ?');
		$update->execute([$nom, $slug, $id]);
		header('Location: list.php?success=updated');
		exit;
	}
}

function generateSlug(string $text): string
{
	if (function_exists('transliterator_transliterate')) {
		$text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);
	} else {
		$ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
		$text = $ascii !== false ? $ascii : $text;
		$text = function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
	}

	$text = preg_replace('/[^a-z0-9]+/', '-', $text);
	return trim((string)$text, '-');
}

require_once '../../includes/header.php';
?>

<h1>Modifier la catégorie</h1>

<?php if (!empty($errors)): ?>
	<div class="errors">
		<?php foreach ($errors as $error): ?>
			<p class="error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></p>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<form method="post" class="article-form">
	<div class="form-group">
		<label for="nom">Nom *</label>
		<input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" maxlength="120" required>
	</div>

	<div class="form-group">
		<label for="slug">Slug *</label>
		<input type="text" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" maxlength="150" required>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn-primary"><i class="fa-regular fa-floppy-disk"></i> Mettre à jour</button>
		<a href="list.php" class="btn-secondary">Annuler</a>
	</div>
</form>

<?php require_once '../../includes/footer.php'; ?>

