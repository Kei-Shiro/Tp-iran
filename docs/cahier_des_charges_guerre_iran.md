# 📋 CAHIER DES CHARGES & TO-DO LIST
## Mini-Projet Web Design — Site d'informations sur la Guerre en Iran
### Délai : Mardi 31 mars 2026 à 14h00 | Travail Binôme

---

> **⚠️ RÈGLE D'OR DU PROJET**
> - ❌ Aucun framework (pas de Laravel, Symfony, React, Vue, Bootstrap...)
> - ✅ PHP pur + MySQL + HTML/CSS/JS vanilla uniquement
> - ✅ URL Rewriting obligatoire (via `.htaccess`)
> - ✅ Le site doit tourner dans des **conteneurs Docker**

---

## 🗂️ TABLE DES MATIÈRES

1. [Présentation du projet](#1-présentation-du-projet)
2. [Architecture & Structure des fichiers](#2-architecture--structure-des-fichiers)
3. [Phase 1 — Mise en place de l'environnement Docker](#phase-1--mise-en-place-de-lenvironnement-docker)
4. [Phase 2 — Base de données](#phase-2--base-de-données)
5. [Phase 3 — Back-Office (BO)](#phase-3--back-office-bo)
6. [Phase 4 — Front-Office (FO)](#phase-4--front-office-fo)
7. [Phase 5 — SEO & Optimisation](#phase-5--seo--optimisation)
8. [Phase 6 — Tests Lighthouse](#phase-6--tests-lighthouse)
9. [Phase 7 — Livraison](#phase-7--livraison)
10. [To-Do List Complète](#to-do-list-complète)

---

## 1. PRÉSENTATION DU PROJET

### C'est quoi exactement ?
On doit créer **deux sites web** qui communiquent avec la **même base de données** :

| Site | Rôle | Qui l'utilise ? |
|------|------|-----------------|
| **Front-Office (FO)** | Affiche les articles/informations sur la guerre en Iran | Le public (tout le monde) |
| **Back-Office (BO)** | Permet d'ajouter, modifier, supprimer des articles | L'administrateur (vous) |

### Analogie simple
> Imagine un journal papier. Le **FO**, c'est le journal que tout le monde lit. Le **BO**, c'est la salle de rédaction où les journalistes écrivent et gèrent les articles.

---

## 2. ARCHITECTURE & STRUCTURE DES FICHIERS

### Voici comment organiser vos dossiers (à créer dès le début) :

```
projet/
│
├── docker-compose.yml          ← Le chef d'orchestre de Docker
├── .env                        ← Variables d'environnement (mots de passe BDD, etc.)
│
├── frontoffice/                ← SITE PUBLIC
│   ├── Dockerfile
│   ├── public/
│   │   ├── .htaccess           ← URL Rewriting
│   │   ├── index.php           ← Page d'accueil
│   │   ├── article.php         ← Page d'un article
│   │   ├── categorie.php       ← Page d'une catégorie
│   │   └── assets/
│   │       ├── css/
│   │       │   └── style.css
│   │       ├── js/
│   │       │   └── main.js
│   │       └── images/
│   ├── includes/
│   │   ├── db.php              ← Connexion à la base de données
│   │   ├── header.php          ← En-tête commun à toutes les pages
│   │   └── footer.php          ← Pied de page commun
│   └── templates/
│       ├── home.tpl.php
│       ├── article.tpl.php
│       └── categorie.tpl.php
│
├── backoffice/                 ← SITE ADMIN
│   ├── Dockerfile
│   ├── public/
│   │   ├── .htaccess
│   │   ├── index.php           ← Redirige vers login ou dashboard
│   │   ├── login.php           ← Page de connexion
│   │   ├── dashboard.php       ← Tableau de bord
│   │   ├── articles/
│   │   │   ├── list.php        ← Liste des articles
│   │   │   ├── create.php      ← Créer un article
│   │   │   ├── edit.php        ← Modifier un article
│   │   │   └── delete.php      ← Supprimer un article
│   │   ├── categories/
│   │   │   ├── list.php
│   │   │   ├── create.php
│   │   │   └── delete.php
│   │   └── assets/
│   │       ├── css/
│   │       │   └── admin.css
│   │       └── js/
│   │           └── admin.js
│   └── includes/
│       ├── db.php
│       ├── auth.php            ← Vérification de session (est-on connecté ?)
│       ├── header.php
│       └── footer.php
│
└── database/
    └── init.sql                ← Script SQL de création de la BDD
```

---

## PHASE 1 — MISE EN PLACE DE L'ENVIRONNEMENT DOCKER

> **C'est quoi Docker ?** Imagine que Docker crée des "boîtes" isolées sur ton ordinateur. Dans chaque boîte, il y a un programme qui tourne tout seul, sans interférer avec le reste.

### ✅ TO-DO Phase 1

#### Étape 1.1 — Installer Docker
- [ ] Télécharger Docker Desktop sur https://www.docker.com
- [ ] L'installer et vérifier qu'il tourne (icône de baleine dans la barre des tâches)
- [ ] Ouvrir un terminal et taper : `docker --version` → doit afficher une version

#### Étape 1.2 — Créer le fichier `docker-compose.yml`

Ce fichier décrit les 3 "boîtes" (conteneurs) dont on a besoin :
1. **mysql** → la base de données
2. **frontoffice** → le site public (PHP + Apache)
3. **backoffice** → le site admin (PHP + Apache)

```yaml
# docker-compose.yml
version: '3.8'

services:

  # ─── BASE DE DONNÉES ───────────────────────────────────────────
  mysql:
    image: mysql:8.0
    container_name: guerre_iran_db
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: guerre_iran
      MYSQL_USER: admin
      MYSQL_PASSWORD: adminpassword
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
      - ./database/init.sql:/docker-entrypoint-initdb.d/init.sql
    networks:
      - guerre_iran_network

  # ─── FRONT-OFFICE ──────────────────────────────────────────────
  frontoffice:
    build: ./frontoffice
    container_name: guerre_iran_fo
    restart: always
    ports:
      - "8080:80"          # Accessible sur http://localhost:8080
    volumes:
      - ./frontoffice:/var/www/html
    depends_on:
      - mysql
    networks:
      - guerre_iran_network

  # ─── BACK-OFFICE ───────────────────────────────────────────────
  backoffice:
    build: ./backoffice
    container_name: guerre_iran_bo
    restart: always
    ports:
      - "8081:80"          # Accessible sur http://localhost:8081
    volumes:
      - ./backoffice:/var/www/html
    depends_on:
      - mysql
    networks:
      - guerre_iran_network

networks:
  guerre_iran_network:
    driver: bridge

volumes:
  mysql_data:
```

#### Étape 1.3 — Créer les Dockerfiles

**`frontoffice/Dockerfile`** et **`backoffice/Dockerfile`** (identiques) :

```dockerfile
# On part d'une image PHP avec Apache déjà installé
FROM php:8.2-apache

# On installe l'extension PHP pour MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# On active le module Apache pour le URL rewriting
RUN a2enmod rewrite

# On configure Apache pour autoriser le .htaccess
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/override.conf \
    && a2enconf override

# On expose le port 80
EXPOSE 80
```

#### Étape 1.4 — Lancer Docker
```bash
# Dans le terminal, dans le dossier racine du projet :
docker-compose up -d

# Vérifier que tout tourne :
docker-compose ps

# Voir les logs en cas de problème :
docker-compose logs
```

- [ ] `docker-compose up -d` fonctionne sans erreur
- [ ] http://localhost:8080 affiche quelque chose
- [ ] http://localhost:8081 affiche quelque chose

---

## PHASE 2 — BASE DE DONNÉES

> **C'est quoi une base de données ?** C'est comme un classeur Excel géant mais beaucoup plus puissant, organisé en tableaux (tables).

### 📐 Modélisation de la base de données

On a besoin de ces **tables** :

```
┌─────────────────┐       ┌─────────────────────┐       ┌──────────────────┐
│    categories   │       │      articles        │       │     admins       │
├─────────────────┤       ├─────────────────────┤       ├──────────────────┤
│ id (PK)         │◄──┐   │ id (PK)             │       │ id (PK)          │
│ nom             │   └───│ categorie_id (FK)   │       │ username         │
│ slug            │       │ titre               │       │ password (hash)  │
│ description     │       │ slug                │       │ email            │
│ created_at      │       │ contenu             │       │ created_at       │
└─────────────────┘       │ resume              │       └──────────────────┘
                          │ image               │
                          │ alt_image           │
                          │ meta_title          │
                          │ meta_description    │
                          │ statut              │
                          │ created_at          │
                          │ updated_at          │
                          └─────────────────────┘
```

### ✅ TO-DO Phase 2

#### Étape 2.1 — Créer le fichier SQL

Créer `database/init.sql` :

```sql
-- ============================================================
-- Création de la base de données
-- ============================================================

CREATE DATABASE IF NOT EXISTS guerre_iran
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE guerre_iran;

-- ============================================================
-- Table des catégories
-- (ex: Politique, Militaire, Humanitaire, Diplomatie...)
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100)  NOT NULL,
    slug        VARCHAR(110)  NOT NULL UNIQUE,  -- URL propre ex: "politique"
    description TEXT,
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table des articles
-- ============================================================
CREATE TABLE IF NOT EXISTS articles (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    categorie_id     INT           NOT NULL,
    titre            VARCHAR(255)  NOT NULL,
    slug             VARCHAR(265)  NOT NULL UNIQUE,  -- URL propre ex: "offensive-du-12-mars"
    contenu          LONGTEXT      NOT NULL,
    resume           TEXT,                            -- Court résumé pour la liste
    image            VARCHAR(300),                    -- Chemin vers l'image
    alt_image        VARCHAR(255),                    -- Texte alternatif (obligatoire SEO)
    meta_title       VARCHAR(70),                     -- Titre SEO (max 60-70 chars)
    meta_description VARCHAR(170),                    -- Description SEO (max 155-170 chars)
    statut           ENUM('publie','brouillon') DEFAULT 'brouillon',
    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Table des administrateurs
-- ============================================================
CREATE TABLE IF NOT EXISTS admins (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,              -- TOUJOURS stocker le hash, jamais le mot de passe en clair !
    email      VARCHAR(150) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Données par défaut
-- ============================================================

-- Admin par défaut (login: admin / password: Admin1234)
-- Le hash ci-dessous correspond à password_hash('Admin1234', PASSWORD_BCRYPT)
INSERT INTO admins (username, password, email) VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin@guerre-iran.local'
);

-- Catégories de base
INSERT INTO categories (nom, slug, description) VALUES
('Politique',     'politique',     'Actualités politiques liées au conflit en Iran'),
('Militaire',     'militaire',     'Opérations et stratégies militaires'),
('Humanitaire',   'humanitaire',   'Impact humanitaire sur les populations civiles'),
('Diplomatie',    'diplomatie',    'Négociations et relations internationales'),
('Économie',      'economie',      'Conséquences économiques du conflit');

-- Quelques articles d'exemple
INSERT INTO articles (categorie_id, titre, slug, contenu, resume, alt_image, meta_title, meta_description, statut) VALUES
(1, 'Origine du conflit iranien', 'origine-conflit-iranien',
 '<p>Le conflit trouve ses racines dans les tensions géopolitiques...</p><p>Plusieurs facteurs ont conduit à cette situation...</p>',
 'Retour sur les origines du conflit qui secoue l Iran depuis plusieurs mois.',
 'Carte de l Iran montrant les zones de tension',
 'Origine du conflit iranien | Guerre Iran',
 'Analyse des origines du conflit en Iran, des tensions géopolitiques et des facteurs déclencheurs.',
 'publie'),
(2, 'Bilan des opérations militaires', 'bilan-operations-militaires',
 '<p>Les opérations militaires ont connu plusieurs phases...</p>',
 'Bilan chiffré et cartographique des opérations militaires menées depuis le début du conflit.',
 'Soldats en position dans une zone de conflit',
 'Bilan militaire du conflit iranien | Guerre Iran',
 'Analyse du bilan des opérations militaires en Iran depuis le début du conflit en 2024.',
 'publie');
```

#### Étape 2.2 — Créer le fichier de connexion PHP

**`frontoffice/includes/db.php`** et **`backoffice/includes/db.php`** :

```php
<?php
// ============================================================
// CONNEXION À LA BASE DE DONNÉES
// ============================================================
// On utilise PDO (PHP Data Objects), qui est plus sécurisé
// et plus moderne que l'ancien mysql_connect()

define('DB_HOST', 'mysql');           // Nom du service Docker
define('DB_NAME', 'guerre_iran');
define('DB_USER', 'admin');
define('DB_PASS', 'adminpassword');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;  // "static" : on ne crée la connexion qu'une seule fois

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // Affiche les erreurs SQL
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Retourne des tableaux associatifs
            PDO::ATTR_EMULATE_PREPARES   => false,                     // Sécurité : requêtes préparées réelles
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // En production, NE PAS afficher le message d'erreur à l'utilisateur !
            die('Erreur de connexion à la base de données.');
        }
    }

    return $pdo;
}
```

- [ ] Fichier `database/init.sql` créé
- [ ] Table `categories` créée avec les bons champs
- [ ] Table `articles` créée avec les bons champs
- [ ] Table `admins` créée avec mot de passe hashé
- [ ] Admin par défaut inséré (admin / Admin1234)
- [ ] Données de test insérées
- [ ] `db.php` créé et fonctionne (pas d'erreur de connexion)

---

## PHASE 3 — BACK-OFFICE (BO)

> Le Back-Office, c'est la "cuisine" du site. L'administrateur y gère tout le contenu. Les visiteurs du site public ne peuvent pas y accéder.

### ✅ TO-DO Phase 3

#### Étape 3.1 — Système d'authentification

**`backoffice/public/login.php`** :

```php
<?php
session_start();
require_once '../includes/db.php';

$error = '';

// Si déjà connecté, on redirige vers le dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $db = getDB();
        $stmt = $db->prepare('SELECT id, username, password FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        // password_verify() compare le mot de passe saisi avec le hash stocké
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Identifiants incorrects.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Back-Office</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-box">
        <h1>Administration</h1>
        <h2>Connexion</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Identifiant</label>
                <input type="text" id="username" name="username" required
                       value="<?= htmlspecialchars($username ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Se connecter</button>
        </form>
    </div>
</body>
</html>
```

**`backoffice/includes/auth.php`** — À inclure sur CHAQUE page protégée :

```php
<?php
// Ce fichier vérifie que l'admin est connecté.
// Si ce n'est pas le cas, il redirige vers la page de login.
// INCLURE CE FICHIER EN TOUT PREMIER SUR CHAQUE PAGE DU BO !

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: /login.php');
    exit;
}
```

#### Étape 3.2 — Dashboard (tableau de bord)

**`backoffice/public/dashboard.php`** :

```php
<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$db = getDB();

// Compteurs pour le tableau de bord
$totalArticles   = $db->query('SELECT COUNT(*) FROM articles')->fetchColumn();
$totalPublies    = $db->query("SELECT COUNT(*) FROM articles WHERE statut='publie'")->fetchColumn();
$totalCategories = $db->query('SELECT COUNT(*) FROM categories')->fetchColumn();

// 5 derniers articles
$derniers = $db->query('SELECT a.titre, a.statut, a.created_at, c.nom AS categorie
                        FROM articles a
                        JOIN categories c ON a.categorie_id = c.id
                        ORDER BY a.created_at DESC LIMIT 5')->fetchAll();

require_once '../includes/header.php';
?>

<h1>Tableau de bord</h1>

<div class="stats">
    <div class="stat-card">
        <h2><?= $totalArticles ?></h2>
        <p>Articles au total</p>
    </div>
    <div class="stat-card">
        <h2><?= $totalPublies ?></h2>
        <p>Articles publiés</p>
    </div>
    <div class="stat-card">
        <h2><?= $totalCategories ?></h2>
        <p>Catégories</p>
    </div>
</div>

<h2>Derniers articles</h2>
<table>
    <thead>
        <tr><th>Titre</th><th>Catégorie</th><th>Statut</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($derniers as $art): ?>
        <tr>
            <td><?= htmlspecialchars($art['titre']) ?></td>
            <td><?= htmlspecialchars($art['categorie']) ?></td>
            <td><span class="badge <?= $art['statut'] ?>"><?= $art['statut'] ?></span></td>
            <td><?= date('d/m/Y', strtotime($art['created_at'])) ?></td>
            <td>
                <a href="articles/edit.php?id=<?= $art['id'] ?>">Modifier</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
```

#### Étape 3.3 — CRUD Articles

> **CRUD** = Create (Créer), Read (Lire), Update (Modifier), Delete (Supprimer). C'est le minimum pour gérer du contenu.

**`backoffice/public/articles/list.php`** :

```php
<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

$db = getDB();

// Suppression si demandée
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Vérification du token CSRF (sécurité)
    if (isset($_GET['token']) && $_GET['token'] === $_SESSION['csrf_token']) {
        $db->prepare('DELETE FROM articles WHERE id = ?')->execute([$id]);
        header('Location: list.php?success=deleted');
        exit;
    }
}

// Récupération de tous les articles avec leur catégorie
$articles = $db->query(
    'SELECT a.id, a.titre, a.statut, a.created_at, c.nom AS categorie
     FROM articles a
     JOIN categories c ON a.categorie_id = c.id
     ORDER BY a.created_at DESC'
)->fetchAll();

// Génération du token CSRF (sécurité contre les attaques)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../../includes/header.php';
?>

<div class="page-header">
    <h1>Gestion des articles</h1>
    <a href="create.php" class="btn-primary">+ Nouvel article</a>
</div>

<?php if (isset($_GET['success'])): ?>
    <p class="success">Article <?= $_GET['success'] === 'deleted' ? 'supprimé' : 'enregistré' ?> avec succès !</p>
<?php endif; ?>

<table class="data-table">
    <thead>
        <tr>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Statut</th>
            <th>Date de création</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $art): ?>
        <tr>
            <td><?= htmlspecialchars($art['titre']) ?></td>
            <td><?= htmlspecialchars($art['categorie']) ?></td>
            <td>
                <span class="badge badge-<?= $art['statut'] ?>">
                    <?= $art['statut'] === 'publie' ? '✅ Publié' : '📝 Brouillon' ?>
                </span>
            </td>
            <td><?= date('d/m/Y à H:i', strtotime($art['created_at'])) ?></td>
            <td class="actions">
                <a href="edit.php?id=<?= $art['id'] ?>" class="btn-edit">✏️ Modifier</a>
                <a href="list.php?delete=<?= $art['id'] ?>&token=<?= $_SESSION['csrf_token'] ?>"
                   class="btn-delete"
                   onclick="return confirm('Supprimer cet article définitivement ?')">
                   🗑️ Supprimer
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../../includes/footer.php'; ?>
```

**`backoffice/public/articles/create.php`** :

```php
<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

$db = getDB();
$errors = [];
$success = false;

// Récupération des catégories pour le menu déroulant
$categories = $db->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ── Récupération et nettoyage des données ──────────────────
    $titre            = trim($_POST['titre'] ?? '');
    $contenu          = trim($_POST['contenu'] ?? '');
    $resume           = trim($_POST['resume'] ?? '');
    $categorie_id     = (int)($_POST['categorie_id'] ?? 0);
    $alt_image        = trim($_POST['alt_image'] ?? '');
    $meta_title       = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $statut           = in_array($_POST['statut'] ?? '', ['publie', 'brouillon']) ? $_POST['statut'] : 'brouillon';

    // ── Validation ─────────────────────────────────────────────
    if (empty($titre))        $errors[] = 'Le titre est obligatoire.';
    if (empty($contenu))      $errors[] = 'Le contenu est obligatoire.';
    if ($categorie_id <= 0)   $errors[] = 'Veuillez choisir une catégorie.';
    if (strlen($meta_title) > 70) $errors[] = 'Le meta title doit faire moins de 70 caractères.';
    if (strlen($meta_description) > 170) $errors[] = 'La meta description doit faire moins de 170 caractères.';

    // ── Génération du slug (URL propre) ────────────────────────
    // Ex: "Offensive du 12 Mars" → "offensive-du-12-mars"
    $slug = generateSlug($titre);

    // ── Gestion de l'image ─────────────────────────────────────
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Format d\'image non autorisé (JPG, PNG, WebP uniquement).';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'L\'image ne doit pas dépasser 2 Mo.';
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = $slug . '-' . time() . '.' . strtolower($ext);
            $upload_dir = '/var/www/html/public/assets/images/articles/';

            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename);
            $image_path = 'assets/images/articles/' . $filename;
        }
    }

    // ── Enregistrement si pas d'erreur ─────────────────────────
    if (empty($errors)) {
        $stmt = $db->prepare(
            'INSERT INTO articles
             (categorie_id, titre, slug, contenu, resume, image, alt_image, meta_title, meta_description, statut)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $categorie_id, $titre, $slug, $contenu, $resume,
            $image_path, $alt_image, $meta_title, $meta_description, $statut
        ]);

        header('Location: list.php?success=created');
        exit;
    }
}

// Fonction pour générer un slug
function generateSlug(string $text): string {
    // Remplacement des caractères accentués
    $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);
    // Remplacement des espaces et caractères spéciaux par des tirets
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

require_once '../../includes/header.php';
?>

<h1>Nouvel article</h1>

<?php if (!empty($errors)): ?>
    <div class="errors">
        <?php foreach ($errors as $e): ?>
            <p class="error">❌ <?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="article-form">

    <div class="form-group">
        <label for="titre">Titre de l'article *</label>
        <input type="text" id="titre" name="titre" required maxlength="255"
               value="<?= htmlspecialchars($titre ?? '') ?>">
        <small>Ce titre sera affiché comme H1 sur la page de l'article</small>
    </div>

    <div class="form-group">
        <label for="categorie_id">Catégorie *</label>
        <select id="categorie_id" name="categorie_id" required>
            <option value="">-- Choisir une catégorie --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                    <?= ($categorie_id ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="resume">Résumé (affiché sur la liste des articles)</label>
        <textarea id="resume" name="resume" rows="3"><?= htmlspecialchars($resume ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="contenu">Contenu de l'article *</label>
        <textarea id="contenu" name="contenu" rows="20" required><?= htmlspecialchars($contenu ?? '') ?></textarea>
        <small>Vous pouvez utiliser du HTML (h2, h3, p, ul, li, strong...)</small>
    </div>

    <fieldset>
        <legend>📷 Image</legend>
        <div class="form-group">
            <label for="image">Fichier image (JPG, PNG, WebP — max 2 Mo)</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group">
            <label for="alt_image">Texte alternatif de l'image (alt) *</label>
            <input type="text" id="alt_image" name="alt_image" maxlength="255"
                   value="<?= htmlspecialchars($alt_image ?? '') ?>">
            <small>Décrivez l'image en une phrase. Obligatoire pour le SEO et l'accessibilité.</small>
        </div>
    </fieldset>

    <fieldset>
        <legend>🔍 SEO</legend>
        <div class="form-group">
            <label for="meta_title">Meta Title <span class="counter" id="counter-title">0/70</span></label>
            <input type="text" id="meta_title" name="meta_title" maxlength="70"
                   value="<?= htmlspecialchars($meta_title ?? '') ?>"
                   oninput="updateCounter('meta_title', 'counter-title', 70)">
            <small>Titre affiché dans les résultats Google. Idéalement 50-60 caractères.</small>
        </div>
        <div class="form-group">
            <label for="meta_description">Meta Description <span class="counter" id="counter-desc">0/170</span></label>
            <textarea id="meta_description" name="meta_description" rows="3" maxlength="170"
                      oninput="updateCounter('meta_description', 'counter-desc', 170)"><?= htmlspecialchars($meta_description ?? '') ?></textarea>
            <small>Description affichée sous le titre dans Google. Idéalement 150-160 caractères.</small>
        </div>
    </fieldset>

    <div class="form-group">
        <label for="statut">Statut</label>
        <select id="statut" name="statut">
            <option value="brouillon" <?= ($statut ?? '') === 'brouillon' ? 'selected' : '' ?>>📝 Brouillon</option>
            <option value="publie"    <?= ($statut ?? '') === 'publie'    ? 'selected' : '' ?>>✅ Publié</option>
        </select>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">💾 Enregistrer l'article</button>
        <a href="list.php" class="btn-secondary">Annuler</a>
    </div>
</form>

<script>
function updateCounter(inputId, counterId, max) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    const len = input.value.length;
    counter.textContent = len + '/' + max;
    counter.style.color = len > max * 0.9 ? 'red' : 'inherit';
}
// Initialiser les compteurs au chargement
updateCounter('meta_title', 'counter-title', 70);
updateCounter('meta_description', 'counter-desc', 170);
</script>

<?php require_once '../../includes/header.php'; ?>
```

- [ ] Page `login.php` créée et fonctionnelle
- [ ] Connexion avec admin/Admin1234 fonctionne
- [ ] Déconnexion fonctionne
- [ ] `auth.php` protège toutes les pages du BO
- [ ] Dashboard affiche les statistiques
- [ ] Liste des articles s'affiche
- [ ] Création d'article fonctionne (avec upload image)
- [ ] Modification d'article fonctionne
- [ ] Suppression d'article fonctionne (avec confirmation)
- [ ] Gestion des catégories (CRUD) fonctionne

---

## PHASE 4 — FRONT-OFFICE (FO)

> Le Front-Office, c'est ce que voit le visiteur lambda. Il doit être beau, rapide, et optimisé pour Google.

### ✅ TO-DO Phase 4

#### Étape 4.1 — URL Rewriting (`.htaccess`)

> **C'est quoi le rewriting ?** Au lieu d'avoir `/article.php?id=12`, on veut `/article/offensive-du-12-mars`. C'est plus beau et meilleur pour le SEO.

**`frontoffice/public/.htaccess`** :

```apache
# Activation du moteur de réécriture
RewriteEngine On

# ─── Règles de réécriture ─────────────────────────────────────────

# Si le fichier ou dossier existe physiquement, on ne touche pas à l'URL
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Page d'accueil → index.php
RewriteRule ^$                    index.php [L]

# Page d'un article → /article/mon-slug-ici
RewriteRule ^article/([a-z0-9\-]+)/?$    article.php?slug=$1 [L,QSA]

# Page d'une catégorie → /categorie/politique
RewriteRule ^categorie/([a-z0-9\-]+)/?$  categorie.php?slug=$1 [L,QSA]

# Page "À propos"
RewriteRule ^a-propos/?$          apropos.php [L]

# Toute autre URL → 404
RewriteRule ^(.*)$                404.php [L]
```

**`backoffice/public/.htaccess`** :

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Login
RewriteRule ^login/?$             login.php [L]
RewriteRule ^logout/?$            logout.php [L]
RewriteRule ^dashboard/?$         dashboard.php [L]
RewriteRule ^articles/?$          articles/list.php [L]
RewriteRule ^articles/create/?$   articles/create.php [L]
RewriteRule ^articles/edit/([0-9]+)/?$  articles/edit.php?id=$1 [L,QSA]
RewriteRule ^categories/?$        categories/list.php [L]
```

#### Étape 4.2 — Page d'accueil

**`frontoffice/public/index.php`** :

```php
<?php
require_once '../includes/db.php';

$db = getDB();

// Récupérer les 10 derniers articles publiés avec leur catégorie
$articles = $db->query(
    "SELECT a.titre, a.slug, a.resume, a.image, a.alt_image, a.created_at,
            c.nom AS categorie, c.slug AS cat_slug
     FROM articles a
     JOIN categories c ON a.categorie_id = c.id
     WHERE a.statut = 'publie'
     ORDER BY a.created_at DESC
     LIMIT 10"
)->fetchAll();

// Article à la une (le plus récent)
$une = array_shift($articles);  // On retire le premier de la liste

// Toutes les catégories pour le menu
$categories = $db->query('SELECT nom, slug FROM categories ORDER BY nom')->fetchAll();

$pageTitle       = 'Guerre en Iran — Actualités et informations';
$metaDescription = 'Suivez en temps réel les dernières actualités sur le conflit en Iran : analyses politiques, militaires, humanitaires et diplomatiques.';

require_once '../includes/header.php';
?>

<main>
    <!-- Article à la une -->
    <?php if ($une): ?>
    <section class="une">
        <article class="article-une">
            <?php if ($une['image']): ?>
                <img src="<?= htmlspecialchars($une['image']) ?>"
                     alt="<?= htmlspecialchars($une['alt_image']) ?>"
                     class="une-image" loading="eager">
            <?php endif; ?>
            <div class="une-content">
                <span class="cat-badge"><?= htmlspecialchars($une['categorie']) ?></span>
                <h2>
                    <a href="/article/<?= htmlspecialchars($une['slug']) ?>">
                        <?= htmlspecialchars($une['titre']) ?>
                    </a>
                </h2>
                <p class="resume"><?= htmlspecialchars($une['resume']) ?></p>
                <a href="/article/<?= htmlspecialchars($une['slug']) ?>" class="lire-plus">
                    Lire l'article →
                </a>
            </div>
        </article>
    </section>
    <?php endif; ?>

    <!-- Grille des autres articles -->
    <section class="articles-grid">
        <h2>Dernières actualités</h2>
        <div class="grid">
            <?php foreach ($articles as $art): ?>
            <article class="article-card">
                <?php if ($art['image']): ?>
                    <img src="<?= htmlspecialchars($art['image']) ?>"
                         alt="<?= htmlspecialchars($art['alt_image']) ?>"
                         loading="lazy">
                <?php endif; ?>
                <div class="card-content">
                    <a href="/categorie/<?= htmlspecialchars($art['cat_slug']) ?>" class="cat-badge">
                        <?= htmlspecialchars($art['categorie']) ?>
                    </a>
                    <h3>
                        <a href="/article/<?= htmlspecialchars($art['slug']) ?>">
                            <?= htmlspecialchars($art['titre']) ?>
                        </a>
                    </h3>
                    <p><?= htmlspecialchars($art['resume']) ?></p>
                    <time datetime="<?= $art['created_at'] ?>">
                        <?= date('d/m/Y', strtotime($art['created_at'])) ?>
                    </time>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>
```

#### Étape 4.3 — Page d'article (SEO critique !)

**`frontoffice/public/article.php`** :

```php
<?php
require_once '../includes/db.php';

$db  = getDB();
$slug = $_GET['slug'] ?? '';

// Sécurité : validation du slug
if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
    http_response_code(404);
    include '404.php';
    exit;
}

// Récupération de l'article
$stmt = $db->prepare(
    "SELECT a.*, c.nom AS categorie, c.slug AS cat_slug
     FROM articles a
     JOIN categories c ON a.categorie_id = c.id
     WHERE a.slug = ? AND a.statut = 'publie'
     LIMIT 1"
);
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    include '404.php';
    exit;
}

// Articles liés (même catégorie)
$stmt2 = $db->prepare(
    "SELECT titre, slug, resume, image, alt_image
     FROM articles
     WHERE categorie_id = ? AND slug != ? AND statut = 'publie'
     ORDER BY created_at DESC LIMIT 3"
);
$stmt2->execute([$article['categorie_id'], $slug]);
$lies = $stmt2->fetchAll();

// Variables pour le header (SEO)
$pageTitle       = $article['meta_title']       ?: $article['titre'] . ' | Guerre Iran';
$metaDescription = $article['meta_description'] ?: $article['resume'];

require_once '../includes/header.php';
?>

<main class="article-page">
    <article>
        <!-- Fil d'ariane (breadcrumb) — bon pour le SEO -->
        <nav class="breadcrumb" aria-label="Fil d'ariane">
            <a href="/">Accueil</a>
            <span> › </span>
            <a href="/categorie/<?= htmlspecialchars($article['cat_slug']) ?>">
                <?= htmlspecialchars($article['categorie']) ?>
            </a>
            <span> › </span>
            <span><?= htmlspecialchars($article['titre']) ?></span>
        </nav>

        <!-- En-tête de l'article -->
        <header class="article-header">
            <span class="cat-badge"><?= htmlspecialchars($article['categorie']) ?></span>

            <!-- H1 : UN SEUL PAR PAGE, contient le titre de l'article -->
            <h1><?= htmlspecialchars($article['titre']) ?></h1>

            <div class="article-meta">
                <time datetime="<?= $article['created_at'] ?>">
                    Publié le <?= date('d F Y', strtotime($article['created_at'])) ?>
                </time>
                <?php if ($article['updated_at'] !== $article['created_at']): ?>
                    — <time datetime="<?= $article['updated_at'] ?>">
                        Mis à jour le <?= date('d F Y', strtotime($article['updated_at'])) ?>
                    </time>
                <?php endif; ?>
            </div>
        </header>

        <!-- Image principale -->
        <?php if ($article['image']): ?>
        <figure class="article-figure">
            <img src="<?= htmlspecialchars($article['image']) ?>"
                 alt="<?= htmlspecialchars($article['alt_image']) ?>"
                 class="article-img"
                 loading="eager">
            <figcaption><?= htmlspecialchars($article['alt_image']) ?></figcaption>
        </figure>
        <?php endif; ?>

        <!-- Résumé -->
        <?php if ($article['resume']): ?>
        <p class="article-resume"><strong><?= htmlspecialchars($article['resume']) ?></strong></p>
        <?php endif; ?>

        <!-- Contenu principal (HTML stocké en BDD) -->
        <!-- ATTENTION : utiliser htmlspecialchars() si c'est du texte pur.
             Ici on affiche du HTML donc on ne l'échappe PAS,
             mais on s'assure que seul l'admin peut saisir ce contenu ! -->
        <div class="article-contenu">
            <?= $article['contenu'] ?>
        </div>
    </article>

    <!-- Articles liés -->
    <?php if (!empty($lies)): ?>
    <aside class="articles-lies">
        <h2>Articles liés</h2>
        <div class="grid-small">
            <?php foreach ($lies as $lie): ?>
            <article class="article-card-small">
                <?php if ($lie['image']): ?>
                    <img src="<?= htmlspecialchars($lie['image']) ?>"
                         alt="<?= htmlspecialchars($lie['alt_image']) ?>"
                         loading="lazy">
                <?php endif; ?>
                <h3>
                    <a href="/article/<?= htmlspecialchars($lie['slug']) ?>">
                        <?= htmlspecialchars($lie['titre']) ?>
                    </a>
                </h3>
            </article>
            <?php endforeach; ?>
        </div>
    </aside>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>
```

#### Étape 4.4 — Header commun (SEO essentiel !)

**`frontoffice/includes/header.php`** :

```php
<?php
// Ces variables doivent être définies AVANT d'inclure ce fichier :
// $pageTitle       = 'Titre de la page';
// $metaDescription = 'Description pour Google';
// $categories      = [...]; (si pas encore chargées)

if (!isset($categories)) {
    require_once __DIR__ . '/db.php';
    $categories = getDB()->query('SELECT nom, slug FROM categories ORDER BY nom')->fetchAll();
}

$canonical = 'https://www.guerre-iran.local' . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ══ BALISES META SEO (OBLIGATOIRES) ══════════════════════ -->
    <title><?= htmlspecialchars($pageTitle ?? 'Guerre en Iran — Informations') ?></title>
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? '') ?>">
    <meta name="robots" content="index, follow">

    <!-- URL canonique : évite le contenu dupliqué -->
    <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

    <!-- Open Graph (pour le partage sur les réseaux sociaux) -->
    <meta property="og:type"        content="website">
    <meta property="og:title"       content="<?= htmlspecialchars($pageTitle ?? '') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription ?? '') ?>">
    <meta property="og:locale"      content="fr_FR">
    <?php if (isset($article['image'])): ?>
    <meta property="og:image"       content="<?= htmlspecialchars($article['image']) ?>">
    <?php endif; ?>

    <!-- ══ CSS ══════════════════════════════════════════════════ -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <header class="site-header">
        <div class="container">
            <a href="/" class="logo">
                <!-- H1 sur l'accueil, sinon p -->
                <strong>Guerre en Iran</strong>
                <span>Actualités & Analyses</span>
            </a>

            <nav class="main-nav" aria-label="Navigation principale">
                <ul>
                    <li><a href="/">Accueil</a></li>
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="/categorie/<?= htmlspecialchars($cat['slug']) ?>">
                            <?= htmlspecialchars($cat['nom']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <li><a href="/a-propos">À propos</a></li>
                </ul>
            </nav>
        </div>
    </header>
```

- [ ] `.htaccess` FO créé et les URLs propres fonctionnent
- [ ] `.htaccess` BO créé et fonctionne
- [ ] Page d'accueil affiche les articles
- [ ] Page article s'affiche correctement
- [ ] Page catégorie s'affiche correctement
- [ ] Page 404 personnalisée créée
- [ ] Header avec toutes les balises méta créé
- [ ] Footer créé

---

## PHASE 5 — SEO & OPTIMISATION

> Le SEO, c'est l'art de plaire à Google pour apparaître dans les premiers résultats.

### ✅ TO-DO Phase 5 — Checklist SEO complète

#### Balises META
- [ ] `<title>` présent et unique sur chaque page (max 70 caractères)
- [ ] `<meta name="description">` présent sur chaque page (max 170 caractères)
- [ ] `<meta name="robots" content="index, follow">` présent
- [ ] `<link rel="canonical">` présent sur chaque page

#### Structure des titres (H1 → H6)
- [ ] **Un seul `<h1>` par page** (le titre principal de la page)
  - Accueil : titre du site ou "Dernières actualités"
  - Article : titre de l'article
  - Catégorie : nom de la catégorie
- [ ] `<h2>` pour les sections principales
- [ ] `<h3>` pour les sous-sections
- [ ] Ne JAMAIS sauter de niveaux (pas de H1 → H3 sans H2)

#### Images
- [ ] Chaque `<img>` a un attribut `alt` descriptif
- [ ] Les images sont redimensionnées (pas d'image de 5 Mo en pleine taille !)
- [ ] Utilisation du format WebP quand possible
- [ ] `loading="lazy"` sur les images hors écran
- [ ] `loading="eager"` sur l'image principale (au-dessus de la ligne de flottaison)

#### Performance
- [ ] CSS minifié (ou petit fichier CSS)
- [ ] Pas de JavaScript bloquant (mettre les scripts en bas de page ou avec `defer`)
- [ ] Compression GZIP activée dans `.htaccess` :

```apache
# Ajouter dans .htaccess :
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript text/plain
</IfModule>

# Cache des fichiers statiques
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/png  "access plus 1 month"
    ExpiresByType text/css   "access plus 1 week"
</IfModule>
```

#### Accessibilité (bonus SEO)
- [ ] Balise `<html lang="fr">` présente
- [ ] Tous les liens ont un texte descriptif (pas de "cliquez ici")
- [ ] Navigation avec `<nav>` et `aria-label`
- [ ] Contraste suffisant entre texte et fond

---

## PHASE 6 — TESTS LIGHTHOUSE

> **Lighthouse** est un outil de Google qui note votre site sur 4 critères (Performance, SEO, Accessibilité, Bonnes pratiques). L'objectif est d'avoir **au minimum 80/100 sur chaque critère**.

### Comment faire le test Lighthouse ?

1. Ouvrir Chrome
2. Aller sur votre site (ex: http://localhost:8080)
3. Appuyer sur **F12** (ouvrir les DevTools)
4. Cliquer sur l'onglet **"Lighthouse"**
5. Cocher : **Performance**, **Accessibilité**, **Meilleures pratiques**, **SEO**
6. Choisir : **Mobile** → cliquer **Analyser la page**
7. Répéter avec **Ordinateur**

### ✅ TO-DO Phase 6

- [ ] Test Lighthouse **Mobile** effectué
  - [ ] Performance : ≥ 70
  - [ ] Accessibilité : ≥ 80
  - [ ] Meilleures pratiques : ≥ 80
  - [ ] SEO : ≥ 90
- [ ] Test Lighthouse **Ordinateur** effectué
  - [ ] Performance : ≥ 80
  - [ ] Accessibilité : ≥ 80
  - [ ] Meilleures pratiques : ≥ 80
  - [ ] SEO : ≥ 90
- [ ] Captures d'écran des résultats sauvegardées pour le document technique

### Problèmes courants et corrections :

| Problème Lighthouse | Correction |
|---------------------|------------|
| "Images not sized correctly" | Ajouter `width` et `height` aux balises `<img>` |
| "Missing alt attributes" | Ajouter `alt` à toutes les images |
| "Meta description missing" | Ajouter `<meta name="description">` |
| "Render-blocking resources" | Mettre CSS en `<head>`, JS en bas de body avec `defer` |
| "Does not have a `<title>`" | Ajouter `<title>` dans le `<head>` |

---

## PHASE 7 — LIVRAISON

### ✅ TO-DO Phase 7

#### Étape 7.1 — Dépôt Git public

```bash
# Dans le dossier du projet :
git init
git add .
git commit -m "Initial commit — Projet guerre en Iran"

# Créer un dépôt sur GitHub ou GitLab et pousser :
git remote add origin https://github.com/votre-pseudo/guerre-iran.git
git push -u origin main
```

- [ ] Dépôt GitHub ou GitLab **public** créé
- [ ] Code poussé sur le dépôt
- [ ] URL du dépôt notée

#### Étape 7.2 — Document technique

Créer un fichier PDF ou Word contenant :

- [ ] **Numéro ETU** des deux membres du binôme
- [ ] **Captures d'écran du Front-Office** :
  - [ ] Page d'accueil (desktop)
  - [ ] Page d'accueil (mobile)
  - [ ] Page d'un article
  - [ ] Page d'une catégorie
- [ ] **Captures d'écran du Back-Office** :
  - [ ] Page de login
  - [ ] Dashboard
  - [ ] Liste des articles
  - [ ] Formulaire de création d'article
- [ ] **Modélisation de la base de données** (schéma avec les tables et relations)
- [ ] **Identifiants par défaut du BO** :
  - URL : `http://localhost:8081/login`
  - Login : `admin`
  - Mot de passe : `Admin1234`
- [ ] **Résultats Lighthouse** (captures d'écran mobile et desktop)

#### Étape 7.3 — ZIP à livrer

```bash
# Arrêter Docker avant de zipper
docker-compose down

# Créer le zip
zip -r projet_guerre_iran_ETU12345.zip . \
    --exclude "*.git*" \
    --exclude "node_modules/*"
```

- [ ] ZIP créé et testé (dézippé dans un autre dossier, puis `docker-compose up -d`)
- [ ] Le site FO fonctionne après dézippage
- [ ] Le site BO fonctionne après dézippage
- [ ] La BDD se crée automatiquement au premier lancement

---

## TO-DO LIST COMPLÈTE (RÉSUMÉ)

Cochez chaque case au fur et à mesure. C'est votre feuille de route !

### 🐳 Docker
- [ ] Docker Desktop installé
- [ ] `docker-compose.yml` créé
- [ ] `Dockerfile` FO créé
- [ ] `Dockerfile` BO créé
- [ ] `docker-compose up -d` fonctionne
- [ ] FO accessible sur http://localhost:8080
- [ ] BO accessible sur http://localhost:8081

### 🗄️ Base de données
- [ ] `database/init.sql` créé
- [ ] Tables `categories`, `articles`, `admins` créées
- [ ] Données de test insérées
- [ ] Admin par défaut : login `admin` / mot de passe `Admin1234`
- [ ] `db.php` fonctionne (pas d'erreur de connexion)

### 🔐 Back-Office
- [ ] Page `/login` affiche le formulaire
- [ ] Connexion admin fonctionne
- [ ] Déconnexion fonctionne
- [ ] Dashboard affiche les stats
- [ ] **CRUD Articles** complet (créer, lister, modifier, supprimer)
- [ ] **CRUD Catégories** complet
- [ ] Upload d'image fonctionne
- [ ] Validation des formulaires (champs requis, taille méta...)
- [ ] Sessions sécurisées (auth.php sur chaque page protégée)

### 🌐 Front-Office
- [ ] `.htaccess` avec URL rewriting actif
- [ ] URLs propres fonctionnent (`/article/mon-slug`, `/categorie/politique`)
- [ ] Page d'accueil avec liste des articles
- [ ] Page article avec contenu complet
- [ ] Page catégorie filtrée
- [ ] Page 404 personnalisée
- [ ] Navigation avec toutes les catégories

### 🔍 SEO
- [ ] `<title>` unique sur chaque page
- [ ] `<meta name="description">` sur chaque page
- [ ] Un seul `<h1>` par page
- [ ] Structure H1 → H2 → H3 respectée dans les articles
- [ ] `alt` sur toutes les images
- [ ] `<html lang="fr">` présent
- [ ] `<link rel="canonical">` présent
- [ ] URL propres (rewriting actif)

### 🏆 Lighthouse
- [ ] Test Mobile effectué et screenshot sauvegardé
- [ ] Test Desktop effectué et screenshot sauvegardé
- [ ] Scores ≥ 80 sur tous les critères

### 📦 Livraison
- [ ] Dépôt Git public créé et URL notée
- [ ] Document technique complet (captures FO/BO, modèle BDD, credentials)
- [ ] ZIP final créé et testé
- [ ] Livraison avant le **31 mars 2026 à 14h00**

---

## 📌 RÉCAPITULATIF DES CREDENTIALS

| Élément | Valeur |
|---------|--------|
| URL Front-Office | http://localhost:8080 |
| URL Back-Office | http://localhost:8081 |
| Login BO | `admin` |
| Mot de passe BO | `Admin1234` |
| BDD Host | `mysql` (nom Docker) |
| BDD Name | `guerre_iran` |
| BDD User | `admin` |
| BDD Password | `adminpassword` |

---

*Document créé pour le Mini-Projet Web Design — Mars 2026*
*Règle : Aucun framework — PHP pur + MySQL + HTML/CSS/JS vanilla*
