# Fonctionnalités de Lexora

Ce document détaille l'ensemble des fonctionnalités de l'application, regroupées par domaine.

## 1. Authentification (Laravel Breeze)

- Connexion, inscription et déconnexion (sessions).
- Vérification de l'adresse e-mail (obligatoire avant l'accès au tableau de bord et aux ressources : middleware `verified`).
- Réinitialisation de mot de passe oublié.
- Confirmation de mot de passe pour les zones sensibles.
- Gestion du profil : nom, prénom, téléphone, e-mail, suppression du compte, changement de mot de passe.
- L'interface utilisateur est intégralement en français.

## 2. Tableau de bord

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

## 3. Clients

- CRUD complet : création, liste avec recherche, consultation, modification, suppression.
- Deux types de client : `Particulier` et `Entreprise`.
- Champs : nom, prénom (facultatif — null pour une entreprise), téléphone, e-mail, adresse.
- Recherche insensible à la casse sur nom, prénom, e-mail, téléphone (`scopeRecherche`).
- Page de détail avec onglets : « Dossiers » et « Factures » (UI remplacée par onglets `x-cloak` depuis l'épisode 18).
- La suppression est interdite si le client possède encore des dossiers ou des factures (contrainte `restrictOnDelete`).

## 4. Dossiers

- CRUD complet : création, liste avec recherche, consultation, modification, suppression.
- Numérotation séquentielle annuelle automatique au format `DOS-AAAA-XXXXX` (5 chiffres).
- Champs : type d'affaire, statut, dates d'ouverture/fermeture, avocat assigné (règle métier « EstAvocat »), client, archivage.
- Statuts : `En cours` (défaut), `Gagné`, `Perdu`, `Fermé`. Le changement de statut archive le dossier s'il est clos.
- Recherche (`scopeRecherche`) sur numéro, client, avocat, type d'affaire, statut.
- Historique horodaté : chaque action significative (création, modification, document, audience, paiement…) est tracée dans la table `historiques` via `Dossier::enregistrerAction()`.
- Les audiences et documents sont gérés au sein de la page du dossier.

## 5. Audiences

- Planification des audiences à l'intérieur d'un dossier (routes imbriquées `dossiers/{dossier}/audiences`).
- Création et modification (pas de liste ni de détail dédié — accès depuis le dossier).
- Champs : date, heure, tribunal, observations, statut (`Prévue` / `Annulée` / `Terminée`), avocat assigné.
- Une audience sans date et heure valides est refusée par le formulaire de validation.
- La suppression d'un dossier supprime ses audiences (contrainte `cascadeOnDelete`).

## 6. Documents

- Téléversement et suppression de fichiers dans un dossier.
- Champs : nom (défaut : nom du fichier), type (`Contrat`, `Plaidoirie`, `Jugement`, `Preuve`, `Autre`), taille en octets, téléverseur.
- Fichiers stockés sur le disque `public` (`storage/app/public/documents/{dossier_id}/…`) et servis via `php artisan storage:link`.
- Toute action de téléversement/suppression est tracée dans l'historique du dossier.

## 7. Factures

- CRUD complet : création, liste avec recherche, consultation, modification, suppression.
- Numérotation séquentielle annuelle automatique au format `FAC-AAAA-XXXXX` (5 chiffres).
- Champs : montant, date, statut, client, dossier associé (facultatif).
- Statuts : `Non payée` (défaut) / `Payée`. Le statut est recalculé automatiquement à l'ajout ou à la suppression d'un paiement (`Facture::synchroniserStatut()`).
- Montant restant dû calculé dynamiquement (`getMontantRestantAttribute`), sans requête supplémentaire superflue dans les listes.
- Recherche sur numéro de facture, numéro de dossier ou critères client.

## 8. Paiements

- Encaissement des paiements sur une facture (routes imbriquées `factures/{facture}/paiements`).
- Création et suppression uniquement (accès depuis la page de la facture).
- Champs : montant, date de paiement, mode (`Espèces`, `Virement`, `Carte`…), référence facultative.
- Règle métier : un paiement ne peut pas dépasser le montant restant dû.
- À chaque création/suppression, le statut de la facture est resynchronisé et l'opération est journalisée sur le dossier lié.
- La suppression d'une facture supprime ses paiements (`cascadeOnDelete`).

## 9. Notifications de rappel

- La commande Artisan `audiences:rappel` (RG30) génère une notification « Audience à venir » pour chaque audience prévue dans l'horizon configuré :
  ```bash
  php artisan audiences:rappel --horizon=3
  ```
- Le destinataire est l'avocat assigné à l'audience ; le message cite le tribunal, la date, l'heure et le numéro de dossier.
- Idempotente : une audience déjà notifiée (notification non lue identique) n'est pas notifiée de nouveau.
- Optimisée en un seul lot : les audiences déjà notifiées sont exclues via une requête `whereIn` groupée (pas de vérification par audience).
- À planifier par le planificateur Laravel (`schedule:run` via cron, ou planificateur Cloud).

## 10. Administration (utilisateurs et rôles)

- Page de gestion des utilisateurs (`/admin/users`), réservée aux administrateurs.
- Liste des utilisateurs avec leur rôle, changement de rôle via une liste déroulante.
- Garde-fou : impossible de rétrograder ou supprimer le dernier administrateur restant.
- Seul le rôle « Administrateur » accède à cette zone (voir [`docs/roles.md`](docs/roles.md)).

## 11. Sécurité et qualité

- Masquage des accès privés et blocs opératoires requis par rôle.
- Règle de validation « EstAvocat » : l'avocat assigné doit réellement posséder le rôle Avocat.
- Détection proactive des requêtes N+1 (`Model::preventLazyLoading` hors production) et suite de tests dédiée mesurant le nombre de requêtes par page.
- 133 tests PHPUnit couvrant règle métiers, autorisations par rôle, CRUD, schéma de base de données et performances des requêtes.