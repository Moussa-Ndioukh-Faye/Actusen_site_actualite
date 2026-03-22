<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['utilisateur']['role'],['editeur','administrateur'])) { header('Location: ../accueil.php'); exit(); }
$base_url='../'; $page_title='Articles';
$msg_ok = $_SESSION['msg_ok'] ?? null; unset($_SESSION['msg_ok']);
$articles = $pdo->query('SELECT a.id,a.titre,a.date_publication,c.nom AS cat_nom,u.prenom,u.nom AS auteur_nom FROM articles a JOIN categories c ON a.id_categorie=c.id JOIN utilisateurs u ON a.id_auteur=u.id ORDER BY a.date_publication DESC')->fetchAll();
require_once '../includes/entete.php'; require_once '../includes/menu.php';
?>
<div class="page-hd">
  <div class="page-hd-left">
    <h1><span class="icon icon-lg">edit_note</span> Gestion des articles</h1>
    <span class="page-hd-meta"><?= count($articles) ?> article<?= count($articles)>1?'s':'' ?></span>
  </div>
  <a href="ajouter.php" class="btn btn-primary"><span class="icon icon-sm">add_circle</span> Nouvel article</a>
</div>
<?php if ($msg_ok): ?><div class="alert alert-success"><?= e($msg_ok) ?></div><?php endif; ?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>#</th><th>Titre</th><th>Catégorie</th><th>Auteur</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      <?php if (empty($articles)): ?>
      <tr><td colspan="6"><div class="empty-state"><span class="icon" style="font-size:2.5rem;opacity:.4;display:block;margin-bottom:.5rem">article</span><p>Aucun article.</p></div></td></tr>
      <?php else: foreach ($articles as $a): ?>
      <tr>
        <td class="td-num"><?= $a['id'] ?></td>
        <td><a href="detail.php?id=<?= $a['id'] ?>" style="font-weight:600;color:var(--ink)"><?= e(mb_strimwidth($a['titre'],0,60,'…')) ?></a></td>
        <td><span class="cat-tag"><?= e($a['cat_nom']) ?></span></td>
        <td class="text-sm text-muted"><?= e($a['prenom'].' '.$a['auteur_nom']) ?></td>
        <td class="text-sm text-muted mono"><?= fmt_date($a['date_publication']) ?></td>
        <td><div class="row-actions">
          <a href="detail.php?id=<?= $a['id'] ?>" class="btn btn-ghost btn-xs" title="Voir"><span class="icon icon-sm">visibility</span></a>
          <a href="modifier.php?id=<?= $a['id'] ?>" class="btn btn-warn btn-xs" title="Modifier"><span class="icon icon-sm">edit</span></a>
          <a href="supprimer.php?id=<?= $a['id'] ?>" class="btn btn-danger btn-xs" title="Supprimer"><span class="icon icon-sm">delete</span></a>
        </div></td>
      </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php require_once '../includes/pied.php'; ?>
