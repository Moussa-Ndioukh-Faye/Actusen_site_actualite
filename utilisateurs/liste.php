<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if ($_SESSION['utilisateur']['role'] !== 'administrateur') { header('Location: ../accueil.php'); exit(); }
$base_url='../'; $page_title='Membres';
$msg_ok=$_SESSION['msg_ok']??null; $msg_err=$_SESSION['msg_err']??null;
unset($_SESSION['msg_ok'],$_SESSION['msg_err']);
$users=$pdo->query('SELECT id,nom,prenom,login,role,date_creation FROM utilisateurs ORDER BY date_creation DESC')->fetchAll();
require_once '../includes/entete.php'; require_once '../includes/menu.php';
?>
<div class="page-hd">
  <div class="page-hd-left">
    <h1><span class="icon icon-lg">group</span> Gestion des membres</h1>
    <span class="page-hd-meta"><?= count($users) ?> compte<?= count($users)>1?'s':'' ?> enregistré<?= count($users)>1?'s':'' ?></span>
  </div>
  <a href="ajouter.php" class="btn btn-primary"><span class="icon icon-sm">person_add</span> Nouveau membre</a>
</div>
<?php if($msg_ok):?><div class="alert alert-success"><?=e($msg_ok)?></div><?php endif;?>
<?php if($msg_err):?><div class="alert alert-danger"><?=e($msg_err)?></div><?php endif;?>
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>#</th><th>Nom complet</th><th>Identifiant</th><th>Rôle</th><th>Inscrit le</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach($users as $u): $isSelf=($u['id']===$_SESSION['utilisateur']['id']); ?>
      <tr <?= $isSelf?'style="background:rgba(201,168,76,.06)"':'' ?>>
        <td class="td-num"><?= $u['id'] ?></td>
        <td>
          <div style="display:flex;align-items:center;gap:.6rem">
            <div class="art-author-dot" style="width:30px;height:30px;font-size:11px;flex-shrink:0"><?= initiales($u['prenom'],$u['nom']) ?></div>
            <div>
              <div style="font-weight:600;font-size:14px"><?= e($u['prenom'].' '.$u['nom']) ?></div>
              <?php if($isSelf):?><div style="font-size:11px;color:var(--success);display:flex;align-items:center;gap:2px"><span class="icon icon-sm">person_check</span> Votre compte</div><?php endif;?>
            </div>
          </div>
        </td>
        <td><span class="mono"><?= e($u['login']) ?></span></td>
        <td><span class="badge <?= $u['role']==='administrateur'?'badge-admin':'badge-editor' ?>"><?= e($u['role']) ?></span></td>
        <td class="text-sm text-muted"><?= fmt_date($u['date_creation']) ?></td>
        <td><div class="row-actions">
          <a href="modifier.php?id=<?= $u['id'] ?>" class="btn btn-warn btn-xs" title="Modifier"><span class="icon icon-sm">edit</span></a>
          <?php if(!$isSelf):?>
            <a href="supprimer.php?id=<?= $u['id'] ?>" class="btn btn-danger btn-xs" title="Supprimer"><span class="icon icon-sm">delete</span></a>
          <?php else:?>
            <button class="btn btn-danger btn-xs" disabled style="opacity:.35" title="Vous ne pouvez pas vous supprimer"><span class="icon icon-sm">delete</span></button>
          <?php endif;?>
        </div></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once '../includes/pied.php'; ?>
