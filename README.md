# DaCar - Site de Vente et Location de Véhicules

DaCar est une application web PHP permettant la gestion d'un site de vente et location de véhicules. Le projet utilise une architecture simple avec PHP, MySQL, HTML, CSS et JavaScript.

## Fonctionnalités

- Affichage des derniers véhicules ajoutés
- Catalogue complet des véhicules disponibles
- Détails des véhicules avec galerie d'images
- Système de panier d'achat
- Options d'achat et de location
- Interface responsive adaptée à tous les appareils

## Structure du Projet

```
DaCar/
├── assets/
│   ├── CSS/
│   │   └── style.css
│   ├── images/
│   │   ├── vehicles/
│   │   └── hero-bg.jpg
│   └── jscript/
│       └── main.js
├── config/
│   └── config.php
├── database/
│   └── terancar.sql
├── includes/
│   ├── footer.php
│   ├── header.php
│   └── navbar.php
├── public/
│   ├── catalogue.php
│   ├── checkout.php
│   ├── contact.php
│   ├── details.php
│   ├── index.php
│   ├── login.php
│   ├── panier.php
│   └── register.php
└── README.md
```

## Installation

1. Clonez ce dépôt dans votre répertoire web (par exemple, dans `htdocs` pour XAMPP)
2. Importez le fichier `database/terancar.sql` dans votre serveur MySQL
3. Configurez les paramètres de connexion à la base de données dans `config/config.php`
4. Accédez au site via votre navigateur : `http://localhost/DaCar/public/index.php`

## Configuration Requise

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx, etc.)
- Navigateur web moderne

## Structure de la Base de Données

La base de données `terancar` contient les tables suivantes :

- `vehicules` : Stocke les informations sur les véhicules disponibles
- `utilisateurs` : Gère les comptes utilisateurs
- `commandes` : Enregistre les commandes d'achat
- `locations` : Enregistre les locations de véhicules

## Améliorations Futures

- Système d'authentification complet
- Gestion des avis clients
- Système de recherche avancée
- Intégration de passerelles de paiement
- Tableau de bord administrateur
- Système de réservation en ligne pour les locations

## Crédits

Développé par [Votre Nom] - 2023

## Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.
