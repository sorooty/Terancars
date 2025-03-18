# Documentation TeranCar

Ce document centralise les informations essentielles pour la maintenance et l'évolution du site TeranCar.

## Table des matières

1. [Configuration générale](#configuration-générale)
2. [Gestion des véhicules](#gestion-des-véhicules)
   - [Ajout de véhicules](#ajout-de-véhicules)
   - [Gestion des images](#gestion-des-images)
3. [Interface utilisateur](#interface-utilisateur)
   - [Galerie d'images](#galerie-dimages)
4. [Gestion des utilisateurs](#gestion-des-utilisateurs)
5. [Système de panier](#système-de-panier)
6. [Commandes et paiements](#commandes-et-paiements)

## Configuration générale

Le fichier principal de configuration se trouve dans `config/config.php`. Il contient :
- Les paramètres de connexion à la base de données
- Les chemins d'accès aux différents dossiers
- Les constantes globales de l'application

## Gestion des véhicules

### Ajout de véhicules

Pour ajouter un nouveau véhicule dans la base de données, utilisez la requête SQL suivante :

```sql
INSERT INTO vehicules (marque, modele, annee, prix, stock, disponible_location, tarif_location_journalier, carburant, transmission, kilometrage, statut) 
VALUES ('Marque', 'Modèle', 2024, 30000.00, 3, 1, 40.00, 'essence', 'automatique', 0, 'disponible');
```

Assurez-vous de personnaliser les valeurs selon les caractéristiques du véhicule à ajouter.

### Gestion des images

Deux méthodes sont disponibles pour associer des images aux véhicules :

#### Méthode simple

1. Nommez votre image avec l'ID du véhicule (ex: `28.jpg` pour le véhicule d'ID 28)
2. Placez cette image dans le dossier `public/images/vehicules/`

#### Méthode avancée (avec la table images_vehicules)

Cette méthode permet d'associer plusieurs images à un même véhicule et de définir une image principale.

1. Ajoutez l'image dans le dossier `public/images/vehicules/`
2. Insérez une entrée dans la table `images_vehicules` :

```sql
INSERT INTO images_vehicules (id_vehicule, url_image, is_principale) 
VALUES (ID_VEHICULE, 'images/vehicules/nom-image.jpg', 0);
```

Notes :
- `id_vehicule` doit correspondre à un ID existant dans la table vehicules
- `is_principale = 1` définit une image comme principale (une seule par véhicule)
- `is_principale = 0` pour les images secondaires
- Le chemin dans `url_image` est relatif au dossier public

## Interface utilisateur

### Galerie d'images

La page de détail des véhicules dispose d'une galerie d'images interactive. Les éléments principaux de cette galerie sont :

- `.image-gallery` : conteneur principal de la galerie
- `.gallery-slider` : conteneur des images qui défilent
- `.gallery-img` : chaque image individuelle
- `.gallery-prev` et `.gallery-next` : boutons de navigation
- `.gallery-dot` : indicateurs de position

Si vous rencontrez des problèmes d'affichage des boutons de navigation, vérifiez les styles suivants dans `public/assets/css/vehicule.css` :

```css
.gallery-prev,
.gallery-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.8);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
    color: var(--primary-color);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.gallery-prev { left: 1rem; }
.gallery-next { right: 1rem; }
```

Ces styles assurent que les boutons de navigation sont bien positionnés et visibles sur les côtés de la galerie.

## Gestion des utilisateurs

À documenter

## Système de panier

À documenter

## Commandes et paiements

À documenter