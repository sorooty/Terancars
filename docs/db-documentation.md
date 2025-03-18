# Documentation de la Base de Données TeranCar

## 📊 Vue d'ensemble

Ce document présente la structure complète de la base de données TeranCar après optimisation. La base de données a été restructurée pour améliorer les performances, la cohérence et la maintenance.

## 🛠 Modifications effectuées

### Tables supprimées

Les tables suivantes ont été supprimées car elles étaient redondantes ou inutilisées :

1. **clients** - Remplacée par `utilisateurs` (sauvegarde dans `clients_backup`)
2. **produits** - Remplacée par `vehicules` (sauvegarde dans `produits_backup`)
3. **details_commandes** - Simplifiée et intégrée à `commandes`
4. **mouvements_stock** - Supprimée car trop complexe pour les besoins actuels
5. **rendez_vous** - Fonctionnalité non implémentée
6. **notifications** - Fonctionnalité non implémentée
7. **support** - Fonctionnalité non implémentée

### Tables ajoutées

1. **images_vehicules** - Pour gérer plusieurs images par véhicule
2. **specifications_vehicules** - Pour stocker les détails techniques des véhicules

### Tables modifiées

1. **vehicules** - Ajout du champ `statut` et des index de recherche
2. **panier** - Restructurée pour être liée à la base de données plutôt qu'à la session
3. **avis_clients** - Modification des références pour pointer vers `utilisateurs` et `vehicules`
4. **locations** - Modification des références pour pointer vers `utilisateurs` et `vehicules`
5. **commandes** - Modification de la référence de `id_client` vers `id_utilisateur`
6. **favoris** - Modification de la référence de `id_produit` vers `id_vehicule`
7. **paiements** - Amélioration avec des champs plus adaptés

## 📋 Structure finale

### Table `utilisateurs`

Cette table centralise toutes les informations sur les utilisateurs du système.

| Colonne | Type | Description |
|---------|------|-------------|
| id_utilisateur | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique de l'utilisateur |
| nom | VARCHAR(255) | Nom complet de l'utilisateur |
| email | VARCHAR(255) UNIQUE | Email de l'utilisateur (identifiant de connexion) |
| telephone | VARCHAR(15) | Numéro de téléphone |
| mot_de_passe | VARCHAR(255) | Mot de passe hashé |
| role | ENUM('client','admin','vendeur','support') | Rôle de l'utilisateur dans le système |
| date_inscription | TIMESTAMP | Date d'inscription |

### Table `vehicules`

Table centrale contenant tous les véhicules disponibles à la vente ou à la location.

| Colonne | Type | Description |
|---------|------|-------------|
| id_vehicule | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique du véhicule |
| marque | VARCHAR(100) | Marque du véhicule (indexée) |
| modele | VARCHAR(100) | Modèle du véhicule |
| annee | INT | Année de fabrication (indexée) |
| prix | DECIMAL(10,2) | Prix de vente (indexé) |
| stock | INT | Nombre d'unités disponibles |
| disponible_location | BOOLEAN | Indique si disponible à la location |
| tarif_location_journalier | DECIMAL(10,2) | Tarif journalier si location possible |
| carburant | ENUM | Type de carburant |
| transmission | ENUM | Type de transmission |
| kilometrage | INT | Kilométrage du véhicule |
| statut | ENUM | Statut actuel du véhicule |

### Table `panier`

Stocke les éléments du panier de chaque utilisateur de façon persistante.

| Colonne | Type | Description |
|---------|------|-------------|
| id_panier | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_utilisateur | INT | Référence à l'utilisateur |
| id_vehicule | INT | Référence au véhicule |
| type | ENUM('achat','location') | Type d'opération |
| quantite | INT | Nombre d'unités |
| date_debut_location | DATE | Date de début si location |
| date_fin_location | DATE | Date de fin si location |
| date_ajout | TIMESTAMP | Date d'ajout au panier |

### Table `commandes`

Enregistre les commandes des utilisateurs.

| Colonne | Type | Description |
|---------|------|-------------|
| id_commande | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_utilisateur | INT | Référence à l'utilisateur |
| date_commande | DATETIME | Date de la commande |
| montant_total | DECIMAL(10,2) | Montant total |
| statut | ENUM | Statut de la commande |

### Table `paiements`

Enregistre les transactions de paiement.

| Colonne | Type | Description |
|---------|------|-------------|
| id_paiement | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_commande | INT | Référence à la commande |
| reference_transaction | VARCHAR(50) | Référence unique de transaction |
| mode_paiement | ENUM | Mode de paiement utilisé |
| montant | DECIMAL(10,2) | Montant payé |
| statut | ENUM | Statut du paiement |
| date_paiement | TIMESTAMP | Date du paiement |

### Table `locations`

Gère les locations de véhicules.

| Colonne | Type | Description |
|---------|------|-------------|
| id_location | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_utilisateur | INT | Référence à l'utilisateur |
| id_vehicule | INT | Référence au véhicule |
| date_debut | DATETIME | Date de début de location |
| date_fin | DATETIME | Date de fin de location |
| tarif_total | DECIMAL(10,2) | Tarif total de la location |
| statut_location | ENUM | Statut de la location |

### Table `images_vehicules`

Stocke les images associées aux véhicules.

| Colonne | Type | Description |
|---------|------|-------------|
| id_image | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_vehicule | INT | Référence au véhicule |
| url_image | VARCHAR(255) | Chemin vers l'image |
| is_principale | BOOLEAN | Indique si c'est l'image principale |

### Table `specifications_vehicules`

Stocke les spécifications détaillées des véhicules.

| Colonne | Type | Description |
|---------|------|-------------|
| id_spec | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_vehicule | INT | Référence au véhicule |
| nom_spec | VARCHAR(100) | Nom de la spécification |
| valeur_spec | VARCHAR(100) | Valeur de la spécification |

### Table `avis_clients`

Stocke les avis des clients sur les véhicules.

| Colonne | Type | Description |
|---------|------|-------------|
| id_avis | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_utilisateur | INT | Référence à l'utilisateur |
| id_vehicule | INT | Référence au véhicule |
| note | INT | Note de 1 à 5 |
| commentaire | TEXT | Commentaire textuel |
| date_avis | DATETIME | Date de l'avis |

### Table `favoris`

Gère les véhicules favoris des utilisateurs.

| Colonne | Type | Description |
|---------|------|-------------|
| id_favori | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| id_utilisateur | INT | Référence à l'utilisateur |
| id_vehicule | INT | Référence au véhicule |
| date_ajout | TIMESTAMP | Date d'ajout aux favoris |

### Table `messages`

Stocke les messages de contact des utilisateurs.

| Colonne | Type | Description |
|---------|------|-------------|
| id_message | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| nom | VARCHAR(100) | Nom de l'expéditeur |
| prenom | VARCHAR(100) | Prénom de l'expéditeur |
| email | VARCHAR(255) | Email de l'expéditeur |
| telephone | VARCHAR(20) | Téléphone de l'expéditeur |
| sujet | VARCHAR(255) | Sujet du message |
| message | TEXT | Contenu du message |
| date_envoi | TIMESTAMP | Date d'envoi |

### Table `accessoires`

Stocke les accessoires disponibles (table conservée pour évolution future).

| Colonne | Type | Description |
|---------|------|-------------|
| id_accessoire | INT PRIMARY KEY AUTO_INCREMENT | Identifiant unique |
| nom | VARCHAR(255) | Nom de l'accessoire |
| description | TEXT | Description de l'accessoire |
| prix | DECIMAL(10,2) | Prix de l'accessoire |
| stock | INT | Nombre d'unités disponibles |
| categorie | VARCHAR(100) | Catégorie de l'accessoire |

## 🔄 Relations entre les tables

```
utilisateurs ─┬─ panier ─────────── vehicules
              ├─ commandes ────┬─── paiements
              ├─ locations ────┤
              ├─ avis_clients ─┤
              └─ favoris ──────┘
              
vehicules ────┬─ images_vehicules
              └─ specifications_vehicules
```

## 📊 Indexation et performances

Des index ont été ajoutés sur les colonnes fréquemment utilisées pour les recherches :
- `vehicules.marque`
- `vehicules.prix`
- `vehicules.annee`

## 🛡️ Sécurité des données

- Les mots de passe sont stockés sous forme hashée
- Des contraintes de clés étrangères protègent l'intégrité référentielle
- Les transactions sont sécurisées par des références uniques

## 🚀 Améliorations futures possibles

1. Ajouter une table `details_commandes` si besoin de tracer les produits individuels d'une commande
2. Implémenter la table `accessoires` dans le site web
3. Créer une table pour gérer les promotions
4. Ajouter un mécanisme de journalisation (logging) des actions importantes 