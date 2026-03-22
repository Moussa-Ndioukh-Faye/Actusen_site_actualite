<?php
require_once 'config/connexion.php';
$base_url = ''; $page_title = 'Accueil';

$par_page   = 6;
$page_num   = max(1, (int)($_GET['page'] ?? 1));
$filtre_cat = isset($_GET['categorie']) && ctype_digit($_GET['categorie']) ? (int)$_GET['categorie'] : null;

if ($filtre_cat) {
    $t = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE id_categorie=:c');
    $t->execute([':c'=>$filtre_cat]);
} else {
    $t = $pdo->query('SELECT COUNT(*) FROM articles');
}
$nb_total  = (int)$t->fetchColumn();
$nb_pages  = max(1, (int)ceil($nb_total / $par_page));
$page_num  = min($page_num, $nb_pages);
$offset    = ($page_num - 1) * $par_page;

$sql = 'SELECT a.id,a.titre,a.description_courte,a.date_publication,
               c.id AS cat_id, c.nom AS cat_nom,
               u.nom AS auteur_nom, u.prenom AS auteur_prenom
        FROM articles a
        JOIN categories c ON a.id_categorie=c.id
        JOIN utilisateurs u ON a.id_auteur=u.id';
if ($filtre_cat) $sql.=' WHERE a.id_categorie=:cat';
$sql.=' ORDER BY a.date_publication DESC LIMIT :lim OFFSET :off';
$stmt = $pdo->prepare($sql);
if ($filtre_cat) $stmt->bindValue(':cat',$filtre_cat,PDO::PARAM_INT);
$stmt->bindValue(':lim',$par_page,PDO::PARAM_INT);
$stmt->bindValue(':off',$offset,PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

$nom_filtre = null;
if ($filtre_cat) {
    $sc = $pdo->prepare('SELECT nom FROM categories WHERE id=:id');
    $sc->execute([':id'=>$filtre_cat]);
    $nom_filtre = $sc->fetchColumn();
}

$cats_sidebar = $pdo->query(
    'SELECT c.id,c.nom,COUNT(a.id) AS nb
     FROM categories c LEFT JOIN articles a ON c.id=a.id_categorie
     GROUP BY c.id,c.nom ORDER BY c.nom'
)->fetchAll();

function pg_url(int $p, ?int $cat): string {
    return 'accueil.php?page='.$p.($cat ? '&categorie='.$cat : '');
}

// Message de bienvenue après inscription
$msg_bienvenue = $_SESSION['msg_ok'] ?? null;
unset($_SESSION['msg_ok']);

require_once 'includes/entete.php';
require_once 'includes/menu.php';

$vedette = $articles[0] ?? null;
$rest    = array_slice($articles, $page_num===1 && $vedette && !$filtre_cat ? 1 : 0);
?>

<?php if ($msg_bienvenue): ?>
<div class="alert alert-success" style="margin-bottom:1.5rem"><?= $msg_bienvenue ?></div>
<?php endif; ?>

<div class="layout-sidebar">
  <div>
    <div class="page-hd">
      <div class="page-hd-left">
        <h1>
          <?php if ($nom_filtre): ?>
            <span class="icon icon-lg">folder_open</span> <?= e($nom_filtre) ?>
          <?php else: ?>
            <span class="icon icon-lg">newspaper</span> Dernières actualités
          <?php endif; ?>
        </h1>
        <span class="page-hd-meta"><?= $nb_total ?> article<?= $nb_total>1?'s':'' ?> · Page <?= $page_num ?>/<?= $nb_pages ?></span>
      </div>
      <?php if ($nom_filtre): ?>
      <a href="accueil.php" class="btn btn-ghost btn-sm"><span class="icon icon-sm">close</span> Retirer le filtre</a>
      <?php endif; ?>
    </div>

    <?php if (empty($articles)): ?>
    <div class="empty-state">
      <span class="icon icon-xl" style="font-size:3rem;opacity:.4;display:block;margin-bottom:.75rem">inbox</span>
      <p>Aucun article<?= $nom_filtre?' dans cette catégorie':'' ?> pour le moment.</p>
    </div>

    <?php else: ?>

    <?php if ($page_num === 1 && !$filtre_cat && $vedette): ?>
    <article class="article-featured">
      <div class="article-featured-img">
        <span class="article-featured-cat-stamp"><?= e($vedette['cat_nom']) ?></span>
        <span class="article-featured-num">01</span>
      </div>
      <div class="article-featured-body">
        <div class="art-meta">
          <a href="accueil.php?categorie=<?= $vedette['cat_id'] ?>" class="cat-tag"><?= e($vedette['cat_nom']) ?></a>
          <span class="art-date"><span class="icon icon-sm">schedule</span> <?= fmt_date($vedette['date_publication']) ?></span>
        </div>
        <h2><a href="articles/detail.php?id=<?= $vedette['id'] ?>"><?= e($vedette['titre']) ?></a></h2>
        <p class="art-desc"><?= e($vedette['description_courte']) ?></p>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:.75rem;flex-wrap:wrap">
          <div class="art-author">
            <div class="art-author-dot"><?= initiales($vedette['auteur_prenom'],$vedette['auteur_nom']) ?></div>
            <?= e($vedette['auteur_prenom'].' '.$vedette['auteur_nom']) ?>
          </div>
          <a href="articles/detail.php?id=<?= $vedette['id'] ?>" class="btn btn-primary btn-sm">
            <span class="icon icon-sm">arrow_forward</span> Lire l'article
          </a>
        </div>
      </div>
    </article>
    <?php endif; ?>

    <?php if ($rest): ?>
    <div class="articles-grid">
      <?php foreach ($rest as $art): ?>
      <article class="article-card">
        <div class="article-card-stripe"></div>
        <div class="article-card-body">
          <div class="art-meta">
            <a href="accueil.php?categorie=<?= $art['cat_id'] ?>" class="cat-tag"><?= e($art['cat_nom']) ?></a>
            <span class="art-date"><span class="icon icon-sm">schedule</span> <?= fmt_date($art['date_publication']) ?></span>
          </div>
          <h2 class="art-title"><a href="articles/detail.php?id=<?= $art['id'] ?>"><?= e($art['titre']) ?></a></h2>
          <p class="art-desc"><?= e(mb_strimwidth($art['description_courte'],0,120,'…')) ?></p>
          <div class="art-footer">
            <div class="art-author">
              <div class="art-author-dot"><?= initiales($art['auteur_prenom'],$art['auteur_nom']) ?></div>
              <?= e($art['auteur_prenom'].' '.$art['auteur_nom']) ?>
            </div>
            <a href="articles/detail.php?id=<?= $art['id'] ?>" class="btn btn-ghost btn-xs">
              Lire <span class="icon icon-sm">chevron_right</span>
            </a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($nb_pages > 1): ?>
    <div class="pagination">
      <?php if ($page_num > 1): ?>
        <a href="<?= pg_url($page_num-1,$filtre_cat) ?>"><span class="icon icon-sm">chevron_left</span> Préc.</a>
      <?php else: ?>
        <span class="pg-disabled"><span class="icon icon-sm">chevron_left</span> Préc.</span>
      <?php endif; ?>
      <?php for ($p = 1; $p <= $nb_pages; $p++): ?>
        <?php if ($p===$page_num): ?>
          <span class="pg-current"><?= $p ?></span>
        <?php else: ?>
          <a href="<?= pg_url($p,$filtre_cat) ?>"><?= $p ?></a>
        <?php endif; ?>
      <?php endfor; ?>
      <?php if ($page_num < $nb_pages): ?>
        <a href="<?= pg_url($page_num+1,$filtre_cat) ?>">Suiv. <span class="icon icon-sm">chevron_right</span></a>
      <?php else: ?>
        <span class="pg-disabled">Suiv. <span class="icon icon-sm">chevron_right</span></span>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>
  </div>

  <aside class="sidebar">
    <div class="sidebar-widget">
      <div class="widget-head"><span class="icon icon-sm widget-head-icon">category</span> Catégories</div>
      <div class="widget-body">
        <ul style="list-style:none">
          <li class="cat-list-item">
            <a href="accueil.php" class="<?= !$filtre_cat?'active':'' ?>">
              Toutes les actualités <span class="cat-count"><?= $nb_total ?></span>
            </a>
          </li>
          <?php foreach ($cats_sidebar as $c): ?>
          <li class="cat-list-item">
            <a href="accueil.php?categorie=<?= $c['id'] ?>" class="<?= $filtre_cat===$c['id']?'active':'' ?>">
              <?= e($c['nom']) ?> <span class="cat-count"><?= $c['nb'] ?></span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <?php if (!isset($_SESSION['utilisateur'])): ?>
    <div class="sidebar-widget">
      <div class="widget-head"><span class="icon icon-sm widget-head-icon">lock</span> Espace membres</div>
      <div class="widget-body" style="padding:1.1rem;text-align:center">
        <span class="icon" style="font-size:2.5rem;color:var(--gold);display:block;margin-bottom:.5rem;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">account_circle</span>
        <p style="font-size:13px;color:var(--ink-soft);margin-bottom:1rem;line-height:1.6">
          Connectez-vous pour accéder à l'espace de publication.
        </p>
        <a href="connexion.php" class="btn btn-gold" style="width:100%;justify-content:center">
          <span class="icon icon-sm">login</span> Se connecter
        </a>
      </div>
    </div>
    <?php endif; ?>
  </aside>
</div>

<?php require_once 'includes/pied.php'; ?>
