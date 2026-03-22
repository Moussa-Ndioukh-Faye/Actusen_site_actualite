<?php
// config/connexion.php
$host = 'localhost'; $dbname = 'site_actualite';
$user = 'root';      $pass   = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die('<div style="font-family:monospace;padding:20px;background:#fef2f2;border-left:4px solid #c0392b;color:#7f1d1d">
        <strong>Erreur de connexion BDD :</strong> ' .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>');
}

/** Anti-XSS : échapper toute valeur avant affichage */
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/** Initiales d'un prénom + nom */
function initiales(string $prenom, string $nom): string {
    return mb_strtoupper(mb_substr($prenom,0,1) . mb_substr($nom,0,1));
}

/** Formater une date */
function fmt_date(string $dt, bool $time = false): string {
    $months = ['jan','fév','mars','avr','mai','juin','juil','août','sep','oct','nov','déc'];
    $d = new DateTime($dt);
    $str = $d->format('j') . ' ' . $months[(int)$d->format('n')-1] . '. ' . $d->format('Y');
    if ($time) $str .= ' à ' . $d->format('H:i');
    return $str;
}
