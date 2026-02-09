-- =============================================================================
-- MODULE STOCK - DONNÉES DE TEST
-- =============================================================================
-- Ce fichier contient des données de test pour le module Stock
-- À exécuter après 04_avis-data-achat.sql et 05_avis-data-vente.sql

USE gestion_entreprise_test;

-- ============================================
-- LOTS (FIFO/LIFO/FEFO)
-- ============================================
-- Lots créés lors des réceptions fournisseur

INSERT INTO lot (id_article, id_depot, lot_numero, date_entree, quantite_initiale, cout_unitaire, date_limite_utilisation_optimale, date_limite_consommation) VALUES
-- Lots Article AVBIS001 (Biscuit Avoine) - Dépôt 1
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 'LOT-BIS-001', DATE_SUB(NOW(), INTERVAL 90 DAY), 100, 1200.00, DATE_ADD(NOW(), INTERVAL 6 MONTH), DATE_ADD(NOW(), INTERVAL 12 MONTH)),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 'LOT-BIS-002', DATE_SUB(NOW(), INTERVAL 60 DAY), 150, 1250.00, DATE_ADD(NOW(), INTERVAL 8 MONTH), DATE_ADD(NOW(), INTERVAL 14 MONTH)),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 'LOT-BIS-003', DATE_SUB(NOW(), INTERVAL 30 DAY), 200, 1220.00, DATE_ADD(NOW(), INTERVAL 10 MONTH), DATE_ADD(NOW(), INTERVAL 16 MONTH)),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 'LOT-BIS-004', DATE_SUB(NOW(), INTERVAL 7 DAY), 100, 1280.00, DATE_ADD(NOW(), INTERVAL 11 MONTH), DATE_ADD(NOW(), INTERVAL 17 MONTH)),

-- Lots Article AVCHO002 (Chocolat Noir) - Dépôt 1
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 'LOT-CHO-001', DATE_SUB(NOW(), INTERVAL 85 DAY), 80, 1800.00, DATE_ADD(NOW(), INTERVAL 4 MONTH), DATE_ADD(NOW(), INTERVAL 8 MONTH)),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 'LOT-CHO-002', DATE_SUB(NOW(), INTERVAL 55 DAY), 120, 1850.00, DATE_ADD(NOW(), INTERVAL 6 MONTH), DATE_ADD(NOW(), INTERVAL 10 MONTH)),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 'LOT-CHO-003', DATE_SUB(NOW(), INTERVAL 25 DAY), 100, 1820.00, DATE_ADD(NOW(), INTERVAL 8 MONTH), DATE_ADD(NOW(), INTERVAL 12 MONTH)),

-- Lots Article AVFAR003 (Farine Blé) - Dépôt 1
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 'LOT-FAR-001', DATE_SUB(NOW(), INTERVAL 80 DAY), 50, 65000.00, DATE_ADD(NOW(), INTERVAL 3 MONTH), DATE_ADD(NOW(), INTERVAL 6 MONTH)),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 'LOT-FAR-002', DATE_SUB(NOW(), INTERVAL 50 DAY), 40, 66000.00, DATE_ADD(NOW(), INTERVAL 5 MONTH), DATE_ADD(NOW(), INTERVAL 8 MONTH)),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 'LOT-FAR-003', DATE_SUB(NOW(), INTERVAL 20 DAY), 60, 64500.00, DATE_ADD(NOW(), INTERVAL 7 MONTH), DATE_ADD(NOW(), INTERVAL 10 MONTH)),

-- Lots Article AVSUC004 (Sucre Cristal) - Dépôt 1
((SELECT id_article FROM article WHERE code = 'AVSUC004'), 1, 'LOT-SUC-001', DATE_SUB(NOW(), INTERVAL 75 DAY), 80, 52000.00, NULL, NULL),
((SELECT id_article FROM article WHERE code = 'AVSUC004'), 1, 'LOT-SUC-002', DATE_SUB(NOW(), INTERVAL 45 DAY), 60, 53000.00, NULL, NULL),

-- Lots Article AVHUI005 (Huile Tournesol) - Dépôt 1
((SELECT id_article FROM article WHERE code = 'AVHUI005'), 1, 'LOT-HUI-001', DATE_SUB(NOW(), INTERVAL 70 DAY), 100, 18000.00, DATE_ADD(NOW(), INTERVAL 12 MONTH), DATE_ADD(NOW(), INTERVAL 18 MONTH)),
((SELECT id_article FROM article WHERE code = 'AVHUI005'), 1, 'LOT-HUI-002', DATE_SUB(NOW(), INTERVAL 35 DAY), 80, 18500.00, DATE_ADD(NOW(), INTERVAL 14 MONTH), DATE_ADD(NOW(), INTERVAL 20 MONTH)),

-- Lots pour Dépôt 2 (Tamatave)
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 2, 'LOT-BIS-TAM-001', DATE_SUB(NOW(), INTERVAL 40 DAY), 50, 1300.00, DATE_ADD(NOW(), INTERVAL 9 MONTH), DATE_ADD(NOW(), INTERVAL 15 MONTH)),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 2, 'LOT-CHO-TAM-001', DATE_SUB(NOW(), INTERVAL 35 DAY), 40, 1900.00, DATE_ADD(NOW(), INTERVAL 5 MONTH), DATE_ADD(NOW(), INTERVAL 9 MONTH));

-- ============================================
-- MOUVEMENTS DE STOCK - ENTRÉES (Réceptions)
-- ============================================
-- Type 1 = ACHAT_RECEPTION

INSERT INTO mouvement_stock (id_article, id_depot, id_lot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
-- Entrées liées aux réceptions fournisseur
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 1, 1, 1, 'reception_fournisseur', 1, 100, 1200.00, 'MVT-ENT-001', 'Réception REC00001', DATE_SUB(NOW(), INTERVAL 90 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 2, 1, 2, 'reception_fournisseur', 1, 150, 1250.00, 'MVT-ENT-002', 'Réception REC00002', DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_SUB(NOW(), INTERVAL 60 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 3, 1, 3, 'reception_fournisseur', 1, 200, 1220.00, 'MVT-ENT-003', 'Réception REC00003', DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 4, 1, 4, 'reception_fournisseur', 1, 100, 1280.00, 'MVT-ENT-004', 'Réception REC00004', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY), 1),

((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 5, 1, 5, 'reception_fournisseur', 1, 80, 1800.00, 'MVT-ENT-005', 'Réception chocolat', DATE_SUB(NOW(), INTERVAL 85 DAY), DATE_SUB(NOW(), INTERVAL 85 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 6, 1, 6, 'reception_fournisseur', 1, 120, 1850.00, 'MVT-ENT-006', 'Réception chocolat', DATE_SUB(NOW(), INTERVAL 55 DAY), DATE_SUB(NOW(), INTERVAL 55 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 7, 1, 7, 'reception_fournisseur', 1, 100, 1820.00, 'MVT-ENT-007', 'Réception chocolat', DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY), 1),

((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 8, 1, 8, 'reception_fournisseur', 1, 50, 65000.00, 'MVT-ENT-008', 'Réception farine', DATE_SUB(NOW(), INTERVAL 80 DAY), DATE_SUB(NOW(), INTERVAL 80 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 9, 1, 9, 'reception_fournisseur', 1, 40, 66000.00, 'MVT-ENT-009', 'Réception farine', DATE_SUB(NOW(), INTERVAL 50 DAY), DATE_SUB(NOW(), INTERVAL 50 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 10, 1, 10, 'reception_fournisseur', 1, 60, 64500.00, 'MVT-ENT-010', 'Réception farine', DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY), 1),

((SELECT id_article FROM article WHERE code = 'AVSUC004'), 1, 11, 1, 11, 'reception_fournisseur', 1, 80, 52000.00, 'MVT-ENT-011', 'Réception sucre', DATE_SUB(NOW(), INTERVAL 75 DAY), DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVSUC004'), 1, 12, 1, 12, 'reception_fournisseur', 1, 60, 53000.00, 'MVT-ENT-012', 'Réception sucre', DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_SUB(NOW(), INTERVAL 45 DAY), 1),

((SELECT id_article FROM article WHERE code = 'AVHUI005'), 1, 13, 1, 13, 'reception_fournisseur', 1, 100, 18000.00, 'MVT-ENT-013', 'Réception huile', DATE_SUB(NOW(), INTERVAL 70 DAY), DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVHUI005'), 1, 14, 1, 14, 'reception_fournisseur', 1, 80, 18500.00, 'MVT-ENT-014', 'Réception huile', DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 35 DAY), 1);

-- ============================================
-- MOUVEMENTS DE STOCK - SORTIES (Livraisons)
-- ============================================
-- Type 7 = VENTE_LIVRAISON

INSERT INTO mouvement_stock (id_article, id_depot, id_lot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
-- Sorties liées aux livraisons client (référence aux commandes 1-5 qui ont été livrées)
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 1, 7, 1, 'livraison_client', 0, 50, 1200.00, 'MVT-SOR-001', 'Livraison CMD-001201', DATE_SUB(NOW(), INTERVAL 42 DAY), DATE_SUB(NOW(), INTERVAL 42 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 5, 7, 2, 'livraison_client', 0, 100, 1800.00, 'MVT-SOR-002', 'Livraison CMD-001205', DATE_SUB(NOW(), INTERVAL 37 DAY), DATE_SUB(NOW(), INTERVAL 37 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 1, 7, 3, 'livraison_client', 0, 32, 1200.00, 'MVT-SOR-003', 'Livraison CMD-001210', DATE_SUB(NOW(), INTERVAL 32 DAY), DATE_SUB(NOW(), INTERVAL 32 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 8, 7, 4, 'livraison_client', 0, 15, 65000.00, 'MVT-SOR-004', 'Livraison CMD-001215', DATE_SUB(NOW(), INTERVAL 27 DAY), DATE_SUB(NOW(), INTERVAL 27 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 6, 7, 5, 'livraison_client', 0, 20, 1850.00, 'MVT-SOR-005', 'Livraison CMD-001220', DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY), 1);

-- ============================================
-- MOUVEMENTS DE STOCK - AUTRES TYPES
-- ============================================

-- Pertes/Casses (Type 10 = PERTE_CASSE)
INSERT INTO mouvement_stock (id_article, id_depot, id_lot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 2, 10, 0, NULL, 0, 5, 1250.00, 'MVT-PERTE-001', 'Casse lors manutention', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY), 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 6, 10, 0, NULL, 0, 3, 1850.00, 'MVT-PERTE-002', 'Produits endommagés stockage', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), 1);

-- Retour client (Type 2 = RETOUR_CLIENT)
INSERT INTO mouvement_stock (id_article, id_depot, id_lot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 3, 2, 7, 'commande_client', 1, 10, 1220.00, 'MVT-RET-001', 'Retour client - Articles non conformes', DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), 1);

-- Transfert inter-dépôt (Types 3 et 8)
INSERT INTO mouvement_stock (id_article, id_depot, id_lot, id_type_mouvement_stock, id_reference, table_reference, sens, quantite, cout_unitaire, mouvement_numero, motif, date_mouvement, date_validation, created_by) VALUES
-- Sortie du dépôt 1
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 2, 8, 0, 'transfert', 0, 50, 1250.00, 'MVT-TRF-SOR-001', 'Transfert vers Tamatave', DATE_SUB(NOW(), INTERVAL 40 DAY), DATE_SUB(NOW(), INTERVAL 40 DAY), 1),
-- Entrée au dépôt 2
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 2, 15, 3, 0, 'transfert', 1, 50, 1250.00, 'MVT-TRF-ENT-001', 'Transfert depuis Antananarivo', DATE_SUB(NOW(), INTERVAL 40 DAY), DATE_SUB(NOW(), INTERVAL 40 DAY), 1);

-- ============================================
-- VALIDATION DES MOUVEMENTS
-- ============================================
INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) VALUES
(1, 'validé', 1), (2, 'validé', 1), (3, 'validé', 1), (4, 'validé', 1), (5, 'validé', 1),
(6, 'validé', 1), (7, 'validé', 1), (8, 'validé', 1), (9, 'validé', 1), (10, 'validé', 1),
(11, 'validé', 1), (12, 'validé', 1), (13, 'validé', 1), (14, 'validé', 1),
(15, 'validé', 1), (16, 'validé', 1), (17, 'validé', 1), (18, 'validé', 1), (19, 'validé', 1),
(20, 'validé', 1), (21, 'validé', 1), (22, 'validé', 1), (23, 'validé', 1), (24, 'validé', 1);

-- ============================================
-- DÉTAILS CONSOMMATION PAR LOT (pour sorties)
-- ============================================
INSERT INTO mouvement_stock_lot_detail (id_mouvement_stock, id_lot, quantite, cout_unitaire, valeur) VALUES
-- Sortie MVT-SOR-001
(15, 1, 50, 1200.00, 60000.00),
-- Sortie MVT-SOR-002
(16, 5, 80, 1800.00, 144000.00),
(16, 6, 20, 1850.00, 37000.00),
-- Sortie MVT-SOR-003
(17, 1, 18, 1200.00, 21600.00),
(17, 2, 14, 1250.00, 17500.00),
-- Sortie MVT-SOR-004
(18, 8, 15, 65000.00, 975000.00),
-- Sortie MVT-SOR-005
(19, 6, 20, 1850.00, 37000.00);

-- ============================================
-- MISE À JOUR STOCK COURANT
-- ============================================
-- Suppression des données existantes pour éviter les doublons
DELETE FROM stock_courant WHERE id_article IN (SELECT id_article FROM article WHERE code IN ('AVBIS001', 'AVCHO002', 'AVFAR003', 'AVSUC004', 'AVHUI005'));

INSERT INTO stock_courant (id_article, id_depot, quantite, valeur_stock, cout_moyen) VALUES
-- Dépôt 1 (Antananarivo)
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 373, 453850.00, 1216.75),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 177, 323100.00, 1825.42),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 135, 8752500.00, 64833.33),
((SELECT id_article FROM article WHERE code = 'AVSUC004'), 1, 140, 7340000.00, 52428.57),
((SELECT id_article FROM article WHERE code = 'AVHUI005'), 1, 180, 3280000.00, 18222.22),

-- Dépôt 2 (Tamatave)
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 2, 50, 65000.00, 1300.00),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 2, 40, 76000.00, 1900.00);

-- ============================================
-- RÉSERVATIONS DE STOCK
-- ============================================
INSERT INTO stock_reservation (id_article, id_depot, id_client, quantite, date_expiration, reference, created_by) VALUES
-- Réservations pour commandes en cours
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 4, 32, DATE_ADD(NOW(), INTERVAL 7 DAY), 'CMD-001225', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 1, 12, DATE_ADD(NOW(), INTERVAL 7 DAY), 'CMD-001228', 1),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 5, 10, DATE_ADD(NOW(), INTERVAL 5 DAY), 'CMD-001230', 1),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 3, 30, DATE_ADD(NOW(), INTERVAL 3 DAY), 'CMD-001235', 1),
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 4, 31, DATE_ADD(NOW(), INTERVAL 3 DAY), 'CMD-001238', 1);

-- ============================================
-- CAMPAGNES D'INVENTAIRE
-- ============================================
INSERT INTO inventaire_campagne (code, libelle, description, type_campagne, statut, date_planification, date_debut_prevue, date_fin_prevue, created_by) VALUES
('INV-2025-12', 'Inventaire Annuel 2025', 'Inventaire général de fin d''année', 'GENERAL', 'CLOTURE', DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_SUB(NOW(), INTERVAL 58 DAY), DATE_SUB(NOW(), INTERVAL 55 DAY), 1),
('INV-2026-01', 'Inventaire Janvier 2026', 'Inventaire mensuel', 'PARTIEL', 'CLOTURE', DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 28 DAY), DATE_SUB(NOW(), INTERVAL 26 DAY), 1),
('INV-2026-02', 'Inventaire Février 2026', 'Inventaire mensuel en cours', 'PARTIEL', 'EN_COURS', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 1),
('INV-2026-03', 'Inventaire Mars 2026', 'Inventaire planifié', 'PARTIEL', 'PLANIFIE', NOW(), DATE_ADD(NOW(), INTERVAL 20 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 1);

-- Dépôts concernés par les campagnes
INSERT INTO inventaire_campagne_depot (id_inventaire_campagne, id_depot, id_site, zone, commentaire) VALUES
(1, 1, 1, 'Zone A - Stockage principal', 'Inventaire complet'),
(1, 2, 2, 'Zone B - Réserve', 'Inventaire complet'),
(2, 1, 1, 'Zone A', 'Inventaire partiel matières premières'),
(3, 1, 1, 'Zone A', 'En cours de comptage'),
(4, 1, 1, 'Zone A', 'Planifié pour mars');

-- Cibles des campagnes
INSERT INTO inventaire_campagne_cible (id_inventaire_campagne, type_cible, id_article, id_article_famille, inclure_lots, commentaire) VALUES
(1, 'TOUS', NULL, NULL, TRUE, 'Tous les articles'),
(2, 'FAMILLE', NULL, (SELECT id_article_famille FROM article_famille WHERE code = 'FAM-MP'), TRUE, 'Matières premières uniquement'),
(3, 'FAMILLE', NULL, (SELECT id_article_famille FROM article_famille WHERE code = 'FAM-ING'), TRUE, 'Ingrédients spéciaux'),
(4, 'TOUS', NULL, NULL, TRUE, 'Inventaire général');

-- Équipes d'inventaire
INSERT INTO inventaire_equipe (id_inventaire_campagne, nom_equipe, id_responsable, commentaire) VALUES
(1, 'Équipe Alpha', 1, 'Équipe principale'),
(1, 'Équipe Beta', 2, 'Équipe secondaire'),
(2, 'Équipe Stock', 1, 'Comptage matières premières'),
(3, 'Équipe Ingrédients', 1, 'Comptage en cours');

-- Membres des équipes
INSERT INTO inventaire_equipe_membre (id_inventaire_equipe, id_employe, role_membre) VALUES
(1, 1, 'SUPERVISEUR'),
(1, 2, 'COMPTEUR'),
(2, 3, 'SUPERVISEUR'),
(2, 4, 'COMPTEUR'),
(3, 1, 'SUPERVISEUR'),
(3, 5, 'COMPTEUR'),
(4, 1, 'SUPERVISEUR'),
(4, 2, 'COMPTEUR');

-- Comptages d'inventaire (campagne clôturée)
INSERT INTO inventaire_comptage (id_inventaire_campagne, id_depot, id_article, id_lot, quantite_theorique, quantite_comptee, ecart, commentaire, date_comptage, created_by) VALUES
-- Campagne 1 (Annuel 2025)
(1, 1, (SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 100, 98, -2, 'Écart constaté - Casse non déclarée', DATE_SUB(NOW(), INTERVAL 56 DAY), 1),
(1, 1, (SELECT id_article FROM article WHERE code = 'AVBIS001'), 2, 150, 150, 0, 'OK', DATE_SUB(NOW(), INTERVAL 56 DAY), 1),
(1, 1, (SELECT id_article FROM article WHERE code = 'AVCHO002'), 5, 80, 79, -1, 'Écart mineur', DATE_SUB(NOW(), INTERVAL 56 DAY), 1),
(1, 1, (SELECT id_article FROM article WHERE code = 'AVFAR003'), 8, 50, 50, 0, 'OK', DATE_SUB(NOW(), INTERVAL 56 DAY), 1),
(1, 1, (SELECT id_article FROM article WHERE code = 'AVSUC004'), 11, 80, 82, 2, 'Écart positif - Erreur réception?', DATE_SUB(NOW(), INTERVAL 56 DAY), 1),

-- Campagne 2 (Janvier 2026)
(2, 1, (SELECT id_article FROM article WHERE code = 'AVFAR003'), 9, 40, 40, 0, 'OK', DATE_SUB(NOW(), INTERVAL 27 DAY), 1),
(2, 1, (SELECT id_article FROM article WHERE code = 'AVSUC004'), 12, 60, 59, -1, 'Écart - Prélèvement non enregistré', DATE_SUB(NOW(), INTERVAL 27 DAY), 1),
(2, 1, (SELECT id_article FROM article WHERE code = 'AVHUI005'), 13, 100, 100, 0, 'OK', DATE_SUB(NOW(), INTERVAL 27 DAY), 1),

-- Campagne 3 (En cours - Février 2026)
(3, 1, (SELECT id_article FROM article WHERE code = 'AVBIS001'), 3, 200, 198, -2, 'Comptage en cours', DATE_SUB(NOW(), INTERVAL 1 DAY), 1),
(3, 1, (SELECT id_article FROM article WHERE code = 'AVCHO002'), 6, 120, 117, -3, 'Vérification nécessaire', DATE_SUB(NOW(), INTERVAL 1 DAY), 1);

-- Validations des campagnes clôturées
INSERT INTO inventaire_campagne_validation (id_inventaire_campagne, validated_by, total_ecart, total_valeur_ecart, commentaire, date_validation) VALUES
(1, 1, -1, -5450.00, 'Inventaire annuel validé - Écarts mineurs acceptés', DATE_SUB(NOW(), INTERVAL 55 DAY)),
(2, 1, -1, -53000.00, 'Inventaire matières premières validé', DATE_SUB(NOW(), INTERVAL 26 DAY));

-- ============================================
-- CLÔTURE MENSUELLE DU STOCK
-- ============================================
INSERT INTO stock_cloture_periode (annee, mois, statut, date_cloture) VALUES
(2025, 10, 'CLOTURE', '2025-10-31 23:59:59'),
(2025, 11, 'CLOTURE', '2025-11-30 23:59:59'),
(2025, 12, 'CLOTURE', '2025-12-31 23:59:59'),
(2026, 1, 'CLOTURE', '2026-01-31 23:59:59'),
(2026, 2, 'OUVERT', NULL);

-- Détails de clôture (mois précédents)
INSERT INTO stock_cloture_detail (id_stock_cloture_periode, id_article, id_depot, qty_ouverture, valeur_ouverture, cump_ouverture, qty_cloture, valeur_cloture, cump_cloture) VALUES
-- Décembre 2025
(3, (SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 200, 240000.00, 1200.00, 280, 341600.00, 1220.00),
(3, (SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 150, 270000.00, 1800.00, 180, 328500.00, 1825.00),
(3, (SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 80, 5200000.00, 65000.00, 120, 7800000.00, 65000.00),
(3, (SELECT id_article FROM article WHERE code = 'AVSUC004'), 1, 100, 5200000.00, 52000.00, 130, 6760000.00, 52000.00),
(3, (SELECT id_article FROM article WHERE code = 'AVHUI005'), 1, 120, 2160000.00, 18000.00, 160, 2920000.00, 18250.00),

-- Janvier 2026
(4, (SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 280, 341600.00, 1220.00, 350, 427000.00, 1220.00),
(4, (SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 180, 328500.00, 1825.00, 200, 365000.00, 1825.00),
(4, (SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 120, 7800000.00, 65000.00, 145, 9425000.00, 65000.00),
(4, (SELECT id_article FROM article WHERE code = 'AVSUC004'), 1, 130, 6760000.00, 52000.00, 140, 7280000.00, 52000.00),
(4, (SELECT id_article FROM article WHERE code = 'AVHUI005'), 1, 160, 2920000.00, 18250.00, 180, 3285000.00, 18250.00);

-- ============================================
-- ALERTES STOCK (via stock_courant et stock_min)
-- ============================================
-- Mise à jour des seuils d'alerte sur les articles
UPDATE article SET stock_min = 50 WHERE code = 'AVBIS001';
UPDATE article SET stock_min = 30 WHERE code = 'AVCHO002';
UPDATE article SET stock_min = 20 WHERE code = 'AVFAR003';
UPDATE article SET stock_min = 25 WHERE code = 'AVSUC004';
UPDATE article SET stock_min = 40 WHERE code = 'AVHUI005';

-- ============================================
-- DONNÉES POUR KPI STOCK
-- ============================================

-- Articles en rupture (pour test)
INSERT INTO stock_courant (id_article, id_depot, quantite, valeur_stock, cout_moyen) VALUES
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 2, 5, 325000.00, 65000.00),  -- Sous le seuil à Tamatave
((SELECT id_article FROM article WHERE code = 'AVSUC004'), 2, 0, 0.00, 0.00);  -- Rupture totale à Tamatave

-- Produits proches péremption (lots avec DLC proche)
UPDATE lot SET date_limite_consommation = DATE_ADD(NOW(), INTERVAL 15 DAY) WHERE lot_numero = 'LOT-CHO-001';
UPDATE lot SET date_limite_consommation = DATE_ADD(NOW(), INTERVAL 20 DAY) WHERE lot_numero = 'LOT-FAR-001';
UPDATE lot SET date_limite_consommation = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE lot_numero = 'LOT-CHO-TAM-001';
