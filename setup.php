<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>ActuSen — Installation</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&display=swap">
  <style>
    :root{--navy:#0d1b2a;--gold:#c9a84c;--success:#16a34a;--danger:#c0392b;--warn:#d97706;}
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'DM Sans',sans-serif;background:#f0f2f5;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem}
    .icon{font-family:'Material Symbols Rounded';font-style:normal;font-size:20px;line-height:1;display:inline-flex;align-items:center;vertical-align:middle}
    .card{background:white;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.12);overflow:hidden;width:100%;max-width:560px}
    .card-top{background:var(--navy);padding:2rem;text-align:center;position:relative;overflow:hidden}
    .card-top::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 70% 0%,rgba(201,168,76,.2),transparent 60%)}
    .logo{font-family:'DM Sans',sans-serif;font-size:2rem;font-weight:900;color:white;position:relative}
    .logo em{font-style:italic;color:var(--gold)}
    .card-top p{color:rgba(255,255,255,.55);font-size:13px;margin-top:.4rem;position:relative}
    .card-body{padding:2rem}
    .step{display:flex;gap:.75rem;padding:.85rem 1rem;border-radius:10px;margin-bottom:.6rem;border:1px solid #e5e7eb;background:#f9fafb;align-items:flex-start}
    .step-icon{width:32px;height:32px;border-radius:50%;display:grid;place-items:center;flex-shrink:0;font-size:14px;font-weight:700}
    .step-icon.wait   {background:#e5e7eb;color:#6b7280}
    .step-icon.ok     {background:#dcfce7;color:var(--success)}
    .step-icon.err    {background:#fee2e2;color:var(--danger)}
    .step-icon.running{background:#fef3c7;color:var(--warn)}
    .step-title{font-weight:600;font-size:14px;margin-bottom:2px}
    .step-desc{font-size:12.5px;color:#6b7280;line-height:1.5}
    .step-desc code{background:#f1f5f9;padding:1px 5px;border-radius:4px;font-size:11.5px}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;border:none;transition:all .15s;text-decoration:none;width:100%;justify-content:center;margin-top:1.25rem}
    .btn-go{background:var(--gold);color:var(--navy)}
    .btn-go:hover{background:#d4a830}
    .btn-login{background:var(--navy);color:white}
    .btn-login:hover{background:#1e3048}
    .creds{background:var(--navy);border-radius:10px;padding:1.1rem 1.25rem;margin-top:1rem}
    .creds p{font-size:13px;font-weight:600;color:rgba(255,255,255,.6);margin-bottom:.6rem}
    .cred-row{display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid rgba(255,255,255,.07);font-size:13.5px}
    .cred-row:last-child{border-bottom:none}
    .cred-label{color:rgba(255,255,255,.55)}
    .cred-val{color:white;font-weight:700;font-family:monospace}
    .alert{padding:12px 16px;border-radius:8px;font-size:13.5px;margin-bottom:1rem;display:flex;gap:8px;align-items:flex-start;border-left:4px solid}
    .alert-err{background:#fef2f2;border-color:var(--danger);color:#7f1d1d}
    .alert-ok{background:#f0fdf4;border-color:var(--success);color:#14532d}
  </style>
</head>
<body>
<?php
$host   = 'localhost';
$dbname = 'site_actualite';
$user   = 'root';
$pass   = '';        // Vide sur XAMPP par défaut

$steps  = [];
$done   = false;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---- Étape 1 : Connexion MySQL sans BDD ----
    try {
        $pdo0 = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $steps[] = ['ok', 'Connexion MySQL', 'Connexion au serveur MySQL réussie.'];
    } catch (PDOException $e) {
        $steps[] = ['err', 'Connexion MySQL', 'Impossible de se connecter : ' . $e->getMessage()];
        $error = true;
    }

    if (!$error) {
        // ---- Étape 2 : Créer la base de données ----
        try {
            $pdo0->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo0->exec("USE `$dbname`");
            $steps[] = ['ok', 'Base de données', "La base <code>$dbname</code> a été créée ou existe déjà."];
        } catch (PDOException $e) {
            $steps[] = ['err', 'Base de données', 'Erreur : ' . $e->getMessage()];
            $error = true;
        }
    }

    if (!$error) {
        // ---- Étape 3 : Créer les tables ----
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS categories (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nom VARCHAR(100) NOT NULL UNIQUE,
                    description TEXT
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS utilisateurs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nom VARCHAR(100) NOT NULL,
                    prenom VARCHAR(100) NOT NULL,
                    login VARCHAR(100) NOT NULL UNIQUE,
                    mot_de_passe VARCHAR(255) NOT NULL,
                    role ENUM('editeur','administrateur') NOT NULL,
                    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            $pdo->exec("
                CREATE TABLE IF NOT EXISTS articles (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    titre VARCHAR(255) NOT NULL,
                    description_courte VARCHAR(500) NOT NULL,
                    contenu TEXT NOT NULL,
                    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
                    id_categorie INT NOT NULL,
                    id_auteur INT NOT NULL,
                    FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE RESTRICT,
                    FOREIGN KEY (id_auteur) REFERENCES utilisateurs(id) ON DELETE RESTRICT
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            $steps[] = ['ok', 'Tables', 'Tables <code>categories</code>, <code>utilisateurs</code> et <code>articles</code> créées avec succès.'];
        } catch (PDOException $e) {
            $steps[] = ['err', 'Tables', 'Erreur : ' . $e->getMessage()];
            $error = true;
        }
    }

    if (!$error) {
        // ---- Étape 4 : Insérer les catégories ----
        try {
            $nb_cats = (int)$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
            if ($nb_cats === 0) {
                $pdo->exec("INSERT INTO categories (nom, description) VALUES
                    ('Technologie', 'Informatique, IA, startups et innovations numériques'),
                    ('Sport',       'Football, basketball, athlétisme et toutes les disciplines'),
                    ('Politique',   'Vie politique nationale et internationale'),
                    ('Éducation',   'Système éducatif, bourses, examens et formation'),
                    ('Culture',     'Arts, musique, cinéma et événements culturels')
                ");
                $steps[] = ['ok', 'Catégories', '5 catégories insérées.'];
            } else {
                $steps[] = ['ok', 'Catégories', "$nb_cats catégorie(s) déjà présente(s) — aucune modification."];
            }
        } catch (PDOException $e) {
            $steps[] = ['err', 'Catégories', 'Erreur : ' . $e->getMessage()];
        }
    }

    if (!$error) {
        // ---- Étape 5 : Créer les comptes avec vrais hashs PHP ----
        try {
            $nb_users = (int)$pdo->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
            if ($nb_users === 0) {
                $hash_admin    = password_hash('admin123',    PASSWORD_DEFAULT);
                $hash_editeur1 = password_hash('editeur123', PASSWORD_DEFAULT);
                $hash_editeur2 = password_hash('editeur123', PASSWORD_DEFAULT);

                $ins = $pdo->prepare("INSERT INTO utilisateurs (nom,prenom,login,mot_de_passe,role) VALUES(:n,:p,:l,:m,:r)");

                $ins->execute([':n'=>'Diallo', ':p'=>'Moussa',  ':l'=>'admin',    ':m'=>$hash_admin,    ':r'=>'administrateur']);
                $ins->execute([':n'=>'Fall',   ':p'=>'Fatou',   ':l'=>'editeur1', ':m'=>$hash_editeur1, ':r'=>'editeur']);
                $ins->execute([':n'=>'Ndiaye', ':p'=>'Aminata', ':l'=>'editeur2', ':m'=>$hash_editeur2, ':r'=>'editeur']);

                $steps[] = ['ok', 'Comptes utilisateurs',
                    'Admin : <code>admin</code> / <code>admin123</code> &nbsp;|&nbsp; Éditeurs : <code>editeur1</code> ou <code>editeur2</code> / <code>editeur123</code>'];
            } else {
                // Remettre à zéro les mots de passe si souhaité
                $hash_admin    = password_hash('admin123',    PASSWORD_DEFAULT);
                $hash_editeur  = password_hash('editeur123', PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE utilisateurs SET mot_de_passe=:m WHERE login='admin'")->execute([':m'=>$hash_admin]);
                $pdo->prepare("UPDATE utilisateurs SET mot_de_passe=:m WHERE login IN ('editeur1','editeur2')")->execute([':m'=>$hash_editeur]);
                $steps[] = ['ok', 'Comptes utilisateurs', "$nb_users compte(s) déjà présent(s) — mots de passe réinitialisés."];
            }
        } catch (PDOException $e) {
            $steps[] = ['err', 'Comptes utilisateurs', 'Erreur : ' . $e->getMessage()];
            $error = true;
        }
    }

    if (!$error) {
        // ---- Étape 6 : Insérer les articles de test ----
        try {
            $nb_arts = (int)$pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn();
            if ($nb_arts === 0) {
                $id_admin = (int)$pdo->query("SELECT id FROM utilisateurs WHERE login='admin' LIMIT 1")->fetchColumn();
                $cats_ids = [];
                foreach ($pdo->query('SELECT id FROM categories ORDER BY id')->fetchAll() as $row) {
                    $cats_ids[] = $row['id'];
                }
                if ($id_admin && count($cats_ids) >= 3) {
                    $ins = $pdo->prepare("INSERT INTO articles (titre,description_courte,contenu,id_categorie,id_auteur) VALUES(:t,:d,:c,:cat,:a)");
                    $ins->execute([':t'=>"L'intelligence artificielle révolutionne le monde du travail",':d'=>"Les experts s'accordent : l'IA transformera les métiers d'ici 2030. Quelles perspectives pour les diplômés africains ?",':c'=>"L'intelligence artificielle transforme radicalement le monde du travail. Des secteurs entiers voient leurs processus automatisés tandis que de nouveaux métiers émergent.\n\nLes filières informatique de l'ESP et de l'UCAD adaptent leurs programmes pour préparer les étudiants à ces nouvelles réalités. Il est crucial de se former dès maintenant aux outils de l'IA et au machine learning.",':cat'=>$cats_ids[0],':a'=>$id_admin]);
                    $ins->execute([':t'=>"BAC 2025 : taux de réussite historique de 52,3 %",':d'=>"Un record national ! Les filières scientifiques atteignent 61 % de taux de réussite. Les inscriptions universitaires sont ouvertes.",':c'=>"L'Office du Baccalauréat a publié les résultats définitifs de la session 2025. Avec 52,3 % de taux de réussite global, cette année marque un tournant historique.\n\nLes filières scientifiques (S1, S2, S3) affichent 61 % de réussite. Les inscriptions dans les universités publiques pour septembre sont ouvertes sur le portail campusen.sn.",':cat'=>$cats_ids[3],':a'=>$id_admin]);
                    $ins->execute([':t'=>"Budget 2026 : les grandes orientations économiques",':d'=>"Le projet de loi de finances prévoit 7 200 milliards FCFA avec un accent fort sur l'éducation et les infrastructures.",':c'=>"Le ministre des Finances a présenté le projet de loi de finances pour 2026. Le budget global s'élève à 7 200 milliards de francs CFA, en hausse de 12 %.\n\nPriorités : infrastructure routière (850 milliards), éducation (1 200 milliards), santé (680 milliards) et transformation numérique (320 milliards).",':cat'=>$cats_ids[2],':a'=>$id_admin]);
                    $ins->execute([':t'=>"CAN 2025 : le Sénégal qualifié pour les demi-finales",':d'=>"Les Lions de la Teranga s'imposent 3-0 et valident leur ticket pour le dernier carré. Retour sur une soirée historique.",':c'=>"Le Sénégal a validé son ticket pour les demi-finales de la CAN 2025 avec une victoire convaincante 3 buts à 0.\n\nDès la 12e minute, le capitaine ouvrait le score sur corner. La seconde période a vu les Lions inscrire deux autres buts. L'équipe affrontera le Maroc en demi-finale dans quatre jours.",':cat'=>$cats_ids[1],':a'=>$id_admin]);
                    $ins->execute([':t'=>"Festival culturel de Dakar : trois jours de fête panafricaine",':d'=>"La 12e édition a réuni des artistes de 25 pays africains. Plus de 50 000 visiteurs autour du thème de l'unité continentale.",':c'=>"La place de l'Indépendance de Dakar a vibré durant trois jours. Cette 12e édition a accueilli plus de 50 000 visiteurs venus de tout le continent.\n\nLe grand prix de la peinture a été décerné à une jeune artiste sénégalaise de 24 ans. Dakar accueillera aussi l'édition 2026, confirmant son statut de capitale culturelle d'Afrique de l'Ouest.",':cat'=>$cats_ids[4],':a'=>$id_admin]);
                    $steps[] = ['ok', 'Articles de test', '5 articles d\'exemple insérés.'];
                }
            } else {
                $steps[] = ['ok', 'Articles de test', "$nb_arts article(s) déjà présent(s) — aucune modification."];
            }
        } catch (PDOException $e) {
            $steps[] = ['ok', 'Articles de test', 'Ignoré — table non vide.'];
        }

        $done = true;
    }
}
?>

<div class="card">
  <div class="card-top">
    <div class="logo">Actu<em>Sen</em></div>
    <p>Assistant d'installation — ESP · Génie Informatique · L2 GLSI</p>
  </div>

  <div class="card-body">

    <?php if (!$_POST): ?>
      <!-- Formulaire initial -->
      <p style="font-size:14px;color:#4b5563;margin-bottom:1.25rem;line-height:1.6">
        Ce fichier va créer la base de données, les tables et les comptes utilisateurs avec des mots de passe correctement hashés en PHP.
      </p>

      <div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:.85rem 1rem;margin-bottom:1.25rem;font-size:13px;color:#78350f">
        <strong>Prérequis :</strong> XAMPP / WAMP lancé avec Apache + MySQL actifs.
        Ce script se connecte avec <code>root</code> / <em>mot de passe vide</em> (par défaut XAMPP).
      </div>

      <form method="POST">
        <button type="submit" class="btn btn-go">
          <span class="icon" style="font-family:'Material Symbols Rounded';font-style:normal">rocket_launch</span>
          Lancer l'installation
        </button>
      </form>

    <?php elseif ($done): ?>
      <!-- Succès -->
      <div class="alert alert-ok">
        <span class="icon" style="font-family:'Material Symbols Rounded';font-style:normal;font-size:18px">check_circle</span>
        <div><strong>Installation réussie !</strong> Votre site est prêt à l'emploi.</div>
      </div>

      <?php foreach ($steps as $s): ?>
      <div class="step">
        <div class="step-icon <?= $s[0] ?>">
          <?php if ($s[0]==='ok'): ?><span class="icon" style="font-family:'Material Symbols Rounded';font-style:normal;font-size:16px">check</span>
          <?php else: ?><span class="icon" style="font-family:'Material Symbols Rounded';font-style:normal;font-size:16px">close</span>
          <?php endif; ?>
        </div>
        <div>
          <div class="step-title"><?= $s[1] ?></div>
          <div class="step-desc"><?= $s[2] ?></div>
        </div>
      </div>
      <?php endforeach; ?>

      <div class="creds">
        <p>Identifiants de connexion :</p>
        <div class="cred-row"><span class="cred-label">Admin login</span><span class="cred-val">admin</span></div>
        <div class="cred-row"><span class="cred-label">Admin mot de passe</span><span class="cred-val">admin123</span></div>
        <div class="cred-row"><span class="cred-label">Éditeur login</span><span class="cred-val">editeur1</span></div>
        <div class="cred-row"><span class="cred-label">Éditeur mot de passe</span><span class="cred-val">editeur123</span></div>
      </div>

      <a href="accueil.php" class="btn btn-login">
        <span class="icon" style="font-family:'Material Symbols Rounded';font-style:normal">arrow_forward</span>
        Aller sur le site
      </a>
      <a href="connexion.php" class="btn btn-go" style="margin-top:.5rem">
        <span class="icon" style="font-family:'Material Symbols Rounded';font-style:normal">login</span>
        Se connecter
      </a>

    <?php else: ?>
      <!-- Erreur -->
      <div class="alert alert-err">
        <span class="icon" style="font-family:'Material Symbols Rounded';font-style:normal;font-size:18px">error</span>
        <div><strong>Erreur lors de l'installation.</strong> Vérifiez que MySQL est bien démarré dans XAMPP.</div>
      </div>
      <?php foreach ($steps as $s): ?>
      <div class="step">
        <div class="step-icon <?= $s[0] ?>">
          <?php if ($s[0]==='ok'): ?><span class="icon" style="font-family:'Material Symbols Rounded';font-style:normal;font-size:16px">check</span>
          <?php else: ?><span class="icon" style="font-family:'Material Symbols Rounded';font-style:normal;font-size:16px">close</span>
          <?php endif; ?>
        </div>
        <div>
          <div class="step-title"><?= $s[1] ?></div>
          <div class="step-desc"><?= $s[2] ?></div>
        </div>
      </div>
      <?php endforeach; ?>
      <form method="POST" style="margin-top:1rem">
        <button type="submit" class="btn btn-go">
          <span class="icon" style="font-family:'Material Symbols Rounded';font-style:normal">refresh</span>
          Réessayer
        </button>
      </form>
    <?php endif; ?>

  </div>
</div>

</body>
</html>
