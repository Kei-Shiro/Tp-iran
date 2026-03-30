<?php
http_response_code(404);
$pageTitle = '404 - Page introuvable';
$metaDescription = 'La page demandee est introuvable.';
require_once '../includes/header.php';
?>

<main class="container article-page">
	<section class="card-bg shadow-sm rounded-lg p-8">
		<h1>Erreur 404</h1>
		<p>La page demandee n existe pas ou a ete deplacee.</p>
		<p><a href="/">Retourner a l accueil</a></p>
	</section>
</main>

<?php require_once '../includes/footer.php'; ?>

