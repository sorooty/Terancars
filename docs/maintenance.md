# Guide de Maintenance du site TeranCar

Ce guide est destiné aux administrateurs et développeurs responsables de la maintenance du site TeranCar. Il présente les principales fonctionnalités et procédures à connaître pour assurer le bon fonctionnement du site.

## Table des matières

1. [Gestion des véhicules](#gestion-des-véhicules)
   - [Ajouter un nouveau véhicule](#ajouter-un-nouveau-véhicule)
   - [Mettre à jour un véhicule existant](#mettre-à-jour-un-véhicule-existant)
   - [Supprimer un véhicule](#supprimer-un-véhicule)

2. [Gestion des images](#gestion-des-images)
   - [Ajouter des images à un véhicule](#ajouter-des-images-à-un-véhicule)
   - [Définir une image principale](#définir-une-image-principale)
   - [Supprimer des images](#supprimer-des-images)

## Gestion des véhicules

### Ajouter un nouveau véhicule

Pour ajouter un nouveau véhicule dans la base de données, utilisez la requête SQL suivante :

```sql
INSERT INTO vehicules (
    marque, 
    modele, 
    annee, 
    prix, 
    stock, 
    disponible_location, 
    tarif_location_journalier, 
    carburant, 
    transmission, 
    kilometrage, 
    statut
) 
VALUES (
    'Marque', 
    'Modèle', 
    2024, 
    30000.00, 
    3, 
    1, 
    40.00, 
    'essence', 
    'automatique', 
    0, 
    'disponible'
);
```

#### Paramètres importants :
- `disponible_location` : 1 pour disponible, 0 pour non disponible
- `carburant` : Valeurs acceptées : 'essence', 'diesel', 'électrique', 'hybride'
- `transmission` : Valeurs acceptées : 'automatique', 'manuelle'
- `statut` : Valeurs acceptées : 'disponible', 'vendu', 'en location', 'maintenance'

### Mettre à jour un véhicule existant

Pour mettre à jour les informations d'un véhicule existant :

```sql
UPDATE vehicules SET 
    prix = 32000.00,
    stock = 4,
    statut = 'disponible'
WHERE id_vehicule = 28;
```

### Supprimer un véhicule

Pour supprimer un véhicule de la base de données :

```sql
DELETE FROM vehicules WHERE id_vehicule = 28;
```

**Attention :** Cette opération supprimera également toutes les images associées au véhicule grâce à la contrainte de clé étrangère `ON DELETE CASCADE`.

## Gestion des images

### Ajouter des images à un véhicule

Il existe deux méthodes pour associer des images à un véhicule :

#### 1. Méthode simple (par convention de nommage)

1. Nommez votre image avec l'ID du véhicule comme nom de fichier (ex: `28.jpg` pour le véhicule d'ID 28)
2. Placez cette image dans le dossier `public/images/vehicules/`

L'application reconnaîtra automatiquement cette image comme étant associée au véhicule d'ID 28.

#### 2. Méthode avancée (avec la table images_vehicules)

Cette méthode permet d'associer plusieurs images à un véhicule et de définir une image principale.

1. Ajoutez d'abord l'image dans le dossier `public/images/vehicules/` (le nom du fichier peut être quelconque)
2. Insérez une entrée dans la table `images_vehicules` :

```sql
INSERT INTO images_vehicules (id_vehicule, url_image, is_principale) 
VALUES (28, 'images/vehicules/nom-image.jpg', 0);
```

Notez que :
- `id_vehicule` doit correspondre à un ID existant dans la table vehicules
- `url_image` est le chemin relatif de l'image par rapport au dossier public
- `is_principale` indique si l'image est l'image principale (1) ou non (0)

### Définir une image principale

Pour définir une image comme principale :

```sql
-- D'abord, réinitialiser toutes les images du véhicule comme non-principales
UPDATE images_vehicules SET is_principale = 0 WHERE id_vehicule = 28;

-- Ensuite, définir l'image souhaitée comme principale
UPDATE images_vehicules SET is_principale = 1 WHERE id_image = 42;
```

### Supprimer des images

Pour supprimer une image de la base de données :

```sql
DELETE FROM images_vehicules WHERE id_image = 42;
```

**Note :** Cette opération ne supprime pas le fichier physique. Pour une maintenance complète, pensez à supprimer également le fichier du serveur.

---

Ce document sera mis à jour régulièrement avec les nouvelles fonctionnalités et procédures de maintenance du site TeranCar.

Dernière mise à jour : 18 mars 2024 