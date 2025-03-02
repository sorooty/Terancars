# Gestion des Branches Git pour le Projet DaCar/Terancar

Ce document explique la stratégie de gestion des branches Git utilisée pour ce projet, permettant de séparer le développement personnel du développement assisté par IA.

## Structure des Branches

Le projet utilise deux branches principales :

- **`main`** : Branche principale dédiée au développement personnel sans assistance d'IA
- **`version-ia`** : Branche dédiée aux modifications et améliorations développées avec l'assistance d'une IA

## Objectif de cette Séparation

Cette séparation permet de :

1. Maintenir une version "pure" développée entièrement par le développeur
2. Expérimenter avec des fonctionnalités assistées par IA sans affecter le travail principal
3. Comparer les approches et méthodologies entre développement traditionnel et assisté
4. Faciliter l'évaluation des contributions respectives dans le cadre d'un projet académique ou d'apprentissage

## Commandes Git Utiles

### Navigation entre les Branches

Pour basculer entre les différentes versions du projet :

```bash
# Passer à la version personnelle
git checkout main

# Passer à la version assistée par IA
git checkout version-ia
```

### Mise à Jour des Branches

#### Pour la branche principale (main)

```bash
git checkout main
# Faire vos modifications
git add .
git commit -m "Description des modifications personnelles"
git push origin main
```

#### Pour la branche IA (version-ia)

```bash
git checkout version-ia
# Faire vos modifications assistées par IA
git add .
git commit -m "Description des modifications assistées par IA"
git push origin version-ia
```

### Comparaison des Branches

Pour voir les différences entre les deux approches :

```bash
# Voir toutes les différences
git diff main version-ia

# Voir les différences pour un fichier spécifique
git diff main version-ia -- chemin/vers/fichier.php
```

## Fusion Sélective (Cherry-Pick)

Si vous souhaitez intégrer une fonctionnalité spécifique de la branche IA vers la branche principale :

```bash
# Identifier le commit à intégrer
git log version-ia

# Basculer sur main
git checkout main

# Intégrer le commit spécifique
git cherry-pick <hash-du-commit>
```

## Création de la Branche IA

La branche `version-ia` a été créée avec les commandes suivantes :

```bash
git checkout -b version-ia
git add .
git commit -m "Version développée avec l'aide de l'IA incluant les outils de diagnostic et les améliorations"
git push -u origin version-ia
```

## Fonctionnalités Spécifiques à la Branche IA

La branche `version-ia` contient notamment :

1. Des outils de diagnostic pour tester la connexion à la base de données
2. Une API de test pour vérifier la communication front-end/back-end
3. Des améliorations de l'interface utilisateur
4. Des optimisations de performance et de sécurité
5. Une meilleure gestion des erreurs et des exceptions

## Bonnes Pratiques

- Commiter régulièrement avec des messages descriptifs
- Maintenir une séparation claire entre les deux branches
- Documenter les fonctionnalités spécifiques à chaque branche
- Utiliser des tags pour marquer les versions importantes
- Faire des revues de code pour comparer les approches

---

*Document créé le : 15/05/2024* 