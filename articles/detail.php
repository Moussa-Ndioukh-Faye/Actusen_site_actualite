<?php
require_once '../config/connexion.php';
$base_url='../';
$id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if (!$id||$id<=0) { header('Location: ../accueil.php'); exit(); }
$stmt = $pdo->prepare('SELECT a.*,c.nom AS cat_nom,c.id AS cat_id,u.nom AS auteur_nom,u.prenom AS auteur_prenom FROM articles a JOIN categories c ON a.id_categorie=c.id JOIN utilisateurs u ON a.id_auteur=u.id WHERE a.id=:id');
$stmt->execute([':id'=>$id]);
$art = $stmt->fetch();
if (!$art) { header('Location: ../accueil.php'); exit(); }
$page_title = $art['titre'];
$peut_gerer = isset($_SESSION['utilisateur']) && in_array($_SESSION['utilisateur']['role'],['editeur','administrateur']);
$autres = $pdo->prepare('SELECT a.id,a.titre,a.date_publication FROM articles a WHERE a.id_categorie=:cat AND a.id!=:id ORDER BY a.date_publication DESC LIMIT 4');
$autres->execute([':cat'=>$art['cat_id'],':id'=>$id]);
$autres_arts = $autres->fetchAll();
require_once '../includes/entete.php';
require_once '../includes/menu.php';
?>
<div class="breadcrumb">
  <a href="../accueil.php"><span class="icon icon-sm">home</span> Accueil</a>
  <span class="breadcrumb-sep">›</span>
  <a href="../accueil.php?categorie=<?= $art['cat_id'] ?>"><?= e($art['cat_nom']) ?></a>
  <span class="breadcrumb-sep">›</span>
  <span><?= e(mb_strimwidth($art['titre'],0,50,'…')) ?></span>
</div>

<div class="layout-sidebar">
  <article>
    <div class="detail-header">
      <div class="art-meta" style="margin-bottom:.75rem">
        <a href="../accueil.php?categorie=<?= $art['cat_id'] ?>" class="cat-tag"><?= e($art['cat_nom']) ?></a>
      </div>
      <h1><?= e($art['titre']) ?></h1>
      <div class="detail-meta">
        <span>
          <div class="art-author-dot" style="width:28px;height:28px;font-size:11px"><?= initiales($art['auteur_prenom'],$art['auteur_nom']) ?></div>
          <strong><?= e($art['auteur_prenom'].' '.$art['auteur_nom']) ?></strong>
        </span>
        <span><span class="icon icon-sm">schedule</span> <?= fmt_date($art['date_publication'], true) ?></span>
        <span><span class="icon icon-sm">category</span> <?= e($art['cat_nom']) ?></span>
      </div>
    </div>

    <div class="detail-content detail-dropcap"><?= e($art['contenu']) ?></div>

    <div class="detail-actions">
      <a href="../accueil.php" class="btn btn-ghost"><span class="icon icon-sm">arrow_back</span> Retour</a>
      <a href="../accueil.php?categorie=<?= $art['cat_id'] ?>" class="btn btn-ghost btn-sm">
        <span class="icon icon-sm">folder_open</span> <?= e($art['cat_nom']) ?>
      </a>
      <?php if ($peut_gerer): ?>
      <a href="modifier.php?id=<?= $art['id'] ?>" class="btn btn-warn btn-sm">
        <span class="icon icon-sm">edit</span> Modifier
      </a>
      <a href="supprimer.php?id=<?= $art['id'] ?>" class="btn btn-danger btn-sm">
        <span class="icon icon-sm">delete</span> Supprimer
      </a>
      <?php endif; ?>
    </div>
  </article>

  <aside class="sidebar">
    <div class="sidebar-widget">
      <div class="widget-head"><span class="icon icon-sm widget-head-icon">auto_stories</span> Même catégorie</div>
      <div class="widget-body">
        <?php if ($autres_arts): ?>
        <ul style="list-style:none">
          <?php foreach ($autres_arts as $a): ?>
          <li style="padding:7px 6px;border-bottom:1px solid var(--border)">
            <a href="detail.php?id=<?= $a['id'] ?>" style="font-size:13px;color:var(--ink-soft);text-decoration:none;line-height:1.4;display:block">
              <?= e(mb_strimwidth($a['titre'],0,65,'…')) ?>
            </a>
            <span style="font-size:11px;color:var(--muted)"><?= fmt_date($a['date_publication']) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p style="font-size:13px;color:var(--muted);padding:.5rem">Aucun autre article dans cette catégorie.</p>
        <?php endif; ?>
      </div>
    </div>
    <div class="sidebar-widget">
      <div class="widget-head"><span class="icon icon-sm widget-head-icon">info</span> Informations</div>
      <div class="widget-body" style="padding:1rem">
        <div style="font-size:13px;color:var(--ink-soft);line-height:2.1">
          <div><strong>Catégorie :</strong> <?= e($art['cat_nom']) ?></div>
          <div><strong>Auteur :</strong> <?= e($art['auteur_prenom'].' '.$art['auteur_nom']) ?></div>
          <div><strong>Publié le :</strong> <?= fmt_date($art['date_publication']) ?></div>
        </div>
      </div>
    </div>
  </aside>
</div>
<?php require_once '../includes/pied.php'; ?>
