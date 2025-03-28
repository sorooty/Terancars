# Documentation de la Base de Données TeranCar

## 📊 Vue d'ensemble

Ce document présente la structure actuelle de la base de données TeranCar. La base de données a été optimisée pour les besoins spécifiques du site de vente et location de véhicules.

## 🛠 État actuel

### Tables actives

1. **utilisateurs** - Gestion des comptes utilisateurs
2. **vehicules** - Catalogue des véhicules
3. **images_vehicules** - Gestion des images des véhicules
4. **panier** - Gestion du panier d'achat et de location
5. **commandes** - Suivi des commandes
6. **paiements** - Gestion des transactions
7. **avis_clients** - Témoignages et avis
8. **messages** - Formulaire de contact
9. **accessoires** - Catalogue d'accessoires (préparé pour évolution future)
10. **favoris** - Gestion des favoris (en développement)

### Tables de sauvegarde

1. **clients_backup** - Ancienne structure des clients
2. **produits_backup** - Ancienne structure des produits

## 📋 Structure détaillée

### Table `utilisateurs`

Table centrale pour la gestion des utilisateurs.

| Colonne | Type | Description |
|---------|------|-------------|
| id_utilisateur | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| nom | VARCHAR(255) | Nom complet |
| email | VARCHAR(255) UNIQUE | Email (identifiant de connexion) |
| telephone | VARCHAR(15) | Numéro de téléphone |
| mot_de_passe | VARCHAR(255) | Mot de passe hashé |
| role | ENUM('client','admin') | Rôle de l'utilisateur |
| date_inscription | TIMESTAMP | Date d'inscription |

### Table `vehicules`

Catalogue principal des véhicules.

| Colonne | Type | Description |
|---------|------|-------------|
| id_vehicule | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| marque | VARCHAR(100) | Marque (indexée) |
| modele | VARCHAR(100) | Modèle |
| annee | INT | Année de fabrication |
| prix | DECIMAL(10,2) | Prix de vente |
| stock | INT | Nombre d'unités disponibles |
| disponible_location | BOOLEAN | Disponibilité pour location |
| tarif_location_journalier | DECIMAL(10,2) | Tarif journalier |
| carburant | ENUM('essence','diesel','électrique','hybride') | Type de carburant |
| transmission | ENUM('automatique','manuelle') | Type de transmission |
| kilometrage | INT | Kilométrage |
| statut | ENUM('disponible','vendu','en location','maintenance') | État actuel |

### Table `images_vehicules`

Gestion des galeries d'images.

| Colonne | Type | Description |
|---------|------|-------------|
| id_image | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_vehicule | INT | Référence au véhicule |
| url_image | VARCHAR(255) | Chemin de l'image |
| is_principale | BOOLEAN | Image principale (true/false) |

### Table `panier`

Gestion du panier avec support achat et location.

| Colonne | Type | Description |
|---------|------|-------------|
| id_panier | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_utilisateur | INT | Référence utilisateur |
| id_vehicule | INT | Référence véhicule |
| type | ENUM('achat','location') | Type d'opération |
| quantite | INT | Quantité |
| date_debut_location | DATE | Début location |
| date_fin_location | DATE | Fin location |
| date_ajout | TIMESTAMP | Date d'ajout |

### Table `commandes`

Suivi des commandes.

| Colonne | Type | Description |
|---------|------|-------------|
| id_commande | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_utilisateur | INT | Référence utilisateur |
| date_commande | DATETIME | Date de commande |
| montant_total | DECIMAL(10,2) | Montant total |
| statut | ENUM('en attente','payé','annulé') | État de la commande |

### Table `paiements`

Gestion des transactions.

| Colonne | Type | Description |
|---------|------|-------------|
| id_paiement | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_commande | INT | Référence commande |
| reference_transaction | VARCHAR(50) | Référence unique |
| mode_paiement | ENUM('carte','paypal','virement','especes') | Mode de paiement |
| montant | DECIMAL(10,2) | Montant |
| statut | ENUM('en_attente','accepte','refuse','rembourse') | État du paiement |
| date_paiement | TIMESTAMP | Date de paiement |

### Table `avis_clients`

Gestion des témoignages.

| Colonne | Type | Description |
|---------|------|-------------|
| id_avis | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_utilisateur | INT | Référence utilisateur |
| id_vehicule | INT | Référence véhicule (optionnel) |
| note | INT CHECK (note BETWEEN 1 AND 5) | Note sur 5 |
| commentaire | TEXT | Commentaire |
| date_avis | DATETIME | Date de l'avis |

### Table `messages`

Formulaire de contact.

| Colonne | Type | Description |
|---------|------|-------------|
| id_message | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| nom | VARCHAR(100) | Nom |
| prenom | VARCHAR(100) | Prénom |
| email | VARCHAR(255) | Email |
| telephone | VARCHAR(20) | Téléphone |
| sujet | VARCHAR(255) | Sujet |
| message | TEXT | Contenu |
| date_envoi | TIMESTAMP | Date d'envoi |

## 🔄 Relations principales

```
utilisateurs ─┬─ panier ─── vehicules ─── images_vehicules
              ├─ commandes ─ paiements
              ├─ avis_clients
              └─ favoris
```

## 📊 Optimisations

### Index actifs
- `vehicules.marque` - Recherche par marque
- `vehicules.prix` - Filtrage par prix
- `vehicules.annee` - Filtrage par année
- `utilisateurs.email` - Connexion et unicité

### Contraintes
- Clés étrangères avec `ON DELETE CASCADE` pour `images_vehicules`
- Clés étrangères avec `ON DELETE CASCADE` pour `panier`
- Contrainte CHECK sur `avis_clients.note`

## 🔒 Sécurité

- Hashage des mots de passe avec algorithme moderne
- Protection contre les injections SQL via PDO
- Contraintes d'intégrité sur les relations
- Validation des données avant insertion

## 🚀 Évolutions prévues

1. Implémentation complète des favoris
2. Activation du système d'accessoires
3. Ajout d'un système de notifications
4. Historique des modifications de véhicules 