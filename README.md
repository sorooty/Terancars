# TeranCar - Plateforme de Vente et Location de Véhicules

![TeranCar Logo](public/assets/images/logo.png)

## 📝 Description

TeranCar est une plateforme web moderne dédiée à la vente et à la location de véhicules de qualité. Notre site offre une expérience utilisateur intuitive et élégante, permettant aux clients de parcourir, comparer et choisir parmi une large sélection de véhicules.

## 🚀 Fonctionnalités

- **Catalogue Interactif** : Parcourez notre sélection de véhicules avec des filtres avancés
- **Système d'Authentification** : Création de compte et connexion sécurisée
- **Interface Responsive** : Design adaptatif pour tous les appareils
- **Section Marques** : Accès rapide aux véhicules par marque
- **Système de Contact** : Formulaire de contact et informations de l'entreprise
- **Avis Clients** : Témoignages et retours d'expérience

## 🛠 Technologies Utilisées

- HTML5
- CSS3 (avec variables CSS pour une personnalisation facile)
- JavaScript
- PHP
- Font Awesome pour les icônes
- Google Fonts (Poppins)

## 🎨 Design

Le site utilise une palette de couleurs moderne et professionnelle :
- Bleu marine (`#0B1A30`) : Couleur principale
- Rose/Violet (`#B088B0`) : Couleur secondaire/accent
- Blanc et nuances de gris pour le contraste

## 💻 Installation

1. Clonez le repository :
```bash
git clone https://github.com/votre-username/TeranCar.git
```

2. Placez les fichiers dans votre serveur web (par exemple, dans le dossier `htdocs` de XAMPP)

3. Assurez-vous que PHP est installé et configuré

4. Accédez au site via votre navigateur :
```
http://localhost/TeranCar
```

## 📁 Structure du Projet

```
TeranCar/
├── app/
│   ├── config/
│   │   ├── database.php      # Configuration de la base de données
│   │   └── config.php        # Variables globales et constantes
│   ├── controllers/
│   │   ├── AuthController.php          # Gestion authentification
│   │   ├── VehicleController.php       # Gestion des véhicules
│   │   └── ContactController.php       # Gestion des contacts
│   ├── models/
│   │   ├── Database.php     # Classe de connexion à la BDD
│   │   ├── User.php         # Gestion des utilisateurs
│   │   ├── Vehicle.php      # Gestion des véhicules
│   │   └── Booking.php      # Gestion des réservations
│   └── views/
│       ├── layouts/
│       │   ├── header.php   # En-tête commune
│       │   └── footer.php   # Pied de page commun
│       ├── auth/
│       │   ├── login.php    # Page de connexion
│       │   └── register.php # Page d'inscription
│       └── vehicles/
│           ├── list.php     # Liste des véhicules
│           └── detail.php   # Détail d'un véhicule
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── style.css              # Styles globaux
│   │   │   ├── components/            # Styles des composants
│   │   │   │   ├── header.css
│   │   │   │   ├── footer.css
│   │   │   │   └── forms.css
│   │   │   └── pages/                 # Styles spécifiques aux pages
│   │   │       ├── home.css
│   │   │       └── catalogue.css
│   │   ├── js/
│   │   │   ├── main.js               # JavaScript principal
│   │   │   └── components/           # Scripts des composants
│   │   │       ├── slider.js
│   │   │       └── filter.js
│   │   └── images/
│   │       ├── logo/
│   │       └── vehicles/
│   ├── includes/             # Fichiers d'inclusion PHP
│   │   ├── functions.php    # Fonctions utilitaires
│   │   └── process/         # Traitement des formulaires
│   │       ├── auth.php     # Traitement authentification
│   │       └── contact.php  # Traitement formulaire contact
│   └── index.php            # Point d'entrée principal
├── database/
│   └── terancar.sql         # Structure et données initiales
└── README.md

```

## 🔄 Liaison Front-end/Back-end

### 1. Configuration de la Base de Données
```php
// app/config/database.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'terancar');
```

### 2. Exemple de Modèle
```php
// app/models/Vehicle.php
class Vehicle {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllVehicles() {
        $query = "SELECT * FROM vehicles";
        return $this->db->query($query);
    }
}
```

### 3. Exemple de Contrôleur
```php
// app/controllers/VehicleController.php
class VehicleController {
    private $vehicleModel;

    public function __construct() {
        $this->vehicleModel = new Vehicle();
    }

    public function showVehicles() {
        $vehicles = $this->vehicleModel->getAllVehicles();
        require_once '../app/views/vehicles/list.php';
    }
}
```

### 4. Exemple de Vue
```php
// app/views/vehicles/list.php
<?php require_once '../app/views/layouts/header.php'; ?>

<div class="vehicles-list">
    <?php foreach($vehicles as $vehicle): ?>
        <div class="vehicle-card">
            <img src="<?= $vehicle['image'] ?>" alt="<?= $vehicle['name'] ?>">
            <h3><?= $vehicle['name'] ?></h3>
            <p><?= $vehicle['price'] ?> €</p>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
```

### 5. Traitement des Formulaires
```php
// public/includes/process/contact.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST['message']);
    
    // Traitement et envoi à la base de données
}
```

Cette structure simplifiée permet :
- Une séparation claire entre front-end et back-end
- Une organisation logique des fichiers
- Une maintenance facile
- Une intégration simple du travail d'équipe

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commit vos changements
4. Push sur la branche
5. Ouvrir une Pull Request

## 📫 Contact

- Site Web : (à insérer après hosting)
- Email : contact@terancar.fr
- Téléphone : +33 1 23 45 67

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails. 