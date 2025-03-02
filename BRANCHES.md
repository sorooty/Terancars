## Différences entre les Branches

### Fichier de Configuration

Les deux branches utilisent des fichiers de configuration différents :

#### Branche `main` (config/config.php)

- Utilise la base de données "dacar"
- Port MySQL standard (3306)
- Nom du site : "DaCar"
- Fonctionnalités de base uniquement

#### Branche `version-ia` (config/config.php)

- Utilise la base de données "terancar"
- Port MySQL personnalisé (3307)
- Nom du site : "Terancar"
- Fonctionnalités avancées :
  - Vérification de l'existence des tables et colonnes
  - Débogage de la base de données
  - Journalisation des erreurs
  - Vérification de la structure de la base de données

Cette séparation permet de maintenir deux environnements distincts, chacun avec sa propre configuration adaptée à son contexte de développement. 