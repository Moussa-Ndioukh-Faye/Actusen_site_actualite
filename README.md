# 📰 ActuSen — Site d'actualités en PHP

![PHP](https://img.shields.io/badge/PHP-777bb4?style=flat-square&logo=php&logoColor=white) ![MySQL](https://img.shields.io/badge/MySQL-4479a1?style=flat-square&logo=mysql&logoColor=white) ![ESP](https://img.shields.io/badge/ESP_Dakar-Projet_académique-1f6f50?style=flat-square)

Site d'actualités dynamique avec un espace d'administration : publication d'articles classés par catégories, gestion des comptes et contrôle d'accès par rôle.

## Fonctionnalités

- **Articles** : liste, lecture détaillée, ajout, modification, suppression
- **Catégories** : gestion complète (CRUD)
- **Utilisateurs** : inscription, connexion, gestion des comptes
- **Rôles** : `editeur` (gère les contenus) et `administrateur` (gère aussi les utilisateurs)
- **Sécurité** : mots de passe hachés (`password_hash` / `password_verify`), requêtes préparées PDO
- **Installation guidée** : la page `setup.php` crée la base et les tables

## Structure

```
accueil.php          page d'accueil (derniers articles)
articles/            CRUD des articles
categories/          CRUD des catégories
utilisateurs/        CRUD des utilisateurs
config/connexion.php connexion PDO à MySQL
includes/            en-tête, menu, pied de page
site_actualite.sql   script de la base
```

## Lancer le projet

1. Copier le dossier dans `htdocs` (XAMPP) ou `www` (WAMP/Laragon).
2. Démarrer Apache et MySQL.
3. Ouvrir `http://localhost/Actusen_site_actualite/setup.php` pour créer la base, ou importer `site_actualite.sql` dans phpMyAdmin.
4. Adapter les identifiants dans `config/connexion.php` si besoin.
5. Se connecter depuis `connexion.php`.

> Version aboutie du projet. La première version se trouve dans [projet_php_site_d_actualites](https://github.com/Moussa-Ndioukh-Faye/projet_php_site_d_actualites).

---

<p align="center">Réalisé par <a href="https://github.com/Moussa-Ndioukh-Faye"><b>Moussa Ndioukh Faye</b></a> · École Supérieure Polytechnique de Dakar (ESP/UCAD)</p>
