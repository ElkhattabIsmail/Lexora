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
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Lancement du projet](#lancement-du-projet)
- [Commandes](#commandes)
- [Routes de l'application](#routes-de-lapplication)
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

| Composant      | Technologie                                   |
| -------------- | --------------------------------------------- |
| Backend        | PHP 8.3, Laravel 13, Eloquent ORM             |
| Base de données | MySQL 8 (SQLite pour les tests)             |
| Frontend       | Tailwind CSS 3, Alpine.js 3, Vite             |
| Auth           | Laravel Breeze (sessions, vérification e-mail)|
| Tests          | PHPUnit 12 (133 tests), Laravel Pint          |
| Console        | Commande `audiences:rappel` planifiable (RG30)|

## Prérequis

| Outil      | Version minimale |
| ---------- | ---------------- |
| PHP        | 8.3              |
| Composer   | 2.x              |
| Node.js    | 20 (LTS)         |
| NPM        | 10               |
| Base de données | MySQL 8 **ou** SQLite 3.35+ |

## Installation

```bash
# 1. Récupérer le code source
git clone <url-du-dépôt> lexora
cd lexora

# 2. Installer les dépendances PHP
composer install

# 3. Créer le fichier d'environnement et générer la clé
cp .env.example .env                 # Windows : copy .env.example .env
php artisan key:generate

# 4. Installer les dépendances frontend
npm install
npm run build                        # production, ou : npm run dev (développement)

# 5. Configurer la base de données dans .env
#    SQLite (défaut) : DB_CONNECTION=sqlite puis touch database/database.sqlite
#    MySQL          : créer la base et renseigner DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD

# 6. Migrer et peupler la base
php artisan migrate
php artisan db:seed                  # rôles + comptes de démonstration

# 7. Accès aux documents téléversés
php artisan storage:link
```

> **Installation automatique** : `composer run setup` enchaîne les étapes 2 à 6 (hors seed). Ensuite `php artisan db:seed` et `php artisan storage:link`.

> Tous les comptes de démonstration partagent le mot de passe `password` (voir [`docs/roles.md`](docs/roles.md)).

## Lancement du projet

```bash
# Démarrage complet (serveur Laravel + Vite en parallèle)
composer run dev

# Ou manuellement, dans deux terminaux :
php artisan serve        # backend  → http://localhost:8000
npm run dev              # frontend → recompilation Vite (hot reload)
```

## Commandes

Les commandes nécessaires au fonctionnement et au développement du projet :

| Commande                          | Description |
| --------------------------------- | ----------- |
| `composer install`               | Installe les dépendances PHP. |
| `composer run setup`             | Tout-en-un : dépendances PHP + `.env` + clé + migration + build frontend. |
| `composer run dev`               | Lance `php artisan serve` et `npm run dev` simultanément. |
| `composer test`                  | Lance la suite de tests (`config:clear` + `php artisan test`). |
| `npm install`                    | Installe les dépendances frontend. |
| `npm run dev`                    | Compile les assets en mode développement (rechargement). |
| `npm run build`                  | Compile et optimise les assets pour la production. |
| `php artisan serve`              | Démarre le serveur de développement (`http://localhost:8000`). |
| `php artisan key:generate`       | Génère la clé d'application `APP_KEY`. |
| `php artisan migrate`            | Exécute les migrations (schéma de base). |
| `php artisan db:seed`            | Peuple la base (rôles, comptes démo, données). |
| `php artisan storage:link`       | Crée le lien `public/storage` pour les documents. |
| `php artisan audiences:rappel`   | Génère les rappels d'audience (`--horizon=3` jour(s)). |
| `php artisan optimize:clear`     | Vide les caches (config, route, cache, view…). |
| `php artisan schedule:list`      | Liste les tâches planifiées. |
| `php artisan route:list`         | Liste les routes publiées. |
| `php artisan test`               | Exécute la suite de tests (133 tests PHPUnit). |
| `vendor/bin/pint`                | Formate le code PHP (Laravel Pint). |

## Routes de l'application

Liste complète des 52 routes publiques, organisées par zone d'accès. Voir aussi `php artisan route:list`.

### Publique (invités + authentifiés)

| Méthode | URI                      | Nom       | Contrôleur / action |
| ------- | ------------------------ | --------- | ------------------- |
| GET     | `/`                      | —         | Vue `welcome`       |
| GET     | `/dashboard`             | `dashboard` | `DashboardController@index` |

### Authentification (invités)

| Méthode | URI                      | Nom              | Contrôleur / action |
| ------- | ------------------------ | ---------------- | ------------------- |
| GET     | `/register`              | `register`       | `RegisteredUserController@create` |
| POST    | `/register`              | —                | `RegisteredUserController@store`  |
| GET     | `/login`                 | `login`          | `AuthenticatedSessionController@create` |
| POST    | `/login`                 | —                | `AuthenticatedSessionController@store` |
| GET     | `/forgot-password`       | `password.request` | `PasswordResetLinkController@create` |
| POST    | `/forgot-password`       | `password.email` | `PasswordResetLinkController@store` |
| GET     | `/reset-password/{token}`| `password.reset` | `NewPasswordController@create` |
| POST    | `/reset-password`        | `password.store` | `NewPasswordController@store`  |

### Authentifié

| Méthode | URI                             | Nom                 | Contrôleur / action |
| ------- | ------------------------------- | ------------------- | ------------------- |
| GET     | `/verify-email`                 | `verification.notice` | `EmailVerificationPromptController` |
| GET     | `/verify-email/{id}/{hash}`     | `verification.verify` | `VerifyEmailController` |
| POST    | `/email/verification-notification` | `verification.send` | `EmailVerificationNotificationController@store` |
| GET     | `/confirm-password`             | `password.confirm` | `ConfirmablePasswordController@show` |
| POST    | `/confirm-password`             | —                  | `ConfirmablePasswordController@store` |
| PUT     | `/password`                     | `password.update`  | `PasswordController@update` |
| POST    | `/logout`                       | `logout`           | `AuthenticatedSessionController@destroy` |
| GET     | `/profile`                      | `profile.edit`     | `ProfileController@edit` |
| PATCH   | `/profile`                      | `profile.update`   | `ProfileController@update` |
| DELETE  | `/profile`                      | `profile.destroy`  | `ProfileController@destroy` |

### Ressources principales (`auth` + `verified` + `role:Avocat,Administrateur`)

Clients :

| Méthode | URI                     | Nom            | Contrôleur / action |
| ------- | ----------------------- | -------------- | ------------------- |
| GET     | `/clients`              | `clients.index` | `ClientController@index` |
| POST    | `/clients`              | `clients.store` | `ClientController@store` |
| GET     | `/clients/create`       | `clients.create`| `ClientController@create` |
| GET     | `/clients/{client}`     | `clients.show`  | `ClientController@show` |
| PUT/PATCH | `/clients/{client}`   | `clients.update`| `ClientController@update` |
| DELETE  | `/clients/{client}`     | `clients.destroy`| `ClientController@destroy` |
| GET     | `/clients/{client}/edit`| `clients.edit`  | `ClientController@edit` |

Dossiers :

| Méthode | URI                     | Nom             | Contrôleur / action |
| ------- | ----------------------- | --------------- | ------------------- |
| GET     | `/dossiers`             | `dossiers.index` | `DossierController@index` |
| POST    | `/dossiers`             | `dossiers.store` | `DossierController@store` |
| GET     | `/dossiers/create`      | `dossiers.create`| `DossierController@create` |
| GET     | `/dossiers/{dossier}`   | `dossiers.show`  | `DossierController@show` |
| PUT/PATCH | `/dossiers/{dossier}` | `dossiers.update`| `DossierController@update` |
| DELETE  | `/dossiers/{dossier}`   | `dossiers.destroy`| `DossierController@destroy` |
| GET     | `/dossiers/{dossier}/edit` | `dossiers.edit`| `DossierController@edit` |

Audiences (imbriquées dans un dossier) :

| Méthode | URI                                           | Nom                        | Contrôleur / action |
| ------- | --------------------------------------------- | -------------------------- | ------------------- |
| GET     | `/dossiers/{dossier}/audiences/create`        | `dossiers.audiences.create`| `AudienceController@create` |
| POST    | `/dossiers/{dossier}/audiences`               | `dossiers.audiences.store` | `AudienceController@store` |
| GET     | `/dossiers/{dossier}/audiences/{audience}/edit` | `dossiers.audiences.edit`| `AudienceController@edit` |
| PUT/PATCH | `/dossiers/{dossier}/audiences/{audience}`   | `dossiers.audiences.update`| `AudienceController@update` |
| DELETE  | `/dossiers/{dossier}/audiences/{audience}`    | `dossiers.audiences.destroy`| `AudienceController@destroy` |

Documents (imbriqués dans un dossier) :

| Méthode | URI                                           | Nom                         | Contrôleur / action |
| ------- | --------------------------------------------- | --------------------------- | ------------------- |
| POST    | `/dossiers/{dossier}/documents`              | `dossiers.documents.store`  | `DocumentController@store` |
| DELETE  | `/dossiers/{dossier}/documents/{document}`   | `dossiers.documents.destroy`| `DocumentController@destroy` |

Factures :

| Méthode | URI                     | Nom             | Contrôleur / action |
| ------- | ----------------------- | --------------- | ------------------- |
| GET     | `/factures`             | `factures.index`| `FactureController@index` |
| POST    | `/factures`             | `factures.store`| `FactureController@store` |
| GET     | `/factures/create`      | `factures.create`| `FactureController@create` |
| GET     | `/factures/{facture}`   | `factures.show`  | `FactureController@show` |
| PUT/PATCH | `/factures/{facture}` | `factures.update`| `FactureController@update` |
| DELETE  | `/factures/{facture}`   | `factures.destroy`| `FactureController@destroy` |
| GET     | `/factures/{facture}/edit` | `factures.edit`| `FactureController@edit` |

Paiements (imbriqués dans une facture) :

| Méthode | URI                                              | Nom                          | Contrôleur / action |
| ------- | ------------------------------------------------ | ---------------------------- | ------------------- |
| POST    | `/factures/{facture}/paiements`                 | `factures.paiements.store`   | `PaiementController@store` |
| DELETE  | `/factures/{facture}/paiements/{paiement}`      | `factures.paiements.destroy` | `PaiementController@destroy` |

### Administration (`auth` + `role:Administrateur`)

| Méthode | URI                          | Nom                  | Contrôleur / action |
| ------- | ---------------------------- | -------------------- | ------------------- |
| GET     | `/admin/users`               | `admin.users.index`  | `AdminUserController@index` |
| PATCH   | `/admin/users/{user}/role`   | `admin.users.update-role` | `AdminUserController@updateRole` |

## Documentation

| Sujet                 | Fichier                                        |
| --------------------- | ---------------------------------------------- |
| Guide d'installation  | [`docs/installation.md`](docs/installation.md) |
| Fonctionnalités       | [`docs/features.md`](docs/features.md)          |
| Base de données       | [`docs/database.md`](docs/database.md)          |
| Architecture          | [`docs/architecture.md`](docs/architecture.md)  |
| Rôles utilisateurs    | [`docs/roles.md`](docs/roles.md)                |

## Structure du projet

Le projet suit les conventions standard de Laravel : routes dans `routes/web.php`, contrôleurs dans `app/Http/Controllers`, modèles Eloquent dans `app/Models`, migrations et factories dans `database/`, vues Blade + Alpine dans `resources/views`. Les spécificités du projet (middleware de rôle, règle `EstAvocat`, trait de numérotation, commande de rappel) sont décrites dans [`docs/architecture.md`](docs/architecture.md).

## Tests

La suite de tests couvre les règles métier, les CRUD, la sécurité par rôles, l'ergonomie, le schéma de base de données et la détection des requêtes N+1.

```bash
php artisan test         # 133 tests, 404 assertions
vendor/bin/pint          # formatage du code (Laravel Pint)
```

## Licence

Projet open source distribué sous la licence MIT.