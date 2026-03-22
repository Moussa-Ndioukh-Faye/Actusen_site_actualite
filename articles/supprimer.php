<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['utilisateur']['role'],['editeur','administrateur'])) { header('Location: ../accueil.php'); exit(); }
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT)?:filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id||$id<=0) { header('Location: liste.php'); exit(); }
$stmt=$pdo->prepare('SELECT id,titre FROM articles WHERE id=:id');$stmt->execute([':id'=>$id]);$art=$stmt->fetch();
if (!$art) { header('Location: liste.php'); exit(); }
if ($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['confirmer'])) {
    $pdo->prepare('DELETE FROM articles WHERE id=:id')->execute([':id'=>$id]);
    $_SESSION['msg_ok']='Article supprimé avec succès.'; header('Location: liste.php'); exit();
}
$base_url='../';$page_title='Supprimer l\'article';
require_once '../includes/entete.php';require_once '../includes/menu.php';
?>
<div class="confirm-wrap">
  <div class="confirm-card">
    <div class="confirm-card-header">
      <div class="confirm-icon"><span class="icon icon-fill" style="font-size:3rem;color:var(--danger)">delete_forever</span></div>
      <h2>Confirmer la suppression</h2>
      <p style="color:var(--muted);font-size:14px;margin-top:.4rem">Vous allez supprimer définitivement :</p>
      <div class="confirm-item-name">"<?= e($art['titre']) ?>"</div>
    </div>
    <div class="confirm-card-body">
      <div class="alert alert-warn">Cette action est <strong>irréversible</strong>. L'article sera définitivement perdu.</div>
      <div class="confirm-actions">
        <form method="POST" action="supprimer.php?id=<?= $id ?>">
          <input type="hidden" name="id" value="<?= $id ?>">
          <input type="hidden" name="confirmer" value="1">
          <button type="submit" class="btn btn-danger">
            <span class="icon icon-sm">delete_forever</span> Supprimer définitivement
          </button>
        </form>
        <a href="liste.php" class="btn btn-ghost"><span class="icon icon-sm">arrow_back</span> Annuler</a>
      </div>
    </div>
  </div>
</div>
<?php require_once '../includes/pied.php'; ?>
