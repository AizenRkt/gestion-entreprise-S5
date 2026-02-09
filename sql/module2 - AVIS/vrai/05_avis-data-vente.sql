-- =============================================================================
-- MODULE VENTES - DONNÉES DE TEST
-- =============================================================================
-- Ce fichier contient des données de test pour le dashboard KPI Ventes
-- À exécuter après ventes_tables.sql

USE gestion_entreprise_test;

-- ============================================
-- CLIENTS
-- ============================================
INSERT INTO client (nom, telephone, email, adresse, id_type) VALUES
('Groupe Commercial A', '01 23 45 67 89', 'contact@groupea.fr', '15 Rue du Commerce, 75001 Paris', 2),
('Entreprise B SARL', '01 34 56 78 90', 'commande@entrepriseb.fr', '28 Avenue des Affaires, 69002 Lyon', 2),
('Petit Commerçant C', '01 45 67 89 01', 'c.petit@commerce.fr', '5 Place du Marché, 33000 Bordeaux', 1),
('Grand Distributeur D', '01 56 78 90 12', 'achats@distributeur-d.fr', '100 Boulevard Industrie, 59000 Lille', 3),
('Client Particulier E', '06 12 34 56 78', 'e.client@email.fr', '8 Rue Résidentielle, 44000 Nantes', 1),
('Revendeur F', '01 67 89 01 23', 'ventes@revendeurf.fr', '42 Route Logistique, 13000 Marseille', 4);

-- ============================================
-- COMMANDES CLIENT
-- ============================================
-- Commandes validées et livrées (succès)
INSERT INTO commande_client (commande_numero, commande_date, id_client, id_depot, montant_ht, montant_tva, montant_ttc, statut, created_by, valide_par, date_validation) VALUES
('CMD-001201', DATE_SUB(NOW(), INTERVAL 45 DAY), 1, 1, 5000.00, 1000.00, 6000.00, 'CLOTURE', 1, 1, DATE_SUB(NOW(), INTERVAL 44 DAY)),
('CMD-001205', DATE_SUB(NOW(), INTERVAL 40 DAY), 4, 1, 8500.00, 1700.00, 10200.00, 'CLOTURE', 1, 1, DATE_SUB(NOW(), INTERVAL 39 DAY)),
('CMD-001210', DATE_SUB(NOW(), INTERVAL 35 DAY), 2, 1, 3200.00, 640.00, 3840.00, 'CLOTURE', 1, 1, DATE_SUB(NOW(), INTERVAL 34 DAY)),
('CMD-001215', DATE_SUB(NOW(), INTERVAL 30 DAY), 5, 1, 1350.00, 270.00, 1620.00, 'CLOTURE', 1, 1, DATE_SUB(NOW(), INTERVAL 29 DAY)),
('CMD-001220', DATE_SUB(NOW(), INTERVAL 25 DAY), 3, 1, 1900.00, 380.00, 2280.00, 'CLOTURE', 1, 1, DATE_SUB(NOW(), INTERVAL 24 DAY)),

-- Commandes en cours (validées, non livrées)
('CMD-001225', DATE_SUB(NOW(), INTERVAL 20 DAY), 4, 1, 3200.00, 640.00, 3840.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 19 DAY)),
('CMD-001228', DATE_SUB(NOW(), INTERVAL 18 DAY), 1, 1, 1200.00, 240.00, 1440.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 17 DAY)),
('CMD-001230', DATE_SUB(NOW(), INTERVAL 15 DAY), 5, 1, 890.00, 178.00, 1068.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 14 DAY)),

-- Commandes en retard (validées il y a > 7 jours, non livrées)
('CMD-001235', DATE_SUB(NOW(), INTERVAL 12 DAY), 3, 1, 2800.00, 560.00, 3360.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 11 DAY)),
('CMD-001238', DATE_SUB(NOW(), INTERVAL 10 DAY), 4, 1, 3100.00, 620.00, 3720.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
('CMD-001240', DATE_SUB(NOW(), INTERVAL 8 DAY), 2, 1, 1450.00, 290.00, 1740.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
('CMD-001242', DATE_SUB(NOW(), INTERVAL 8 DAY), 2, 1, 1850.00, 370.00, 2220.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
('CMD-001245', DATE_SUB(NOW(), INTERVAL 9 DAY), 1, 1, 2300.00, 460.00, 2760.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 8 DAY)),

-- Commandes récentes
('CMD-001250', DATE_SUB(NOW(), INTERVAL 5 DAY), 6, 1, 4500.00, 900.00, 5400.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
('CMD-001252', DATE_SUB(NOW(), INTERVAL 3 DAY), 1, 1, 2100.00, 420.00, 2520.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
('CMD-001255', DATE_SUB(NOW(), INTERVAL 2 DAY), 3, 1, 1600.00, 320.00, 1920.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- ============================================
-- LIGNES COMMANDE CLIENT (avec remises)
-- ============================================
INSERT INTO ligne_commande_client (id_commande_client, id_article, quantite, prix_unitaire, remise_pourcent, remise_validee, taux_tva, montant_ht, montant_tva, montant_ttc) VALUES
-- CMD-001201
(1, 1, 50, 100.00, 0, FALSE, 20, 5000.00, 1000.00, 6000.00),
-- CMD-001205 (avec remise)
(2, 2, 100, 90.00, 5, FALSE, 20, 8500.00, 1700.00, 10200.00),
-- CMD-001210
(3, 1, 32, 100.00, 0, FALSE, 20, 3200.00, 640.00, 3840.00),
-- CMD-001215 (avec remise élevée)
(4, 3, 15, 100.00, 10, TRUE, 20, 1350.00, 270.00, 1620.00),
-- CMD-001220 (erreur prix - avoir)
(5, 2, 20, 95.00, 0, FALSE, 20, 1900.00, 380.00, 2280.00),
-- CMD-001225
(6, 1, 32, 100.00, 0, FALSE, 20, 3200.00, 640.00, 3840.00),
-- CMD-001228 (retour produit - avoir)
(7, 1, 12, 100.00, 0, FALSE, 20, 1200.00, 240.00, 1440.00),
-- CMD-001230
(8, 3, 10, 89.00, 0, FALSE, 20, 890.00, 178.00, 1068.00),
-- CMD-001235 (annulée)
(9, 2, 30, 95.00, 2, FALSE, 20, 2800.00, 560.00, 3360.00),
-- CMD-001238
(10, 1, 31, 100.00, 0, FALSE, 20, 3100.00, 620.00, 3720.00),
-- CMD-001240 (annulée - stock)
(11, 1, 15, 98.00, 1, FALSE, 20, 1450.00, 290.00, 1740.00),
-- CMD-001242
(12, 2, 20, 95.00, 3, FALSE, 20, 1850.00, 370.00, 2220.00),
-- CMD-001245
(13, 1, 23, 100.00, 0, FALSE, 20, 2300.00, 460.00, 2760.00),
-- CMD-001250
(14, 2, 50, 90.00, 0, FALSE, 20, 4500.00, 900.00, 5400.00),
-- CMD-001252
(15, 1, 21, 100.00, 0, FALSE, 20, 2100.00, 420.00, 2520.00),
-- CMD-001255
(16, 3, 18, 90.00, 1, FALSE, 20, 1600.00, 320.00, 1920.00);

-- ============================================
-- LIVRAISONS CLIENT
-- ============================================
INSERT INTO livraison_client (livraison_numero, livraison_date, id_commande_client, id_depot, statut, cree_par, valide_par, date_validation) VALUES
-- Livraisons complètes
('LIV-001201', DATE_SUB(NOW(), INTERVAL 42 DAY), 1, 1, 'LIVREE', 1, 1, DATE_SUB(NOW(), INTERVAL 42 DAY)),
('LIV-001205', DATE_SUB(NOW(), INTERVAL 37 DAY), 2, 1, 'LIVREE', 1, 1, DATE_SUB(NOW(), INTERVAL 37 DAY)),
('LIV-001210', DATE_SUB(NOW(), INTERVAL 32 DAY), 3, 1, 'LIVREE', 1, 1, DATE_SUB(NOW(), INTERVAL 32 DAY)),
('LIV-001215', DATE_SUB(NOW(), INTERVAL 27 DAY), 4, 1, 'LIVREE', 1, 1, DATE_SUB(NOW(), INTERVAL 27 DAY)),
('LIV-001220', DATE_SUB(NOW(), INTERVAL 22 DAY), 5, 1, 'LIVREE', 1, 1, DATE_SUB(NOW(), INTERVAL 22 DAY));

-- ============================================
-- LIGNES LIVRAISON CLIENT
-- ============================================
INSERT INTO ligne_livraison_client (id_livraison_client, id_ligne_commande_client, quantite_livree) VALUES
(1, 1, 50),
(2, 2, 100),
(3, 3, 32),
(4, 4, 15),
(5, 5, 20);

-- ============================================
-- FACTURES CLIENT
-- ============================================
INSERT INTO facture_client (numero_facture, date_facture, client_id, livraison_client_id, montant_ht, montant_tva, montant_ttc, statut, cree_par, valide_par, date_validation) VALUES
('FACT-001201', DATE_SUB(NOW(), INTERVAL 40 DAY), 1, 1, 5000.00, 1000.00, 6000.00, 'PAYE', 1, 1, DATE_SUB(NOW(), INTERVAL 40 DAY)),
('FACT-001205', DATE_SUB(NOW(), INTERVAL 35 DAY), 4, 2, 8500.00, 1700.00, 10200.00, 'PAYE', 1, 1, DATE_SUB(NOW(), INTERVAL 35 DAY)),
('FACT-001210', DATE_SUB(NOW(), INTERVAL 30 DAY), 2, 3, 3200.00, 640.00, 3840.00, 'PAYE', 1, 1, DATE_SUB(NOW(), INTERVAL 30 DAY)),
('FACT-001215', DATE_SUB(NOW(), INTERVAL 25 DAY), 5, 4, 1350.00, 270.00, 1620.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 25 DAY)),
('FACT-001220', DATE_SUB(NOW(), INTERVAL 20 DAY), 3, 5, 1900.00, 380.00, 2280.00, 'VALIDE', 1, 1, DATE_SUB(NOW(), INTERVAL 20 DAY));

-- ============================================
-- ENCAISSEMENTS CLIENT
-- ============================================
INSERT INTO encaissement_client (numero_encaissement, date_encaissement, facture_client_id, montant, id_mode_paiement, reference_paiement, cree_par) VALUES
('ENC-001201', DATE_SUB(NOW(), INTERVAL 38 DAY), 1, 6000.00, 3, 'VIR-2024-001', 1),
('ENC-001205', DATE_SUB(NOW(), INTERVAL 33 DAY), 2, 10200.00, 3, 'VIR-2024-002', 1),
('ENC-001210', DATE_SUB(NOW(), INTERVAL 28 DAY), 3, 3840.00, 2, 'CHQ-123456', 1);

-- ============================================
-- DONNÉES SUPPLÉMENTAIRES POUR KPI
-- ============================================

-- Table temporaire pour annulations (si pas de colonne statut annulé)
-- CREATE TABLE IF NOT EXISTS commande_annulation (
--     id_annulation INT AUTO_INCREMENT PRIMARY KEY,
--     id_commande_client INT NOT NULL,
--     date_annulation DATE NOT NULL,
--     motif VARCHAR(100),
--     raison_detail TEXT,
--     montant_impact DECIMAL(15,2),
--     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (id_commande_client) REFERENCES commande_client(id_commande_client)
-- );

INSERT INTO commande_annulation (id_commande_client, date_annulation, motif, raison_detail, montant_impact) VALUES
(9, DATE_SUB(NOW(), INTERVAL 11 DAY), 'Demande client', 'Client a changé d''avis', 3360.00),
(11, DATE_SUB(NOW(), INTERVAL 7 DAY), 'Stock insuffisant', 'Article en rupture prévue', 1740.00);

-- Table pour avoirs/crédits
-- CREATE TABLE IF NOT EXISTS avoir_client (
--     id_avoir INT AUTO_INCREMENT PRIMARY KEY,
--     numero_avoir VARCHAR(50) NOT NULL UNIQUE,
--     date_avoir DATE NOT NULL,
--     id_commande_client INT NOT NULL,
--     id_client INT NOT NULL,
--     type_avoir VARCHAR(50),
--     motif TEXT,
--     montant DECIMAL(15,2) NOT NULL,
--     statut ENUM('En attente', 'Émis', 'Validé') DEFAULT 'En attente',
--     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (id_commande_client) REFERENCES commande_client(id_commande_client),
--     FOREIGN KEY (id_client) REFERENCES client(id_client)
-- );

INSERT INTO avoir_client (numero_avoir, date_avoir, id_commande_client, id_client, type_avoir, motif, montant, statut) VALUES
('AV-001228', DATE_SUB(NOW(), INTERVAL 10 DAY), 7, 1, 'Retour produit', 'Article non conforme - Retourné 08/01', 1200.00, 'Émis'),
('AV-001220', DATE_SUB(NOW(), INTERVAL 8 DAY), 5, 3, 'Erreur prix', 'Prix facturé différent du devis', 380.00, 'En attente'),
('AV-001215', DATE_SUB(NOW(), INTERVAL 5 DAY), 4, 5, 'Casse transport', '5 articles cassés à la livraison', 1620.00, 'Émis'),
('AV-001205', DATE_SUB(NOW(), INTERVAL 2 DAY), 2, 4, 'Geste commercial', 'Retard livraison > 15 jours - compensation', 800.00, 'Émis');
