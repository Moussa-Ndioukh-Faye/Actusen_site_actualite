<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['utilisateur']['role'],['editeur','administrateur'])) { header('Location: ../accueil.php'); exit(); }
$erreurs=[]; $v=['nom'=>'','description'=>''];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $v['nom']=trim($_POST['nom']??''); $v['description']=trim($_POST['description']??'');
    if (empty($v['nom'])) $erreurs['nom']='Le nom est obligatoire.';
    elseif(mb_strlen($v['nom'])>100) $erreurs['nom']='Maximum 100 caractères.';
    else { $vn=$pdo->prepare('SELECT id FROM categories WHERE nom=:n');$vn->execute([':n'=>$v['nom']]);if($vn->fetch())$erreurs['nom']='Cette catégorie existe déjà.'; }
    if (empty($erreurs)) {
        $pdo->prepare('INSERT INTO categories (nom,description) VALUES(:n,:d)')->execute([':n'=>$v['nom'],':d'=>$v['description']?:null]);
        $_SESSION['msg_ok']='Catégorie "'.$v['nom'].'" créée !'; header('Location: liste.php'); exit();
    }
}
$base_url='../';$page_title='Nouvelle catégorie';
require_once '../includes/entete.php';require_once '../includes/menu.php';
?>
<div class="breadcrumb"><a href="liste.php">Catégories</a><span class="breadcrumb-sep">›</span><span>Nouvelle</span></div>
<div class="page-hd"><h1><span class="icon icon-lg">add_circle</span> Nouvelle catégorie</h1><a href="liste.php" class="btn btn-ghost btn-sm"><span class="icon icon-sm">arrow_back</span> Retour</a></div>
<div class="card form-card"><div class="card-body">
<form id="fCat" method="POST" action="ajouter.php" novalidate>
  <div class="form-group">
    <label class="form-label" for="nom">Nom <span class="req">*</span></label>
    <input type="text" id="nom" name="nom" class="form-control <?=isset($erreurs['nom'])?'is-invalid':''?>"
           value="<?=e($v['nom'])?>" maxlength="100" placeholder="Ex : Technologie">
    <span class="form-error" id="err-nom"><?=e($erreurs['nom']??'')?></span>
  </div>
  <div class="form-group">
    <label class="form-label" for="description">Description <small class="text-muted">(optionnel)</small></label>
    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Description de la catégorie"><?=e($v['description'])?></textarea>
  </div>
  <div class="form-actions">
    <button type="submit" class="btn btn-primary"><span class="icon icon-sm">save</span> Créer</button>
    <a href="liste.php" class="btn btn-ghost">Annuler</a>
  </div>
</form>
</div></div>
<script>
document.getElementById('fCat').addEventListener('submit',function(e){
  e.preventDefault();document.getElementById('err-nom').textContent='';document.getElementById('nom').classList.remove('is-invalid');
  const nom=document.getElementById('nom').value.trim();
  if(!nom){document.getElementById('err-nom').textContent='Le nom est obligatoire.';document.getElementById('nom').classList.add('is-invalid');}
  else if(nom.length>100){document.getElementById('err-nom').textContent='Max 100 car.';document.getElementById('nom').classList.add('is-invalid');}
  else this.submit();
});
</script>
<?php require_once '../includes/pied.php'; ?>
