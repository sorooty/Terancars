# Guide de déploiement

Ce document explique comment lancer l'application TeranCar en environnement local ou sur un serveur.

## Prérequis
- PHP 8 et l'extension PDO MySQL
- MySQL/MariaDB
- Un serveur web (Apache ou Nginx)

## Configuration
1. Copier le fichier `.env.example` vers `.env` puis adapter les valeurs si nécessaire :
   ```bash
   cp .env.example .env
   ```
   Les variables disponibles :
   - `SITE_URL` : chemin de base de l'application (ex. `/DaCar` ou vide en production)
   - `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` : paramètres de connexion MySQL

2. Vérifier le fichier `config/config.php`. Les paramètres sont désormais récupérés depuis les variables d'environnement avec des valeurs par défaut.

## Lancement en local
Pour un test rapide sans Apache, exécutez :
```bash
./start_local.sh
```
Puis ouvrez `http://localhost:8000` dans votre navigateur.

## Déploiement sur un serveur Apache
1. Copier les fichiers du dépôt sur votre serveur.
2. Configurer Apache pour que le `DocumentRoot` pointe vers le dossier `public` du projet.
3. S'assurer que `mod_rewrite` est activé afin que le fichier `.htaccess` gère le routage.
4. Adapter la valeur `SITE_URL` dans votre fichier `.env` (souvent vide en production).
5. Redémarrer Apache.

Avec cette configuration, le front‑end et l'API (dossier `api/`) seront disponibles et reliés à la même base de données.
