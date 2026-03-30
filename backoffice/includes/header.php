<?php
$uri = $_SERVER['REQUEST_URI'] ?? '/dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Guerre en Iran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/dashboard" class="brand">
            <i class="fa-solid fa-tower-broadcast"></i>
            <span>Backoffice Media</span>
        </a>

        <nav aria-label="Navigation d'administration">
            <a href="/dashboard" class="nav-item <?= strpos($uri, 'dashboard') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i> Tableau de bord
            </a>
            <a href="/articles" class="nav-item <?= strpos($uri, 'articles') !== false ? 'active' : '' ?>">
                <i class="fa-regular fa-newspaper"></i> Articles
            </a>
            <a href="/categories" class="nav-item <?= strpos($uri, 'categories') !== false ? 'active' : '' ?>">
                <i class="fa-solid fa-tags"></i> Categories
            </a>
        </nav>

        <a href="/logout" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Deconnexion</a>
    </aside>

    <main class="admin-main">
