# TeranCar - Plateforme de Vente et Location de Véhicules

  <img src="public/images/banners/TeranCarsBan.png" alt="TeranCar Banner" width="100%"/>
</div>

TeranCar est une application web moderne pour la vente et la location de véhicules à Dakar, développée avec PHP et MySQL.

🌐 **Site en production** : [https://terancar-production.up.railway.app/](https://terancar-production.up.railway.app/)

<div align="center">
  <p>
    <a href="https://terancar-production.up.railway.app/">
      <img src="https://img.shields.io/badge/Production-Railway-blue?style=for-the-badge&logo=railway" alt="Railway Production"/>
    </a>
    <a href="LICENSE">
      <img src="https://img.shields.io/badge/License-MIT-purple.svg?style=for-the-badge" alt="MIT License"/>
    </a>
  </p>
</div>

## 🚀 Fonctionnalités

- 🔍 Recherche et filtrage avancés des véhicules
- 🔄 Tri par prix et année
- 🛒 Système de panier pour l'achat et la location
- 👤 Gestion des comptes utilisateurs
- 💖 Système de favoris
- 📱 Interface responsive

## 📋 Prérequis

- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Apache 2.4 ou supérieur
- XAMPP (recommandé pour le développement local)

## ⚙️ Installation

### Développement local

1. Clonez le dépôt dans votre dossier htdocs de XAMPP :
```bash
git clone https://github.com/votre-username/DaCar.git
cd DaCar
```

2. Configurez votre base de données MySQL en important le fichier SQL fourni.

3. Configurez les paramètres de connexion à la base de données dans `config/config.php`.

4. Assurez-vous que les services Apache et MySQL sont démarrés dans XAMPP.

### Déploiement Railway

Le projet est déployé automatiquement sur Railway à partir de la branche principale. La configuration inclut :
- Base de données MySQL hébergée sur Railway
- Variables d'environnement pour les connexions sécurisées
- HTTPS automatique
- Déploiement continu

## 🌐 Accès à l'application

- **Production** : [https://terancar-production.up.railway.app/](https://terancar-production.up.railway.app/)
- **Local** : `http://localhost/DaCar`
- **PhpMyAdmin (local)** : `http://localhost/phpmyadmin`

## 📞 Informations de contact (Fictives)

- **Téléphone** : +221 78 465 59 27 
- **Email** : contact@terancars.sn
- **Adresse** : 97 Route de la Corniche Dakar, Sénégal

## 🎨 Structure du projet

```
DaCar/
├── config/             # Configuration de la base de données
├── includes/           # Fichiers d'inclusion PHP
├── public/            
│   ├── assets/        # CSS, JS, images
│   ├── images/        # Images des véhicules
│   └── pages/         # Pages de l'application
├── .htaccess          # Configuration Apache
└── README.md
```

## 🔧 Configuration

### Base de données
- **Local** :
  - Nom de la base : `dacar`
  - Utilisateur : `root`
  - Mot de passe : `` (vide)
- **Production** :
  - Configuration via variables d'environnement Railway
  - Connexion sécurisée SSL/TLS

### Apache
Le fichier `.htaccess` est configuré pour :
- Gérer les redirections
- Protéger les fichiers sensibles
- Activer la réécriture d'URL
- Gérer les erreurs 404 et 403
- Forcer HTTPS en production

## 🛠️ Développement

Pour travailler sur le projet en local :
1. Assurez-vous que XAMPP est installé et que les services sont démarrés
2. Placez le projet dans le dossier `htdocs`
3. Accédez à l'application via `http://localhost/DaCar`

## 🔐 Sécurité

- Protection contre les injections SQL
- Validation des entrées utilisateur
- Protection des fichiers sensibles
- Gestion sécurisée des sessions
- HTTPS forcé en production
- Variables d'environnement sécurisées

## 📱 Responsive Design

L'application est entièrement responsive et s'adapte à tous les appareils :
- Desktop (>1200px)
- Tablette (768px - 1199px)
- Mobile (<767px)

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

## 📝 License

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 📧 Contact

Pour toute question ou suggestion :
- Ouvrez une issue sur GitHub
- Contactez-nous par email : contact@terancars.sn
- Appelez-nous : +221 78 123 45 67 
