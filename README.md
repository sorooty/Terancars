# TeranCar - Plateforme de Vente et Location de Véhicules

  <img src="public/images/banners/TeranCarsBan.png" alt="TeranCar Banner" width="100%"/>
</div>

TeranCar est une plateforme web moderne permettant la location et la vente de véhicules à Dakar, Sénégal. Le site offre une expérience utilisateur intuitive avec des fonctionnalités avancées de recherche, de réservation et de gestion de compte.

🌐 **Site en développement local**

<div align="center">
  <p>
    <a href="LICENSE">
      <img src="https://img.shields.io/badge/License-MIT-purple.svg?style=for-the-badge" alt="MIT License"/>
    </a>
  </p>
</div>

## 🚀 Fonctionnalités

### 👤 Gestion des utilisateurs
- Inscription et connexion sécurisées
- Profils utilisateurs avec rôles différenciés
- Système de rôles (client, admin)
- Gestion des favoris (en développement)

### 🚘 Gestion des véhicules
- Catalogue complet avec filtres avancés
- Filtrage par marque, modèle, prix, année, carburant et transmission
- Système de recherche en temps réel
- Galerie d'images avec navigation
- Gestion des images principales et secondaires
- Spécifications détaillées des véhicules

### 🛒 Système de panier
- Panier persistant en base de données
- Support pour achat et location
- Gestion des quantités
- Calcul automatique des prix
- Vérification de disponibilité

### 💳 Système de paiement
- Interface de paiement sécurisée
- Gestion des commandes
- Historique des transactions
- Confirmation par email (en développement)

### 📞 Support client
- Formulaire de contact
- Système de messagerie
- Témoignages clients
- FAQ (en développement)

## 📋 Prérequis

- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur (Port 3307)
- Apache 2.4 ou supérieur
- XAMPP (recommandé pour le développement local)
- Extensions PHP requises :
  - PDO
  - PDO_MySQL
  - GD
  - mbstring
  - json

## ⚙️ Installation

### Développement local

1. Clonez le dépôt dans votre dossier htdocs de XAMPP :
```bash
git clone https://github.com/votre-username/DaCar.git
cd DaCar
```

2. Importez le fichier `terancarDB.sql` dans votre base de données MySQL.

3. Configurez les paramètres de connexion dans `config/config.php`.

4. Assurez-vous que les services Apache et MySQL sont démarrés dans XAMPP.

## 🌐 Accès à l'application

- **Local** : `http://localhost/DaCar`
- **PhpMyAdmin** : `http://localhost/phpmyadmin`

## 🛠️ Structure du projet

```
DaCar/
├── app/
│   └── views/           # Composants réutilisables
├── config/
│   └── config.php       # Configuration globale
├── includes/
│   ├── init.php        # Initialisation
│   ├── functions.php   # Fonctions utilitaires
│   ├── template.php    # Template principal
│   └── entry.php       # Page d'entrée
├── public/
│   ├── assets/         # Ressources statiques
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── pages/          # Pages du site
│       ├── auth/       # Authentification
│       ├── catalogue/  # Catalogue
│       ├── vehicule/   # Détails véhicules
│       ├── panier/     # Gestion panier
│       └── errors/     # Pages d'erreur
├── docs/               # Documentation
└── .htaccess          # Configuration Apache
```

## 🔧 Configuration

### Base de données
- **Local** :
  - Nom de la base : `terancar`
  - Port : `3307`
  - Utilisateur : `root`
  - Mot de passe : `` (vide)

### Apache
Le fichier `.htaccess` gère :
- Les redirections
- La réécriture d'URL
- La gestion des erreurs
- La protection des dossiers

## 🔐 Sécurité

- Protection contre les injections SQL avec PDO
- Validation des entrées utilisateur
- Protection des fichiers sensibles
- Gestion sécurisée des sessions
- Protection CSRF
- Mots de passe hachés

## 📱 Responsive Design

L'application est optimisée pour :
- Desktop (>1200px)
- Tablette (768px - 1199px)
- Mobile (<767px)

## 🎨 Interface utilisateur

- Design moderne avec animations
- Navigation fluide
- Galerie d'images interactive
- Filtres dynamiques
- Thème personnalisé
- Interface en français

## 🔄 Système de routage

Routage personnalisé avec :
- URLs propres
- Gestion des paramètres
- Redirection intelligente
- Pages d'erreur personnalisées

## 📊 Base de données

Tables principales :
- `utilisateurs` : Gestion des comptes
- `vehicules` : Catalogue
- `images_vehicules` : Gestion des images
- `panier` : Système de panier
- `commandes` : Suivi des achats
- `messages` : Communication
- `avis_clients` : Témoignages

## 📞 Contact

- **Email** : contact@terancars.sn
- **Adresse** : Dakar, Sénégal

## 📝 License

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
1. Fork le projet
2. Créer une branche pour votre fonctionnalité
