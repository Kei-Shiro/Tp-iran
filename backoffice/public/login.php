<?php
require_once '../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (isset($_SESSION['admin_id'])) {
	header('Location: /dashboard');
	exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['username'] ?? '');
	$password = (string)($_POST['password'] ?? '');

	if ($username === '' || $password === '') {
		$error = 'Veuillez remplir les champs obligatoires.';
	} else {
		$stmt = getDB()->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
		$stmt->execute([$username]);
		$admin = $stmt->fetch();

		if ($admin && password_verify($password, $admin['password_hash'])) {
			session_regenerate_id(true);
			$_SESSION['admin_id'] = (int)$admin['id'];
			$_SESSION['admin_username'] = $admin['username'];
			header('Location: /dashboard');
			exit;
		}

		$error = 'Identifiants invalides.';
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Connexion admin</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
	<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<main class="container" style="max-width: 460px; margin: 8vh auto;">
	<section class="article-form">
		<h1>Connexion admin</h1>
		<p>Acces reserve a la redaction.</p>

		<?php if ($error !== ''): ?>
			<p class="error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></p>
		<?php endif; ?>

		<form method="post" action="/login">
			<div class="form-group">
				<label for="username">Identifiant</label>
				<input type="text" id="username" name="username" required>
			</div>
			<div class="form-group">
				<label for="password">Mot de passe</label>
				<input type="password" id="password" name="password" required>
			</div>
			<button type="submit" class="btn-primary"><i class="fa-solid fa-right-to-bracket"></i> Se connecter</button>
		</form>
	</section>
</main>
</body>
</html>


