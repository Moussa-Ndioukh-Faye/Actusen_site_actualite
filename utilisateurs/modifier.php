<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if ($_SESSION['utilisateur']['role']!=='administrateur') { header('Location: ../accueil.php'); exit(); }
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT)?:filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id) { header('Location: liste.php'); exit(); }
$stmt=$pdo->prepare('SELECT id,nom,prenom,login,role FROM utilisateurs WHERE id=:id');$stmt->execute([':id'=>$id]);$user=$stmt->fetch();
if (!$user) { header('Location: liste.php'); exit(); }
$erreurs=[];$v=['nom'=>$user['nom'],'prenom'=>$user['prenom'],'login'=>$user['login'],'role'=>$user['role']];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $v['nom']=trim($_POST['nom']??'');$v['prenom']=trim($_POST['prenom']??'');
    $v['login']=trim($_POST['login']??'');$v['role']=trim($_POST['role']??'');
    $nmdp=$_POST['nouveau_mdp']??'';$nmdpc=$_POST['nouveau_mdp_confirm']??'';
    if(empty($v['nom']))$erreurs['nom']='Obligatoire.';
    if(empty($v['prenom']))$erreurs['prenom']='Obligatoire.';
    if(empty($v['login'])){$erreurs['login']='Obligatoire.';}
    else{$vl=$pdo->prepare('SELECT id FROM utilisateurs WHERE login=:l AND id!=:id');$vl->execute([':l'=>$v['login'],':id'=>$id]);if($vl->fetch())$erreurs['login']='Login déjà pris.';}
    if(!empty($nmdp)){
        if(mb_strlen($nmdp)<6)$erreurs['mdp']='Min. 6 car.';
        elseif($nmdp!==$nmdpc)$erreurs['mdp_c']='Ne correspondent pas.';
    }
    if(!in_array($v['role'],['editeur','administrateur']))$erreurs['role']='Rôle invalide.';
    if(empty($erreurs)){
        if(!empty($nmdp)){
            $pdo->prepare('UPDATE utilisateurs SET nom=:n,prenom=:p,login=:l,role=:r,mot_de_passe=:m WHERE id=:id')
                ->execute([':n'=>$v['nom'],':p'=>$v['prenom'],':l'=>$v['login'],':r'=>$v['role'],':m'=>password_hash($nmdp,PASSWORD_DEFAULT),':id'=>$id]);
        } else {
            $pdo->prepare('UPDATE utilisateurs SET nom=:n,prenom=:p,login=:l,role=:r WHERE id=:id')
                ->execute([':n'=>$v['nom'],':p'=>$v['prenom'],':l'=>$v['login'],':r'=>$v['role'],':id'=>$id]);
        }
        if($id===$_SESSION['utilisateur']['id']){$_SESSION['utilisateur']['nom']=$v['nom'];$_SESSION['utilisateur']['prenom']=$v['prenom'];$_SESSION['utilisateur']['role']=$v['role'];}
        $_SESSION['msg_ok']='Membre modifié avec succès !'; header('Location: liste.php'); exit();
    }
}
$base_url='../';$page_title='Modifier membre';
require_once '../includes/entete.php';require_once '../includes/menu.php';
?>
<div class="breadcrumb"><a href="liste.php">Membres</a><span class="breadcrumb-sep">›</span><span>Modifier</span></div>
<div class="page-hd"><h1>✏️ Modifier le membre</h1><a href="liste.php" class="btn btn-ghost btn-sm"><span class="icon icon-sm">arrow_back</span> Retour</a></div>
<?php if($id===$_SESSION['utilisateur']['id']):?>
<div class="alert alert-info">ℹ️ Vous modifiez votre propre compte. Les changements s'appliquent immédiatement.</div>
<?php endif;?>
<div class="card form-card"><div class="card-body">
  <form id="fMU" method="POST" action="modifier.php?id=<?=$id?>" novalidate>
    <input type="hidden" name="id" value="<?=$id?>">
    <div class="form-grid-2">
      <div class="form-group">
        <label class="form-label" for="prenom">Prénom <span class="req">*</span></label>
        <input type="text" id="prenom" name="prenom" class="form-control <?=isset($erreurs['prenom'])?'is-invalid':''?>" value="<?=e($v['prenom'])?>">
        <span class="form-error" id="err-prenom"><?=e($erreurs['prenom']??'')?></span>
      </div>
      <div class="form-group">
        <label class="form-label" for="nom">Nom <span class="req">*</span></label>
        <input type="text" id="nom" name="nom" class="form-control <?=isset($erreurs['nom'])?'is-invalid':''?>" value="<?=e($v['nom'])?>">
        <span class="form-error" id="err-nom"><?=e($erreurs['nom']??'')?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="login">Identifiant <span class="req">*</span></label>
      <input type="text" id="login" name="login" class="form-control <?=isset($erreurs['login'])?'is-invalid':''?>" value="<?=e($v['login'])?>">
      <span class="form-error" id="err-login"><?=e($erreurs['login']??'')?></span>
    </div>
    <div class="form-group">
      <label class="form-label" for="role">Rôle <span class="req">*</span></label>
      <select id="role" name="role" class="form-control">
        <option value="editeur"        <?=$v['role']==='editeur'?'selected':''?>>Éditeur</option>
        <option value="administrateur" <?=$v['role']==='administrateur'?'selected':''?>>Administrateur</option>
      </select>
    </div>
    <div style="border:1px solid var(--border);border-radius:var(--radius);padding:1.1rem;background:var(--surface-2);margin-bottom:1.25rem">
      <div style="font-size:13px;font-weight:600;color:var(--muted);margin-bottom:.85rem">
        🔑 Nouveau mot de passe <em style="font-weight:400">(laisser vide pour conserver l'actuel)</em>
      </div>
      <div class="form-grid-2">
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" for="nouveau_mdp">Nouveau mot de passe</label>
          <input type="password" id="nouveau_mdp" name="nouveau_mdp" class="form-control <?=isset($erreurs['mdp'])?'is-invalid':''?>" placeholder="Min. 6 car." autocomplete="new-password">
          <span class="form-error" id="err-mdp"><?=e($erreurs['mdp']??'')?></span>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label" for="nouveau_mdp_confirm">Confirmation</label>
          <input type="password" id="nouveau_mdp_confirm" name="nouveau_mdp_confirm" class="form-control <?=isset($erreurs['mdp_c'])?'is-invalid':''?>" placeholder="Répéter" autocomplete="new-password">
          <span class="form-error" id="err-mdp-c"><?=e($erreurs['mdp_c']??'')?></span>
        </div>
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-warn"><span class="icon icon-sm">save</span> Enregistrer</button>
      <a href="liste.php" class="btn btn-ghost">Annuler</a>
    </div>
  </form>
</div></div>
<script>
document.getElementById('fMU').addEventListener('submit',function(e){
  e.preventDefault();let ok=true;
  ['err-prenom','err-nom','err-login','err-mdp','err-mdp-c'].forEach(id=>document.getElementById(id).textContent='');
  document.querySelectorAll('.form-control').forEach(el=>el.classList.remove('is-invalid'));
  if(!document.getElementById('prenom').value.trim()){document.getElementById('err-prenom').textContent='Obligatoire.';document.getElementById('prenom').classList.add('is-invalid');ok=false;}
  if(!document.getElementById('nom').value.trim()){document.getElementById('err-nom').textContent='Obligatoire.';document.getElementById('nom').classList.add('is-invalid');ok=false;}
  if(!document.getElementById('login').value.trim()){document.getElementById('err-login').textContent='Obligatoire.';document.getElementById('login').classList.add('is-invalid');ok=false;}
  const mdp=document.getElementById('nouveau_mdp').value;
  if(mdp){
    if(mdp.length<6){document.getElementById('err-mdp').textContent='Min. 6 car.';document.getElementById('nouveau_mdp').classList.add('is-invalid');ok=false;}
    else if(mdp!==document.getElementById('nouveau_mdp_confirm').value){document.getElementById('err-mdp-c').textContent='Ne correspondent pas.';document.getElementById('nouveau_mdp_confirm').classList.add('is-invalid');ok=false;}
  }
  if(ok)this.submit();
});
</script>
<?php require_once '../includes/pied.php'; ?>
