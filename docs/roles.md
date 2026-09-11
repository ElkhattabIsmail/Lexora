# Rôles utilisateurs

Lexora s'appuie sur un système de rôles simples (table `roles`, clé `role_id` sur `users`). Un utilisateur possède exactement un rôle.

## Les trois rôles

| Rôle                  | Description |
| --------------------- | ----------- |
| **Administrateur**    | Accès complet : toutes les ressources ET la gestion des utilisateurs et de leurs rôles. |
| **Avocat**            | Accès complet aux ressources du cabinet : clients, dossiers, audiences, documents, factures, paiements. |
| **Assistant Juridique** | Aucun accès aux ressources du cabinet (page 403). L'infrastructure du rôle existe pour son extension future (vision lecture seule prévue). |

## Matrice des permissions

| Fonction                              | Admin | Avocat | Assistant |
| ------------------------------------- | :---: | :----: | :-------: |
| Connexion / inscription / profil      |  ✔   |   ✔   |     ✔     |
| Tableau de bord (`/dashboard`)        |  ✔   |   ✔   |    —¹     |
| Clients, dossiers, audiences, documents, factures, paiements |  ✔ |   ✔   |    ✖     |
| Administration des utilisateurs (`/admin/users`) |  ✔ |   ✖   |    ✖     |
| Rétrograder/supprimer le dernier admin |  ✖   |   —   |     —     |

1. Le tableau de bord exige simplement une session vérifiée ; les assistants juridiques peuvent donc y accéder actuellement (réservé aux rôles opérationnels par décision d'évolution ultérieure).

## Application des règles

### Middleware `role`

L'alias `role` est enregistré dans `bootstrap/app.php` et appliqué dans `routes/web.php` :

- **Ressources principales** : `middleware(['auth', 'verified', 'role:Avocat,Administrateur'])` — couvre `clients`, `dossiers`, `dossiers.audiences`, `dossiers.documents`, `factures`, `factures.paiements`.
- **Administration** : `middleware(['auth', 'role:Administrateur'])` — `admin.users.index` et `admin.users.update-role`.

Le middleware `CheckRole` :
- non connecté → redirection vers `login` ;
- rôle non autorisé → `403` (page personnalisée `resources/views/errors/403.blade.php`) ;
- liste vide → aucune restriction (accès authentifié).

### Modèle `User`

```php
$user->hasRole(['Avocat', 'Administrateur']); // bool
$user->isAdministrateur();
$user->isAvocat();
$user->isAssistantJuridique();
```

Le rôle de l'utilisateur connecté est pré-chargé (`Authenticated` event dans `AppServiceProvider`) pour que les vues (menu latéral) connaissent les permissions sans requête supplémentaire.

### Garde-fou du dernier administrateur

`AdminUserController::updateRole` refuse toute modification qui laisserait zéro administrateur (rétrogradation du dernier admin). Cette règle est couverte par des tests.

## Comptes de démonstration

Créés par `php artisan db:seed` — mot de passe commun : `password`.

| Rôle                | E-mail                       |
| ------------------- | ---------------------------- |
| Administrateur      | admin@lexora.ma              |
| Avocat              | avocat.berrada@lexora.ma     |
| Avocat              | avocat.benjelloun@lexora.ma  |
| Avocat              | avocat.tazi@lexora.ma        |
| Assistant Juridique | assistant.idrissi@lexora.ma  |
| Assistant Juridique | assistant.chraibi@lexora.ma  |

## Ajouter ou modifier un rôle

1. Insérer le rôle dans la table `roles` (voir `RoleSeeder`).
2. Ajouter sa capacité dans les groupes de routes (`routes/web.php`) et, si nécessaire, dans le menu latéral (`resources/views/layouts/sidebar.blade.php`).
3. Étendre la matrice ci-dessus et ajouter un test d'autorisation (`tests/Feature`).