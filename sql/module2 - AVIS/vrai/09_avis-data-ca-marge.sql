-- =============================================================================
-- DONNÉES CA ET MARGE BRUTE - FÉVRIER 2026
-- =============================================================================
-- Exécuter ce script pour alimenter les KPIs CA et Marge Brute
-- Date actuelle : 9 février 2026

USE gestion_entreprise_test;

-- ============================================
-- 1. FACTURES CLIENT FÉVRIER 2026
-- Pour le Chiffre d'Affaires (montant_ttc) et Marge (montant_ht)
-- ============================================

-- Vérifier/créer des livraisons pour lier les factures
INSERT IGNORE INTO livraison_client (livraison_numero, livraison_date, id_commande_client, id_depot, statut, cree_par, valide_par, date_validation)
SELECT 
    CONCAT('LIV-FEV-', LPAD(ROW_NUMBER() OVER (), 3, '0')),
    DATE_SUB(NOW(), INTERVAL (7 - ROW_NUMBER() OVER ()) DAY),
    id_commande_client,
    id_depot,
    'LIVREE',
    1,
    1,
    DATE_SUB(NOW(), INTERVAL (7 - ROW_NUMBER() OVER ()) DAY)
FROM commande_client
WHERE id_commande_client <= 5
LIMIT 5;

-- Supprimer les anciennes factures de test février si elles existent
DELETE FROM facture_client WHERE numero_facture LIKE 'FACT-FEV-2026-%';

-- Insérer les factures de février 2026
INSERT INTO facture_client (numero_facture, date_facture, client_id, livraison_client_id, montant_ht, montant_tva, montant_ttc, statut, cree_par, valide_par, date_validation) VALUES
-- Semaine 1 (1-7 février)
('FACT-FEV-2026-001', '2026-02-02 10:30:00', 1, 1, 125000.00, 25000.00, 150000.00, 'PAYE', 1, 1, '2026-02-02 10:30:00'),
('FACT-FEV-2026-002', '2026-02-03 14:15:00', 2, 1, 87500.00, 17500.00, 105000.00, 'PAYE', 1, 1, '2026-02-03 14:15:00'),
('FACT-FEV-2026-003', '2026-02-04 09:00:00', 3, 1, 156000.00, 31200.00, 187200.00, 'VALIDE', 1, 1, '2026-02-04 09:00:00'),
('FACT-FEV-2026-004', '2026-02-05 11:45:00', 4, 1, 68000.00, 13600.00, 81600.00, 'PAYE', 1, 1, '2026-02-05 11:45:00'),
('FACT-FEV-2026-005', '2026-02-06 16:00:00', 1, 1, 94500.00, 18900.00, 113400.00, 'VALIDE', 1, 1, '2026-02-06 16:00:00'),
-- Semaine 2 (actuellement)
('FACT-FEV-2026-006', '2026-02-07 08:30:00', 2, 1, 112000.00, 22400.00, 134400.00, 'VALIDE', 1, 1, '2026-02-07 08:30:00'),
('FACT-FEV-2026-007', '2026-02-08 10:00:00', 3, 1, 78500.00, 15700.00, 94200.00, 'VALIDE', 1, 1, '2026-02-08 10:00:00'),
('FACT-FEV-2026-008', '2026-02-09 09:15:00', 4, 1, 145000.00, 29000.00, 174000.00, 'BROUILLON', 1, NULL, NULL);

-- ============================================
-- 2. MOUVEMENTS VENTE_LIVRAISON FÉVRIER 2026
-- Pour le coût des ventes (quantite * cout_unitaire)
-- ============================================

-- Supprimer les anciens mouvements de test
DELETE FROM mouvement_stock WHERE mouvement_numero LIKE 'MVT-VENTE-FEV26-%';

-- Mouvements de vente (sorties stock) - type 7 = VENTE_LIVRAISON
INSERT INTO mouvement_stock (id_article, id_depot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
-- Ventes du 2 février
((SELECT id_article FROM article WHERE code = 'AVBIS001' LIMIT 1), 1, 7, 1, 'livraison_client', 0, 100, 1220.00, 'MVT-VENTE-FEV26-001', 'Vente Biscuits', '2026-02-02 10:30:00', '2026-02-02 10:30:00', 1),
-- Ventes du 3 février
((SELECT id_article FROM article WHERE code = 'AVCHO002' LIMIT 1), 1, 7, 2, 'livraison_client', 0, 45, 1820.00, 'MVT-VENTE-FEV26-002', 'Vente Chocolat', '2026-02-03 14:15:00', '2026-02-03 14:15:00', 1),
-- Ventes du 4 février
((SELECT id_article FROM article WHERE code = 'AVFAR003' LIMIT 1), 1, 7, 3, 'livraison_client', 0, 20, 64500.00, 'MVT-VENTE-FEV26-003', 'Vente Farine', '2026-02-04 09:00:00', '2026-02-04 09:00:00', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001' LIMIT 1), 1, 7, 3, 'livraison_client', 0, 80, 1250.00, 'MVT-VENTE-FEV26-004', 'Vente Biscuits', '2026-02-04 09:00:00', '2026-02-04 09:00:00', 1),
-- Ventes du 5 février
((SELECT id_article FROM article WHERE code = 'AVSUC004' LIMIT 1), 1, 7, 4, 'livraison_client', 0, 12, 52000.00, 'MVT-VENTE-FEV26-005', 'Vente Sucre', '2026-02-05 11:45:00', '2026-02-05 11:45:00', 1),
-- Ventes du 6 février
((SELECT id_article FROM article WHERE code = 'AVHUI005' LIMIT 1), 1, 7, 5, 'livraison_client', 0, 50, 18200.00, 'MVT-VENTE-FEV26-006', 'Vente Huile', '2026-02-06 16:00:00', '2026-02-06 16:00:00', 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002' LIMIT 1), 1, 7, 5, 'livraison_client', 0, 35, 1850.00, 'MVT-VENTE-FEV26-007', 'Vente Chocolat', '2026-02-06 16:00:00', '2026-02-06 16:00:00', 1),
-- Ventes du 7 février
((SELECT id_article FROM article WHERE code = 'AVBIS001' LIMIT 1), 1, 7, 6, 'livraison_client', 0, 90, 1280.00, 'MVT-VENTE-FEV26-008', 'Vente Biscuits', '2026-02-07 08:30:00', '2026-02-07 08:30:00', 1),
-- Ventes du 8 février
((SELECT id_article FROM article WHERE code = 'AVFAR003' LIMIT 1), 1, 7, 7, 'livraison_client', 0, 10, 65000.00, 'MVT-VENTE-FEV26-009', 'Vente Farine', '2026-02-08 10:00:00', '2026-02-08 10:00:00', 1),
-- Ventes du 9 février (aujourd'hui)
((SELECT id_article FROM article WHERE code = 'AVCHO002' LIMIT 1), 1, 7, 8, 'livraison_client', 0, 60, 1820.00, 'MVT-VENTE-FEV26-010', 'Vente Chocolat', '2026-02-09 09:15:00', '2026-02-09 09:15:00', 1),
((SELECT id_article FROM article WHERE code = 'AVSUC004' LIMIT 1), 1, 7, 8, 'livraison_client', 0, 25, 53000.00, 'MVT-VENTE-FEV26-011', 'Vente Sucre', '2026-02-09 09:15:00', '2026-02-09 09:15:00', 1);

-- Valider les mouvements
INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by)
SELECT id_mouvement_stock, 'validé', 1 
FROM mouvement_stock 
WHERE mouvement_numero LIKE 'MVT-VENTE-FEV26-%';

-- ============================================
-- 3. FACTURES JANVIER 2026 (pour comparaison M-1)
-- ============================================
DELETE FROM facture_client WHERE numero_facture LIKE 'FACT-JAN-2026-%';

INSERT INTO facture_client (numero_facture, date_facture, client_id, livraison_client_id, montant_ht, montant_tva, montant_ttc, statut, cree_par, valide_par, date_validation) VALUES
('FACT-JAN-2026-001', '2026-01-05', 1, 1, 98000.00, 19600.00, 117600.00, 'PAYE', 1, 1, '2026-01-05'),
('FACT-JAN-2026-002', '2026-01-10', 2, 1, 125000.00, 25000.00, 150000.00, 'PAYE', 1, 1, '2026-01-10'),
('FACT-JAN-2026-003', '2026-01-15', 3, 1, 87500.00, 17500.00, 105000.00, 'PAYE', 1, 1, '2026-01-15'),
('FACT-JAN-2026-004', '2026-01-20', 4, 1, 142000.00, 28400.00, 170400.00, 'PAYE', 1, 1, '2026-01-20'),
('FACT-JAN-2026-005', '2026-01-25', 1, 1, 68000.00, 13600.00, 81600.00, 'PAYE', 1, 1, '2026-01-25'),
('FACT-JAN-2026-006', '2026-01-28', 2, 1, 95000.00, 19000.00, 114000.00, 'PAYE', 1, 1, '2026-01-28');

-- Mouvements janvier pour coût des ventes M-1
DELETE FROM mouvement_stock WHERE mouvement_numero LIKE 'MVT-VENTE-JAN26-%';

INSERT INTO mouvement_stock (id_article, id_depot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
((SELECT id_article FROM article WHERE code = 'AVBIS001' LIMIT 1), 1, 7, 1, 'livraison_client', 0, 75, 1200.00, 'MVT-VENTE-JAN26-001', 'Vente janvier', '2026-01-05', '2026-01-05', 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002' LIMIT 1), 1, 7, 2, 'livraison_client', 0, 55, 1780.00, 'MVT-VENTE-JAN26-002', 'Vente janvier', '2026-01-10', '2026-01-10', 1),
((SELECT id_article FROM article WHERE code = 'AVFAR003' LIMIT 1), 1, 7, 3, 'livraison_client', 0, 12, 64000.00, 'MVT-VENTE-JAN26-003', 'Vente janvier', '2026-01-15', '2026-01-15', 1),
((SELECT id_article FROM article WHERE code = 'AVSUC004' LIMIT 1), 1, 7, 4, 'livraison_client', 0, 22, 51500.00, 'MVT-VENTE-JAN26-004', 'Vente janvier', '2026-01-20', '2026-01-20', 1),
((SELECT id_article FROM article WHERE code = 'AVHUI005' LIMIT 1), 1, 7, 5, 'livraison_client', 0, 35, 18000.00, 'MVT-VENTE-JAN26-005', 'Vente janvier', '2026-01-25', '2026-01-25', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001' LIMIT 1), 1, 7, 6, 'livraison_client', 0, 60, 1220.00, 'MVT-VENTE-JAN26-006', 'Vente janvier', '2026-01-28', '2026-01-28', 1);

INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by)
SELECT id_mouvement_stock, 'validé', 1 
FROM mouvement_stock 
WHERE mouvement_numero LIKE 'MVT-VENTE-JAN26-%';

-- ============================================
-- RÉSUMÉ DES DONNÉES
-- ============================================
SELECT 'FÉVRIER 2026 - CA' as periode, 
       COUNT(*) as nb_factures, 
       SUM(montant_ht) as total_ht, 
       SUM(montant_ttc) as total_ttc 
FROM facture_client 
WHERE MONTH(date_facture) = 2 AND YEAR(date_facture) = 2026;

SELECT 'FÉVRIER 2026 - COÛT VENTES' as periode,
       COUNT(*) as nb_mouvements,
       SUM(quantite * cout_unitaire) as cout_total
FROM mouvement_stock ms
JOIN mouvement_stock_type mst ON ms.id_type_mouvement_stock = mst.id_type_mouvement_stock
WHERE mst.code = 'VENTE_LIVRAISON'
  AND MONTH(ms.date_mouvement) = 2 AND YEAR(ms.date_mouvement) = 2026;

SELECT 'JANVIER 2026 - CA' as periode, 
       COUNT(*) as nb_factures, 
       SUM(montant_ht) as total_ht, 
       SUM(montant_ttc) as total_ttc 
FROM facture_client 
WHERE MONTH(date_facture) = 1 AND YEAR(date_facture) = 2026;

SELECT 'Données CA et Marge insérées avec succès!' as status;
