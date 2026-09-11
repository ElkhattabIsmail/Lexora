# Installation de Lexora

Ce guide décrit l'installation de Lexora dans un environnement local, étape par étape.

## Prérequis

| Outil     | Version minimale |
| --------- | ---------------- |
| PHP       | 8.3              |
| Composer  | 2.x              |
| Node.js   | 20 (LTS)         |
| NPM       | 10               |
| Base de données | MySQL 8 **ou** SQLite 3.35+ |

> Lexora fonctionne avec n'importe quel SGBD supporté par Laravel (MySQL, MariaDB, PostgreSQL, SQLite). Le fichier d'exemple `.env.example` est configuré pour SQLite ; la base de développement utilise MySQL 8.

## 1. Récupérer le code source

```bash
git clone <url-du-dépôt> lexora
cd lexora
```

## 2. Installer les dépendances PHP

```bash
composer install
```

## 3. Configurer l'environnement

```bash
cp .env.example .env      # Windows : copy .env.example .env
php artisan key:generate
```

### Choix de la base de données

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

## 4. Installer les dépendances frontend

```bash
npm install
npm run build      # ou : npm run dev (démarrer Vite en mode développement)
```

## 5. Migrer et peupler la base

```bash
php artisan migrate
php artisan db:seed
```

Le seed crée les rôles, 6 comptes de démonstration et un jeu de données réaliste (clients, dossiers, audiences, documents, factures, paiements, notifications, historique).

## 6. Accès aux documents téléversés

Les documents sont stockés dans `storage/app/public` et servis via un lien symbolique :

```bash
php artisan storage:link
```

## 7. Lancer l'application

```bash
php artisan serve          # http://localhost:8000
```

En parallèle, dans un second terminal (développement) :

```bash
npm run dev
```

## Installation automatique (tout-en-un)

Le script Composer `setup` enchaîne toutes les étapes 2 à 5 :

```bash
composer run setup
```

Puis, complétez avec le seed et le lien storage :

```bash
php artisan db:seed
php artisan storage:link
```

## Comptes de démonstration

Tous les comptes partagent le mot de passe `password` :

| Rôle                | E-mail                       |
| ------------------- | ---------------------------- |
| Administrateur      | admin@lexora.ma              |
| Avocat              | avocat.berrada@lexora.ma     |
| Avocat              | avocat.benjelloun@lexora.ma  |
| Avocat              | avocat.tazi@lexora.ma        |
| Assistant Juridique | assistant.idrissi@lexora.ma  |
| Assistant Juridique | assistant.chraibi@lexora.ma  |

## Rappels d'audience (commandes planifiées)

La commande `audiences:rappel` génère une notification avant chaque audience :

```bash
php artisan audiences:rappel --horizon=3
```

- `--horizon` : nombre de jours avant l'audience pour envoyer le rappel (défaut : 3).
- Idempotente : une même audience ne reçoit qu'un seul rappel.
- À planifier via le planificateur Laravel (crontab `* * * * * php artisan schedule:run`) ou un système de tâches Cloud.

## Dépannage

| Problème | Solution |
| -------- | -------- |
| « Unable to locate file in Vite manifest » | Exécuter `npm run build`, ou `npm run dev` en parallèle. |
| Documents non affichés (404) | Vérifier que `php artisan storage:link` a bien été exécuté. |
| `SQLSTATE[HY000]` au `migrate` | Vérifier les identifiants et l'existence de la base dans le `.env`. |
| Erreurs liées au cache | `php artisan optimize:clear` puis relancer. |