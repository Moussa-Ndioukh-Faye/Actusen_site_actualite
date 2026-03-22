<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['utilisateur']['role'],['editeur','administrateur'])) { header('Location: ../accueil.php'); exit(); }
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT)?:filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id) { header('Location: liste.php'); exit(); }
$stmt=$pdo->prepare('SELECT * FROM categories WHERE id=:id');$stmt->execute([':id'=>$id]);$cat=$stmt->fetch();
if (!$cat) { header('Location: liste.php'); exit(); }
$nb_stmt=$pdo->prepare('SELECT COUNT(*) FROM articles WHERE id_categorie=:id');$nb_stmt->execute([':id'=>$id]);
$nb=(int)$nb_stmt->fetchColumn();
if ($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['confirmer'])) {
    if ($nb>0) { $_SESSION['msg_err']="Impossible : $nb article(s) utilisent cette catégorie."; header('Location: liste.php'); exit(); }
    try { $pdo->prepare('DELETE FROM categories WHERE id=:id')->execute([':id'=>$id]); $_SESSION['msg_ok']='Catégorie supprimée.'; }
    catch(PDOException $e) { $_SESSION['msg_err']='Suppression impossible (articles liés).'; }
    header('Location: liste.php'); exit();
}
$base_url='../';$page_title='Supprimer catégorie';
require_once '../includes/entete.php';require_once '../includes/menu.php';
?>
<div class="confirm-wrap">
  <div class="confirm-card">
    <div class="confirm-card-header">
      <div class="confirm-icon"><span class="icon icon-fill" style="font-size:3rem;color:var(--danger)">label_off</span></div>
      <h2>Supprimer la catégorie</h2>
      <div class="confirm-item-name">"<?= e($cat['nom']) ?>"</div>
    </div>
    <div class="confirm-card-body">
      <?php if ($nb > 0): ?>
        <div class="alert alert-warn">
          <span class="icon icon-sm">warning</span>
          Cette catégorie contient <strong><?= $nb ?> article(s)</strong>. Supprimez ou réaffectez ces articles avant de la supprimer.
        </div>
        <div class="confirm-actions"><a href="liste.php" class="btn btn-ghost"><span class="icon icon-sm">arrow_back</span> Retour</a></div>
      <?php else: ?>
        <div class="alert alert-danger"><span class="icon icon-sm">error</span> Cette action est <strong>irréversible</strong>.</div>
        <div class="confirm-actions">
          <form method="POST" action="supprimer.php?id=<?= $id ?>">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="confirmer" value="1">
            <button type="submit" class="btn btn-danger"><span class="icon icon-sm">delete_forever</span> Supprimer</button>
          </form>
          <a href="liste.php" class="btn btn-ghost"><span class="icon icon-sm">arrow_back</span> Annuler</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once '../includes/pied.php'; ?>
