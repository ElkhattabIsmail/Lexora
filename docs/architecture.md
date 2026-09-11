# Architecture de Lexora

## Stack technique

| Couche         | Choix                                     | Notes |
| -------------- | ----------------------------------------- | ----- |
| Framework      | Laravel 13 (PHP 8.3)                      | Eloquent, Blade, Breeze (auth) |
| Base de données| MySQL 8 / SQLite                          | Requêtes portables |
| Frontend       | Tailwind CSS 3 + Alpine.js 3.4 + Vite     | SPA-lite : interactions Alpine, services Vite |
| Linting        | Laravel Pint                              | `vendor/bin/pint --dirty` |
| Tests          | PHPUnit 12 (133 tests)                    | Intégrés à Laravel (`php artisan test`) |

## Structure des dossiers

```
app/
├── Console/Commands/RappelAudiences.php   # commande de rappel d'audience
├── Http/
│   ├── Controllers/                        # Dashboard, Client, Dossier, Audience,
│   │   ├── Admin/AdminUserController.php   # Document, Facture, Paiement, Profile
│   │   └── Auth/                           # contrôleurs Breeze
│   ├── Middleware/CheckRole.php            # middleware « role » (alias)
│   └── Requests/                           # Form Requests (validation métier)
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

## Cycle de vie d'une requête

1. **Routes** (`routes/web.php`) : trois groupes cohérents :
   - Enregistrement : `auth` + `verified` (dashboard) ; gestion du profil : `auth`.
   - Ressources principales (clients, dossiers, audiences, documents, factures, paiements) : `auth` + `verified` + `role:Avocat,Administrateur`.
   - Administration (gestion des utilisateurs) : `auth` + `role:Administrateur`.
2. **Middleware** : `CheckRole` reçoit la liste des rôles autorisés ; redirige vers `login` si non connecté, sinon `403` si le rôle ne correspond pas.
3. **Contrôleur** : délègue la validation aux **Form Requests**. Les requests impliquent des règles métier (avocat assigné = `EstAvocat` ; montant de paiement plafonné au restant dû).
4. **Eloquent / SGBD** : opérations via les modèles, agrégats SQL pour le tableau de bord.
5. **Vue Blade** : rendu serveur + Alpine pour les onglets, menus et toasts.

## Patterns métier

### Numérotation séquentielle — `GeneratesSequentialReference`

Le trait génère une référence annuelle au format `PREFIX-AAAA-XXXXX` (séquence 5 chiffres) :

```php
$this->generateSequentialReference(Dossier::class, 'DOS'); // DOS-2026-00001
$this->generateSequentialReference(Facture::class, 'FAC'); // FAC-2026-00001
```

La séquence s'appuie sur le `MAX(id)` de l'année — cohérent sur tous les SGBD et sûr grâce à la contrainte UNIQUE.

### Règle « EstAvocat » — `app/Rules/EstAvocat.php`

Valide qu'un champ `user_id` désigne réellement un utilisateur portant le rôle `Avocat`. Appliquée aux champs `avocat_id` des dossiers et audiences.

### Recherche — `scopeRecherche`

Chaque entité listable (Client, Dossier, Facture, User) expose une portée `scopeRecherche(Builder $query, string $search)`. La recherche est insensible à la casse (`LIKE`) et combine les critères pertinents (`orWhereHas` pour dossier/facture).

### Synchronisation des statuts — `Facture::synchroniserStatut()`

À chaque création ou suppression d'un paiement, le statut de la facture est recalculé : `Payée` si le cumul des paiements atteint le montant, sinon `Non payée`. Le montant restant est exposé via l'accesseur `getMontantRestantAttribute`.

### Historique des dossiers — `Dossier::enregistrerAction()`

Toute action significative (création, modification, téléversement, audience, paiement) est journalisée dans la table `historiques` (texte de l'action, `date_action`, utilisateur). Les contrôleurs concernés passent par le `loadMissing('dossier')` pour ne déclencher qu'une seule requête par opération.

### Rôles et utilisateurs — `app/Models/User.php`

- `hasRole(string|array $roles)` : vérifie le rôle de l'utilisateur (utilisé par le middleware et les vues).
- `isAdministrateur()`, `isAvocat()`, `isAssistantJuridique()` : raccourcis lisibles.
- `static avocats()` : requête renvoyant les utilisateurs de rôle `Avocat` (listes déroulantes d'affectation).
- `AppServiceProvider` écoute l'événement `Authenticated` pour charger la relation `role` de l'utilisateur connecté en une requête.

### Performances et N+1

- `Model::preventLazyLoading(! app()->environment('production'))` : toute relation chargée paresseusement pendant une hydration multiple lève une exception en développement (et dans les tests).
- Les listes et pages de détail utilisent `with(['client', 'avocat', ...])` ; les affichages « prochaines audiences » et « derniers dossiers » du tableau de bord sont chargés avec leurs relations par `with`.
- Le tableau de bord agrège en SQL : taux de réussite via `SUM(CASE WHEN …)` et histogramme mensuel via `pluck()->countBy()` (aucune hydration).
- Une suite de tests dédiée (`tests/Feature/NPlusOneDetectionTest.php`) compte les requêtes via `DB::listen` et fixe un budget par page.

### Commande de rappel — `audiences:rappel`

Filtre les audiences `Prévue` dans l'horizon (`--horizon`, défaut 3 jours), génère une notification par avocat, puis exclut en un seul `whereIn` groupé les messages non lus déjà envoyés (idempotence et volume minimal de requêtes). Programmable via le planificateur Laravel.

## Frontend

- **Mise en page** : layout applicatif avec barre latérale role-aware (menus adaptés à l'utilisateur), barre de navigation responsive et pied de page.
- **Composants Blade** : `status-badge` (badges colorés par statut métier), boutons, inputs, modale, messages flash.
- **Alpine.js** : onglets des pages de détail (`x-cloak`), menus déroulants, fenêtres modales de confirmation.
- **Vite** : pipeline Tailwind (v3) + Alpine via `laravel-vite-plugin`.

## Bases de données et migrations

12 migrations : les tables métier (voir [`docs/database.md`](docs/database.md)) plus les tables standards Laravel (`cache`, `jobs`, `sessions`, `password_reset_tokens`). Les migrations définissent les contraintes de suppression (restrict / cascade / nullOnDelete) et les index de recherche.

## Tests

- Tests d'authentification et d'autorisations par rôle (un Assistant Juridique reçoit `403` sur les ressources principales ; seul l'admin gère les utilisateurs ; verrou du dernier administrateur).
- Tests CRUD et règles métier (particulier/entreprise, statuts, paiements, rappels, séquences).
- Tests de schéma (colonnes, index, contraintes) et tests de performances des requêtes (budget N+1 par page).

## Étendre l'application

1. **Nouvelle entité métier** : migration → modèle + factory → Form Request → contrôleur → routes dans le groupe `role:*` → vues CRUD → tests.
2. **Nouveau rôle** : ajout dans `RoleSeeder`, capacité dans les groupes de routes et le menu latéral.
3. **Nouvelle commande planifiée** : fichier dans `app/Console/Commands`, enregistrée dans `routes/console.php`.