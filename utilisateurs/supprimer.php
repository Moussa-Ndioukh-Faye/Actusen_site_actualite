<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if ($_SESSION['utilisateur']['role'] !== 'administrateur') { header('Location: ../accueil.php'); exit(); }
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT)?:filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id) { header('Location: liste.php'); exit(); }
if ($id===(int)$_SESSION['utilisateur']['id']) { $_SESSION['msg_err']='Vous ne pouvez pas supprimer votre propre compte.'; header('Location: liste.php'); exit(); }
$stmt=$pdo->prepare('SELECT id,nom,prenom,login,role FROM utilisateurs WHERE id=:id');$stmt->execute([':id'=>$id]);$user=$stmt->fetch();
if (!$user) { header('Location: liste.php'); exit(); }
$nb_stmt=$pdo->prepare('SELECT COUNT(*) FROM articles WHERE id_auteur=:id');$nb_stmt->execute([':id'=>$id]);
$nb=(int)$nb_stmt->fetchColumn();
if ($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['confirmer'])) {
    if ($nb>0) { $_SESSION['msg_err']="Impossible : $nb article(s) sont liés à ce compte."; header('Location: liste.php'); exit(); }
    try { $pdo->prepare('DELETE FROM utilisateurs WHERE id=:id')->execute([':id'=>$id]); $_SESSION['msg_ok']='Compte de '.$user['prenom'].' '.$user['nom'].' supprimé.'; }
    catch(PDOException $e) { $_SESSION['msg_err']='Suppression impossible.'; }
    header('Location: liste.php'); exit();
}
$base_url='../';$page_title='Supprimer membre';
require_once '../includes/entete.php';require_once '../includes/menu.php';
?>
<div class="confirm-wrap">
  <div class="confirm-card">
    <div class="confirm-card-header">
      <div class="confirm-icon"><span class="icon icon-fill" style="font-size:3rem;color:var(--danger)">person_remove</span></div>
      <h2>Supprimer le compte</h2>
      <div class="confirm-item-name"><?= e($user['prenom'].' '.$user['nom']) ?></div>
    </div>
    <div class="confirm-card-body">
      <div style="background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius);padding:1rem;margin-bottom:1rem;font-size:13.5px;line-height:2">
        <div><strong>Login :</strong> <span class="mono"><?= e($user['login']) ?></span></div>
        <div><strong>Rôle :</strong> <span class="badge <?= $user['role']==='administrateur'?'badge-admin':'badge-editor' ?>"><?= e($user['role']) ?></span></div>
      </div>
      <?php if ($nb > 0): ?>
        <div class="alert alert-warn"><span class="icon icon-sm">warning</span> Ce membre est auteur de <strong><?= $nb ?> article(s)</strong>. Supprimez ses articles d'abord.</div>
        <div class="confirm-actions"><a href="liste.php" class="btn btn-ghost"><span class="icon icon-sm">arrow_back</span> Retour</a></div>
      <?php else: ?>
        <div class="alert alert-danger"><span class="icon icon-sm">error</span> Cette action est <strong>irréversible</strong>. Le compte sera définitivement supprimé.</div>
        <div class="confirm-actions">
          <form method="POST" action="supprimer.php?id=<?= $id ?>">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="confirmer" value="1">
            <button type="submit" class="btn btn-danger"><span class="icon icon-sm">delete_forever</span> Supprimer définitivement</button>
          </form>
          <a href="liste.php" class="btn btn-ghost"><span class="icon icon-sm">arrow_back</span> Annuler</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once '../includes/pied.php'; ?>
