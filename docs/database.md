# Base de données

Schéma complet de l'application (12 migrations). Les requêtes restent portables sur tous les SGBD supportés par Laravel (SQLite, MySQL, PostgreSQL…).

## Vue d'ensemble

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

## Table `roles`

| Colonne   | Type            | Contrainte            |
| --------- | --------------- | --------------------- |
| id        | bigint          | PK, auto-increment    |
| nom       | string          | REQUIS                |
| timestamps |                | created_at / updated_at |

Valeurs seedées : `Administrateur`, `Avocat`, `Assistant Juridique`.

## Table `users`

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

## Table `password_reset_tokens`

Clé primaire : `email`. Colonnes : `token`, `created_at` (nullable).

## Table `sessions`

| Colonne       | Type      | Contrainte                 |
| ------------- | --------- | -------------------------- |
| id            | string    | PK                         |
| user_id       | bigint    | FK → `users.id`, NULLABLE, indexé |
| ip_address    | string(45)| NULLABLE                  |
| user_agent    | text      | NULLABLE                  |
| payload       | longText  | REQUIS                    |
| last_activity | integer   | indexé                    |

## Table `clients`

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

## Table `dossiers`

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

## Table `audiences`

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

## Table `documents`

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

## Table `factures`

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

## Table `paiements`

| Colonne        | Type    | Contrainte                                   |
| -------------- | ------- | -------------------------------------------- |
| id             | bigint  | PK                                           |
| montant        | decimal(10,2) | REQUIS                                |
| date_paiement  | date    | REQUIS, indexé                               |
| mode_paiement  | string  | REQUIS (`Espèces`, `Virement`, `Carte`…), indexé |
| reference      | string  | NULLABLE                                     |
| facture_id     | bigint  | FK → `factures.id`, `cascadeOnDelete`, indexé |
| timestamps     |         |                                              |

## Table `historiques`

| Colonne     | Type       | Contrainte                                  |
| ----------- | ---------- | ------------------------------------------- |
| id          | bigint     | PK                                          |
| action      | text       | REQUIS (description de l'action)            |
| date_action | timestamp  | REQUIS, indexé                              |
| dossier_id  | bigint     | FK → `dossiers.id`, `cascadeOnDelete`, indexé |
| user_id     | bigint     | FK → `users.id`, `restrictOnDelete`, indexé  |
| timestamps  |            |                                             |

## Table `notifications`

| Colonne | Type   | Contrainte                                |
| ------- | ------ | ----------------------------------------- |
| id      | bigint | PK                                        |
| titre   | string | REQUIS                                    |
| message | text   | REQUIS                                    |
| type    | enum   | `Audience` / `Paiement` / `Interne` (défaut `Interne`), indexé |
| lu      | boolean| défaut `false`, indexé (utilisé par `audiences:rappel`) |
| user_id | bigint | FK → `users.id`, `cascadeOnDelete`, indexé |
| timestamps |      |                                           |

## Tables Laravel standards

| Table                    | Rôle                    |
| ------------------------ | ----------------------- |
| `cache` / `cache_locks`  | Cache (pilote `database`) |
| `jobs` / `job_batches` / `failed_jobs` | Files de travaux (pilote `database`) |

## Notes d'indexation

- Toutes les colonnes FK et les statuts sont indexés.
- Les dates de recherche fréquentes (`dossiers.date_ouverture`, `audiences.date`, `paiements.date_paiement`, `historiques.date_action`) sont indexées.
- Les colonnes `numero_dossier` et `numero_facture` disposent d'un index en plus de leur contrainte UNIQUE.
- Les recherches textuelles (`scopeRecherche`) utilisent `LIKE` — adapté au volume d'un cabinet ; pour des volumes importants, envisager un index FULLTEXT ou un moteur de recherche.