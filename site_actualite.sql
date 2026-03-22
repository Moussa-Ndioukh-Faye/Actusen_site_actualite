-- ============================================================
-- ActuSen — Script SQL complet
-- ESP · Département Génie Informatique · L2 GLSI
-- ============================================================

CREATE DATABASE IF NOT EXISTS site_actualite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE site_actualite;

DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS categories;

CREATE TABLE categories (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nom         VARCHAR(100) NOT NULL UNIQUE,
  description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE utilisateurs (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nom            VARCHAR(100) NOT NULL,
  prenom         VARCHAR(100) NOT NULL,
  login          VARCHAR(100) NOT NULL UNIQUE,
  mot_de_passe   VARCHAR(255) NOT NULL,
  role           ENUM('editeur','administrateur') NOT NULL,
  date_creation  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE articles (
  id                 INT AUTO_INCREMENT PRIMARY KEY,
  titre              VARCHAR(255)  NOT NULL,
  description_courte VARCHAR(500)  NOT NULL,
  contenu            TEXT          NOT NULL,
  date_publication   DATETIME      DEFAULT CURRENT_TIMESTAMP,
  id_categorie       INT           NOT NULL,
  id_auteur          INT           NOT NULL,
  FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE RESTRICT,
  FOREIGN KEY (id_auteur)    REFERENCES utilisateurs(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---- Catégories ----
INSERT INTO categories (nom, description) VALUES
('Technologie', 'Informatique, IA, startups et innovations numériques'),
('Sport',       'Football, basketball, athlétisme et toutes les disciplines'),
('Politique',   'Vie politique nationale et internationale'),
('Éducation',   'Système éducatif, bourses, examens et formation'),
('Culture',     'Arts, musique, cinéma et événements culturels');

-- ---- Utilisateurs (mot de passe : admin123 pour tous) ----
INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
('Diallo',  'Moussa',  'admin',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrateur'),
('Fall',    'Fatou',   'editeur1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editeur'),
('Ndiaye',  'Aminata', 'editeur2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'editeur');

-- ---- Articles de test ----
INSERT INTO articles (titre, description_courte, contenu, id_categorie, id_auteur) VALUES
('L\'intelligence artificielle révolutionne le monde du travail',
 'Les experts s\'accordent : l\'IA transformera profondément les métiers d\'ici 2030. Tour d\'horizon des perspectives pour les jeunes diplômés africains.',
 'L\'intelligence artificielle est en train de transformer radicalement le monde du travail. Des secteurs entiers voient leurs processus automatisés, tandis que de nouveaux métiers émergent à une vitesse sans précédent.\n\nLes études récentes indiquent que près de 40 % des emplois actuels pourraient être impactés d\'ici 2030, certains disparaissant, d\'autres se réinventant. Pour les jeunes diplômés sénégalais, la situation est paradoxale : d\'un côté, certains postes se raréfient ; de l\'autre, des milliers d\'opportunités en data science, en machine learning et en développement d\'applications IA voient le jour chaque année.\n\nLes entreprises sénégalaises commencent elles aussi à intégrer ces technologies, notamment dans les secteurs bancaire et télécom. L\'École Supérieure Polytechnique et l\'UCAD adaptent d\'ailleurs leurs programmes pour préparer leurs étudiants à ces nouvelles réalités.\n\nIl est donc crucial pour les étudiants en informatique de se former dès maintenant aux outils de l\'IA, au machine learning et à l\'analyse de données.',
 1, 1),

('CAN 2025 : le Sénégal qualifié pour les demi-finales',
 'Les Lions de la Teranga ont réalisé une performance exceptionnelle en écrasant leur adversaire 3-0. Retour sur les temps forts d\'une soirée historique.',
 'Le Sénégal a validé son ticket pour les demi-finales de la CAN 2025 avec une victoire convaincante. Dès la 12e minute, le capitaine ouvrait le score sur un corner parfaitement botté.\n\nLa seconde période a vu les Lions pousser encore plus fort, inscrivant deux autres buts qui ont définitivement scellé la qualification. L\'équipe nationale affrontera le Maroc dans quatre jours.\n\nLe sélectionneur a salué la cohésion exceptionnelle de son groupe et l\'engagement sans faille de chaque joueur. Les supporters sénégalais peuvent légitimement rêver d\'un deuxième sacre continental consécutif, ce qui serait une première dans l\'histoire du football africain.\n\nLa prochaine rencontre est prévue au stade Léopold Sédar Senghor, et les billets se sont écoulés en moins de 24 heures.',
 2, 2),

('Réforme du système éducatif : les grandes lignes du nouveau programme',
 'Le ministère de l\'Éducation nationale a présenté les contours de la réforme qui entrera en vigueur dès la prochaine rentrée scolaire.',
 'Le gouvernement sénégalais a officiellement présenté sa nouvelle réforme éducative, qui concernera l\'ensemble des niveaux d\'enseignement. La nouveauté phare est l\'introduction obligatoire de l\'informatique et du numérique dès le cycle moyen.\n\nLes langues nationales seront également mieux intégrées dans les programmes, avec des cours dispensés en wolof et en pulaar dans certaines matières expérimentales. Cette mesure vise à réduire les inégalités scolaires entre zones urbaines et rurales.\n\nPour l\'enseignement supérieur, un accent particulier est mis sur les filières professionnalisantes et l\'entrepreneuriat. Les bourses d\'études seront revalorisées de 15 % dès la rentrée prochaine pour accompagner ces changements structurels.\n\nLes syndicats d\'enseignants ont globalement accueilli positivement cette réforme, tout en demandant des garanties sur la formation des enseignants aux nouveaux programmes.',
 4, 1),

('Festival culturel de Dakar : trois jours de fête panafricaine',
 'La 12e édition du festival culturel a réuni des artistes venus de 25 pays africains autour du thème de l\'unité et du dialogue des cultures.',
 'La place de l\'Indépendance de Dakar a vibré durant trois jours au rythme du Festival Culturel International. Cette 12e édition, placée sous le signe de la fraternité africaine, a accueilli plus de 50 000 visiteurs venus de tout le continent.\n\nDes expositions de peinture, de sculpture et d\'artisanat traditionnel ont côtoyé des concerts de musique contemporaine et de jazz africain. Le grand prix de la peinture a été décerné à une jeune artiste sénégalaise de 24 ans, diplômée des Beaux-Arts de Dakar, pour une œuvre monumentale sur la diaspora.\n\nLa cérémonie de clôture, présidée par les autorités culturelles, a été l\'occasion d\'annoncer que Dakar accueillera également l\'édition 2026, confirmant ainsi son statut de capitale culturelle de l\'Afrique de l\'Ouest.',
 5, 2),

('Budget 2026 : les grandes orientations économiques dévoilées',
 'Le projet de loi de finances prévoit une hausse significative des investissements dans les infrastructures, l\'éducation et la santé publique.',
 'Le ministre des Finances a présenté ce matin en Conseil des ministres le projet de loi de finances pour 2026. Le budget global s\'élève à 7 200 milliards de francs CFA, soit une hausse de 12 % par rapport à 2025.\n\nLes grands axes prioritaires sont : l\'infrastructure routière avec 850 milliards alloués, l\'éducation et la formation professionnelle avec 1 200 milliards, et la santé avec 680 milliards. Le numérique fait son entrée dans les priorités nationales avec 320 milliards dédiés à la transformation digitale de l\'administration.\n\nL\'opposition a salué l\'effort sur l\'éducation tout en pointant l\'insuffisance des mesures en faveur de l\'emploi des jeunes. Le projet sera soumis au vote de l\'Assemblée nationale dans les prochaines semaines.',
 3, 3),

('PaySen lève 2 millions USD pour son application de paiement mobile',
 'La startup dakaroise spécialisée dans les paiements mobiles annonce une levée de fonds record pour accélérer son expansion en Afrique de l\'Ouest.',
 'La startup sénégalaise PaySen vient de boucler une levée de fonds de 2 millions de dollars auprès d\'investisseurs africains et européens. Fondée en 2023 par trois ingénieurs diplômés de l\'ESP, l\'application compte déjà 150 000 utilisateurs actifs à travers le Sénégal.\n\nPaySen permet aux particuliers et aux commerçants d\'effectuer des paiements, des transferts et de gérer leur épargne entièrement depuis leur smartphone, sans avoir besoin d\'un compte bancaire traditionnel. Cette innovation financière s\'inscrit dans la stratégie nationale d\'inclusion financière.\n\nCette levée de fonds permettra à la startup d\'étendre ses services au Mali, en Côte d\'Ivoire et au Burkina Faso d\'ici la fin de l\'année. Une belle réussite pour l\'écosystème tech sénégalais et une fierté pour l\'École Supérieure Polytechnique.',
 1, 2),

('BAC 2025 : taux de réussite historique de 52,3 %',
 'Un record national ! Les filières scientifiques tirent leur épingle du jeu avec 61 % de taux de réussite. Les inscriptions universitaires sont ouvertes.',
 'L\'Office du Baccalauréat a publié les résultats définitifs de la session 2025. Avec un taux de réussite global de 52,3 %, cette année marque un tournant dans l\'histoire de l\'éducation sénégalaise.\n\nLes filières scientifiques (S1, S2, S3) affichent les meilleurs résultats avec 61 % de taux de réussite, suivies des filières techniques à 54 % et des filières littéraires à 48 %. Les régions de Dakar et Thiès obtiennent les meilleurs résultats régionaux.\n\nLe ministre de l\'Éducation a salué le travail remarquable des enseignants et des élèves, attribuant ces bons résultats à la réforme des méthodes pédagogiques initiée en 2023 et à l\'introduction du numérique en classe.\n\nLes inscriptions dans les universités publiques pour la rentrée de septembre sont ouvertes depuis ce matin sur le portail campusen.sn.',
 4, 1),

('Championnat national de basketball : Dakar Université Champion',
 'Les joueurs de l\'Université de Dakar remportent le titre pour la troisième année consécutive après une finale haletante contre Saint-Louis.',
 'La salle Marius Ndiaye a vécu une soirée inoubliable. L\'équipe de basketball de l\'Université de Dakar a conservé son titre national en s\'imposant en finale 78 à 72 face à l\'équipe de Saint-Louis.\n\nMenée de 8 points à la mi-temps, Dakar Université a réalisé un comeback spectaculaire en seconde période, portée par un Ousmane Sy en état de grâce avec 24 points, 8 rebonds et 5 passes décisives.\n\nCette victoire qualifie l\'équipe pour le championnat universitaire panafricain qui se tiendra à Abidjan en juin prochain. Une belle opportunité de représenter le Sénégal sur la scène continentale. Le coach a promis une préparation intensive de six semaines pour ce rendez-vous continental.',
 2, 3);
