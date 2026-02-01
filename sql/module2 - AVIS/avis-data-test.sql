-- Test data for stock module (AVIS)
-- Creates families, articles, depots, lots, current stock, sample movements and a reservation

-- Families (one requiring lot traceability, one general)
INSERT INTO article_famille (code, nom, description, necessite_lot) VALUES
('FRESH', 'Périssables', 'Produits périssables - traçabilité lots requise', TRUE),
('GEN', 'Général', 'Articles non périssables', FALSE);

-- Depôt
INSERT INTO depot (code, nom, adresse) VALUES
('MAIN', 'Dépôt principal', 'Rue Principale, Siège');

-- Articles
INSERT INTO article (code, designation, id_famille_article_famille, id_methode_valorisation, allocation_defaut, unite, prix_achat, prix_vente, stock_min, actif)
VALUES
('YOG1', 'Yaourt nature 125g', (SELECT id_article_famille FROM article_famille WHERE code='FRESH'), 1, 'fefo', 'pcs', 2.00, 3.50, 0, TRUE),
('GEN1', 'Visserie - lot standard', (SELECT id_article_famille FROM article_famille WHERE code='GEN'), 3, 'fifo', 'pcs', 0.50, 1.20, 10, TRUE);

-- Lots for perishable article (one expired, one valid)
INSERT INTO lot (id_article, id_depot, lot_numero, date_entree, quantite_initiale, cout_unitaire, date_limite_utilisation_optimale, date_limite_consommation)
VALUES
((SELECT id_article FROM article WHERE code='YOG1'), (SELECT id_depot FROM depot WHERE code='MAIN'), 'YOG-LOT-OLD-001', '2024-12-01 09:00:00', 50, 1.80, NULL, '2025-01-01'),
((SELECT id_article FROM article WHERE code='YOG1'), (SELECT id_depot FROM depot WHERE code='MAIN'), 'YOG-LOT-REC-001', '2025-12-01 09:00:00', 100, 2.00, NULL, '2026-03-01');

-- Current stock (reflects both lots above)
-- valeur_stock = 50*1.80 + 100*2.00 = 90 + 200 = 290
INSERT INTO stock_courant (id_article, id_depot, quantite, valeur_stock, cout_moyen)
VALUES
((SELECT id_article FROM article WHERE code='YOG1'), (SELECT id_depot FROM depot WHERE code='MAIN'), 150, 290.00, ROUND(290.00/150,4)),
((SELECT id_article FROM article WHERE code='GEN1'), (SELECT id_depot FROM depot WHERE code='MAIN'), 500, 250.00, ROUND(250.00/500,4));

-- Sample reservation (for delivery reference)
INSERT INTO stock_reservation (id_article, id_depot, quantite, reference, created_by)
VALUES
((SELECT id_article FROM article WHERE code='YOG1'), (SELECT id_depot FROM depot WHERE code='MAIN'), 20, 'ORDER#12345', 1);

-- Sample movements
-- 1) Exit movement (VENTE_LIVRAISON) for YOG1 with declared unit cost higher than CUMP -> positive variance
INSERT INTO mouvement_stock (id_article, id_depot, id_lot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, motif, date_mouvement, date_validation, created_by, mouvement_numero)
VALUES
((SELECT id_article FROM article WHERE code='YOG1'), (SELECT id_depot FROM depot WHERE code='MAIN'), NULL,
 (SELECT id_type_mouvement_stock FROM mouvement_stock_type WHERE code='VENTE_LIVRAISON' LIMIT 1), 12345, 'orders', 0, 10, 2.20, 'Livraison client #12345', '2026-02-01 10:00:00', NOW(), 1, NULL),

-- 2) Exit movement (CUMP) for GEN1 with declared unit cost lower than CUMP -> negative variance
((SELECT id_article FROM article WHERE code='GEN1'), (SELECT id_depot FROM depot WHERE code='MAIN'), NULL,
 (SELECT id_type_mouvement_stock FROM mouvement_stock_type WHERE code='INVENTAIRE_MOINS' LIMIT 1), 0, NULL, 0, 5, 0.40, 'Ajustement sortie', '2026-02-01 11:00:00', NOW(), 1, NULL);

-- 3) Entry movement to create a new lot for YOG1 (ACHAT_RECEPTION)
INSERT INTO mouvement_stock (id_article, id_depot, id_lot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, motif, date_mouvement, date_validation, created_by, mouvement_numero)
VALUES
((SELECT id_article FROM article WHERE code='YOG1'), (SELECT id_depot FROM depot WHERE code='MAIN'),
 (SELECT id_lot FROM lot WHERE lot_numero='YOG-LOT-REC-001' LIMIT 1),
 (SELECT id_type_mouvement_stock FROM mouvement_stock_type WHERE code='ACHAT_RECEPTION' LIMIT 1), 0, NULL, 1, 100, 2.00, 'Réception fournisseur', '2025-12-01 09:15:00', NOW(), 1, NULL);

-- Small helper: expose sample lot rows and movements for manual checks
-- SELECT * FROM lot WHERE id_article = (SELECT id_article FROM article WHERE code='YOG1');
-- SELECT * FROM mouvement_stock WHERE id_article = (SELECT id_article FROM article WHERE code='YOG1');

