<?php // includes/pied.php ?>
</div><!-- .container -->
</main>

<footer class="site-footer">
  <div class="container">
    <div class="footer-inner">
      <div>
        <div class="footer-logo">Actu<em>Sen</em></div>
        <div class="footer-text">L'actualité sénégalaise en temps réel</div>
      </div>
      <div class="footer-text text-center">
        © <?= date('Y') ?> — École Supérieure Polytechnique · Département Génie Informatique · L2 GLSI
      </div>
      <div class="footer-links">
        <a href="<?= $base_url ?? '' ?>accueil.php">Accueil</a>
        <a href="<?= $base_url ?? '' ?>connexion.php">Connexion</a>
      </div>
    </div>
  </div>
</footer>

<script>
const d = new Date();
const jours = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
const mois  = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
const el = document.getElementById('hd-date');
if (el) el.textContent = jours[d.getDay()] + ' ' + d.getDate() + ' ' + mois[d.getMonth()] + ' ' + d.getFullYear();
</script>
</body>
</html>
