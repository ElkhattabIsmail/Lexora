# Présentation Lexora — Slides

Structure prête à être reportée dans PowerPoint / Google Slides / reveal.js.
Durée cible : **10 minutes** (~11 slides).

---

## Slide 1 — Titre

**⚖️ Lexora**

Gestion de cabinet d'avocats — dossiers, audiences, documents, facturation, notifications.

*Prénom Nom — Projet de fin d'études*

---

## Slide 2 — Problématique

**Organiser un cabinet d'avocats sans outil dédié, c'est :**

- Des dossiers éparpillés (papier, tableurs, e-mails)
- Des audiences non anticipées → rappels manqués
- Une facturation et des encaissements non suivis
- Aucune traçabilité des actions

**Objectif :** une application web unique, en français, centralisant toute l'activité du cabinet.

---

## Slide 3 — Solution

**Lexora** : application web de gestion complète.

- Suivi des **dossiers** et de leur **historique** horodaté
- Planification des **audiences** avec **rappels automatiques**
- Gestion **documentaire** (téléversement, purge en file d'attente)
- **Facturation** avec encaissement des paiements et calcul du restant dû
- **Administration** des utilisateurs et des rôles

---

## Slide 4 — Stack technique

| Couche | Technologie |
| ------ | ----------- |
| Backend | PHP 8.3, **Laravel 13**, Eloquent |
| Base de données | MySQL 8 / SQLite (tests) |
| Frontend | **Tailwind CSS 3**, Alpine.js 3, Vite |
| Authentification | Laravel Breeze (sessions, e-mail vérifié) |
| Qualité | **141 tests PHPUnit**, Pint, détection N+1 |

---

## Slide 5 — Modèle de données

```
roles ──< users ──< dossiers ──< audiences
                │        ├──< documents
                │        └──< historiques
clients ──< dossiers / < factures ──< paiements
```

- 12 migrations, 10 modèles Eloquent
- Contraintes de suppression adaptées : `restrict` / `cascade` / `nullOnDelete`
- Toutes les clés étrangères et statuts indexés

---

## Slide 6 — Fonctionnalités principales

1. **Tableau de bord** : KPIs en agrégats SQL (dossiers actifs, revenus du mois, taux de réussite…)
2. **Dossiers** : numérotation `DOS-AAAA-XXXXX`, statuts, archivage, historique
3. **Audiences & rappels** : commande `audiences:rappel`, idempotente, planifiée chaque jour à 07h00
4. **Documents** : upload délégué à une file d'attente, purge asynchrone
5. **Factures / paiements** : statut auto-synchronisé, paiement plafonné au restant dû

---

## Slide 7 — Sécurité et rôles

- **3 rôles** : Administrateur, Avocat, Assistant Juridique
- Middleware `role` sur les groupes de routes ; page 403 personnalisée
- Règle métier « EstAvocat » sur l'affectation d'un dossier
- **Garde-fou** : on ne peut pas rétrograder/supprimer le dernier administrateur
- E-mail vérifié obligatoire pour accéder aux ressources

---

## Slide 8 — Qualité et performance

- **141 tests PHPUnit / 425 assertions** : règles métier, rôles, CRUD, schéma, ergonomie
- Détection proactive **N+1** : `preventLazyLoading` + suite dédiée mesurant les requêtes par page
- Opérations lourdes déléguées à la **file d'attente** (documents, rappels)
- Agrégats SQL sans hydration superflue (tableau de bord)

---

## Slide 9 — Démonstration (live)

- Connexion administrateur → tableau de bord
- Parcours d'un dossier : création d'audience, téléversement de document, historique
- Encaissement d'un paiement → facture « Payée » automatiquement
- Rappels d'audience via la commande Artisan
- Gestion des utilisateurs et changement de rôle

---

## Slide 10 — Bilan et perspectives

**Bilan**
- Application complète, testée, documentée et déployable
- Interface 100 % en français, responsive

**Perspectives**
- Assistant Juridique : accès en lecture seule
- Recherche FULLTEXT pour les gros volumes
- Statistiques financières et export PDF des factures

---

## Slide 11 — Merci

**⚖️ Lexora**

Questions / Réponses.

Dépôt GitHub : `https://github.com/ElkhattabIsmail/Lexora`