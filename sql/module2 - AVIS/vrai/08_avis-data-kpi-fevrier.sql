-- =============================================================================
-- MODULE KPI - DONNÉES FÉVRIER 2026
-- =============================================================================
-- Ce fichier ajoute des données pour le mois courant (février 2026)
-- À exécuter en dernier après tous les autres fichiers data

USE gestion_entreprise_test;

-- ============================================
-- FACTURES CLIENT - FÉVRIER 2026
-- ============================================
-- Factures pour le mois actuel (pour CA et Marge brute)

-- D'abord, créer des commandes et livraisons pour février
INSERT INTO commande_client (commande_numero, commande_date, id_client, id_depot, montant_ht, montant_tva, montant_ttc, created_by) VALUES
('CMD-002001', DATE_SUB(NOW(), INTERVAL 7 DAY), 1, 1, 12500.00, 2500.00, 15000.00, 1),
('CMD-002002', DATE_SUB(NOW(), INTERVAL 6 DAY), 2, 1, 8200.00, 1640.00, 9840.00, 1),
('CMD-002003', DATE_SUB(NOW(), INTERVAL 5 DAY), 3, 1, 15800.00, 3160.00, 18960.00, 1),
('CMD-002004', DATE_SUB(NOW(), INTERVAL 4 DAY), 4, 1, 6500.00, 1300.00, 7800.00, 1),
('CMD-002005', DATE_SUB(NOW(), INTERVAL 3 DAY), 5, 1, 9300.00, 1860.00, 11160.00, 1),
('CMD-002006', DATE_SUB(NOW(), INTERVAL 2 DAY), 6, 1, 11200.00, 2240.00, 13440.00, 1),
('CMD-002007', DATE_SUB(NOW(), INTERVAL 1 DAY), 1, 1, 7800.00, 1560.00, 9360.00, 1);

-- Livraisons pour ces commandes
INSERT INTO livraison_client (livraison_numero, livraison_date, id_commande_client, id_depot, statut, cree_par, valide_par, date_validation) 
SELECT 
    CONCAT('LIV-', commande_numero),
    DATE_ADD(commande_date, INTERVAL 1 DAY),
    id_commande_client,
    id_depot,
    'LIVREE',
    1,
    1,
    DATE_ADD(commande_date, INTERVAL 1 DAY)
FROM commande_client 
WHERE commande_numero LIKE 'CMD-002%';

-- Factures pour février (liées aux nouvelles livraisons)
INSERT INTO facture_client (numero_facture, date_facture, client_id, livraison_client_id, montant_ht, montant_tva, montant_ttc, statut, cree_par, valide_par, date_validation)
SELECT 
    CONCAT('FACT-', cc.commande_numero),
    lc.livraison_date,
    cc.id_client,
    lc.id_livraison_client,
    cc.montant_ht,
    cc.montant_tva,
    cc.montant_ttc,
    'VALIDE',
    1,
    1,
    lc.livraison_date
FROM commande_client cc
JOIN livraison_client lc ON lc.id_commande_client = cc.id_commande_client
WHERE cc.commande_numero LIKE 'CMD-002%';

-- ============================================
-- MOUVEMENTS VENTE_LIVRAISON - FÉVRIER 2026
-- ============================================
-- Pour la productivité picking et le taux de service

INSERT INTO mouvement_stock (id_article, id_depot, id_lot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
-- Sorties liées aux nouvelles commandes février
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 3, 7, (SELECT id_commande_client FROM commande_client WHERE commande_numero = 'CMD-002001'), 'livraison_client', 0, 45, 1220.00, 'MVT-FEV-001', 'Livraison CMD-002001', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 7, 7, (SELECT id_commande_client FROM commande_client WHERE commande_numero = 'CMD-002001'), 'livraison_client', 0, 35, 1820.00, 'MVT-FEV-002', 'Livraison CMD-002001', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 10, 7, (SELECT id_commande_client FROM commande_client WHERE commande_numero = 'CMD-002002'), 'livraison_client', 0, 12, 64500.00, 'MVT-FEV-003', 'Livraison CMD-002002', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVSUC004'), 1, 12, 7, (SELECT id_commande_client FROM commande_client WHERE commande_numero = 'CMD-002003'), 'livraison_client', 0, 25, 53000.00, 'MVT-FEV-004', 'Livraison CMD-002003', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVHUI005'), 1, 14, 7, (SELECT id_commande_client FROM commande_client WHERE commande_numero = 'CMD-002003'), 'livraison_client', 0, 20, 18500.00, 'MVT-FEV-005', 'Livraison CMD-002003', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 4, 7, (SELECT id_commande_client FROM commande_client WHERE commande_numero = 'CMD-002004'), 'livraison_client', 0, 30, 1280.00, 'MVT-FEV-006', 'Livraison CMD-002004', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 7, 7, (SELECT id_commande_client FROM commande_client WHERE commande_numero = 'CMD-002005'), 'livraison_client', 0, 28, 1820.00, 'MVT-FEV-007', 'Livraison CMD-002005', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 10, 7, (SELECT id_commande_client FROM commande_client WHERE commande_numero = 'CMD-002006'), 'livraison_client', 0, 8, 64500.00, 'MVT-FEV-008', 'Livraison CMD-002006', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 4, 7, (SELECT id_commande_client FROM commande_client WHERE commande_numero = 'CMD-002007'), 'livraison_client', 0, 40, 1280.00, 'MVT-FEV-009', 'Livraison CMD-002007', NOW(), NOW(), 1);

-- Validations des nouveaux mouvements
INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) 
SELECT id_mouvement_stock, 'validé', 1 
FROM mouvement_stock 
WHERE mouvement_numero LIKE 'MVT-FEV-%';

-- ============================================
-- RÉCEPTIONS FOURNISSEUR - FÉVRIER 2026
-- ============================================
-- Pour le Dock-to-Stock KPI
INSERT INTO reception_fournisseur (reception_numero, reception_date, id_fournisseur, id_bon_commande_fournisseur, id_depot, created_by) VALUES
('REC-FEV-001', DATE_SUB(NOW(), INTERVAL 5 DAY), 1, 1, 1, 1),
('REC-FEV-002', DATE_SUB(NOW(), INTERVAL 3 DAY), 2, 2, 1, 1),
('REC-FEV-003', DATE_SUB(NOW(), INTERVAL 1 DAY), 1, 1, 1, 1);

-- Mouvements d'entrée liés aux réceptions (ACHAT_RECEPTION = type 1)
INSERT INTO mouvement_stock (id_article, id_depot, id_lot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 4, 1, (SELECT id_reception_fournisseur FROM reception_fournisseur WHERE reception_numero = 'REC-FEV-001'), 'reception_fournisseur', 1, 100, 1300.00, 'MVT-REC-FEV-001', 'Réception REC-FEV-001', DATE_SUB(NOW(), INTERVAL 5 DAY) + INTERVAL 2 HOUR, DATE_SUB(NOW(), INTERVAL 5 DAY) + INTERVAL 2 HOUR, 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 7, 1, (SELECT id_reception_fournisseur FROM reception_fournisseur WHERE reception_numero = 'REC-FEV-002'), 'reception_fournisseur', 1, 80, 1850.00, 'MVT-REC-FEV-002', 'Réception REC-FEV-002', DATE_SUB(NOW(), INTERVAL 3 DAY) + INTERVAL 4 HOUR, DATE_SUB(NOW(), INTERVAL 3 DAY) + INTERVAL 4 HOUR, 1),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 10, 1, (SELECT id_reception_fournisseur FROM reception_fournisseur WHERE reception_numero = 'REC-FEV-003'), 'reception_fournisseur', 1, 50, 65500.00, 'MVT-REC-FEV-003', 'Réception REC-FEV-003', DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 3 HOUR, DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 3 HOUR, 1);

-- Validations
INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) 
SELECT id_mouvement_stock, 'validé', 1 
FROM mouvement_stock 
WHERE mouvement_numero LIKE 'MVT-REC-FEV-%';

-- ============================================
-- LOTS PROCHES PÉREMPTION (pour obsolescence)
-- ============================================
-- Créer quelques lots critiques (< 30 jours)
INSERT INTO lot (id_article, id_depot, lot_numero, date_entree, quantite_initiale, cout_unitaire, date_limite_utilisation_optimale, date_limite_consommation) VALUES
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 'LOT-CRIT-001', DATE_SUB(NOW(), INTERVAL 180 DAY), 25, 1150.00, DATE_ADD(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY)),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 'LOT-CRIT-002', DATE_SUB(NOW(), INTERVAL 200 DAY), 15, 1750.00, DATE_ADD(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 8 DAY)),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 'LOT-CRIT-003', DATE_SUB(NOW(), INTERVAL 150 DAY), 10, 63000.00, DATE_ADD(NOW(), INTERVAL 12 DAY), DATE_ADD(NOW(), INTERVAL 18 DAY));

-- ============================================
-- ERREURS PICKING (mouvement annulé)
-- ============================================
-- Pour le taux d'erreurs picking
INSERT INTO mouvement_stock (id_article, id_depot, id_lot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 3, 7, 0, 'livraison_client', 0, 5, 1220.00, 'MVT-ERR-001', 'Erreur picking - mauvais article', DATE_SUB(NOW(), INTERVAL 4 DAY), NULL, 1);

-- Marquer comme annulé
INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) 
SELECT id_mouvement_stock, 'annulé', 1 
FROM mouvement_stock 
WHERE mouvement_numero = 'MVT-ERR-001';

-- ============================================
-- MISE À JOUR STOCK COURANT (après mouvements février)
-- ============================================
UPDATE stock_courant SET 
    quantite = quantite - 115,  -- 45+30+40 sorties
    valeur_stock = valeur_stock - (45*1220 + 30*1280 + 40*1280)
WHERE id_article = (SELECT id_article FROM article WHERE code = 'AVBIS001') AND id_depot = 1;

UPDATE stock_courant SET 
    quantite = quantite - 63,  -- 35+28 sorties
    valeur_stock = valeur_stock - (35*1820 + 28*1820)
WHERE id_article = (SELECT id_article FROM article WHERE code = 'AVCHO002') AND id_depot = 1;

UPDATE stock_courant SET 
    quantite = quantite - 20,  -- 12+8 sorties
    valeur_stock = valeur_stock - (20*64500)
WHERE id_article = (SELECT id_article FROM article WHERE code = 'AVFAR003') AND id_depot = 1;

UPDATE stock_courant SET 
    quantite = quantite - 25,
    valeur_stock = valeur_stock - (25*53000)
WHERE id_article = (SELECT id_article FROM article WHERE code = 'AVSUC004') AND id_depot = 1;

UPDATE stock_courant SET 
    quantite = quantite - 20,
    valeur_stock = valeur_stock - (20*18500)
WHERE id_article = (SELECT id_article FROM article WHERE code = 'AVHUI005') AND id_depot = 1;

-- Réintégrer les entrées
UPDATE stock_courant SET 
    quantite = quantite + 100,
    valeur_stock = valeur_stock + (100*1300)
WHERE id_article = (SELECT id_article FROM article WHERE code = 'AVBIS001') AND id_depot = 1;

UPDATE stock_courant SET 
    quantite = quantite + 80,
    valeur_stock = valeur_stock + (80*1850)
WHERE id_article = (SELECT id_article FROM article WHERE code = 'AVCHO002') AND id_depot = 1;

UPDATE stock_courant SET 
    quantite = quantite + 50,
    valeur_stock = valeur_stock + (50*65500)
WHERE id_article = (SELECT id_article FROM article WHERE code = 'AVFAR003') AND id_depot = 1;

-- ============================================
-- DONNÉES HISTORIQUES POUR GRAPHIQUES (12 mois)
-- ============================================
-- Ajouter des périodes de clôture historiques pour les graphiques

INSERT IGNORE INTO stock_cloture_periode (annee, mois, statut, date_cloture) VALUES
(2025, 3, 'CLOTURE', '2025-03-31 23:59:59'),
(2025, 4, 'CLOTURE', '2025-04-30 23:59:59'),
(2025, 5, 'CLOTURE', '2025-05-31 23:59:59'),
(2025, 6, 'CLOTURE', '2025-06-30 23:59:59'),
(2025, 7, 'CLOTURE', '2025-07-31 23:59:59'),
(2025, 8, 'CLOTURE', '2025-08-31 23:59:59'),
(2025, 9, 'CLOTURE', '2025-09-30 23:59:59');

-- Détails de clôture pour graphiques historiques
INSERT INTO stock_cloture_detail (id_stock_cloture_periode, id_article, id_depot, qty_ouverture, valeur_ouverture, cump_ouverture, qty_cloture, valeur_cloture, cump_cloture)
SELECT 
    scp.id_stock_cloture_periode,
    a.id_article,
    1,
    FLOOR(100 + RAND() * 100),
    FLOOR(100000 + RAND() * 500000),
    FLOOR(1000 + RAND() * 1000),
    FLOOR(120 + RAND() * 120),
    FLOOR(120000 + RAND() * 550000),
    FLOOR(1050 + RAND() * 1050)
FROM stock_cloture_periode scp
CROSS JOIN (SELECT id_article FROM article WHERE code IN ('AVBIS001', 'AVCHO002', 'AVFAR003') LIMIT 3) a
WHERE scp.mois BETWEEN 3 AND 9 AND scp.annee = 2025
ON DUPLICATE KEY UPDATE qty_cloture = VALUES(qty_cloture);

-- ============================================
-- INVENTAIRES HISTORIQUES (pour graphiques précision)
-- ============================================
-- Ajouter des campagnes clôturées pour les 12 derniers mois

INSERT INTO inventaire_campagne (code, libelle, description, type_campagne, statut, date_planification, date_debut_prevue, date_fin_prevue, created_by) VALUES
('INV-2025-03', 'Inventaire Mars 2025', 'Inventaire mensuel', 'PARTIEL', 'CLOTURE', '2025-03-01', '2025-03-25', '2025-03-28', 1),
('INV-2025-04', 'Inventaire Avril 2025', 'Inventaire mensuel', 'PARTIEL', 'CLOTURE', '2025-04-01', '2025-04-25', '2025-04-28', 1),
('INV-2025-05', 'Inventaire Mai 2025', 'Inventaire mensuel', 'PARTIEL', 'CLOTURE', '2025-05-01', '2025-05-25', '2025-05-28', 1),
('INV-2025-06', 'Inventaire Juin 2025', 'Inventaire mensuel', 'PARTIEL', 'CLOTURE', '2025-06-01', '2025-06-25', '2025-06-28', 1),
('INV-2025-07', 'Inventaire Juillet 2025', 'Inventaire mensuel', 'PARTIEL', 'CLOTURE', '2025-07-01', '2025-07-25', '2025-07-28', 1),
('INV-2025-08', 'Inventaire Août 2025', 'Inventaire mensuel', 'PARTIEL', 'CLOTURE', '2025-08-01', '2025-08-25', '2025-08-28', 1),
('INV-2025-09', 'Inventaire Septembre 2025', 'Inventaire mensuel', 'PARTIEL', 'CLOTURE', '2025-09-01', '2025-09-25', '2025-09-28', 1),
('INV-2025-10', 'Inventaire Octobre 2025', 'Inventaire mensuel', 'PARTIEL', 'CLOTURE', '2025-10-01', '2025-10-25', '2025-10-28', 1),
('INV-2025-11', 'Inventaire Novembre 2025', 'Inventaire mensuel', 'PARTIEL', 'CLOTURE', '2025-11-01', '2025-11-25', '2025-11-28', 1);

-- Comptages pour ces inventaires (précision ~97-99%)
INSERT INTO inventaire_comptage (id_inventaire_campagne, id_depot, id_article, quantite_theorique, quantite_comptee, ecart, commentaire, date_comptage, created_by)
SELECT 
    ic.id_inventaire_campagne,
    1,
    a.id_article,
    100,
    CASE 
        WHEN ic.code = 'INV-2025-03' THEN 97
        WHEN ic.code = 'INV-2025-04' THEN 98
        WHEN ic.code = 'INV-2025-05' THEN 99
        WHEN ic.code = 'INV-2025-06' THEN 98
        WHEN ic.code = 'INV-2025-07' THEN 97
        WHEN ic.code = 'INV-2025-08' THEN 99
        WHEN ic.code = 'INV-2025-09' THEN 98
        WHEN ic.code = 'INV-2025-10' THEN 99
        WHEN ic.code = 'INV-2025-11' THEN 98
        ELSE 98
    END,
    CASE 
        WHEN ic.code = 'INV-2025-03' THEN -3
        WHEN ic.code = 'INV-2025-04' THEN -2
        WHEN ic.code = 'INV-2025-05' THEN -1
        WHEN ic.code = 'INV-2025-06' THEN -2
        WHEN ic.code = 'INV-2025-07' THEN -3
        WHEN ic.code = 'INV-2025-08' THEN -1
        WHEN ic.code = 'INV-2025-09' THEN -2
        WHEN ic.code = 'INV-2025-10' THEN -1
        WHEN ic.code = 'INV-2025-11' THEN -2
        ELSE -2
    END,
    'Inventaire mensuel',
    ic.date_fin_prevue,
    1
FROM inventaire_campagne ic
CROSS JOIN (SELECT id_article FROM article WHERE code = 'AVBIS001' LIMIT 1) a
WHERE ic.code LIKE 'INV-2025-%' AND ic.code NOT IN ('INV-2025-12');

-- Validations des inventaires
INSERT INTO inventaire_campagne_validation (id_inventaire_campagne, validated_by, total_ecart, total_valeur_ecart, commentaire, date_validation)
SELECT 
    ic.id_inventaire_campagne,
    1,
    -2,
    -2500.00,
    'Inventaire mensuel validé',
    ic.date_fin_prevue
FROM inventaire_campagne ic
WHERE ic.code LIKE 'INV-2025-%' AND ic.code NOT IN ('INV-2025-12', 'INV-2026-01', 'INV-2026-02', 'INV-2026-03');

-- ============================================
-- MOUVEMENTS HISTORIQUES (pour graphiques 12 mois)
-- ============================================
-- Ajouter des mouvements VENTE_LIVRAISON pour les mois précédents

INSERT INTO mouvement_stock (id_article, id_depot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
-- Mars 2025
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 45, 1150.00, 'MVT-HIST-2025-03-01', 'Historique mars', '2025-03-15', '2025-03-15', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 38, 1150.00, 'MVT-HIST-2025-03-02', 'Historique mars', '2025-03-20', '2025-03-20', 1),
-- Avril 2025
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 52, 1180.00, 'MVT-HIST-2025-04-01', 'Historique avril', '2025-04-10', '2025-04-10', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 41, 1180.00, 'MVT-HIST-2025-04-02', 'Historique avril', '2025-04-22', '2025-04-22', 1),
-- Mai 2025
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 48, 1190.00, 'MVT-HIST-2025-05-01', 'Historique mai', '2025-05-12', '2025-05-12', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 55, 1190.00, 'MVT-HIST-2025-05-02', 'Historique mai', '2025-05-25', '2025-05-25', 1),
-- Juin 2025
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 60, 1200.00, 'MVT-HIST-2025-06-01', 'Historique juin', '2025-06-08', '2025-06-08', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 44, 1200.00, 'MVT-HIST-2025-06-02', 'Historique juin', '2025-06-18', '2025-06-18', 1),
-- Juillet 2025
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 35, 1210.00, 'MVT-HIST-2025-07-01', 'Historique juillet', '2025-07-05', '2025-07-05', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 42, 1210.00, 'MVT-HIST-2025-07-02', 'Historique juillet', '2025-07-20', '2025-07-20', 1),
-- Août 2025
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 50, 1215.00, 'MVT-HIST-2025-08-01', 'Historique août', '2025-08-10', '2025-08-10', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 48, 1215.00, 'MVT-HIST-2025-08-02', 'Historique août', '2025-08-22', '2025-08-22', 1),
-- Septembre 2025
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 55, 1218.00, 'MVT-HIST-2025-09-01', 'Historique septembre', '2025-09-08', '2025-09-08', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 47, 1218.00, 'MVT-HIST-2025-09-02', 'Historique septembre', '2025-09-18', '2025-09-18', 1),
-- Octobre 2025
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 62, 1220.00, 'MVT-HIST-2025-10-01', 'Historique octobre', '2025-10-12', '2025-10-12', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 51, 1220.00, 'MVT-HIST-2025-10-02', 'Historique octobre', '2025-10-25', '2025-10-25', 1),
-- Novembre 2025
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 58, 1225.00, 'MVT-HIST-2025-11-01', 'Historique novembre', '2025-11-05', '2025-11-05', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 7, 0, 'livraison_client', 0, 49, 1225.00, 'MVT-HIST-2025-11-02', 'Historique novembre', '2025-11-20', '2025-11-20', 1);

-- Validation des mouvements historiques
INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) 
SELECT id_mouvement_stock, 'validé', 1 
FROM mouvement_stock 
WHERE mouvement_numero LIKE 'MVT-HIST-%';

-- ============================================
-- FACTURES HISTORIQUES (pour graphiques CA)
-- ============================================
-- Créer des factures pour les 12 derniers mois

-- D'abord supprimer les potentiels doublons
DELETE FROM facture_client WHERE numero_facture LIKE 'FACT-HIST-%';

INSERT INTO facture_client (numero_facture, date_facture, client_id, livraison_client_id, montant_ht, montant_tva, montant_ttc, statut, cree_par) VALUES
-- Mars 2025
('FACT-HIST-2025-03-01', '2025-03-15', 1, 1, 45000.00, 9000.00, 54000.00, 'PAYE', 1),
('FACT-HIST-2025-03-02', '2025-03-25', 2, 1, 38000.00, 7600.00, 45600.00, 'PAYE', 1),
-- Avril 2025
('FACT-HIST-2025-04-01', '2025-04-12', 3, 1, 52000.00, 10400.00, 62400.00, 'PAYE', 1),
('FACT-HIST-2025-04-02', '2025-04-28', 4, 1, 41000.00, 8200.00, 49200.00, 'PAYE', 1),
-- Mai 2025
('FACT-HIST-2025-05-01', '2025-05-10', 1, 1, 48000.00, 9600.00, 57600.00, 'PAYE', 1),
('FACT-HIST-2025-05-02', '2025-05-22', 2, 1, 55000.00, 11000.00, 66000.00, 'PAYE', 1),
-- Juin 2025
('FACT-HIST-2025-06-01', '2025-06-08', 3, 1, 60000.00, 12000.00, 72000.00, 'PAYE', 1),
('FACT-HIST-2025-06-02', '2025-06-20', 4, 1, 44000.00, 8800.00, 52800.00, 'PAYE', 1),
-- Juillet 2025
('FACT-HIST-2025-07-01', '2025-07-05', 1, 1, 35000.00, 7000.00, 42000.00, 'PAYE', 1),
('FACT-HIST-2025-07-02', '2025-07-25', 2, 1, 42000.00, 8400.00, 50400.00, 'PAYE', 1),
-- Août 2025
('FACT-HIST-2025-08-01', '2025-08-12', 3, 1, 50000.00, 10000.00, 60000.00, 'PAYE', 1),
('FACT-HIST-2025-08-02', '2025-08-28', 4, 1, 48000.00, 9600.00, 57600.00, 'PAYE', 1),
-- Septembre 2025
('FACT-HIST-2025-09-01', '2025-09-10', 1, 1, 55000.00, 11000.00, 66000.00, 'PAYE', 1),
('FACT-HIST-2025-09-02', '2025-09-25', 2, 1, 47000.00, 9400.00, 56400.00, 'PAYE', 1),
-- Octobre 2025
('FACT-HIST-2025-10-01', '2025-10-08', 3, 1, 62000.00, 12400.00, 74400.00, 'PAYE', 1),
('FACT-HIST-2025-10-02', '2025-10-22', 4, 1, 51000.00, 10200.00, 61200.00, 'PAYE', 1),
-- Novembre 2025
('FACT-HIST-2025-11-01', '2025-11-05', 1, 1, 58000.00, 11600.00, 69600.00, 'PAYE', 1),
('FACT-HIST-2025-11-02', '2025-11-20', 2, 1, 49000.00, 9800.00, 58800.00, 'PAYE', 1);

SELECT 'Données KPI Février 2026 insérées avec succès!' as status;
