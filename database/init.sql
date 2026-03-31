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

-- =============================================================
--  DONNÉES CONCRÈTES — guerre_iran
--  Catégories + 12 articles réalistes avec contenu HTML complet
-- =============================================================

USE guerre_iran;

-- -------------------------------------------------------------
-- CATÉGORIES (5 rubriques)
-- -------------------------------------------------------------
INSERT INTO categories (nom, slug) VALUES
                                       ('Geopolitique',  'geopolitique'),
                                       ('Defense',       'defense'),
                                       ('Economie',      'economie'),
                                       ('Diplomatie',    'diplomatie'),
                                       ('Humanitaire',   'humanitaire')
    ON DUPLICATE KEY UPDATE nom = VALUES(nom);

-- -------------------------------------------------------------
-- ARTICLES (12 articles publiés, répartis sur les 5 catégories)
-- -------------------------------------------------------------

-- ── ARTICLE 1 ─ Géopolitique ─────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Frappes israeliennes sur les sites nucleaires iraniens : chronologie d une escalade',
    'frappes-israeliennes-sites-nucleaires-iraniens-chronologie',
    'Dans la nuit du 14 au 15 avril, l aviation israelienne a conduit une serie de frappes sur trois sites nucleaires iraniens majeurs, declenchant une crise sans precedent depuis 2006.',
    '<p>Dans la nuit du 14 au 15 avril 2026, une vague de frappes aerees israeliennes a frappe simultanement les sites de <strong>Natanz</strong>, <strong>Fordow</strong> et <strong>Isfahan</strong>. L operation, dont la planification aurait dure plusieurs mois selon des sources du renseignement americain, marque un tournant decisif dans le conflit larvé qui oppose Israel a la Republique islamique d Iran depuis plus de deux decennies.</p>

<h2>Deroulement des frappes</h2>
<p>Les premiers missiles ont touche le site souterrain de Fordow vers 01h40 heure locale. Selon le ministere iranien de la Defense, plusieurs centrifugeuses d enrichissement ont ete detruites. Le site de Natanz, principal complexe nucleaire du pays, a subi des degats structurels importants dans ses installations souterraines de niveau inferieur.</p>
<p>L armee de l air israelienne a mobilise plus de soixante appareils, dont des F-35I Adir modifies pour les frappes longue portee. Des avions-ravitailleurs americains auraient facilite le transit au-dessus de l Arabie Saoudite, selon des sources diplomatiques non confirmées officiellement.</p>

<h2>Reactions immediates</h2>
<p>Le Guide supreme Ali Khamenei a promis une <strong>reponse severe et proportionnee</strong>, sans preciser l echeance ni la nature des represailles. Le president iranien a pour sa part convoque une reunion d urgence du Conseil supreme de la securite nationale.</p>
<p>Washington a pris ses distances, affirmant n avoir ete informé qu après le debut de l operation. L Union europeenne a appele a la retenue des deux cotes, tandis que la Russie et la Chine ont denonce une violation flagrante du droit international.</p>

<h2>Implications strategiques</h2>
<p>Pour les analystes, ces frappes reconfigurent profondement l equilibre regional. L Iran dispose encore de capacites d enrichissement sur d autres sites moins connus, mais le programme nucleaire accuse desormais un retard estime entre dix-huit mois et trois ans. La question centrale demeure celle de la riposte iranienne : missiliere directe, activation des proxies au Liban et en Irak, ou operations asymetriques dans le detroit d Ormuz.</p>
<blockquote>« Israel a franchi une ligne rouge. Ce qui suit ne sera pas une demonstration symbolique. » — Porte-parole des Gardiens de la Revolution, 15 avril 2026</blockquote>',
    'uploads/natanz-frappe-avril-2026.jpg',
    'Vue satellitaire du site de Natanz apres les frappes israeliennes, avril 2026',
    'Frappes sur le nucleaire iranien : chronologie | Guerre en Iran',
    'Chronologie et analyse des frappes israeliennes sur Natanz, Fordow et Isfahan dans la nuit du 14 au 15 avril 2026.',
    'publie',
    '2026-04-15 08:30:00'
FROM categories c WHERE c.slug = 'geopolitique'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'frappes-israeliennes-sites-nucleaires-iraniens-chronologie');

-- ── ARTICLE 2 ─ Défense ──────────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Systeme de defense antimissile iranien : ce qui a resiste, ce qui a echoue',
    'systeme-defense-antimissile-iranien-analyse',
    'L arsenal antimissile iranien a montre des failles critiques face aux frappes du 15 avril. Analyse technique des systemes en jeu et de leurs limites.',
    '<p>La nuit des frappes israeliennes a constitue un veritable test grandeur nature pour le dispositif de defense aerienne iranien. Le bilan est severe : sur les trois sites vises, seules les batteries deployees autour d Isfahan ont intercepte une partie des missiles entrants.</p>

<h2>Le bouclier S-300 face a l epreuve</h2>
<p>L Iran dispose depuis 2016 de batteries <strong>S-300PMU2</strong> fournies par la Russie. Ces systemes, concus pour intercepter des missiles balistiques et des avions de combat, ont montre leurs limites face aux munitions rodeuses israeliennes — missiles a faible signature radar volant en essaim coordonne.</p>
<p>Selon des experts en armement interroges par des medias specialises, les F-35I israeliens ont exploite leur capacite de <em>jamming</em> electronique avance pour aveugler temporairement les radars iraniens pendant la phase d approche. L heure choisie — milieu de nuit — a egalement reduit la reactivite des operateurs.</p>

<h2>Les systemes Bavar-373</h2>
<p>L Iran a presente son systeme Bavar-373 comme une alternative domestique superieure au S-300 russe. Lors des frappes d avril, ce systeme n a pas ete engage sur les sites de Natanz et Fordow, soulevant des questions sur son operationnalite reelle ou sur la decision tactique de le conserver en reserve.</p>

<h2>Consequences pour la doctrine defensive</h2>
<p>Les Gardiens de la Revolution (IRGC) procederaient a une revue complete de leur posture defensive. Des sources evoquent le deploiement imminent de nouvelles batteries autour des sites de repli et la dispersion acceleree des equipements sensibles dans des installations souterraines supplementaires.</p>',
    'uploads/systeme-s300-iran.jpg',
    'Batterie de missiles sol-air S-300 deployee en Iran',
    'Defense antimissile iranienne : analyse | Guerre en Iran',
    'Analyse technique du systeme de defense aerienne iranien et de ses failles revelees lors des frappes israeliennes d avril 2026.',
    'publie',
    '2026-04-16 10:15:00'
FROM categories c WHERE c.slug = 'defense'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'systeme-defense-antimissile-iranien-analyse');

-- ── ARTICLE 3 ─ Économie ─────────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Le petrole au-dessus de 140 dollars : le detroit d Ormuz au coeur des craintes',
    'petrole-140-dollars-detroit-ormuz-craintes',
    'Le baril de Brent a franchi la barre des 140 dollars le 16 avril au matin, portee par les craintes d un blocage du detroit d Ormuz, voie de passage de 20 % des exportations mondiales de petrole.',
    '<p>Les marches petroliers ont reagi violemment aux frappes israeliennes. Le Brent a ouvert en hausse de 18 % le 15 avril au matin, franchissant 140 dollars le baril pour la premiere fois depuis le pic de 2022. Le WTI americain a suivi une trajectoire similaire.</p>

<h2>Pourquoi Ormuz concentre les inquietudes</h2>
<p>Le detroit d Ormuz est un goulot d etranglement critique : 17 a 20 millions de barils y transitent quotidiennement, soit environ 20 % de la consommation mondiale. L Iran l a ferme a plusieurs reprises lors de crises precedentes, sans jamais passer a l acte de maniere prolongee. Les analystes estiment qu une fermeture de deux semaines ferait monter le baril entre 180 et 220 dollars.</p>
<p>Les compagnies maritimes ont deja commence a rerouter certains de leurs petroliers par le Cap de Bonne-Esperance, allongeant les trajets de plusieurs semaines et rencherissant considerablement les frais de transport.</p>

<h2>Impacts pour les economies importatrices</h2>
<ul>
<li><strong>Europe :</strong> la dependance au petrole du Golfe reste moderee mais les prix au pompe ont progresse de 12 % en une semaine en France et en Allemagne.</li>
<li><strong>Japon et Coree du Sud :</strong> tres dependants du brut du Golfe, ces pays ont active leurs reserves strategiques.</li>
<li><strong>Chine :</strong> principal importateur mondial, Pekin suit la situation avec une attention particuliere, ses reseaux d approvisionnement transitant majoritairement par Ormuz.</li>
</ul>

<h2>Les producteurs alternatifs en position de force</h2>
<p>L Arabie Saoudite, les Emirats arabes unis et l Irak ont annonce pouvoir augmenter leur production a court terme. L OPEP+ a convoque une reunion extraordinaire pour le 20 avril. Aux Etats-Unis, l administration a deja autorise des liberations supplementaires depuis la Reserve strategique.</p>',
    'uploads/detroit-ormuz-tanker.jpg',
    'Petrolier traversant le detroit d Ormuz, entre Iran et peninsule arabique',
    'Petrole a 140 dollars : le detroit d Ormuz au coeur de la crise | Guerre en Iran',
    'Analyse de l impact des frappes israeliennes sur les marches petroliers et les risques pour le detroit d Ormuz.',
    'publie',
    '2026-04-16 14:00:00'
FROM categories c WHERE c.slug = 'economie'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'petrole-140-dollars-detroit-ormuz-craintes');

-- ── ARTICLE 4 ─ Diplomatie ───────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Conseil de securite de l ONU : veto russe et chinois, impasse totale',
    'conseil-securite-onu-veto-russe-chinois-impasse',
    'La seance d urgence du Conseil de securite de l ONU s est achevee sans resolution, apres le double veto de la Russie et de la Chine sur le texte de condamnation des frappes israeliennes.',
    '<p>Convoques en session d urgence le 15 avril au siege des Nations Unies a New York, les quinze membres du Conseil de securite n ont pas reussi a s accorder sur une resolution commune. La Russie et la Chine ont use de leur droit de veto pour bloquer un texte presenté par la France, le Royaume-Uni et l Allemagne.</p>

<h2>Les arguments des opposants</h2>
<p>L ambassadrice russe a denonce « une agression caracterisee contre un Etat souverain » et exige que le texte condamne egalement les « provocations israeliennes repetees ». Son homologue chinois a insiste sur la necessite de respecter la souverainete de l Iran et de reprendre les negociations dans le cadre de l accord nucleaire JCPOA.</p>

<h2>La position americaine, facteur de blocage</h2>
<p>Les Etats-Unis, bien qu ayant nie toute implication directe, ont refuse de signer un texte condamnant explicitement Israel. L ambassadeur americain a plaide pour une <em>declaration presidentielle</em> non contraignante appelant a la retenue, option immediatement rejetee par Moscou et Pekin comme insuffisante.</p>

<h2>Quelle suite pour la diplomatie internationale ?</h2>
<p>L echec onusien laisse le champ libre aux initiatives bilaterales. Le Qatar, dont le canal diplomatique avec Teheran est reconnu, s est propose comme mediateur. La Turquie a egallement offert ses bons offices. Ces tentatives restent pour l instant sans echo positif de la part de l Iran, qui exige des excuses israeliennes et une compensation avant toute negociation.</p>
<blockquote>« Le Conseil de securite a failli a sa mission premiere. La communaute internationale ne peut se permettre de regarder ailleurs. » — Secretaire general de l ONU, 15 avril 2026</blockquote>',
    NULL,
    NULL,
    'Conseil de securite ONU : veto russe et chinois apres les frappes | Guerre en Iran',
    'Compte-rendu de la session d urgence du Conseil de securite et analyse du blocage diplomatique international.',
    'publie',
    '2026-04-15 22:45:00'
FROM categories c WHERE c.slug = 'diplomatie'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'conseil-securite-onu-veto-russe-chinois-impasse');

-- ── ARTICLE 5 ─ Humanitaire ──────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Populations civiles iraniennes : entre peur des represailles et coupures d electricite',
    'populations-civiles-iraniennes-peur-represailles-coupures',
    'Dans les grandes villes iraniennes, la nuit des frappes a provoque un mouvement de panique. Des habitants de Teheran et d Isfahan temoignent de coupures d electricite, de files d attente aux stations-essence et d un exode partiel vers les campagnes.',
    '<p>A Isfahan, ville de deux millions d habitants situee a moins de vingt kilometres du site nucleaire touche, la population a vecu la nuit du 14 au 15 avril dans la terreur. Des temoignages recueillis par des journalistes de la diaspora iranienne decrivent des explosions audibles depuis le centre-ville, des colonnes de fumee visibles a l aube et des coupures d electricite durant plusieurs heures.</p>

<h2>Un exode spontane vers les campagnes</h2>
<p>Des images diffusees sur les reseaux sociaux iraniens — rapidement censurées par les autorites — montraient des files de vehicules quittant Isfahan et Natanz des la nuit de l attaque. Selon des sources locales, certains villages de la province d Isfahan ont accueilli plusieurs milliers de deplaces urbains dans les jours suivants.</p>

<h2>Ruptures d approvisionnement</h2>
<p>La tension a provoque des comportements de panique economique : les grandes surfaces de Teheran ont ete devalisees de produits de premiere necessite en l espace de quelques heures le 15 avril. Les autorites ont appele au calme et annonce le deblocage de reserves alimentaires strategiques.</p>
<p>Les prix ont flambe sur les marches informels : le dollar s echangeait officieusement a 980 000 rials le 16 avril, un record historique. L acces aux devises etrangeres, deja tres controle, est devenu quasi impossible pour les particuliers.</p>

<h2>La societe civile prise en etau</h2>
<p>Des organisations de defense des droits humains signalent une intensification de la repression contre les voix dissidentes depuis les frappes. Plusieurs journalistes independants et militants connus pour critiquer le regime ont ete arretes. L acces a internet a ete severement restreint pendant quarante-huit heures dans plusieurs provinces.</p>',
    'uploads/isfahan-population-civil.jpg',
    'Residents d Isfahan faisant la queue devant une station-service, 15 avril 2026',
    'Populations civiles iraniennes face aux frappes et a la crise | Guerre en Iran',
    'Temoignages et analyse de la situation humanitaire en Iran apres les frappes israeliennes d avril 2026.',
    'publie',
    '2026-04-17 09:00:00'
FROM categories c WHERE c.slug = 'humanitaire'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'populations-civiles-iraniennes-peur-represailles-coupures');

-- ── ARTICLE 6 ─ Géopolitique ─────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Hezbollah et Hamas : les proxies iraniens en ordre de bataille',
    'hezbollah-hamas-proxies-iraniens-ordre-bataille',
    'Depuis les frappes israeliennes, le Hezbollah libanais a procede a des tirs de roquettes sur le nord d Israel. Le Hamas a appele a une intifada generalisee. Teheran active son reseau de proxies regionaux.',
    '<p>L Iran ne repondra peut-etre pas directement aux frappes israeliennes — du moins pas dans un premier temps. C est la lecture dominante des analystes du Moyen-Orient, qui estiment que Teheran va privilegier une <strong>guerre par procuration</strong> a travers son reseau de milices alliees.</p>

<h2>Le Hezbollah ouvre un deuxieme front</h2>
<p>Des le 16 avril, des salves de roquettes Katyusha et de missiles antichar Kornet ont touche plusieurs localites du nord d Israel, depuis le Liban-Sud. L armee israelienne a riposte par des frappes d artillerie et des raids aeriens sur des positions du Hezbollah dans la vallee de la Bekaa.</p>
<p>Le Hezbollah dispose, selon les evaluations occidentales, d un arsenal de plus de 150 000 roquettes et missiles. Parmi eux, des Fateh-110 et des missiles de croisiere capables d atteindre Tel-Aviv et les infrastructures portuaires d Haifa.</p>

<h2>Les milices irakiennes s activent</h2>
<p>En Irak, les Factions de la resistance — ensemble de milices pro-iraniennes federees sous l egide des Hachd al-Chaabi — ont tire plusieurs drones kamikazes en direction des bases americaines d Ain al-Assad et d Erbil. Ces frappes, sans bilan majeur, constituent un signal politique clair a Washington.</p>

<h2>Les Houthis rouvrent le front mer Rouge</h2>
<p>Au Yemen, les Houthis ont repris leurs attaques contre le trafic maritime en mer Rouge, suspendues depuis janvier 2026. Deux navires commerciaux ont ete touches le 17 avril. L operation americaine Prosperity Guardian, deja deployee dans la zone, a intensifie ses interceptions.</p>',
    NULL,
    NULL,
    'Hezbollah, Hamas, milices irakiennes : les proxies iraniens en action | Guerre en Iran',
    'Analyse de l activation du reseau de proxies iraniens apres les frappes israeliennes : Hezbollah, Houthis et milices irakiennes.',
    'publie',
    '2026-04-17 16:30:00'
FROM categories c WHERE c.slug = 'geopolitique'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'hezbollah-hamas-proxies-iraniens-ordre-bataille');

-- ── ARTICLE 7 ─ Économie ─────────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Sanctions contre l Iran : l Europe relance le mecanisme de snapback',
    'sanctions-iran-europe-mecanisme-snapback',
    'Face a l escalade, les Europeens ont active le mecanisme de retablissement automatique des sanctions prevu par le JCPOA. Une decision aux consequences economiques considerables pour Teheran.',
    '<p>La France, le Royaume-Uni et l Allemagne — les trois signataires europeens du JCPOA — ont notifie le 18 avril leur intention d activer le mecanisme dit de <em>snapback</em>, qui permet le retablissement de l ensemble des sanctions internationales contre l Iran en l absence d un accord de l ensemble des parties.</p>

<h2>Le snapback : comment ca marche</h2>
<p>Prevu a l article 37 du JCPOA, ce mecanisme peut etre declenche par n importe quel signataire qui estime que l Iran ne respecte pas ses engagements. Une fois notifie au Conseil de securite, le retablissement des sanctions est automatique au bout de trente jours, <strong>sauf si le Conseil de securite vote une resolution pour le bloquer</strong> — resolution susceptible d etre vetee par les Etats-Unis et les autres membres permanents.</p>

<h2>Consequences economiques pour l Iran</h2>
<p>Le retablissement des sanctions fermerait definitivement les portes du systeme SWIFT aux banques iraniennes, gelerait les avoirs iraniens a l etranger et reimporserait un embargo sur les exportations petrolieres. L economie iranienne, deja fragilisee par des decennies de sanctions, entrerait dans une phase d isolement quasi-total.</p>
<ul>
<li>Inflation estimee a 45 % en 2025, elle pourrait atteindre 80 % en cas de sanctions completes.</li>
<li>Le rial a perdu 35 % de sa valeur depuis le debut de la crise.</li>
<li>Les reserves de change iraniennes sont evaluees entre 10 et 15 milliards de dollars, un niveau critique.</li>
</ul>',
    NULL,
    NULL,
    'Sanctions contre l Iran : l Europe active le snapback | Guerre en Iran',
    'Analyse du mecanisme de snapback active par les Europeens et de ses consequences economiques pour l Iran.',
    'publie',
    '2026-04-18 11:20:00'
FROM categories c WHERE c.slug = 'economie'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'sanctions-iran-europe-mecanisme-snapback');

-- ── ARTICLE 8 ─ Défense ──────────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'L arsenal balistique iranien : ce que Teheran peut encore faire',
    'arsenal-balistique-iranien-capacites-represailles',
    'Malgre les dommages subis, l Iran conserve un arsenal balistique considerable. Tour d horizon des missiles dont dispose encore Teheran pour une eventuelle frappe de represailles.',
    '<p>Les frappes israeliennes ont principalement vise les capacites nucleaires iraniennes, laissant intact l essentiel de l arsenal conventionnel du pays. L Iran dispose de la flotte de missiles balistiques la plus importante du Moyen-Orient, avec plusieurs centaines d engins de differentes portees.</p>

<h2>Les missiles a courte et moyenne portee</h2>
<p>Le Shahab-3, derive du Nodong nord-coreen, peut atteindre des cibles a 1 300 kilometres — couvrant l ensemble du territoire israelien depuis le sol iranien. Sa variante amelioree, le <strong>Ghadr-1</strong>, est equipee d une tete de guidage plus precise avec une CEP (erreur probable circulaire) estimee a 150-200 metres.</p>
<p>Le <strong>Fateh-110</strong> et ses derives (Zolfaghar, Dezful) sont des missiles a courte portee tres precis, largement utilises lors de frappes precedentes en Syrie et en Irak. Ils constituent l epine dorsale de la puissance de feu conventionnelle iranienne.</p>

<h2>Les hypersoniques : la nouvelle carte</h2>
<p>En fevrier 2023, l Iran a annonce avoir teste avec succes le <strong>Fattah</strong>, un missile hypersonique revendique comme capable de manoeuvrer pour eviter les systemes antimissiles. Sa portee declaree est de 1 400 kilometres. Les analystes occidentaux sont divises sur les capacites reelles de ce systeme.</p>

<h2>Scenario d une frappe de represailles</h2>
<p>Une riposte iranienne directe sur le territoire israelien est consideree comme hautement probable selon plusieurs services de renseignement. Elle pourrait prendre la forme d un tir massif sature — plusieurs centaines de missiles simultanement — pour depasser les capacites d interception du Dome de fer, de David s Sling et de la fleche-3 israelienne.</p>',
    'uploads/missile-balistique-iran.jpg',
    'Tir d essai d un missile balistique iranien de type Shahab, archives IRGC',
    'Arsenal balistique iranien : les capacites de frappe de Teheran | Guerre en Iran',
    'Tour d horizon des missiles balistiques dont dispose l Iran pour une eventuelle riposte aux frappes israeliennes.',
    'publie',
    '2026-04-18 15:00:00'
FROM categories c WHERE c.slug = 'defense'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'arsenal-balistique-iranien-capacites-represailles');

-- ── ARTICLE 9 ─ Diplomatie ───────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Washington entre soutien a Israel et crainte d un embrasement regional',
    'washington-soutien-israel-crainte-embrasement-regional',
    'L administration americaine navigue dans une contradiction croissante : soutien traditionnel a Israel d un cote, volonte d eviter un conflit generalise au Moyen-Orient de l autre.',
    '<p>Depuis les frappes du 15 avril, Washington envoie des signaux contradictoires. Le president americain a reaffirme le soutien « indefectible » des Etats-Unis a la securite d Israel. Dans le meme temps, des officiels du Pentagone ont fait savoir en prive leur inquietude face a une escalade qui pourrait entrainer les Etats-Unis dans un conflit direct qu ils ne souhaitent pas.</p>

<h2>Un porte-avions en position, mais a titre dissuasif</h2>
<p>Le groupe aeronaval du USS Gerald Ford a ete repositionne en mer d Arabie le 16 avril, en renfort du USS Dwight Eisenhower deja present dans la region. Ce deploiement est presente par le Pentagone comme une mesure de dissuasion et de protection des ressortissants americains, pas comme une preparation a une intervention offensive.</p>

<h2>Les lignes rouges americaines</h2>
<p>Le secretaire d Etat a precise lors d une conference de presse que les Etats-Unis interviendront militairement si : l Iran attaque directement des bases americaines de la region, le detroit d Ormuz est ferme de maniere prolongee, ou un missile iranien touche le territoire israelien et provoque un grand nombre de victimes civiles.</p>

<h2>Pression du Congres</h2>
<p>Au Congres, les voix se divisent selon les lignes partisanes habituelles. Les republicains reclament un soutien militaire accru a Israel et des frappes sur les sites de missiles iraniens. Les democrates progressistes exigent une autorisation parlementaire avant tout engagement militaire. Le debat autour de la <em>War Powers Resolution</em> est relance.</p>',
    NULL,
    NULL,
    'Washington entre soutien a Israel et crainte d embrasement | Guerre en Iran',
    'Analyse de la posture americaine face aux frappes israeliennes et aux risques d escalade regionale au Moyen-Orient.',
    'publie',
    '2026-04-19 10:00:00'
FROM categories c WHERE c.slug = 'diplomatie'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'washington-soutien-israel-crainte-embrasement-regional');

-- ── ARTICLE 10 ─ Humanitaire ─────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Diaspora iranienne : entre solidarite avec les civils et rejet du regime',
    'diaspora-iranienne-solidarite-civils-rejet-regime',
    'Des manifestations se sont tenues dans plusieurs capitales europeennes et nord-americaines. La diaspora iranienne exprime une position nuancee : hostilite au regime des mollahs, inquietude pour les proches restés au pays.',
    '<p>A Paris, Londres, Los Angeles et Toronto, des milliers d Iraniens de la diaspora sont descendus dans la rue dans les jours suivant les frappes. Leurs messages etaient deliberement ambigus : ni soutien aux frappes israeliennes, ni solidarite avec un regime qu ils rejettent en majorite.</p>

<h2>« Ni guerre, ni dictature »</h2>
<p>Ce slogan, scan des manifestations, traduit bien la difficulte de la position des exiles. Beaucoup ont fui l Iran precisement a cause du regime islamique ; pourtant, voir leur pays bombarde par une puissance etrangere reveille des sentiments nationalistes profonds et une inquietude sincere pour leurs familles.</p>
<p>« Mes parents sont a Isfahan. Ils m ont appele en pleurant la nuit des frappes. Ils ne soutiennent pas Khamenei, mais ils ont peur de mourir. Ce n est pas la meme chose », confie Shirin, 34 ans, etudiante en doctorat a Paris.</p>

<h2>Le dilemme des opposants politiques</h2>
<p>Les organisations d opposition iranienne en exil sont elles aussi divisees. Certaines, comme les Moudjahidines du peuple (MEK), ont salue les frappes comme un coup porte au regime. D autres, issues du mouvement Femme-Vie-Liberte, ont condamne toute intervention militaire etrangere, estimant que le changement doit venir de l interieur.</p>

<h2>Une collecte de fonds pour les civils</h2>
<p>Des initiatives caritatives se sont rapidement organisees en ligne. Plusieurs ONG travaillant pour l acces aux soins en Iran ont vu leurs dons tripler en une semaine. L acheminement de l aide reste cependant problematique compte tenu des restrictions bancaires et de la fermeture partielle des frontieres.</p>',
    NULL,
    NULL,
    'Diaspora iranienne : entre solidarite et rejet du regime | Guerre en Iran',
    'Comment la diaspora iranienne reagit aux frappes israeliennes et a la crise en Iran : temoignages et analyses.',
    'publie',
    '2026-04-19 14:30:00'
FROM categories c WHERE c.slug = 'humanitaire'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'diaspora-iranienne-solidarite-civils-rejet-regime');

-- ── ARTICLE 11 ─ Géopolitique ─────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Russie et Chine : une solidarite de facade avec Teheran ?',
    'russie-chine-solidarite-iran-analyse',
    'Moscou et Pekin ont bruyamment condamne les frappes israeliennes. Mais derriere la rhetorique, leurs interets reels divergent sensiblement de ceux de l Iran.',
    '<p>Le double veto russe et chinois au Conseil de securite de l ONU ne doit pas masquer la complexite des relations entre ces deux puissances et l Iran. Si Moscou et Pekin ont un interet commun a affaiblir l influence americaine au Moyen-Orient, leurs agendas respectifs ne coincident pas necessairement avec les interets iraniens.</p>

<h2>La Russie : un allie encombrant</h2>
<p>La Russie est a la fois fournisseur d armes de l Iran — notamment des systemes S-300 et potentiellement des composants pour drones — et un concurrent direct sur les marches petroliers et gaziers. Une Iran affaiblie et dependante est utile a Moscou ; une Iran qui ferme Ormuz et fait exploser les prix du brut l est encore plus, dans le contexte des sanctions liees a la guerre en Ukraine.</p>
<p>Cependant, la Russie n a aucun interet a se retrouver entrainee dans un conflit direct avec les Etats-Unis et Israel. Ses signaux de soutien restent donc deliberement vagues et symboliques.</p>

<h2>La Chine joue sa propre partition</h2>
<p>Pekin est le premier acheteur de petrole iranien — absorbant pres de 80 % des exportations sous sanctions — et un investisseur majeur dans les infrastructures iraniennes depuis l accord de partenariat strategique de 2021. Une destabilisation profonde de l Iran menacerait directement ces interets.</p>
<p>Dans le meme temps, la Chine entretient des relations commerciales considerables avec Israel et tient a sa neutralite apparente au Moyen-Orient. Elle privilegie la mediation discrète a l affrontement ouvert.</p>',
    NULL,
    NULL,
    'Russie et Chine face a la crise iranienne : solidarite ou calcul ? | Guerre en Iran',
    'Analyse des positions russes et chinoises sur la crise iranienne et de leurs interets divergents de ceux de Teheran.',
    'publie',
    '2026-04-20 09:00:00'
FROM categories c WHERE c.slug = 'geopolitique'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'russie-chine-solidarite-iran-analyse');

-- ── ARTICLE 12 ─ Défense ─────────────────────────────────────
INSERT INTO articles
(categorie_id, titre, slug, resume, contenu, image, alt_image, meta_title, meta_description, statut, created_at)
SELECT
    c.id,
    'Israel face au scenario de riposte : la doctrine Begin en question',
    'israel-scenario-riposte-doctrine-begin',
    'Depuis les frappes israeliennes, l etat-major planche sur les scenarios de riposte iranienne. La doctrine Begin — frapper preventivemement toute menace nucleaire existentielle — est desormais gravee dans les faits.',
    '<p>La doctrine Begin, du nom du Premier ministre Menahem Begin qui ordonna la destruction du reacteur nucleaire irakien Osirak en 1981, repose sur un principe simple : Israel ne permettra a aucun Etat hostile de se doter de l arme nucleaire. Cette doctrine, officieusement etendue a la Syrie en 2007 avec la destruction du site d Al-Kibar, vient d etre appliquee a l Iran dans une operation d une toute autre envergure.</p>

<h2>Gerer l apres : la doctrine Dahiya comme menace implicite</h2>
<p>La doctrine Dahiya, elaboree apres la guerre de 2006 contre le Hezbollah, stipule qu Israel est pret a cibler des infrastructures civiles d un adversaire pour briser sa volonte de combattre. Son application au territoire iranien — centrales electriques, raffineries, ports — constituerait une escalade sans precedent que l etat-major israelien agite comme deterrence.</p>

<h2>L epuisement des stocks de precision</h2>
<p>Une preoccupation immediate de l armee israelienne concerne ses stocks de munitions de precision. Les frappes du 15 avril ont consomme une part significative des reserves de missiles air-sol longue portee. Le reapprovisionnement via les Etats-Unis est conditionne a des negociations politiques complexes au sein de l administration americaine.</p>

<h2>La mobilisation des reservistes</h2>
<p>Israel a procede le 17 avril a la mobilisation partielle de reservistes dans plusieurs unites blindees et d infanterie, positionnant des forces supplementaires au nord — face au Liban — et au sud — face a Gaza. Cette mobilisation preventive vise a dissuader une riposte terrestre coordonnee des proxies iraniens.</p>',
    NULL,
    NULL,
    'Israel face a la riposte iranienne : la doctrine Begin en question | Guerre en Iran',
    'Analyse de la strategie israelienne face au risque de riposte iranienne et de l application de la doctrine Begin.',
    'publie',
    '2026-04-20 17:00:00'
FROM categories c WHERE c.slug = 'defense'
                    AND NOT EXISTS (SELECT 1 FROM articles WHERE slug = 'israel-scenario-riposte-doctrine-begin');

-- =============================================================
--  VERIFICATION (optionnel — retourne le count par categorie)
-- =============================================================
-- SELECT c.nom, COUNT(a.id) AS nb_articles
-- FROM categories c
-- LEFT JOIN articles a ON a.categorie_id = c.id AND a.statut = 'publie'
-- GROUP BY c.nom ORDER BY c.nom;
