# Module VENTES - Documentation Complète

## 📋 Table des matières
1. [Vue d'ensemble](#vue-densemble)
2. [ÉTAPE 1 - Entités VENTES](#étape-1---entités-ventes)
3. [ÉTAPE 2 - Workflow d'états](#étape-2---workflow-détats)
4. [ÉTAPE 3 - Règles métier](#étape-3---règles-métier)
5. [ÉTAPE 4 - Blocages techniques](#étape-4---blocages-techniques)
6. [ÉTAPE 5 - KPI](#étape-5---kpi)
7. [Architecture technique](#architecture-technique)

---

## Vue d'ensemble

### Flux logique du module VENTES
```
┌──────────┐    ┌───────────┐    ┌──────────┐    ┌─────────────┐
│ COMMANDE │ -> │ LIVRAISON │ -> │ FACTURE  │ -> │ENCAISSEMENT │
│  CLIENT  │    │  CLIENT   │    │  CLIENT  │    │   CLIENT    │
└──────────┘    └───────────┘    └──────────┘    └─────────────┘
     │               │                │                │
     ▼               ▼                ▼                ▼
  BROUILLON      BROUILLON        BROUILLON         (créé)
     │               │                │
     ▼               ▼                ▼
   VALIDE         LIVREE           VALIDE
     │                                │
     ▼                                ▼
   CLOTURE                          PAYE
```

---

## ÉTAPE 1 - Entités VENTES

### 1.1 Table `commande_client`
**Rôle fonctionnel:** 
Représente l'engagement commercial du client. C'est le point de départ du flux de vente après le devis (optionnel). Elle contient les montants HT, TVA, TTC et référence les articles commandés via les lignes de commande.

**Champs clés de traçabilité:**
| Champ | Description |
|-------|-------------|
| `created_by` | Utilisateur qui a créé la commande |
| `created_at` | Date/heure de création |
| `valide_par` | Utilisateur qui a validé |
| `date_validation` | Date/heure de validation |
| `statut` | État du workflow (BROUILLON/VALIDE/CLOTURE) |

### 1.2 Table `livraison_client`
**Rôle fonctionnel:**
Enregistre les sorties physiques de marchandises. Fait le lien entre la commande validée et la sortie effective du stock. Peut être partielle (plusieurs livraisons pour une commande).

**Champs clés de traçabilité:**
| Champ | Description |
|-------|-------------|
| `cree_par` | Utilisateur créateur |
| `valide_par` | Utilisateur validateur |
| `date_validation` | Date de validation |
| `statut` | BROUILLON ou LIVREE |

### 1.3 Table `facture_client`
**Rôle fonctionnel:**
Matérialise la créance envers le client. Document comptable et légal obligatoire. Générée à partir d'une livraison validée.

**Champs clés de traçabilité:**
| Champ | Description |
|-------|-------------|
| `cree_par` | Utilisateur créateur |
| `valide_par` | Utilisateur validateur |
| `date_validation` | Date de validation |
| `statut` | BROUILLON/VALIDE/PAYE |

### 1.4 Table `encaissement_client`
**Rôle fonctionnel:**
Enregistre les paiements reçus des clients. Étape finale du flux de vente. Permet les paiements partiels et multi-modes.

**Champs clés de traçabilité:**
| Champ | Description |
|-------|-------------|
| `cree_par` | Utilisateur qui a enregistré |
| `created_at` | Date d'enregistrement |
| `reference_paiement` | N° chèque, référence virement |
| `id_mode_paiement` | Mode de paiement utilisé |

---

## ÉTAPE 2 - Workflow d'états

### 2.1 Commande Client

| État | Description | Modifiable | Supprimable |
|------|-------------|------------|-------------|
| **BROUILLON** | En cours de saisie | ✅ OUI | ✅ OUI |
| **VALIDE** | Validée, stock réservé | ❌ NON | ❌ NON |
| **CLOTURE** | Entièrement livrée | ❌ NON | ❌ NON |

**Transitions possibles:**
```
BROUILLON → VALIDE    : Validation par responsable commercial
VALIDE → CLOTURE      : Automatique quand toutes livraisons faites
VALIDE → BROUILLON    : Annulation (si aucune livraison)
```

**Actions INTERDITES:**
- ❌ Modifier une commande VALIDE ou CLOTURE
- ❌ Supprimer une commande avec livraisons
- ❌ Créer une livraison sur commande BROUILLON

### 2.2 Livraison Client

| État | Description | Modifiable |
|------|-------------|------------|
| **BROUILLON** | En préparation | ✅ OUI |
| **LIVREE** | Effectuée, stock décrémenté | ❌ NON |

**Transitions:**
```
BROUILLON → LIVREE : Validation avec décrémentation stock
```

**Actions INTERDITES:**
- ❌ Créer livraison sur commande non VALIDEE
- ❌ Modifier une livraison LIVREE
- ❌ Livrer plus que le stock disponible

### 2.3 Facture Client

| État | Description | Modifiable |
|------|-------------|------------|
| **BROUILLON** | En cours de création | ✅ OUI |
| **VALIDE** | Émise, en attente paiement | ❌ NON |
| **PAYE** | Totalement réglée | ❌ NON |

**Transitions:**
```
BROUILLON → VALIDE : Validation comptable
VALIDE → PAYE      : Automatique quand encaissements = TTC
```

**Actions INTERDITES:**
- ❌ Créer facture sur livraison non LIVREE
- ❌ Modifier une facture VALIDE ou PAYE
- ❌ Supprimer une facture avec encaissements

### 2.4 Matrice des rôles

| Action | Commercial | Responsable | Comptable | Admin |
|--------|-----------|-------------|-----------|-------|
| Créer commande | ✅ | ✅ | ❌ | ✅ |
| Valider commande | ❌ | ✅ | ❌ | ✅ |
| Créer livraison | ✅ | ✅ | ❌ | ✅ |
| Valider livraison | ❌ | ✅ | ❌ | ✅ |
| Créer facture | ❌ | ✅ | ✅ | ✅ |
| Valider facture | ❌ | ❌ | ✅ | ✅ |
| Enregistrer encaissement | ❌ | ❌ | ✅ | ✅ |

---

## ÉTAPE 3 - Règles métier

### 3.1 Gestion des remises

| Condition | Action | Erreur si non respectée |
|-----------|--------|------------------------|
| Remise ≤ 10% | Autorisée sans validation | - |
| Remise > 10% et ≤ 30% | Nécessite `remise_validee = TRUE` | "BLOCAGE: Remise de X% nécessite une validation" |
| Remise > 30% | INTERDITE | "BLOCAGE: Remise de X% interdite. Maximum: 30%" |

**Exemple:**
```php
// Dans LigneCommandeClientModel::create()
if ($remisePourcent > 30) {
    throw new Exception("BLOCAGE: Remise de {$remisePourcent}% interdite.");
}
if ($remisePourcent > 10 && !$remiseValidee) {
    throw new Exception("BLOCAGE: Remise de {$remisePourcent}% nécessite validation.");
}
```

### 3.2 Validation des commandes

| Condition | Action |
|-----------|--------|
| Commande en BROUILLON | Peut être validée |
| Au moins une ligne | Obligatoire |
| Stock disponible ≥ quantité | Vérification par article |
| Remises conformes | Vérification des seuils |

### 3.3 Réservation automatique du stock

**Lors de la validation d'une commande:**
1. Vérification du stock disponible pour chaque article
2. Création de mouvements de type `RESERVATION_VENTE`
3. Le stock reste physiquement présent mais marqué comme réservé

**Calcul stock disponible:**
```sql
Stock disponible = stock_courant.quantite - réservations_en_cours
```

### 3.4 Impact sur le stock

| Action | Table `stock_courant` | Table `mouvement_stock` | Table `lot` |
|--------|----------------------|------------------------|-------------|
| Validation commande | Inchangé | Mouvement RESERVATION | Inchangé |
| Annulation commande | Inchangé | Mouvement LIBERATION | Inchangé |
| Validation livraison | quantite -= qte_livrée | Mouvement SORTIE_VENTE | Décrémenté selon méthode |

---

## ÉTAPE 4 - Blocages techniques

### 4.1 BLOCAGE 1: Livraison sans stock disponible

**Condition SQL:**
```sql
SELECT quantite FROM stock_courant 
WHERE id_article = ? AND id_depot = ?
-- Doit être >= quantite_a_livrer
```

**Message d'erreur:**
```
"BLOCAGE: Stock insuffisant pour l'article {designation}. 
Disponible: {stock}, Demandé: {quantite}"
```

**Justification métier:**
Empêcher de promettre des marchandises non disponibles, éviter les ruptures et mécontentements clients.

### 4.2 BLOCAGE 2: Livraison sans commande validée

**Condition SQL:**
```sql
SELECT statut FROM commande_client WHERE id_commande_client = ?
-- Doit être 'VALIDE'
```

**Message d'erreur:**
```
"BLOCAGE: Impossible de créer une livraison. 
La commande {numero} n'est pas validée (statut: {statut})."
```

**Justification métier:**
Assurer que tout engagement de livraison a été préalablement validé commercialement et financièrement.

### 4.3 BLOCAGE 3: Facture sans livraison validée

**Condition SQL:**
```sql
SELECT statut FROM livraison_client WHERE id_livraison_client = ?
-- Doit être 'LIVREE'
```

**Message d'erreur:**
```
"BLOCAGE: Impossible de créer une facture. 
La livraison {numero} n'est pas validée (statut: {statut})."
```

**Justification métier:**
Ne facturer que ce qui a été effectivement livré, conformité comptable.

### 4.4 BLOCAGE 4: Remise non validée dépassant le seuil

**Condition:**
```php
$remisePourcent > REMISE_MAX_SANS_VALIDATION && !$remiseValidee
```

**Message d'erreur:**
```
"BLOCAGE: Remise de {X}% nécessite une validation. 
Seuil sans validation: 10%"
```

**Justification métier:**
Contrôler la politique commerciale, éviter les abus de remises qui impactent la marge.

---

## ÉTAPE 5 - KPI

### 5.1 Commandes en retard

**Objectif métier:** Identifier les commandes validées non livrées dans les délais

**Formule:** 
```
Commandes où statut = 'VALIDE' 
ET pas de livraison 'LIVREE' 
ET (date_actuelle - date_commande) > délai_jours
```

**SQL (VIEW):**
```sql
CREATE VIEW v_commandes_retard AS
SELECT cc.*, c.nom AS client_nom,
       DATEDIFF(CURDATE(), cc.commande_date) AS jours_retard
FROM commande_client cc
LEFT JOIN client c ON cc.id_client = c.id_client
LEFT JOIN livraison_client lc ON cc.id_commande_client = lc.id_commande_client 
    AND lc.statut = 'LIVREE'
WHERE cc.statut = 'VALIDE'
AND lc.id_livraison_client IS NULL
AND DATEDIFF(CURDATE(), cc.commande_date) > 7;
```

### 5.2 Chiffre d'affaires par période

**Objectif métier:** Mesurer la performance commerciale

**Formule:**
```
CA = SUM(montant_ttc) des factures VALIDÉES ou PAYÉES sur la période
```

**SQL (VIEW):**
```sql
CREATE VIEW v_ca_mensuel AS
SELECT YEAR(date_facture) AS annee, MONTH(date_facture) AS mois,
       COUNT(*) AS nombre_factures,
       SUM(montant_ttc) AS ca_ttc,
       AVG(montant_ttc) AS panier_moyen
FROM facture_client
WHERE statut IN ('VALIDE', 'PAYE')
GROUP BY YEAR(date_facture), MONTH(date_facture);
```

### 5.3 Taux de remise moyenne

**Objectif métier:** Contrôler la politique commerciale

**Formule:**
```
Taux moyen = AVG(remise_pourcent) des lignes de commandes validées/clôturées
```

**SQL (VIEW):**
```sql
CREATE VIEW v_taux_remise AS
SELECT AVG(lcc.remise_pourcent) AS remise_moyenne,
       MAX(lcc.remise_pourcent) AS remise_max,
       COUNT(CASE WHEN lcc.remise_pourcent > 10 THEN 1 END) AS nb_remises_elevees
FROM ligne_commande_client lcc
JOIN commande_client cc ON lcc.id_commande_client = cc.id_commande_client
WHERE cc.statut IN ('VALIDE', 'CLOTURE');
```

### 5.4 Backlog commandes non livrées

**Objectif métier:** Mesurer le carnet de commandes à livrer

**Formule:**
```
Backlog = Commandes VALIDÉES avec quantité_restante > 0
```

**SQL (VIEW):**
```sql
CREATE VIEW v_backlog_commandes AS
SELECT cc.*, c.nom AS client_nom,
       SUM(lcc.quantite) - COALESCE(SUM(delivered.qte_livree), 0) AS qte_restante
FROM commande_client cc
JOIN ligne_commande_client lcc ON cc.id_commande_client = lcc.id_commande_client
LEFT JOIN (...) delivered ON ...
WHERE cc.statut = 'VALIDE'
GROUP BY cc.id_commande_client
HAVING qte_restante > 0;
```

### 5.5 Factures non payées (Créances)

**Objectif métier:** Suivre les créances et le recouvrement

**Formule:**
```
Créances = Factures VALIDÉES où (montant_ttc - encaissements) > 0
```

**SQL (VIEW):**
```sql
CREATE VIEW v_factures_impayees AS
SELECT fc.*, c.nom AS client_nom,
       fc.montant_ttc - COALESCE(SUM(ec.montant), 0) AS reste_a_payer,
       DATEDIFF(CURDATE(), fc.date_facture) AS anciennete_jours
FROM facture_client fc
LEFT JOIN encaissement_client ec ON fc.id_facture_client = ec.facture_client_id
WHERE fc.statut = 'VALIDE'
GROUP BY fc.id_facture_client
HAVING reste_a_payer > 0;
```

### 5.6 Dashboard KPI récapitulatif

| KPI | Indicateur | Alerte |
|-----|-----------|--------|
| CA mensuel | Montant TTC facturé | < objectif |
| Backlog | Nb commandes en attente | > seuil |
| Retard | Nb commandes > 7 jours | > 0 |
| Créances | Montant impayé total | > seuil |
| Remises | Taux moyen | > 10% |

---

## Architecture technique

### Structure des fichiers

```
app/
├── models/AVIS/Vente/
│   ├── ClientModel.php
│   ├── CommandeClientModel.php
│   ├── LigneCommandeClientModel.php
│   ├── LivraisonClientModel.php
│   ├── LigneLivraisonClientModel.php
│   ├── FactureClientModel.php
│   ├── EncaissementClientModel.php
│   ├── StockModel.php
│   └── VenteKPIModel.php
├── controllers/AVIS/Vente/
│   └── VenteController.php
├── views/AVIS/Vente/
│   └── dashboard.php
└── config/routes/AVIS/Vente/
    └── VenteRoute.php

sql/module2 - AVIS/
├── mio.sql           # Schéma principal
└── ventes_tables.sql # Tables complémentaires
```

### Routes API

| Méthode | Route | Action |
|---------|-------|--------|
| GET | `/ventes` | Dashboard |
| POST | `/ventes/clients` | Créer client |
| POST | `/ventes/commandes` | Créer commande |
| POST | `/ventes/commandes/{id}/valider` | Valider commande |
| POST | `/ventes/livraisons` | Créer livraison |
| POST | `/ventes/livraisons/{id}/valider` | Valider livraison |
| POST | `/ventes/factures` | Créer facture |
| POST | `/ventes/factures/{id}/valider` | Valider facture |
| POST | `/ventes/encaissements` | Créer encaissement |
| GET | `/ventes/kpi` | Récupérer tous les KPI |
