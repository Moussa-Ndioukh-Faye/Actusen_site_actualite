<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$_role    = $_SESSION['utilisateur']['role'] ?? null;
$_connect = isset($_SESSION['utilisateur']);
$_base    = $base_url ?? '';
$_cur     = basename($_SERVER['PHP_SELF']);
$_cats    = [];
if (isset($pdo)) {
    $_cats = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
}
?>
<nav class="main-nav">
  <div class="container">
    <div class="nav-inner">
      <ul class="nav-links">
        <li>
          <a href="<?= $_base ?>accueil.php" class="<?= $_cur==='accueil.php'?'active':'' ?>">
            <span class="icon icon-sm">home</span> Accueil
          </a>
        </li>
        <?php if ($_cats): ?>
        <li class="has-dropdown">
          <span><span class="icon icon-sm">folder_open</span> Catégories <span class="icon icon-sm" style="font-size:13px">expand_more</span></span>
          <ul class="dropdown-menu">
            <li><a href="<?= $_base ?>accueil.php"><span class="icon icon-sm">article</span> Tous les articles</a></li>
            <?php foreach ($_cats as $c): ?>
            <li><a href="<?= $_base ?>accueil.php?categorie=<?= $c['id'] ?>"><?= e($c['nom']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <?php endif; ?>

        <?php if ($_connect && in_array($_role, ['editeur','administrateur'])): ?>
        <li class="separator"></li>
        <li>
          <a href="<?= $_base ?>articles/liste.php" class="<?= $_cur==='liste.php'?'active':'' ?>">
            <span class="icon icon-sm">edit_note</span> Articles
          </a>
        </li>
        <li>
          <a href="<?= $_base ?>articles/ajouter.php" class="<?= $_cur==='ajouter.php'?'active':'' ?>">
            <span class="icon icon-sm">add_circle</span> Publier
          </a>
        </li>
        <li>
          <a href="<?= $_base ?>categories/liste.php">
            <span class="icon icon-sm">label</span> Catégories
          </a>
        </li>
        <?php endif; ?>

        <?php if ($_connect && $_role === 'administrateur'): ?>
        <li>
          <a href="<?= $_base ?>utilisateurs/liste.php">
            <span class="icon icon-sm">group</span> Membres
          </a>
        </li>
        <?php endif; ?>
      </ul>

      <div class="nav-right">
        <?php if (!$_connect): ?>
          <a href="<?= $_base ?>connexion.php" class="btn btn-nav btn-sm">
            <span class="icon icon-sm">login</span> Connexion
          </a>
        <?php else: ?>
          <a href="<?= $_base ?>deconnexion.php" class="btn btn-nav btn-sm">
            <span class="icon icon-sm">logout</span> Déconnexion
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<main class="main-content">
<div class="container">
