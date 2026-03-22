<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['utilisateur']['role'],['editeur','administrateur'])) { header('Location: ../accueil.php'); exit(); }
$base_url='../'; $page_title='Catégories';
$msg_ok=$_SESSION['msg_ok']??null; $msg_err=$_SESSION['msg_err']??null;
unset($_SESSION['msg_ok'],$_SESSION['msg_err']);
$cats=$pdo->query('SELECT c.id,c.nom,c.description,COUNT(a.id) AS nb FROM categories c LEFT JOIN articles a ON c.id=a.id_categorie GROUP BY c.id,c.nom,c.description ORDER BY c.nom')->fetchAll();
require_once '../includes/entete.php'; require_once '../includes/menu.php';
?>
<div class="page-hd">
  <div class="page-hd-left">
    <h1><span class="icon icon-lg">label</span> Catégories</h1>
    <span class="page-hd-meta"><?= count($cats) ?> catégorie<?= count($cats)>1?'s':'' ?></span>
  </div>
  <a href="ajouter.php" class="btn btn-primary"><span class="icon icon-sm">add_circle</span> Nouvelle catégorie</a>
</div>
<?php if($msg_ok):?><div class="alert alert-success"><?=e($msg_ok)?></div><?php endif;?>
<?php if($msg_err):?><div class="alert alert-danger"><?=e($msg_err)?></div><?php endif;?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>#</th><th>Nom</th><th>Description</th><th>Articles</th><th>Actions</th></tr></thead>
    <tbody>
      <?php if(empty($cats)):?>
      <tr><td colspan="5"><div class="empty-state"><span class="icon" style="font-size:2.5rem;opacity:.4;display:block;margin-bottom:.5rem">label</span><p>Aucune catégorie.</p></div></td></tr>
      <?php else: foreach($cats as $c):?>
      <tr>
        <td class="td-num"><?=$c['id']?></td>
        <td><strong><?=e($c['nom'])?></strong></td>
        <td class="text-sm text-muted"><?=e(mb_strimwidth($c['description']??'',0,60,'…'))?></td>
        <td><span class="badge badge-nb"><span class="icon icon-sm">article</span> <?=$c['nb']?> art.</span></td>
        <td><div class="row-actions">
          <a href="modifier.php?id=<?=$c['id']?>" class="btn btn-warn btn-xs" title="Modifier"><span class="icon icon-sm">edit</span></a>
          <a href="supprimer.php?id=<?=$c['id']?>" class="btn btn-danger btn-xs" title="Supprimer"><span class="icon icon-sm">delete</span></a>
        </div></td>
      </tr>
      <?php endforeach; endif;?>
    </tbody>
  </table>
</div>
<?php require_once '../includes/pied.php'; ?>
