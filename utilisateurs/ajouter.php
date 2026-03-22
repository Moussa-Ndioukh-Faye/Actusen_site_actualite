<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if ($_SESSION['utilisateur']['role']!=='administrateur') { header('Location: ../accueil.php'); exit(); }
$erreurs=[];$v=['nom'=>'','prenom'=>'','login'=>'','role'=>'editeur'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $v['nom']=trim($_POST['nom']??'');$v['prenom']=trim($_POST['prenom']??'');
    $v['login']=trim($_POST['login']??'');$v['role']=trim($_POST['role']??'');
    $mdp=$_POST['mot_de_passe']??'';$mdp_c=$_POST['mot_de_passe_confirm']??'';
    if(empty($v['nom']))    $erreurs['nom']='Obligatoire.';
    if(empty($v['prenom'])) $erreurs['prenom']='Obligatoire.';
    if(empty($v['login'])) { $erreurs['login']='Obligatoire.'; }
    else { $vl=$pdo->prepare('SELECT id FROM utilisateurs WHERE login=:l');$vl->execute([':l'=>$v['login']]);if($vl->fetch())$erreurs['login']='Login déjà pris.'; }
    if(empty($mdp)) $erreurs['mdp']='Obligatoire.';
    elseif(mb_strlen($mdp)<6) $erreurs['mdp']='Minimum 6 caractères.';
    elseif($mdp!==$mdp_c) $erreurs['mdp_c']='Les mots de passe ne correspondent pas.';
    if(!in_array($v['role'],['editeur','administrateur'])) $erreurs['role']='Rôle invalide.';
    if(empty($erreurs)) {
        $pdo->prepare('INSERT INTO utilisateurs (nom,prenom,login,mot_de_passe,role) VALUES(:n,:p,:l,:m,:r)')
            ->execute([':n'=>$v['nom'],':p'=>$v['prenom'],':l'=>$v['login'],':m'=>password_hash($mdp,PASSWORD_DEFAULT),':r'=>$v['role']]);
        $_SESSION['msg_ok']='Membre "'.$v['prenom'].' '.$v['nom'].'" créé !';
        header('Location: liste.php');exit();
    }
}
$base_url='../';$page_title='Nouveau membre';
require_once '../includes/entete.php';require_once '../includes/menu.php';
?>
<div class="breadcrumb"><a href="liste.php">Membres</a><span class="breadcrumb-sep">›</span><span>Nouveau</span></div>
<div class="page-hd"><h1><span class="icon icon-lg">person_add</span> Nouveau membre</h1><a href="liste.php" class="btn btn-ghost btn-sm"><span class="icon icon-sm">arrow_back</span> Retour</a></div>
<div class="card form-card"><div class="card-body">
  <form id="fUser" method="POST" action="ajouter.php" novalidate>
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
      <input type="text" id="login" name="login" class="form-control <?=isset($erreurs['login'])?'is-invalid':''?>" value="<?=e($v['login'])?>" autocomplete="off">
      <span class="form-error" id="err-login"><?=e($erreurs['login']??'')?></span>
    </div>
    <div class="form-grid-2">
      <div class="form-group">
        <label class="form-label" for="mot_de_passe">Mot de passe <span class="req">*</span></label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control <?=isset($erreurs['mdp'])?'is-invalid':''?>" placeholder="Min. 6 car." autocomplete="new-password">
        <span class="form-error" id="err-mdp"><?=e($erreurs['mdp']??'')?></span>
      </div>
      <div class="form-group">
        <label class="form-label" for="mot_de_passe_confirm">Confirmation <span class="req">*</span></label>
        <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" class="form-control <?=isset($erreurs['mdp_c'])?'is-invalid':''?>" placeholder="Répéter" autocomplete="new-password">
        <span class="form-error" id="err-mdp-c"><?=e($erreurs['mdp_c']??'')?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="role">Rôle <span class="req">*</span></label>
      <select id="role" name="role" class="form-control">
        <option value="editeur"        <?=$v['role']==='editeur'?'selected':''?>>Éditeur</option>
        <option value="administrateur" <?=$v['role']==='administrateur'?'selected':''?>>Administrateur</option>
      </select>
      <span class="form-error" id="err-role"><?=e($erreurs['role']??'')?></span>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">💾 Créer le membre</button>
      <a href="liste.php" class="btn btn-ghost">Annuler</a>
    </div>
  </form>
</div></div>
<script>
document.getElementById('fUser').addEventListener('submit',function(e){
  e.preventDefault();let ok=true;
  ['err-prenom','err-nom','err-login','err-mdp','err-mdp-c','err-role'].forEach(id=>document.getElementById(id).textContent='');
  document.querySelectorAll('.form-control').forEach(el=>el.classList.remove('is-invalid'));
  const prenom=document.getElementById('prenom').value.trim();
  const nom=document.getElementById('nom').value.trim();
  const login=document.getElementById('login').value.trim();
  const mdp=document.getElementById('mot_de_passe').value;
  const mdpc=document.getElementById('mot_de_passe_confirm').value;
  if(!prenom){document.getElementById('err-prenom').textContent='Obligatoire.';document.getElementById('prenom').classList.add('is-invalid');ok=false;}
  if(!nom){document.getElementById('err-nom').textContent='Obligatoire.';document.getElementById('nom').classList.add('is-invalid');ok=false;}
  if(!login){document.getElementById('err-login').textContent='Obligatoire.';document.getElementById('login').classList.add('is-invalid');ok=false;}
  if(!mdp){document.getElementById('err-mdp').textContent='Obligatoire.';document.getElementById('mot_de_passe').classList.add('is-invalid');ok=false;}
  else if(mdp.length<6){document.getElementById('err-mdp').textContent='Min. 6 car.';document.getElementById('mot_de_passe').classList.add('is-invalid');ok=false;}
  else if(mdp!==mdpc){document.getElementById('err-mdp-c').textContent='Ne correspondent pas.';document.getElementById('mot_de_passe_confirm').classList.add('is-invalid');ok=false;}
  if(ok)this.submit();
});
</script>
<?php require_once '../includes/pied.php'; ?>
