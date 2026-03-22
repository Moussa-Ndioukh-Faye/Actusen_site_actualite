<?php
require_once '../config/connexion.php';
session_start();
if (!isset($_SESSION['utilisateur'])) { header('Location: ../connexion.php'); exit(); }
if (!in_array($_SESSION['utilisateur']['role'],['editeur','administrateur'])) { header('Location: ../accueil.php'); exit(); }

$cats = $pdo->query('SELECT id,nom FROM categories ORDER BY nom')->fetchAll();
$erreurs=[]; $v=['titre'=>'','description_courte'=>'','contenu'=>'','id_categorie'=>''];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $v['titre']             = trim($_POST['titre']??'');
    $v['description_courte'] = trim($_POST['description_courte']??'');
    $v['contenu']           = trim($_POST['contenu']??'');
    $v['id_categorie']      = trim($_POST['id_categorie']??'');

    if (empty($v['titre']))              $erreurs['titre']='Le titre est obligatoire.';
    elseif (mb_strlen($v['titre'])>255)  $erreurs['titre']='Maximum 255 caractères.';
    if (empty($v['description_courte'])) $erreurs['desc']='La description courte est obligatoire.';
    elseif (mb_strlen($v['description_courte'])>500) $erreurs['desc']='Maximum 500 caractères.';
    if (empty($v['contenu']))            $erreurs['contenu']='Le contenu est obligatoire.';
    if (empty($v['id_categorie'])||!ctype_digit($v['id_categorie'])) $erreurs['cat']='Sélectionnez une catégorie.';

    if (empty($erreurs)) {
        $stmt=$pdo->prepare('INSERT INTO articles (titre,description_courte,contenu,id_categorie,id_auteur) VALUES(:t,:d,:c,:cat,:a)');
        $stmt->execute([':t'=>$v['titre'],':d'=>$v['description_courte'],':c'=>$v['contenu'],':cat'=>(int)$v['id_categorie'],':a'=>(int)$_SESSION['utilisateur']['id']]);
        $_SESSION['msg_ok']='Article publié avec succès !';
        header('Location: liste.php'); exit();
    }
}
$base_url='../'; $page_title='Nouvel article';
require_once '../includes/entete.php';
require_once '../includes/menu.php';
?>
<div class="breadcrumb">
  <a href="liste.php">Articles</a><span class="breadcrumb-sep">›</span><span>Nouvel article</span>
</div>
<div class="page-hd">
  <h1>＋ Nouvel article</h1>
  <a href="liste.php" class="btn btn-ghost btn-sm"><span class="icon icon-sm">arrow_back</span> Retour</a>
</div>
<div class="card form-card">
  <div class="card-body">
    <form id="fArt" method="POST" action="ajouter.php" novalidate>

      <div class="form-group">
        <label class="form-label" for="titre">Titre <span class="req">*</span></label>
        <input type="text" id="titre" name="titre" class="form-control <?= isset($erreurs['titre'])?'is-invalid':'' ?>"
               value="<?= e($v['titre']) ?>" placeholder="Titre accrocheur de l'article" maxlength="255">
        <span class="form-error" id="err-titre"><?= e($erreurs['titre']??'') ?></span>
      </div>

      <div class="form-group">
        <label class="form-label" for="description_courte">Description courte <span class="req">*</span></label>
        <textarea id="description_courte" name="description_courte" rows="3" maxlength="500"
                  class="form-control <?= isset($erreurs['desc'])?'is-invalid':'' ?>"
                  placeholder="Résumé affiché sur l'accueil (max 500 caractères)"><?= e($v['description_courte']) ?></textarea>
        <div class="form-hint"><span id="cnt-desc"><?= mb_strlen($v['description_courte']) ?></span>/500 caractères</div>
        <span class="form-error" id="err-desc"><?= e($erreurs['desc']??'') ?></span>
      </div>

      <div class="form-group">
        <label class="form-label" for="contenu">Contenu complet <span class="req">*</span></label>
        <textarea id="contenu" name="contenu" rows="12"
                  class="form-control <?= isset($erreurs['contenu'])?'is-invalid':'' ?>"
                  placeholder="Rédigez le contenu intégral de l'article..."><?= e($v['contenu']) ?></textarea>
        <span class="form-error" id="err-contenu"><?= e($erreurs['contenu']??'') ?></span>
      </div>

      <div class="form-group">
        <label class="form-label" for="id_categorie">Catégorie <span class="req">*</span></label>
        <select id="id_categorie" name="id_categorie" class="form-control <?= isset($erreurs['cat'])?'is-invalid':'' ?>">
          <option value="">— Sélectionner une catégorie —</option>
          <?php foreach ($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $v['id_categorie']==$c['id']?'selected':'' ?>><?= e($c['nom']) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="form-error" id="err-cat"><?= e($erreurs['cat']??'') ?></span>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><span class="icon icon-sm">publish</span> Publier</button>
        <a href="liste.php" class="btn btn-ghost">Annuler</a>
      </div>
    </form>
  </div>
</div>
<script>
document.getElementById('description_courte').addEventListener('input',function(){document.getElementById('cnt-desc').textContent=this.value.length;});
document.getElementById('fArt').addEventListener('submit',function(e){
  e.preventDefault(); let ok=true;
  ['err-titre','err-desc','err-contenu','err-cat'].forEach(id=>document.getElementById(id).textContent='');
  document.querySelectorAll('.form-control').forEach(el=>el.classList.remove('is-invalid'));
  const checks=[{id:'titre',err:'err-titre',max:255},{id:'description_courte',err:'err-desc',max:500},{id:'contenu',err:'err-contenu',max:null}];
  checks.forEach(c=>{
    const el=document.getElementById(c.id); const v=(el.value||'').trim();
    if(!v){document.getElementById(c.err).textContent='Ce champ est obligatoire.';el.classList.add('is-invalid');ok=false;}
    else if(c.max&&v.length>c.max){document.getElementById(c.err).textContent='Maximum '+c.max+' car.';el.classList.add('is-invalid');ok=false;}
  });
  const cat=document.getElementById('id_categorie');
  if(!cat.value){document.getElementById('err-cat').textContent='Sélectionnez une catégorie.';cat.classList.add('is-invalid');ok=false;}
  if(ok) this.submit();
});
</script>
<?php require_once '../includes/pied.php'; ?>
