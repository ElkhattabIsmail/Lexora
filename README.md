<p align="center">
  <strong style="font-size: 2rem; font-family: Georgia, serif;">⚖️ Lexora</strong>
</p>

<p align="center">
  Application web de gestion de cabinet d'avocats — dossiers, audiences, documents, facturation et notifications.
</p>

<p align="center">
  <img alt="PHP"      src="https://img.shields.io/badge/PHP-8.3-777bb4">
  <img alt="Laravel"  src="https://img.shields.io/badge/Laravel-13-f4645f">
  <img alt="Tailwind" src="https://img.shields.io/badge/Tailwind_CSS-3-38bdf8">
  <img alt="Tests"    src="https://img.shields.io/badge/tests-141%20passed-22c55e">
  <img alt="Licence"  src="https://img.shields.io/badge/licence-MIT-3b82f6">
</p>

---

## Table des matières

- [À propos](#à-propos)
- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Lancement du projet](#lancement-du-projet)
- [Comptes de démonstration](#comptes-de-démonstration)
- [Rappels d'audience](#rappels-daudience)
- [Commandes](#commandes)
- [Routes de l'application](#routes-de-lapplication)
- [Base de données](#base-de-données)
- [Architecture](#architecture)
- [Rôles utilisateurs](#rôles-utilisateurs)
- [Tests](#tests)
- [Licence](#licence)

---

## À propos

**Lexora** est un logiciel de gestion complet pour un cabinet d'avocats : suivi des dossiers et de leur historique, planification des audiences, gestion documentaire, facturation avec encaissement des paiements, notifications de rappel et administration des utilisateurs.

L'interface est entièrement en français. L'application est construite avec Laravel 13, Tailwind CSS 3, Alpine.js et Vite.

## Fonctionnalités

### 1. Authentification (Laravel Breeze)

- Connexion, inscription et déconnexion (sessions).
- Vérification de l'adresse e-mail (obligatoire avant l'accès au tableau de bord et aux ressources : middleware `verified`).
- Réinitialisation de mot de passe oublié.
- Confirmation de mot de passe pour les zones sensibles.
- Gestion du profil : nom, prénom, téléphone, e-mail, suppression du compte, changement de mot de passe.
- L'interface utilisateur est intégralement en français.

### 2. Tableau de bord

Le tableau de bord (`/dashboard`, réservé aux utilisateurs connectés et vérifiés) agrège les indicateurs suivants :

| Indicateur            | Définition |
| --------------------- | ---------- |
| Dossiers actifs       | Dossiers en statut « En cours » et non archivés. |
| Audiences à venir     | Audiences en statut « Prévue » à compter d'aujourd'hui. |
| Total clients         | Nombre de clients enregistrés. |
| Revenus du mois       | Somme des factures « Payée » du mois en cours. |
| Taux de réussite      | Pourcentage de dossiers « Gagné » parmi les dossiers clos (Gagné / Perdu / Fermé). |
| Dossiers par mois     | Histogramme mensuel des ouvertures de dossiers de l'année en cours. |

En complément : les 5 prochaines audiences (avec dossier, client et avocat) et les 5 derniers dossiers créés. Toutes les statistiques sont calculées par des requêtes Eloquent optimisées (agrégats SQL, sans hydratation superflue).

### 3. Clients

- CRUD complet : création, liste avec recherche, consultation, modification, suppression.
- Deux types de client : `Particulier` et `Entreprise`.
- Champs : nom, prénom (facultatif — null pour une entreprise), téléphone, e-mail, adresse.
- Recherche insensible à la casse sur nom, prénom, e-mail, téléphone (`scopeRecherche`).
- Page de détail avec onglets : « Dossiers » et « Factures » (onglets `x-cloak` Alpine).
- La suppression est interdite si le client possède encore des dossiers ou des factures (contrainte `restrictOnDelete`).

### 4. Dossiers

- CRUD complet : création, liste avec recherche, consultation, modification, suppression.
- Numérotation séquentielle annuelle automatique au format `DOS-AAAA-XXXXX` (5 chiffres).
- Champs : type d'affaire, statut, dates d'ouverture/fermeture, avocat assigné (règle métier « EstAvocat »), client, archivage.
- Statuts : `En cours` (défaut), `Gagné`, `Perdu`, `Fermé`. Le changement de statut archive le dossier s'il est clos.
- Recherche (`scopeRecherche`) sur numéro, client, avocat, type d'affaire, statut.
- Historique horodaté : chaque action significative (création, modification, document, audience, paiement…) est tracée dans la table `historiques` via `Dossier::enregistrerAction()`.
- Les audiences et documents sont gérés au sein de la page du dossier.

### 5. Audiences

- Planification des audiences à l'intérieur d'un dossier (routes imbriquées `dossiers/{dossier}/audiences`).
- Création et modification (pas de liste ni de détail dédié — accès depuis le dossier).
- Champs : date, heure, tribunal, observations, statut (`Prévue` / `Annulée` / `Terminée`), avocat assigné.
- Une audience sans date et heure valides est refusée par le formulaire de validation.
- La suppression d'un dossier supprime ses audiences (contrainte `cascadeOnDelete`).

### 6. Documents

- Téléversement et suppression de fichiers dans un dossier.
- Champs : nom (défaut : nom du fichier), type (`Contrat`, `Plaidoirie`, `Jugement`, `Preuve`, `Autre`), taille en octets, téléverseur.
- Fichiers stockés sur le disque `public` (`storage/app/public/documents/{dossier_id}/…`) et servis via `php artisan storage:link`.
- Toute action de téléversement/suppression est tracée dans l'historique du dossier.

### 7. Factures

- CRUD complet : création, liste avec recherche, consultation, modification, suppression.
- Numérotation séquentielle annuelle automatique au format `FAC-AAAA-XXXXX` (5 chiffres).
- Champs : montant, date, statut, client, dossier associé (facultatif).
- Statuts : `Non payée` (défaut) / `Payée`. Le statut est recalculé automatiquement à l'ajout ou à la suppression d'un paiement (`Facture::synchroniserStatut()`).
- Montant restant dû calculé dynamiquement (`getMontantRestantAttribute`), sans requête supplémentaire superflue dans les listes.
- Recherche sur numéro de facture, numéro de dossier ou critères client.

### 8. Paiements

- Encaissement des paiements sur une facture (routes imbriquées `factures/{facture}/paiements`).
- Création et suppression uniquement (accès depuis la page de la facture).
- Champs : montant, date de paiement, mode (`Espèces`, `Virement`, `Carte`…), référence facultative.
- Règle métier : un paiement ne peut pas dépasser le montant restant dû.
- À chaque création/suppression, le statut de la facture est resynchronisé et l'opération est journalisée sur le dossier lié.
- La suppression d'une facture supprime ses paiements (`cascadeOnDelete`).

### 9. Notifications de rappel

- La commande Artisan `audiences:rappel` (RG30) génère une notification « Audience à venir » pour chaque audience prévue dans l'horizon configuré :
  ```bash
  php artisan audiences:rappel --horizon=3
  ```
- Le destinataire est l'avocat assigné à l'audience ; le message cite le tribunal, la date, l'heure et le numéro de dossier.
- Idempotente : une audience déjà notifiée (notification non lue identique) n'est pas notifiée de nouveau.
- Optimisée en un seul lot : les audiences déjà notifiées sont exclues via une requête `whereIn` groupée.
- À planifier par le planificateur Laravel (`schedule:run` via cron, ou planificateur Cloud).

### 10. Administration (utilisateurs et rôles)

- Page de gestion des utilisateurs (`/admin/users`), réservée aux administrateurs.
- Liste des utilisateurs avec leur rôle, changement de rôle via une liste déroulante.
- Garde-fou : impossible de rétrograder ou supprimer le dernier administrateur restant.
- Seul le rôle « Administrateur » accède à cette zone (voir [Rôles utilisateurs](#rôles-utilisateurs)).

### 11. Sécurité et qualité

- Masquage des accès privés et blocs opératoires requis par rôle.
- Règle de validation « EstAvocat » : l'avocat assigné doit réellement posséder le rôle Avocat.
- Détection proactive des requêtes N+1 (`Model::preventLazyLoading` hors production) et suite de tests dédiée mesurant le nombre de requêtes par page.
- 141 tests PHPUnit couvrant règles métier, autorisations par rôle, CRUD, schéma de base de données et performances des requêtes.

## Stack technique

| Composant      | Technologie                                   |
| -------------- | --------------------------------------------- |
| Backend        | PHP 8.3, Laravel 13, Eloquent ORM             |
| Base de données | MySQL 8 (SQLite pour les tests)             |
| Frontend       | Tailwind CSS 3, Alpine.js 3, Vite             |
| Auth           | Laravel Breeze (sessions, vérification e-mail)|
| Tests          | PHPUnit 12 (141 tests), Laravel Pint          |
| Console        | Commande `audiences:rappel` planifiable       |

## Prérequis

| Outil      | Version minimale |
| ---------- | ---------------- |
| PHP        | 8.3              |
| Composer   | 2.x              |
| Node.js    | 20 (LTS)         |
| NPM        | 10               |
| Base de données | MySQL 8 **ou** SQLite 3.35+ |

> Lexora fonctionne avec n'importe quel SGBD supporté par Laravel (MySQL, MariaDB, PostgreSQL, SQLite). Le fichier d'exemple `.env.example` est configuré pour SQLite ; la base de développement utilise MySQL 8.

## Docker

L'application est **dockerisée** : PHP 8.3 (FPM), Nginx, MySQL 8, Node.js/Vite sont fournis dans des conteneurs. **Aucune installation locale de PHP, Composer, MySQL ou Node.js n'est nécessaire.**

### Prérequis (Docker)

| Outil        | Version                |
| ------------ | ---------------------- |
| Docker       | Engine 24+ / Desktop   |
| Docker Compose | v2 (inclus avec Docker Desktop) |

> Sur Windows : installez [Docker Desktop](https://docs.docker.com/desktop/) puis lancez-le avant les commandes ci-dessous.

### Architecture Docker

```
                        host
    ┌────────────────────┼───────────────────────┐
    │  http://localhost:8000 (Nginx)   :5173 (Vite) │
    ├────────куне────────┬───────────┬──────────────┤
    │   nginx (1.27)     │  app      │  node (22)   │
    │  :80 -> :8000      │ php-fpm   │  npm run dev │
    │                    │  :9000    │              │
    │        └───────────┼───────────┘              │
    │  .:/var/www/html + volumes vendor, storage    │
    ├──────────────┬────────────────────────────────┤
    │ db (mysql:8.4)  volume db_data  :3306 -> :3307 │
    ├──────────────┼────────────────────────────────┤
    │ queue  (artisan queue:work)                   │
    │ schedule (artisan schedule:work)              │
    └──────────────┴────────────────────────────────┘
```

- **app** : `php:8.3-fpm` + extensions Laravel (`pdo_mysql`, `pdo_sqlite`, `mbstring`, `gd`, `intl`, `zip`, `bcmath`, `pcntl`, `exif`, `opcache`) + Composer 2.
- **nginx** : sert `public/`, routes Laravel sans `/index.php`, transpilation PHP vers `app:9000`.
- **db** : MySQL 8.4, base `lexora`, utilisateur `lexora` / mot de passe `lexora_secret`, volume persistant `db_data`, port hôte **3307** (évite tout conflit avec un MySQL local).
- **node** : Node 22 Alpine, installe `node_modules`, lance Vite (hot reload) sur le port 5173.
- **queue** : `php artisan queue:work` (téléversement des documents, suppression des dossiers, rappels).
- **schedule** : `php artisan schedule:work` (exécution quotidienne de `audiences:rappel`).

Le code source est monté en lecture/écriture depuis le dépôt (`.:/var/www/html`) : vos modifications sont vues instantanément, sans reconstruire les images. `vendor`, `node_modules` et `storage` sont des volumes nommés Linux (les versions Windows de ces dossiers ne sont donc **pas** utilisées).

### Premier démarrage (clone neuf)

```bash
git clone <url-du-dépôt> lexora
cd lexora

docker compose up -d --build
```

L'entrée de point du conteneur crée `.env` depuis `.env.example` et génère `APP_KEY` automatiquement si nécessaire. Vérifiez l'état, puis initialisez la base :

```bash
docker compose ps

# Migrations + jeu de données de démonstration
docker compose exec app php artisan migrate --seed

# Lien public/storage pour les documents (déjà fait par l'entrée de point)
docker compose exec app php artisan storage:link
```

L'application est alors disponible sur **http://localhost:8000** (comptes de démonstration : voir la section [Comptes de démonstration](#comptes-de-démonstration)).

### Démarrage / arrêt

```bash
docker compose up -d --build   # construire et démarrer tous les conteneurs
docker compose up -d           # démarrer (sans reconstruire)
docker compose down            # arrêter les conteneurs (la base est conservée)
docker compose restart         # redémarrer les conteneurs
docker compose ps              # état des conteneurs
```

### Commandes Artisan

Toutes les commandes Artisan s'exécutent dans le conteneur `app` :

```bash
docker compose exec app php artisan --version
docker compose exec app php artisan migrate:status
docker compose exec app php artisan migrate          # appliquer les migrations
docker compose exec app php artisan migrate --seed   # migrations + seed
docker compose exec app php artisan db:seed          # seed seul
docker compose exec app php artisan audiences:rappel --horizon=3
docker compose exec app php artisan queue:failed
docker compose exec app php artisan test             # suite de tests (141)
docker compose exec app vendor/bin/pint              # formatage
```

### Vite / assets

Deux modes :

- **Développement (hot reload)** — le service `node` tourne en permanence : les balises `@vite` pointent vers le serveur Vite sur **http://localhost:5173** (relance automatique au `npm run dev`). Rien à faire : il démarre avec `docker compose up`.
- **Production / hors ligne** — compiler les assets une fois pour qu'ils soient servis par Nginx :

```bash
docker compose run --rm node npm ci
docker compose run --rm node npm run build
docker compose exec app php artisan optimize:clear
```

Installer des dépendances frontend :

```bash
docker compose run --rm node npm install <paquet>
```

### Logs

```bash
docker compose logs           # logs de tous les services
docker compose logs -f        # suivi en temps réel
docker compose logs app       # logs du backend PHP (Laravel)
docker compose logs nginx
docker compose logs db
docker compose exec app tail -f storage/logs/laravel.log
```

### Accès à la base de données

- Depuis un client SQL hôte : **localhost:3307**, base `lexora`, utilisateur `lexora` / `lexora_secret` (root : `root_secret`).
- Depuis les conteneurs, utilisez le nom de service **`db`** (jamais `localhost`).

### Réinitialiser la base de données

```bash
docker compose down -v        # supprime les conteneurs ET les volumes (base vidée)

docker compose up -d          # reconstruit une base vide
docker compose exec app php artisan migrate --seed
```

> ⚠️ `docker compose down -v` supprime **tous** les volumes nommés du projet : base de données, `storage` (documents téléversés), `vendor` et `node_modules`. Après cela, le premier démarrage réinstalle `vendor` et `node_modules` automatiquement.

### Reconstruire les images

Après modification du `Dockerfile`, de `docker-compose.yml` ou des dépendances `composer.json`/`package.json` :

```bash
docker compose up -d --build
docker compose exec app composer install   # si composer.json a changé
docker compose run --rm node npm ci        # si package.json a changé
```

### Commandes utiles (récapitulatif)

**Cycle de vie des conteneurs :**

| Commande | Description |
| ------------------------ | ------------------------------------------------- |
| `docker compose up -d` | Démarre tous les services en arrière-plan (build si nécessaire). |
| `docker compose up -d --build` | Reconstruit les images puis démarre tous les services. |
| `docker compose build` | Construit (ou reconstruit) les images sans démarrer. |
| `docker compose pull` | Télécharge les dernières versions des images. |
| `docker compose start` | Démarre des conteneurs déjà créés mais arrêtés. |
| `docker compose stop` | Arrête les conteneurs sans les supprimer. |
| `docker compose restart` | Redémarre tous les conteneurs. |
| `docker compose down` | Arrête et supprime les conteneurs (données conservées). |
| `docker compose down -v` | Arrête, supprime les conteneurs ET les volumes (réinitialisation totale). |
| `docker compose ps` | État des conteneurs. |
| `docker compose top` | Processus en cours dans chacun des conteneurs. |
| `docker compose images` | Images utilisées par les services. |

**Inspection de la configuration :**

| Commande | Description |
| ------------------------ | ------------------------------------------------- |
| `docker compose config` | Valide et affiche la configuration Compose résolue. |
| `docker compose config --services` | Liste les services définis. |
| `docker compose config --volumes` | Liste les volumes définis. |
| `docker compose port app 9000` | Affiche le port publié sur l'hôte pour un conteneur. |

**Exécution de commandes dans les conteneurs :**

| Commande | Description |
| ------------------------ | ------------------------------------------------- |
| `docker compose exec app bash` | Ouvre un shell interactif dans le conteneur `app`. |
| `docker compose exec app php artisan ...` | Exécute une commande Artisan dans le conteneur `app`. |
| `docker compose exec app composer install` | (Ré)installe les dépendances PHP. |
| `docker compose exec node sh` | Ouvre un shell dans le conteneur `node`. |
| `docker compose run --rm node npm ci` | (Ré)installe les dépendances frontend (conteneur supprimé ensuite). |
| `docker compose run --rm node npm run build` | Compile les assets pour la production. |
| `docker compose exec db mysql -ulexora -plexora_secret lexora` | Client MySQL dans le conteneur de base de données. |

**Logs :**

| Commande | Description |
| ------------------------ | ------------------------------------------------- |
| `docker compose logs -f` | Suit les logs en temps réel de tous les services. |
| `docker compose logs <service>` | Logs d'un service précis. |
| `docker compose logs --tail=100 app` | Affiche les 100 dernières lignes du service `app`. |
| `docker compose exec app tail -f storage/logs/laravel.log` | Suit le fichier de log de Laravel. |

**Nettoyage :**

| Commande | Description |
| ------------------------ | ------------------------------------------------- |
| `docker compose rm -f` | Supprime les conteneurs arrêtés. |
| `docker volume prune` | Supprime les volumes Docker non utilisés (⚠️ vérifiez avant). |
| `docker system prune` | Supprime conteneurs, images et réseaux Docker inutilisés (⚠️ vérifiez avant). |

> Les services disponibles sont : `app` (PHP-FPM / Laravel), `nginx` (serveur web, port 8000), `db` (MySQL 8, port 3307), `node` (Vite, port 5173), `queue` (worker de jobs) et `schedule` (planificateur).
>
> `docker compose exec <service> <commande>` se limite aux conteneurs en cours d'exécution, tandis que `docker compose run --rm <service> <commande>` crée un conteneur jetable pour la commande (utile pour `node`).

## Installation

### 1. Récupérer le code source

```bash
git clone <url-du-dépôt> lexora
cd lexora
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env      # Windows : copy .env.example .env
php artisan key:generate
```

#### Choix de la base de données

**Option A — SQLite (par défaut, zéro configuration) :**

Dans `.env`, assurez-vous d'avoir :

```dotenv
DB_CONNECTION=sqlite
```

Créez le fichier de base : `touch database/database.sqlite` (Windows : `New-Item database\database.sqlite -ItemType File`).

**Option B — MySQL :**

Dans `.env` :

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lexora
DB_USERNAME=root
DB_PASSWORD=secret
```

Créez préalablement la base : `CREATE DATABASE lexora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`

Autres variables notables du `.env` :

- `APP_NAME=Lexora`
- L'interface est en français (chaînes codées en dur dans les vues et messages de validation)
- `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`
- `FILESYSTEM_DISK=local` (les documents sont stockés sur le disque `public`)

### 4. Installer les dépendances frontend

```bash
npm install
npm run build      # ou : npm run dev (démarrer Vite en mode développement)
```

### 5. Migrer et peupler la base

```bash
php artisan migrate
php artisan db:seed
```

Le seed crée les rôles, 6 comptes de démonstration et un jeu de données réaliste (clients, dossiers, audiences, documents, factures, paiements, notifications, historique).

### 6. Accès aux documents téléversés

Les documents sont stockés dans `storage/app/public` et servis via un lien symbolique :

```bash
php artisan storage:link
```

### Installation automatique (tout-en-un)

Le script Composer `setup` enchaîne les étapes 2 à 5 :

```bash
composer run setup
```

Puis, complétez avec le seed et le lien storage :

```bash
php artisan db:seed
php artisan storage:link
```

### Dépannage

| Problème | Solution |
| -------- | -------- |
| « Unable to locate file in Vite manifest » | Exécuter `npm run build`, ou `npm run dev` en parallèle. |
| Documents non affichés (404) | Vérifier que `php artisan storage:link` a bien été exécuté. |
| `SQLSTATE[HY000]` au `migrate` | Vérifier les identifiants et l'existence de la base dans le `.env`. |
| Erreurs liées au cache | `php artisan optimize:clear` puis relancer. |

## Lancement du projet

```bash
# Démarrage complet (serveur Laravel + Vite en parallèle)
composer run dev

# Ou manuellement, dans deux terminaux :
php artisan serve        # backend  → http://localhost:8000
npm run dev              # frontend → recompilation Vite (hot reload)
```

## Comptes de démonstration

Créés par `php artisan db:seed`. Tous les comptes partagent le mot de passe `password` :

| Rôle                | E-mail                       |
| ------------------- | ---------------------------- |
| Administrateur      | admin@lexora.ma              |
| Avocat              | avocat.berrada@lexora.ma     |
| Avocat              | avocat.benjelloun@lexora.ma  |
| Avocat              | avocat.tazi@lexora.ma        |
| Assistant Juridique | assistant.idrissi@lexora.ma  |
| Assistant Juridique | assistant.chraibi@lexora.ma  |

## Files d'attente et jobs

Les opérations lourdes sont déléguées à la file d'attente Laravel (connexion `database` par défaut) afin de garder des réponses HTTP rapides. Un worker doit tourner pour traiter ces jobs :

```bash
php artisan queue:work
```

Jobs présents dans `app/Jobs` :

| Job                          | Déclencheur                          | Travail effectué |
| ---------------------------- | ------------------------------------ | ---------------- |
| `TraiterTeleversementDocument` | Téléversement d'un document          | Déplace le fichier temporaire vers `documents/{dossier}` et enregistre le document + son historique. |
| `SupprimerDocumentsDossier`   | Suppression d'un dossier             | Purge physiquement les fichiers `documents/{dossier}` et `tmp/{dossier}`. |
| `GenererRappelsAudience`      | Commande `audiences:rappel`          | Génère les notifications de rappel d'audience. |

En environnement de test, `QUEUE_CONNECTION=sync` exécute les jobs immédiatement.

## Rappels d'audience

La commande `audiences:rappel` envoie à la file d'attente la génération des rappels avant chaque audience :

```bash
php artisan audiences:rappel --horizon=3
```

- `--horizon` : nombre de jours avant l'audience pour envoyer le rappel (défaut : 3).
- Idempotente : une même audience ne reçoit qu'un seul rappel.
- Le traitement est confié au job `GenererRappelsAudience` (un worker doit tourner).
- À planifier via le planificateur Laravel (crontab `* * * * * php artisan schedule:run`) ou un système de tâches Cloud.

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
| `php artisan queue:work`         | Traite les jobs en file d'attente (worker). |
| `php artisan queue:failed`       | Liste les jobs en échec (`queue:retry` / `queue:flush` pour les relancer). |
| `php artisan audiences:rappel`   | Génère les rappels d'audience (`--horizon=3` jour(s)). |
| `php artisan optimize:clear`     | Vide les caches (config, route, cache, view…). |
| `php artisan schedule:list`      | Liste les tâches planifiées. |
| `php artisan route:list`         | Liste les routes publiées. |
| `php artisan test`               | Exécute la suite de tests (141 tests PHPUnit). |
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

## Base de données

Schéma complet de l'application (12 migrations). Les requêtes restent portables sur tous les SGBD supportés par Laravel (SQLite, MySQL, PostgreSQL…).

### Vue d'ensemble

```
roles ──< users ──< dossiers ──< audiences
                │        │
                │        ├──< documents
                │        └──< historiques
                └──< notifications

clients ──< dossiers        clients ──< factures ──< paiements
clients ──< factures
```

Relations principales :

- Un **dossier** appartient à un client (`clients`) et à un avocat (`users`).
- Une **audience**, un **document** et un **historique** appartiennent à un dossier.
- Une **facture** appartient à un client et, optionnellement, à un dossier.
- Un **paiement** appartient à une facture.
- Une **notification** appartient à un utilisateur.

### Table `roles`

| Colonne   | Type            | Contrainte            |
| --------- | --------------- | --------------------- |
| id        | bigint          | PK, auto-increment    |
| nom       | string          | REQUIS                |
| timestamps |                | created_at / updated_at |

Valeurs seedées : `Administrateur`, `Avocat`, `Assistant Juridique`.

### Table `users`

| Colonne            | Type       | Contrainte                          |
| ------------------ | ---------- | ----------------------------------- |
| id                 | bigint     | PK, auto-increment                  |
| nom                | string     | REQUIS                              |
| prenom             | string     | REQUIS                              |
| email              | string     | UNIQUE, indexé, REQUIS              |
| telephone          | string     | NULLABLE                            |
| email_verified_at  | timestamp  | NULLABLE                            |
| password           | string     | REQUIS (hash bcrypt)                |
| role_id            | bigint     | FK → `roles.id`, `restrictOnDelete`, indexé, REQUIS |
| remember_token     | string(100)| NULLABLE                           |
| timestamps         |            | created_at / updated_at            |

### Table `password_reset_tokens`

Clé primaire : `email`. Colonnes : `token`, `created_at` (nullable).

### Table `sessions`

| Colonne       | Type      | Contrainte                 |
| ------------- | --------- | -------------------------- |
| id            | string    | PK                         |
| user_id       | bigint    | FK → `users.id`, NULLABLE, indexé |
| ip_address    | string(45)| NULLABLE                  |
| user_agent    | text      | NULLABLE                  |
| payload       | longText  | REQUIS                    |
| last_activity | integer   | indexé                    |

### Table `clients`

| Colonne   | Type    | Contrainte                                 |
| --------- | ------- | ------------------------------------------ |
| id        | bigint  | PK                                         |
| nom       | string  | REQUIS                                     |
| prenom    | string  | NULLABLE (`NULL` pour une entreprise)      |
| telephone | string  | NULLABLE                                   |
| email     | string  | indexé, NULLABLE                           |
| adresse   | string  | NULLABLE                                   |
| type      | enum    | `Particulier` / `Entreprise` (défaut `Particulier`), indexé |
| timestamps |        |                                            |

### Table `dossiers`

| Colonne        | Type    | Contrainte                                            |
| -------------- | ------- | ----------------------------------------------------- |
| id             | bigint  | PK                                                    |
| numero_dossier | string  | UNIQUE, indexé, REQUIS (format `DOS-AAAA-XXXXX`)      |
| type_affaire   | string  | REQUIS                                                |
| statut         | enum    | `En cours` / `Gagné` / `Perdu` / `Fermé` (défaut `En cours`), indexé |
| date_ouverture | date    | REQUIS                                                |
| date_fermeture | date    | NULLABLE                                              |
| archive        | boolean | défaut `false`, indexé                                |
| client_id      | bigint  | FK → `clients.id`, `restrictOnDelete`, indexé          |
| avocat_id      | bigint  | FK → `users.id`, `restrictOnDelete`, indexé            |
| timestamps     |         |                                                       |

### Table `audiences`

| Colonne      | Type    | Contrainte                                            |
| ------------ | ------- | ----------------------------------------------------- |
| id           | bigint  | PK                                                    |
| date         | date    | REQUIS, indexé (requêtes « à venir »)                 |
| heure        | time    | REQUIS                                                |
| tribunal     | string  | REQUIS                                                |
| observations | text    | NULLABLE                                              |
| statut       | enum    | `Prévue` / `Annulée` / `Terminée` (défaut `Prévue`), indexé |
| dossier_id   | bigint  | FK → `dossiers.id`, `cascadeOnDelete`, indexé          |
| avocat_id    | bigint  | FK → `users.id`, `restrictOnDelete`, indexé            |
| timestamps   |         |                                                       |

### Table `documents`

| Colonne     | Type             | Contrainte                                      |
| ----------- | ---------------- | ----------------------------------------------- |
| id          | bigint           | PK                                              |
| nom         | string           | REQUIS                                          |
| chemin      | string           | REQUIS (chemin relatif du fichier)              |
| type        | enum             | `Contrat` / `Plaidoirie` / `Jugement` / `Preuve` / `Autre` (défaut `Autre`), indexé |
| taille      | unsignedBigInteger | NULLABLE (octets)                             |
| dossier_id  | bigint           | FK → `dossiers.id`, `cascadeOnDelete`, indexé    |
| uploaded_by | bigint           | FK → `users.id`, `restrictOnDelete`, indexé      |
| timestamps  |                  |                                                 |

### Table `factures`

| Colonne        | Type    | Contrainte                                            |
| -------------- | ------- | ----------------------------------------------------- |
| id             | bigint  | PK                                                    |
| numero_facture | string  | UNIQUE, indexé, REQUIS (format `FAC-AAAA-XXXXX`)      |
| montant        | decimal(10,2) | REQUIS                                          |
| date_facture   | date    | REQUIS                                                |
| statut         | enum    | `Payée` / `Non payée` (défaut `Non payée`), indexé     |
| client_id      | bigint  | FK → `clients.id`, `restrictOnDelete`, indexé          |
| dossier_id     | bigint  | FK → `dossiers.id`, `nullOnDelete`, indexé, NULLABLE   |
| timestamps     |         |                                                       |

### Table `paiements`

| Colonne        | Type    | Contrainte                                   |
| -------------- | ------- | -------------------------------------------- |
| id             | bigint  | PK                                           |
| montant        | decimal(10,2) | REQUIS                                |
| date_paiement  | date    | REQUIS, indexé                               |
| mode_paiement  | string  | REQUIS (`Espèces`, `Virement`, `Carte`…), indexé |
| reference      | string  | NULLABLE                                     |
| facture_id     | bigint  | FK → `factures.id`, `cascadeOnDelete`, indexé |
| timestamps     |         |                                              |

### Table `historiques`

| Colonne     | Type       | Contrainte                                  |
| ----------- | ---------- | ------------------------------------------- |
| id          | bigint     | PK                                          |
| action      | text       | REQUIS (description de l'action)            |
| date_action | timestamp  | REQUIS, indexé                              |
| dossier_id  | bigint     | FK → `dossiers.id`, `cascadeOnDelete`, indexé |
| user_id     | bigint     | FK → `users.id`, `restrictOnDelete`, indexé  |
| timestamps  |            |                                             |

### Table `notifications`

| Colonne | Type   | Contrainte                                |
| ------- | ------ | ----------------------------------------- |
| id      | bigint | PK                                        |
| titre   | string | REQUIS                                    |
| message | text   | REQUIS                                    |
| type    | enum   | `Audience` / `Paiement` / `Interne` (défaut `Interne`), indexé |
| lu      | boolean| défaut `false`, indexé (utilisé par `audiences:rappel`) |
| user_id | bigint | FK → `users.id`, `cascadeOnDelete`, indexé |
| timestamps |      |                                           |

### Tables Laravel standards

| Table                    | Rôle                    |
| ------------------------ | ----------------------- |
| `cache` / `cache_locks`  | Cache (pilote `database`) |
| `jobs` / `job_batches` / `failed_jobs` | Files de travaux (pilote `database`) |

### Notes d'indexation

- Toutes les colonnes FK et les statuts sont indexés.
- Les dates de recherche fréquentes (`dossiers.date_ouverture`, `audiences.date`, `paiements.date_paiement`, `historiques.date_action`) sont indexées.
- Les colonnes `numero_dossier` et `numero_facture` disposent d'un index en plus de leur contrainte UNIQUE.
- Les recherches textuelles (`scopeRecherche`) utilisent `LIKE` — adapté au volume d'un cabinet ; pour des volumes importants, envisager un index FULLTEXT ou un moteur de recherche.

## Architecture

### Structure des dossiers

```
app/
├── Console/Commands/RappelAudiences.php   # commande de rappel d'audience
├── Http/
│   ├── Controllers/                        # Dashboard, Client, Dossier, Audience,
│   │   ├── Admin/AdminUserController.php   # Document, Facture, Paiement, Profile
│   │   └── Auth/                           # contrôleurs Breeze
│   ├── Middleware/CheckRole.php            # middleware « role » (alias)
│   └── Requests/                           # Form Requests (validation métier)
├── Jobs/                                   # TraiterTeleversementDocument,
│   │                                       # SupprimerDocumentsDossier,
│   │                                       # GenererRappelsAudience
├── Models/                                 # 10 modèles Eloquent
├── Providers/AppServiceProvider.php        # preventLazyLoading + chargement du rôle
├── Rules/EstAvocat.php                     # règle de validation « l'utilisateur est avocat »
├── Traits/GeneratesSequentialReference.php # numérotation DOS-/FAC-
└── View/Components/                        # composants Blade (status-badge…)
bootstrap/app.php                           # alias « role », exceptions JSON
routes/
├── web.php                                 # toutes les routes web
└── console.php                             # programmation de la commande de rappel
database/
├── migrations/                             # 12 migrations (schéma complet)
├── factories/                              # factories pour les tests
└── seeders/                                # rôles, démo, données réalistes
resources/views/                            # vues Blade + Alpine (français)
tests/                                      # tests feature + N+1
```

### Cycle de vie d'une requête

1. **Routes** (`routes/web.php`) : trois groupes cohérents :
   - Enregistrement : `auth` + `verified` (dashboard) ; gestion du profil : `auth`.
   - Ressources principales (clients, dossiers, audiences, documents, factures, paiements) : `auth` + `verified` + `role:Avocat,Administrateur`.
   - Administration (gestion des utilisateurs) : `auth` + `role:Administrateur`.
2. **Middleware** : `CheckRole` reçoit la liste des rôles autorisés ; redirige vers `login` si non connecté, sinon `403` si le rôle ne correspond pas.
3. **Contrôleur** : délègue la validation aux **Form Requests**. Les requests impliquent des règles métier (avocat assigné = `EstAvocat` ; montant de paiement plafonné au restant dû).
4. **Eloquent / SGBD** : opérations via les modèles, agrégats SQL pour le tableau de bord.
5. **Vue Blade** : rendu serveur + Alpine pour les onglets, menus et toasts.

### Patterns métier

#### Numérotation séquentielle — `GeneratesSequentialReference`

Le trait génère une référence annuelle au format `PREFIX-AAAA-XXXXX` (séquence 5 chiffres) :

```php
$this->generateSequentialReference(Dossier::class, 'DOS'); // DOS-2026-00001
$this->generateSequentialReference(Facture::class, 'FAC'); // FAC-2026-00001
```

La séquence s'appuie sur le `MAX(id)` de l'année — cohérent sur tous les SGBD et sûr grâce à la contrainte UNIQUE.

#### Règle « EstAvocat » — `app/Rules/EstAvocat.php`

Valide qu'un champ `user_id` désigne réellement un utilisateur portant le rôle `Avocat`. Appliquée aux champs `avocat_id` des dossiers et audiences.

#### Recherche — `scopeRecherche`

Chaque entité listable (Client, Dossier, Facture, User) expose une portée `scopeRecherche(Builder $query, string $search)`. La recherche est insensible à la casse (`LIKE`) et combine les critères pertinents (`orWhereHas` pour dossier/facture).

#### Synchronisation des statuts — `Facture::synchroniserStatut()`

À chaque création ou suppression d'un paiement, le statut de la facture est recalculé : `Payée` si le cumul des paiements atteint le montant, sinon `Non payée`. Le montant restant est exposé via l'accesseur `getMontantRestantAttribute`.

#### Historique des dossiers — `Dossier::enregistrerAction()`

Toute action significative (création, modification, téléversement, audience, paiement) est journalisée dans la table `historiques` (texte de l'action, `date_action`, utilisateur). Les contrôleurs concernés passent par le `loadMissing('dossier')` pour ne déclencher qu'une seule requête par opération.

#### Rôles et utilisateurs — `app/Models/User.php`

- `hasRole(string|array $roles)` : vérifie le rôle de l'utilisateur (utilisé par le middleware et les vues).
- `isAdministrateur()`, `isAvocat()`, `isAssistantJuridique()` : raccourcis lisibles.
- `static avocats()` : requête renvoyant les utilisateurs de rôle `Avocat` (listes déroulantes d'affectation).
- `AppServiceProvider` écoute l'événement `Authenticated` pour charger la relation `role` de l'utilisateur connecté en une requête.

#### Performances et N+1

- `Model::preventLazyLoading(! app()->environment('production'))` : toute relation chargée paresseusement pendant une hydration multiple lève une exception en développement (et dans les tests).
- Les listes et pages de détail utilisent `with(['client', 'avocat', ...])` ; les affichages « prochaines audiences » et « derniers dossiers » du tableau de bord sont chargés avec leurs relations par `with`.
- Le tableau de bord agrège en SQL : taux de réussite via `SUM(CASE WHEN …)` et histogramme mensuel via `pluck()->countBy()` (aucune hydration).
- Une suite de tests dédiée (`tests/Feature/NPlusOneDetectionTest.php`) compte les requêtes via `DB::listen` et fixe un budget par page.

#### Commande de rappel — `audiences:rappel`

La commande délègue la génération à la file d'attente (job `GenererRappelsAudience`) : elle filtre les audiences `Prévue` dans l'horizon (`--horizon`, défaut 3 jours), génère une notification par avocat, puis exclut en un seul `whereIn` groupé les messages non lus déjà envoyés (idempotence et volume minimal de requêtes). Programmable via le planificateur Laravel.

### Frontend

- **Mise en page** : layout applicatif avec barre latérale role-aware (menus adaptés à l'utilisateur), barre de navigation responsive et pied de page.
- **Composants Blade** : `status-badge` (badges colorés par statut métier), boutons, inputs, modale, messages flash.
- **Alpine.js** : onglets des pages de détail (`x-cloak`), menus déroulants, fenêtres modales de confirmation.
- **Vite** : pipeline Tailwind (v3) + Alpine via `laravel-vite-plugin`.

### Bases de données et migrations

12 migrations : les tables métier (voir [Base de données](#base-de-données)) plus les tables standards Laravel (`cache`, `jobs`, `sessions`, `password_reset_tokens`). Les migrations définissent les contraintes de suppression (restrict / cascade / nullOnDelete) et les index de recherche.

### Étendre l'application

1. **Nouvelle entité métier** : migration → modèle + factory → Form Request → contrôleur → routes dans le groupe `role:*` → vues CRUD → tests.
2. **Nouveau rôle** : ajout dans `RoleSeeder`, capacité dans les groupes de routes et le menu latéral.
3. **Nouvelle commande planifiée** : fichier dans `app/Console/Commands`, enregistrée dans `routes/console.php`.

## Rôles utilisateurs

Lexora s'appuie sur un système de rôles simples (table `roles`, clé `role_id` sur `users`). Un utilisateur possède exactement un rôle.

### Les trois rôles

| Rôle                  | Description |
| --------------------- | ----------- |
| **Administrateur**    | Accès complet : toutes les ressources ET la gestion des utilisateurs et de leurs rôles. |
| **Avocat**            | Accès complet aux ressources du cabinet : clients, dossiers, audiences, documents, factures, paiements. |
| **Assistant Juridique** | Aucun accès aux ressources du cabinet (page 403). L'infrastructure du rôle existe pour son extension future (vision lecture seule prévue). |

### Matrice des permissions

| Fonction                              | Admin | Avocat | Assistant |
| ------------------------------------- | :---: | :----: | :-------: |
| Connexion / inscription / profil      |  ✔   |   ✔   |     ✔     |
| Tableau de bord (`/dashboard`)        |  ✔   |   ✔   |    —¹     |
| Clients, dossiers, audiences, documents, factures, paiements |  ✔ |   ✔   |    ✖     |
| Administration des utilisateurs (`/admin/users`) |  ✔ |   ✖   |    ✖     |
| Rétrograder/supprimer le dernier admin |  ✖   |   —   |     —     |

1. Le tableau de bord exige simplement une session vérifiée ; les assistants juridiques peuvent donc y accéder actuellement (réservé aux rôles opérationnels par décision d'évolution ultérieure).

### Application des règles

#### Middleware `role`

L'alias `role` est enregistré dans `bootstrap/app.php` et appliqué dans `routes/web.php` :

- **Ressources principales** : `middleware(['auth', 'verified', 'role:Avocat,Administrateur'])` — couvre `clients`, `dossiers`, `dossiers.audiences`, `dossiers.documents`, `factures`, `factures.paiements`.
- **Administration** : `middleware(['auth', 'role:Administrateur'])` — `admin.users.index` et `admin.users.update-role`.

Le middleware `CheckRole` :
- non connecté → redirection vers `login` ;
- rôle non autorisé → `403` (page personnalisée `resources/views/errors/403.blade.php`) ;
- liste vide → aucune restriction (accès authentifié).

#### Modèle `User`

```php
$user->hasRole(['Avocat', 'Administrateur']); // bool
$user->isAdministrateur();
$user->isAvocat();
$user->isAssistantJuridique();
```

Le rôle de l'utilisateur connecté est pré-chargé (`Authenticated` event dans `AppServiceProvider`) pour que les vues (menu latéral) connaissent les permissions sans requête supplémentaire.

#### Garde-fou du dernier administrateur

`AdminUserController::updateRole` refuse toute modification qui laisserait zéro administrateur (rétrogradation du dernier admin). Cette règle est couverte par des tests.

### Ajouter ou modifier un rôle

1. Insérer le rôle dans la table `roles` (voir `RoleSeeder`).
2. Ajouter sa capacité dans les groupes de routes (`routes/web.php`) et, si nécessaire, dans le menu latéral (`resources/views/layouts/sidebar.blade.php`).
3. Étendre la matrice ci-dessus et ajouter un test d'autorisation (`tests/Feature`).

## Tests

La suite de tests couvre les règles métier, les CRUD, la sécurité par rôles, l'ergonomie, le schéma de base de données et la détection des requêtes N+1.

```bash
php artisan test         # 141 tests, 425 assertions
vendor/bin/pint          # formatage du code (Laravel Pint)
```

## Licence

Projet open source distribué sous la licence MIT.