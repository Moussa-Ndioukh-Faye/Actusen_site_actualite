<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$_role    = $_SESSION['utilisateur']['role']   ?? null;
$_connect = isset($_SESSION['utilisateur']);
$_base    = $base_url ?? '';
$_title   = isset($page_title) ? e($page_title) . ' — ActuSen' : 'ActuSen · L\'actualité sénégalaise';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= $_title ?></title>
  <link rel="stylesheet" href="<?= $_base ?>css/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap">
</head>
<body>
<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <div class="logo-wrap">
        <a href="<?= $_base ?>accueil.php" class="logo">Actu<em>Sen</em></a>
        <span class="logo-tagline">L'actualité sénégalaise en temps réel</span>
      </div>
      <span class="header-date" id="hd-date"></span>
      <div class="header-actions">
        <?php if ($_connect): ?>
          <div class="user-chip">
            <div class="user-chip-avatar"><?= initiales($_SESSION['utilisateur']['prenom'], $_SESSION['utilisateur']['nom']) ?></div>
            <?= e($_SESSION['utilisateur']['prenom']) ?>
            <span class="role-pill <?= e($_role) ?>"><?= e($_role) ?></span>
          </div>
          <a href="<?= $_base ?>deconnexion.php" class="btn btn-nav btn-sm">
            <span class="icon icon-sm">logout</span> Déconnexion
          </a>
        <?php else: ?>
          <a href="<?= $_base ?>connexion.php" class="btn btn-nav btn-sm">
            <span class="icon icon-sm">login</span> Connexion
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>
