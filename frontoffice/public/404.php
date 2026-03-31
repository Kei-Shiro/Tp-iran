<?php
http_response_code(404);
$pageTitle = '404 - Page introuvable';
$metaDescription = 'La page demandee est introuvable.';
require_once '../includes/header.php';
?>

<main class="page-main">
    <div class="container pb-8 pt-4">
        <div style="text-align: center; padding: 6rem 2rem; background: var(--surface); border-radius: 12px; box-shadow: var(--shadow); margin-top: 3rem; margin-bottom: 3rem; border: 1px solid var(--border);">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 5rem; color: var(--accent); margin-bottom: 2rem;"></i>
            <h1 style="font-family: var(--font-heading); color: var(--primary); font-size: 3rem; margin-bottom: 1rem;">Erreur 404</h1>
            <div style="font-size: 1.2rem; color: var(--text-muted); margin-bottom: 2rem;">
                <p>La page demandée n'existe pas ou a été déplacée.</p>
            </div>
            <a href="/" style="display: inline-block; padding: 12px 30px; background: var(--accent); color: white; border-radius: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; transition: background 0.2s;"><i class="fa-solid fa-arrow-left"></i> Retourner à l'accueil</a>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
