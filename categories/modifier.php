<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['utilisateur']['role'],['editeur','administrateur'])) { header('Location: ../accueil.php'); exit(); }
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT)?:filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id) { header('Location: liste.php'); exit(); }
$stmt=$pdo->prepare('SELECT * FROM categories WHERE id=:id');$stmt->execute([':id'=>$id]);$cat=$stmt->fetch();
if (!$cat) { header('Location: liste.php'); exit(); }
$erreurs=[];$v=['nom'=>$cat['nom'],'description'=>$cat['description']??''];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $v['nom']=trim($_POST['nom']??'');$v['description']=trim($_POST['description']??'');
    if(empty($v['nom']))$erreurs['nom']='Obligatoire.';
    elseif(mb_strlen($v['nom'])>100)$erreurs['nom']='Max 100 car.';
    else{$vn=$pdo->prepare('SELECT id FROM categories WHERE nom=:n AND id!=:id');$vn->execute([':n'=>$v['nom'],':id'=>$id]);if($vn->fetch())$erreurs['nom']='Nom déjà utilisé.';}
    if(empty($erreurs)){
        $pdo->prepare('UPDATE categories SET nom=:n,description=:d WHERE id=:id')->execute([':n'=>$v['nom'],':d'=>$v['description']?:null,':id'=>$id]);
        $_SESSION['msg_ok']='Catégorie modifiée !';header('Location: liste.php');exit();
    }
}
$base_url='../';$page_title='Modifier catégorie';
require_once '../includes/entete.php';require_once '../includes/menu.php';
?>
<div class="breadcrumb"><a href="liste.php">Catégories</a><span class="breadcrumb-sep">›</span><span>Modifier</span></div>
<div class="page-hd"><h1><span class="icon icon-lg">edit</span> Modifier la catégorie</h1><a href="liste.php" class="btn btn-ghost btn-sm"><span class="icon icon-sm">arrow_back</span> Retour</a></div>
<div class="card form-card"><div class="card-body">
<form id="fMod" method="POST" action="modifier.php?id=<?=$id?>" novalidate>
  <input type="hidden" name="id" value="<?=$id?>">
  <div class="form-group">
    <label class="form-label" for="nom">Nom <span class="req">*</span></label>
    <input type="text" id="nom" name="nom" class="form-control <?=isset($erreurs['nom'])?'is-invalid':''?>"
           value="<?=e($v['nom'])?>" maxlength="100">
    <span class="form-error" id="err-nom"><?=e($erreurs['nom']??'')?></span>
  </div>
  <div class="form-group">
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" class="form-control" rows="3"><?=e($v['description'])?></textarea>
  </div>
  <div class="form-actions">
    <button type="submit" class="btn btn-warn"><span class="icon icon-sm">save</span> Enregistrer</button>
    <a href="liste.php" class="btn btn-ghost">Annuler</a>
  </div>
</form>
</div></div>
<script>
document.getElementById('fMod').addEventListener('submit',function(e){
  e.preventDefault();document.getElementById('err-nom').textContent='';document.getElementById('nom').classList.remove('is-invalid');
  const n=document.getElementById('nom').value.trim();
  if(!n){document.getElementById('err-nom').textContent='Obligatoire.';document.getElementById('nom').classList.add('is-invalid');}
  else this.submit();
});
</script>
<?php require_once '../includes/pied.php'; ?>
