<?php
require_once 'config/connexion.php';
if (session_status()===PHP_SESSION_NONE) session_start();
if (isset($_SESSION['utilisateur'])) { header('Location: accueil.php'); exit(); }

$erreur = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';
    if (empty($login)||empty($mdp)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE login=:l LIMIT 1');
        $stmt->execute([':l'=>$login]);
        $u = $stmt->fetch();
        if ($u && password_verify($mdp, $u['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['utilisateur'] = ['id'=>$u['id'],'nom'=>$u['nom'],'prenom'=>$u['prenom'],'login'=>$u['login'],'role'=>$u['role']];
            header('Location: accueil.php'); exit();
        } else {
            $erreur = 'Identifiants incorrects. Veuillez réessayer.';
        }
    }
}
$base_url=''; $page_title='Connexion';
require_once 'includes/entete.php';
?>
<nav class="main-nav"><div class="container"><div class="nav-inner">
  <ul class="nav-links">
    <li><a href="accueil.php"><span class="icon icon-sm">home</span> Accueil</a></li>
  </ul>
</div></div></nav>
<main class="main-content"><div class="container">

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-card-header">
      <div class="auth-logo">Actu<em>Sen</em></div>
      <div class="auth-title">Connexion</div>
      <div class="auth-subtitle">Accès réservé aux éditeurs et administrateurs</div>
    </div>
    <div class="auth-body">
      <?php if ($erreur): ?>
      <div class="alert alert-danger"><?= e($erreur) ?></div>
      <?php endif; ?>
      <form id="fConn" method="POST" action="connexion.php" novalidate>
        <div class="form-group">
          <label class="form-label" for="login">
            <span class="icon icon-sm">person</span> Identifiant <span class="req">*</span>
          </label>
          <input type="text" id="login" name="login" class="form-control"
                 value="<?= e($_POST['login']??'') ?>" placeholder="Votre login" autocomplete="username">
          <span class="form-error" id="err-login"></span>
        </div>
        <div class="form-group">
          <label class="form-label" for="mot_de_passe">
            <span class="icon icon-sm">lock</span> Mot de passe <span class="req">*</span>
          </label>
          <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control"
                 placeholder="••••••••" autocomplete="current-password">
          <span class="form-error" id="err-mdp"></span>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px">
          <span class="icon">login</span> Se connecter
        </button>
      </form>
    </div>
    <div class="auth-footer">
      Accès réservé aux membres enregistrés.<br>
      <span style="font-size:12px">Contactez un administrateur pour obtenir un compte.</span>
    </div>
  </div>
</div>

</div></main>
<script>
document.getElementById('fConn').addEventListener('submit',function(e){
  e.preventDefault(); let ok=true;
  ['err-login','err-mdp'].forEach(id=>document.getElementById(id).textContent='');
  ['login','mot_de_passe'].forEach(id=>document.getElementById(id).classList.remove('is-invalid'));
  if(!document.getElementById('login').value.trim()){
    document.getElementById('err-login').textContent='L\'identifiant est obligatoire.';
    document.getElementById('login').classList.add('is-invalid'); ok=false;
  }
  if(!document.getElementById('mot_de_passe').value){
    document.getElementById('err-mdp').textContent='Le mot de passe est obligatoire.';
    document.getElementById('mot_de_passe').classList.add('is-invalid'); ok=false;
  }
  if(ok) this.submit();
});
</script>
<?php require_once 'includes/pied.php'; ?>
