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

$articleStmt = $db->prepare('SELECT * FROM articles WHERE id = ? LIMIT 1');
$articleStmt->execute([$id]);
$article = $articleStmt->fetch();

if (!$article) {
	header('Location: list.php?error=notfound');
	exit;
}

$categories = $db->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();

$titre = $article['titre'];
$contenu = $article['contenu'];
$resume = $article['resume'];
$categorie_id = (int)$article['categorie_id'];
$alt_image = $article['alt_image'];
$meta_title = $article['meta_title'];
$meta_description = $article['meta_description'];
$statut = $article['statut'];
$image_path = $article['image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$titre = normalizeTitle($_POST['titre'] ?? '');
	$contenu = trim($_POST['contenu'] ?? '');
	$resume = trim($_POST['resume'] ?? '');
	$categorie_id = (int)($_POST['categorie_id'] ?? 0);
	$alt_image = trim($_POST['alt_image'] ?? '');
	$meta_title = trim($_POST['meta_title'] ?? '');
	$meta_description = trim($_POST['meta_description'] ?? '');
	$statut = in_array($_POST['statut'] ?? '', ['publie', 'brouillon'], true) ? $_POST['statut'] : 'brouillon';

	if ($titre === '') {
		$errors[] = 'Le titre est obligatoire.';
	}
	if (mb_strlen($titre, 'UTF-8') > 255) {
		$errors[] = 'Le titre avec mise en forme doit faire moins de 255 caracteres.';
	}
	if (mb_strlen(strip_tags($titre), 'UTF-8') > 255) {
		$errors[] = 'Le texte du titre doit faire moins de 255 caracteres.';
	}
	if ($contenu === '') {
		$errors[] = 'Le contenu est obligatoire.';
	}
	if ($categorie_id <= 0) {
		$errors[] = 'Veuillez choisir une categorie.';
	}
	if (strlen($meta_title) > 70) {
		$errors[] = 'Le meta title doit faire moins de 70 caracteres.';
	}
	if (strlen($meta_description) > 170) {
		$errors[] = 'La meta description doit faire moins de 170 caracteres.';
	}

	$slug = generateSlug(strip_tags($titre));
	if ($slug === '') {
		$errors[] = 'Slug invalide.';
	}

	if (empty($errors)) {
		$slugCheck = $db->prepare('SELECT COUNT(*) FROM articles WHERE slug = ? AND id != ?');
		$slugCheck->execute([$slug, $id]);
		if ((int)$slugCheck->fetchColumn() > 0) {
			$slug .= '-' . $id;
		}
	}

	if (!empty($_FILES['image']['name'])) {
		$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
		$fileType = mime_content_type($_FILES['image']['tmp_name']);

		if (!in_array($fileType, $allowedTypes, true)) {
			$errors[] = 'Format d image non autorise (JPG, PNG, WebP uniquement).';
		} elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
			$errors[] = 'L image ne doit pas depasser 2 Mo.';
		} else {
			$ext = strtolower((string)pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
			$filename = $slug . '-' . time() . '.' . $ext;
			$uploadDir = '/var/www/html/public/assets/images/articles/';

			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
				$image_path = 'assets/images/articles/' . $filename;
			} else {
				$errors[] = 'Echec lors du televersement de l image.';
			}
		}
	}

	if (empty($errors)) {
		$stmt = $db->prepare(
			'UPDATE articles
			 SET categorie_id = ?, titre = ?, slug = ?, contenu = ?, resume = ?, image = ?, alt_image = ?,
				 meta_title = ?, meta_description = ?, statut = ?
			 WHERE id = ?'
		);

		$stmt->execute([
			$categorie_id,
			$titre,
			$slug,
			$contenu,
			$resume,
			$image_path,
			$alt_image,
			$meta_title,
			$meta_description,
			$statut,
			$id,
		]);

		header('Location: list.php?success=updated');
		exit;
	}
}

function normalizeTitle(string $text): string
{
	$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	$text = strip_tags($text, '<strong><em><u><mark><span><sup><sub><br>');
	$text = preg_replace('/<([a-z0-9]+)(?:\s+[^>]*)?>/i', '<$1>', $text);
	$text = preg_replace('/\s+/u', ' ', $text);
	return trim($text);
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
	$text = trim((string)$text, '-');

	return $text !== '' ? $text : 'article';
}

require_once '../../includes/header.php';
?>

<h1>Modifier l article</h1>

<?php if (!empty($errors)): ?>
	<div class="errors">
		<?php foreach ($errors as $error): ?>
			<p class="error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></p>
		<?php endforeach; ?>
	</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="article-form">
	<div class="form-group">
		<label for="titre">Titre *</label>
		<textarea id="titre" name="titre" rows="2" required maxlength="255" data-editor="tinymce-title"><?= htmlspecialchars($titre) ?></textarea>
		<small>Personnalisation autorisee (gras, italique, couleur), avec slug base sur le texte.</small>
	</div>

	<div class="form-group">
		<label for="categorie_id">Categorie *</label>
		<select id="categorie_id" name="categorie_id" required>
			<option value="">-- Choisir une categorie --</option>
			<?php foreach ($categories as $cat): ?>
				<option value="<?= (int)$cat['id'] ?>" <?= $categorie_id === (int)$cat['id'] ? 'selected' : '' ?>>
					<?= htmlspecialchars($cat['nom']) ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="form-group">
		<label for="resume">Resume</label>
		<textarea id="resume" name="resume" rows="3"><?= htmlspecialchars($resume) ?></textarea>
	</div>

	<div class="form-group">
		<label for="contenu">Contenu *</label>
		<textarea id="contenu" name="contenu" rows="20" required data-editor="tinymce-content"><?= htmlspecialchars($contenu) ?></textarea>
	</div>

	<fieldset>
		<legend><i class="fa-regular fa-image"></i> Image</legend>
		<?php if (!empty($image_path)): ?>
			<p><strong>Image actuelle:</strong> <a href="/<?= htmlspecialchars($image_path) ?>" target="_blank" rel="noopener">ouvrir</a></p>
		<?php endif; ?>
		<div class="form-group">
			<label for="image">Nouvelle image (JPG, PNG, WebP, max 2 Mo)</label>
			<input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
		</div>
		<div class="form-group">
			<label for="alt_image">Texte alternatif</label>
			<input type="text" id="alt_image" name="alt_image" maxlength="255" value="<?= htmlspecialchars($alt_image) ?>">
		</div>
	</fieldset>

	<fieldset>
		<legend><i class="fa-solid fa-magnifying-glass-chart"></i> SEO</legend>
		<div class="form-group">
			<label for="meta_title">Meta Title <span class="counter" id="counter-title">0/70</span></label>
			<input type="text" id="meta_title" name="meta_title" maxlength="70" value="<?= htmlspecialchars($meta_title) ?>" data-counter-target="counter-title" data-counter-max="70">
		</div>
		<div class="form-group">
			<label for="meta_description">Meta Description <span class="counter" id="counter-desc">0/170</span></label>
			<textarea id="meta_description" name="meta_description" rows="3" maxlength="170" data-counter-target="counter-desc" data-counter-max="170"><?= htmlspecialchars($meta_description) ?></textarea>
		</div>
	</fieldset>

	<div class="form-group">
		<label for="statut">Statut</label>
		<select id="statut" name="statut">
			<option value="brouillon" <?= $statut === 'brouillon' ? 'selected' : '' ?>>Brouillon</option>
			<option value="publie" <?= $statut === 'publie' ? 'selected' : '' ?>>Publie</option>
		</select>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn-primary"><i class="fa-regular fa-floppy-disk"></i> Mettre a jour</button>
		<a href="list.php" class="btn-secondary">Annuler</a>
	</div>
</form>

<script>
window.APP_CONFIG = window.APP_CONFIG || {};
window.APP_CONFIG.tinyMceApiKey = '<?= addslashes(getenv('TINYMCE_API_KEY') ?: 'no-api-key') ?>';
</script>

<?php require_once '../../includes/footer.php'; ?>

