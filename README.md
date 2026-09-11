<p align="center">
  <strong style="font-size: 2rem; font-family: Georgia, serif;">⚖️ Lexora</strong>
</p>

<p align="center">
  Application web de gestion de cabinet d'avocats — dossiers, audiences, documents, facturation et notifications.
</p>

<p align="center">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.3-777bb4">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-13-f4645f">
  <img alt="Tailwind" src="https://img.shields.io/badge/Tailwind_CSS-3-38bdf8">
  <img alt="Tests" src="https://img.shields.io/badge/tests-133%20passed-22c55e">
  <img alt="Licence" src="https://img.shields.io/badge/licence-MIT-3b82f6">
</p>

---

## Table des matières

- [À propos](#à-propos)
- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Démarrage rapide](#démarrage-rapide)
- [Documentation](#documentation)
- [Structure du projet](#structure-du-projet)
- [Tests](#tests)
- [Licence](#licence)

---

## À propos

**Lexora** est un logiciel de gestion complet pour un cabinet d'avocats : suivi des dossiers et de leur historique, planification des audiences, gestion documentaire, facturation avec encaissement des paiements, notifications de rappel et administration des utilisateurs.

L'interface est entièrement en français. L'application est construite avec Laravel 13, Tailwind CSS 3, Alpine.js et Vite.

## Fonctionnalités

Vue d'ensemble rapide :

- **Tableau de bord** : statistiques en temps réel (dossiers actifs, audiences à venir, clients, revenus du mois, taux de réussite du cabinet, dossiers par mois) et prochaines audiences.
- **Clients** : création, modification, consultation (particuliers / entreprises), recherche, onglets dossiers et factures.
- **Dossiers** : gestion complète avec numérotation séquentielle, statuts, archivage, historique horodaté de chaque action et recherche.
- **Audiences** : planification au sein d'un dossier, statuts (Prévue / Annulée / Terminée).
- **Documents** : téléversement par dossier, catégorisation et consultation.
- **Factures** : numérotation séquentielle, suivi du montant restant dû, statut automatiquement synchronisé avec les paiements.
- **Paiements** : encaissement par mode de paiement, référence facultative, rejet des montants excédentaires.
- **Notifications** : rappels automatiques d'audience via une commande planifiée.
- **Administration** : gestion des utilisateurs et de leurs rôles, garde-fou empêchant de rétrograder le dernier administrateur.

Le détail complet est documenté dans [`docs/features.md`](docs/features.md).

## Stack technique

| Composant  | Technologie                                  |
| ---------- | -------------------------------------------- |
| Backend    | PHP 8.3, Laravel 13, Eloquent ORM            |
| Base de données | MySQL 8 (SQLite pour les tests)          |
| Frontend   | Tailwind CSS 3, Alpine.js 3, Vite           |
| Auth       | Laravel Breeze (sessions, vérification e-mail) |
| Tests      | PHPUnit 12 (133 tests), Laravel Pint        |
| Console    | Commande `audiences:rappel` planifiable (RG30) |

## Démarrage rapide

Prérequis : PHP ≥ 8.3, Composer, Node.js ≥ 20, MySQL 8 (ou SQLite).

bash
composer run setup          # installe les dépendances, crée .env, génère la clé,
                            # migre la base et construit les assets frontend
php artisan db:seed         # rôles + comptes de démonstration
php artisan storage:link    # accès aux documents téléversés
php artisan serve           # démarre le serveur de développement (http://localhost:8000)


> Tous les comptes de démonstration partagent le mot de passe `password` (voir [`docs/roles.md`](docs/roles.md)).

Le guide complet, étape par étape, se trouve dans [`docs/installation.md`](docs/installation.md).

## Documentation

| Sujet                 | Fichier                                       |
| --------------------- | --------------------------------------------- |
| Guide d'installation  | [`docs/installation.md`](docs/installation.md) |
| Fonctionnalités       | [`docs/features.md`](docs/features.md)         |
| Base de données       | [`docs/database.md`](docs/database.md)         |
| Architecture          | [`docs/architecture.md`](docs/architecture.md) |
| Rôles utilisateurs    | [`docs/roles.md`](docs/roles.md)               |

## Structure du projet

Le projet suit les conventions standard de Laravel : routes dans `routes/web.php`, contrôleurs dans `app/Http/Controllers`, modèles Eloquent dans `app/Models`, migrations et factories dans `database/`, vues Blade + Alpine dans `resources/views`. Les spécificités du projet (middleware de rôle, règle `EstAvocat`, trait de numérotation, commande de rappel) sont décrites dans [`docs/architecture.md`](docs/architecture.md).

## Tests

La suite de tests couvre les règles métier, les CRUD, la sécurité par rôles, l'ergonomie, le schéma de base de données et la détection des requêtes N+1.

bash
php artisan test         # 133 tests, 404 assertions
vendor/bin/pint          # formatage du code (Laravel Pint)