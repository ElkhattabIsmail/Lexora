# Démonstration de Lexora

Guide pas-à-pas pour présenter l'application. Durée conseillée : **8 à 10 minutes**.

## Préparation avant la démo (tests déjà verts)

1. Vérifier que les services tournent :

   ```bash
   composer run dev        # serveur Laravel + Vite en parallèle
   ```

2. Dans un second terminal, lancer le worker de file d'attente (documents, rappels) :

   ```bash
   php artisan queue:work
   ```

3. Vérifier les tests et le schéma :


   ```bash
   php artisan test --compact   # 141 tests, 425 assertions
   ```

## Scénario de démonstration

### Étape 1 — Connexion en tant qu'administrateur (1 min)

1. Ouvrir `http://localhost:8000`.
2. Cliquer sur **Connexion**.
3. Saisir `admin@lexora.ma` / mot de passe `password`.
4. Atterrir sur le **tableau de bord** :
   - KPI : dossiers actifs, audiences à venir, total clients, revenus du mois, taux de réussite.
   - Histogramme mensuel d'ouverture des dossiers.
   - Les 5 prochaines audiences et 5 derniers dossiers.

### Étape 2 — Gestion d'un dossier (2 min)

1. Ouvrir le menu **Dossiers** → liste des dossiers (`DOS-2026-xxxx`), recherche et pagination.
2. Rechercher « DOS-2026-0001 » ; ouvrir le dossier **Droit commercial** (client Atlas Logistics).
3. Montrer la **fiche détaillée** : informations, avocat assigné, **historique** horodaté.
4. Onglets **Audiences** et **Documents** (interaction Alpine).
5. Créer une audience (`+ Ajouter` une audience) : date + heure futures, tribunal, statut *Prévue*.
6. Téléverser un document (fichier local) : il passe par la file d'attente puis apparaît avec son type, sa taille et son téléverseur.

### Étape 3 — Facturation et encaissement (2 min)

1. Menu **Factures** → liste, recherche par numéro / dossier / client.
2. Ouvrir une facture **Non payée** (ex. `FAC-2026-xxxx`).
3. Montrer le montant restant dû.
4. Enregistrer un **paiement** (mode Virement) → le statut passe automatiquement à *Payée* et le montant restant se met à jour.
5. Montrer la journalisation dans l'historique du dossier lié (si dossier associé).

### Étape 4 — Rappels d'audience (1 min)

1. Dans le terminal, lancer :

   ```bash
   php artisan audiences:rappel --horizon=7
   ```

2. Recharger la page ; les avocats concernés reçoivent une notification « Audience à venir ».
3. Mentionner la planification automatique journalière (`schedule:run`, 07h00).

### Étape 5 — Rôles et sécurité (1 min)

1. Menu **Administration → Utilisateurs** : liste des utilisateurs et des rôles.
2. Changer le rôle d'un utilisateur via la liste déroulante.
3. Montrer le **garde-fou** : tenter de rétrograder le dernier administrateur → refus.
4. Se déconnecter, se connecter en **Avocat** (`avocat.berrada@lexora.ma`, `password`) : l'accès **Administration** est masqué.
5. (Optionnel) Se connecter via le compte **Assistant Juridique** → page 403 : le rôle n'a volontairement pas accès aux ressources.

### Étape 6 — Qualité et livrable (1 min)

1. Terminal : `php artisan test --compact` → **141 tests, 425 assertions** en quelques secondes.
2. Montrer le dépôt GitHub : historique des commits, README (routes, schéma, architecture), dossier `docs/`.
3. Conclure sur les choix techniques : optimisations anti-N+1, jobs en file d'attente, validation métier par Form Requests.

## Comptes de démonstration

Tous les comptes partagent le mot de passe `password` :

| Rôle                | E-mail                       |
| ------------------- | ---------------------------- |
| Administrateur      | `admin@lexora.ma`            |
| Avocat              | `avocat.berrada@lexora.ma`   |
| Avocat              | `avocat.benjelloun@lexora.ma` |
| Avocat              | `avocat.tazi@lexora.ma`      |
| Assistant Juridique | `assistant.idrissi@lexora.ma` |
| Assistant Juridique | `assistant.chraibi@lexora.ma` |

## Points d'attention pendant la démo

- Les documents téléversés nécessitent `php artisan storage:link` (lien déjà créé en local).
- Sans worker `queue:work`, les documents restent en attente dans la job table et les rappels ne partent pas.
- Les numéros de dossiers/factures sont auto-générés (`DOS-2026-00001`, `FAC-2026-00001`).