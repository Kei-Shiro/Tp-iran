CREATE DATABASE IF NOT EXISTS guerre_iran CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE guerre_iran;

CREATE TABLE IF NOT EXISTS admins (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(100) NOT NULL UNIQUE,
	password_hash VARCHAR(255) NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	nom VARCHAR(120) NOT NULL,
	slug VARCHAR(150) NOT NULL UNIQUE,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS articles (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	categorie_id INT UNSIGNED NOT NULL,
	titre VARCHAR(255) NOT NULL,
	slug VARCHAR(255) NOT NULL UNIQUE,
	resume TEXT NULL,
	contenu MEDIUMTEXT NOT NULL,
	image VARCHAR(255) NULL,
	alt_image VARCHAR(255) NULL,
	meta_title VARCHAR(70) NULL,
	meta_description VARCHAR(170) NULL,
	statut ENUM('publie', 'brouillon') NOT NULL DEFAULT 'brouillon',
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	CONSTRAINT fk_articles_categories
		FOREIGN KEY (categorie_id) REFERENCES categories(id)
		ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_articles_statut ON articles(statut);
CREATE INDEX idx_articles_categorie_id ON articles(categorie_id);

INSERT INTO admins (username, password_hash)
SELECT 'admin', '$2y$10$TbKf5KaZL5ZTRzrWSUPgiuNn006zyvMyw5M6OpveitDwY9cPS9Ui6'
WHERE NOT EXISTS (SELECT 1 FROM admins WHERE username = 'admin');

INSERT INTO categories (nom, slug)
VALUES
	('Geopolitique', 'geopolitique'),
	('Defense', 'defense'),
	('Economie', 'economie')
ON DUPLICATE KEY UPDATE nom = VALUES(nom);

INSERT INTO articles (categorie_id, titre, slug, resume, contenu, meta_title, meta_description, statut)
SELECT
	c.id,
	'Contexte regional et tensions recentes',
	'contexte-regional-et-tensions-recentes',
	'Synthese des principaux faits recents et des enjeux diplomatiques.',
	'<p>Ce contenu initial sert de base pour valider le frontoffice et le backoffice.</p><p>Vous pouvez le modifier depuis l administration.</p>',
	'Contexte regional et tensions recentes',
	'Analyse des tensions recentes dans la region et des impacts geopolitiques.',
	'publie'
FROM categories c
WHERE c.slug = 'geopolitique'
  AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'contexte-regional-et-tensions-recentes');

