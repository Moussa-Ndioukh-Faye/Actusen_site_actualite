<?php
require_once '../config/connexion.php'; session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['utilisateur']['role'],['editeur','administrateur'])) { header('Location: ../accueil.php'); exit(); }
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT)?:filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id||$id<=0) { header('Location: liste.php'); exit(); }
$stmt=$pdo->prepare('SELECT * FROM articles WHERE id=:id');$stmt->execute([':id'=>$id]);$art=$stmt->fetch();
if (!$art) { header('Location: liste.php'); exit(); }
$cats=$pdo->query('SELECT id,nom FROM categories ORDER BY nom')->fetchAll();
$erreurs=[]; $v=['titre'=>$art['titre'],'description_courte'=>$art['description_courte'],'contenu'=>$art['contenu'],'id_categorie'=>$art['id_categorie']];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $v['titre']=$trim_p=trim($_POST['titre']??'');$v['description_courte']=trim($_POST['description_courte']??'');
    $v['contenu']=trim($_POST['contenu']??'');$v['id_categorie']=trim($_POST['id_categorie']??'');
    if (empty($v['titre']))  $erreurs['titre']='Obligatoire.';
    elseif(mb_strlen($v['titre'])>255) $erreurs['titre']='Max 255 car.';
    if (empty($v['description_courte'])) $erreurs['desc']='Obligatoire.';
    elseif(mb_strlen($v['description_courte'])>500) $erreurs['desc']='Max 500 car.';
    if (empty($v['contenu'])) $erreurs['contenu']='Obligatoire.';
    if (empty($v['id_categorie'])||!ctype_digit($v['id_categorie'])) $erreurs['cat']='Sélectionnez une catégorie.';
    if (empty($erreurs)) {
        $upd=$pdo->prepare('UPDATE articles SET titre=:t,description_courte=:d,contenu=:c,id_categorie=:cat WHERE id=:id');
        $upd->execute([':t'=>$v['titre'],':d'=>$v['description_courte'],':c'=>$v['contenu'],':cat'=>(int)$v['id_categorie'],':id'=>$id]);
        $_SESSION['msg_ok']='Article modifié avec succès !'; header('Location: liste.php'); exit();
    }
}
$base_url='../';$page_title='Modifier l\'article';
require_once '../includes/entete.php';require_once '../includes/menu.php';
?>
<div class="breadcrumb"><a href="liste.php">Articles</a><span class="breadcrumb-sep">›</span><span>Modifier</span></div>
<div class="page-hd"><h1><span class="icon icon-lg">edit</span> Modifier l'article</h1><a href="liste.php" class="btn btn-ghost btn-sm"><span class="icon icon-sm">arrow_back</span> Retour</a></div>
<div class="card form-card"><div class="card-body">
<form id="fMod" method="POST" action="modifier.php?id=<?= $id ?>" novalidate>
  <input type="hidden" name="id" value="<?= $id ?>">
  <div class="form-group">
    <label class="form-label" for="titre">Titre <span class="req">*</span></label>
    <input type="text" id="titre" name="titre" class="form-control <?= isset($erreurs['titre'])?'is-invalid':'' ?>" value="<?= e($v['titre']) ?>" maxlength="255">
    <span class="form-error" id="err-titre"><?= e($erreurs['titre']??'') ?></span>
  </div>
  <div class="form-group">
    <label class="form-label" for="description_courte">Description courte <span class="req">*</span></label>
    <textarea id="description_courte" name="description_courte" rows="3" maxlength="500"
              class="form-control <?= isset($erreurs['desc'])?'is-invalid':'' ?>"><?= e($v['description_courte']) ?></textarea>
    <div class="form-hint"><span id="cnt-desc"><?= mb_strlen($v['description_courte']) ?></span>/500</div>
    <span class="form-error" id="err-desc"><?= e($erreurs['desc']??'') ?></span>
  </div>
  <div class="form-group">
    <label class="form-label" for="contenu">Contenu <span class="req">*</span></label>
    <textarea id="contenu" name="contenu" rows="12" class="form-control <?= isset($erreurs['contenu'])?'is-invalid':'' ?>"><?= e($v['contenu']) ?></textarea>
    <span class="form-error" id="err-contenu"><?= e($erreurs['contenu']??'') ?></span>
  </div>
  <div class="form-group">
    <label class="form-label" for="id_categorie">Catégorie <span class="req">*</span></label>
    <select id="id_categorie" name="id_categorie" class="form-control <?= isset($erreurs['cat'])?'is-invalid':'' ?>">
      <option value="">— Sélectionner —</option>
      <?php foreach ($cats as $c): ?>
      <option value="<?= $c['id'] ?>" <?= $v['id_categorie']==$c['id']?'selected':'' ?>><?= e($c['nom']) ?></option>
      <?php endforeach; ?>
    </select>
    <span class="form-error" id="err-cat"><?= e($erreurs['cat']??'') ?></span>
  </div>
  <div class="form-actions">
    <button type="submit" class="btn btn-warn"><span class="icon icon-sm">save</span> Enregistrer</button>
    <a href="liste.php" class="btn btn-ghost">Annuler</a>
  </div>
</form>
</div></div>
<script>
document.getElementById('description_courte').addEventListener('input',function(){document.getElementById('cnt-desc').textContent=this.value.length;});
document.getElementById('fMod').addEventListener('submit',function(e){
  e.preventDefault();let ok=true;
  ['err-titre','err-desc','err-contenu','err-cat'].forEach(id=>document.getElementById(id).textContent='');
  document.querySelectorAll('.form-control').forEach(el=>el.classList.remove('is-invalid'));
  [{id:'titre',err:'err-titre',max:255},{id:'description_courte',err:'err-desc',max:500},{id:'contenu',err:'err-contenu',max:null}].forEach(c=>{
    const el=document.getElementById(c.id);const v=(el.value||'').trim();
    if(!v){document.getElementById(c.err).textContent='Obligatoire.';el.classList.add('is-invalid');ok=false;}
    else if(c.max&&v.length>c.max){document.getElementById(c.err).textContent='Max '+c.max+'.';el.classList.add('is-invalid');ok=false;}
  });
  const cat=document.getElementById('id_categorie');
  if(!cat.value){document.getElementById('err-cat').textContent='Sélectionnez une catégorie.';cat.classList.add('is-invalid');ok=false;}
  if(ok)this.submit();
});
</script>
<?php require_once '../includes/pied.php'; ?>
