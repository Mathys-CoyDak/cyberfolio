# Projet « Cyberfolio »

## Compte administrateur

- URL du *back-office* : `127.0.0.1:8000/admin`
- Identifiant : `admin@exemple.com`
- Mot de passe : `admin123`

## État d'avancement

Les utilisateurs peuvent créer/modifier/supprimer un projet et des technologies.
Un projet peut associées des technologies et inversement.
Les admin peuvent créer/modifier/supprimer tout les utilisateurs/projet/technologies de la base.

## Difficultés rencontrées et solutions

La gestion de l'authentification m'a pris un peu de temps à comprendre: J'ai suivi et repris plusieurs fois le tp7

## Bilan des acquis

- Utilisation de PhpStorm
- Faire un serveur Symfony
- Les automatisations avec les commandes symfony/php

## Remarques complémentaires

Chargez les données initiales pour avoir un utilisateur admin(fixtures) :
   ```bash
   php bin/console doctrine:fixtures:load
   ```

