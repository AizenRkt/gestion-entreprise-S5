-- Données articles pour les suggestions de saisie demande d'achat
INSERT INTO article (code, designation, id_famille_article_famille, id_methode_valorisation, unite, prix_achat, prix_vente, stock_min, actif)
VALUES
('AVBIS001', 'Biscuit Avoine', NULL, NULL, 'pièce', 1200.00, 1500.00, 0, TRUE),
('AVCHO002', 'Chocolat Noir 70%', NULL, NULL, 'pièce', 1800.00, 2300.00, 0, TRUE),
('AVFAR003', 'Farine Blé 50kg', NULL, NULL, 'sac', 65000.00, 78000.00, 0, TRUE),
('AVSUC004', 'Sucre Cristal 50kg', NULL, NULL, 'sac', 52000.00, 64000.00, 0, TRUE),
('AVHUI005', 'Huile Tournesol 5L', NULL, NULL, 'bidon', 18000.00, 22000.00, 0, TRUE);

-- Données fournisseurs (table fournisseur)
INSERT INTO fournisseur (nom, adresse, telephone, email) VALUES
('Grossiste Analakely', 'Analakely, Antananarivo', '+261 34 00 000 01', 'contact@analakely.mg'),
('Distrib Denrées AV', 'Andraharo, Antananarivo', '+261 34 00 000 02', 'ventes@denrees-av.mg'),
('LogiNord', 'Zone industrielle nord', '+261 34 00 000 03', 'info@loginord.mg');

-- Stock courant pour affichage quantité en stock (exemple)
INSERT INTO stock_courant (id_article, id_depot, quantite, valeur_stock, cout_moyen)
VALUES
((SELECT id_article FROM article WHERE code = 'AVBIS001'), 1, 150, 180000.00, 1200.00),
((SELECT id_article FROM article WHERE code = 'AVCHO002'), 1, 80, 144000.00, 1800.00),
((SELECT id_article FROM article WHERE code = 'AVFAR003'), 1, 25, 1625000.00, 65000.00),
((SELECT id_article FROM article WHERE code = 'AVSUC004'), 1, 40, 2080000.00, 52000.00),
((SELECT id_article FROM article WHERE code = 'AVHUI005'), 1, 60, 1080000.00, 18000.00);

-- Dépôts et sites (achats)
INSERT INTO depot (code, nom, adresse) VALUES
('DEP-ANT-01', 'Dépôt Central Antananarivo', 'Andraharo - Zone industrielle'),
('DEP-TAM-01', 'Dépôt Tamatave', 'Zone portuaire'),
('DEP-TUL-01', 'Dépôt Tuléar', 'PK6 RN7');

INSERT INTO site (code, nom) VALUES
('SITE-ANT', 'Site Antananarivo'),
('SITE-TAM', 'Site Tamatave');

INSERT INTO site_depot (id_depot, id_site)
SELECT d.id_depot, s.id_site FROM depot d, site s WHERE d.code = 'DEP-ANT-01' AND s.code = 'SITE-ANT';
INSERT INTO site_depot (id_depot, id_site)
SELECT d.id_depot, s.id_site FROM depot d, site s WHERE d.code = 'DEP-TAM-01' AND s.code = 'SITE-TAM';

-- Modes de paiement (pour paiements fournisseurs)
INSERT INTO mode_paiement (code, libelle) VALUES
('VIR', 'Virement bancaire'),
('ESP', 'Espèces'),
('CHQ', 'Chèque');

-- ==========================================
-- DONNEES DE TEST POUR KPI ACHATS
-- ==========================================

-- Demandes d'achat
INSERT INTO demande_achat (numero, date_demande, id_fournisseur, remarque, statut, montant_ht, montant_tva, montant_ttc, created_by, created_at) VALUES 
('DMDA00001', DATE_SUB(NOW(), INTERVAL 10 DAY), 1, 'Commande urgente biscuits', 'VISEE', 180000, 36000, 216000, 1, DATE_SUB(NOW(), INTERVAL 10 DAY)),
('DMDA00002', DATE_SUB(NOW(), INTERVAL 5 DAY), 2, 'Stock chocolat', 'VISEE', 144000, 28800, 172800, 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
('DMDA00003', DATE_SUB(NOW(), INTERVAL 2 DAY), 3, 'Farine pour la semaine', 'CREE', 650000, 130000, 780000, 1, DATE_SUB(NOW(), INTERVAL 2 DAY));

INSERT INTO demande_achat_ligne (id_demande_achat, id_article, designation, quantite, prix_unitaire, tva) VALUES
(1, 1, 'Biscuit Avoine', 150, 1200, 20),
(2, 2, 'Chocolat Noir 70%', 80, 1800, 20),
(3, 3, 'Farine Blé 50kg', 10, 65000, 20);

-- Bons de commande
INSERT INTO bon_commande_fournisseur (bc_numero, bc_date, id_fournisseur, id_depot, id_demande_achat, montant_ht, montant_tva, montant_ttc, created_by, created_at) VALUES
('BC00001', DATE_SUB(NOW(), INTERVAL 9 DAY), 1, 1, 1, 180000, 36000, 216000, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
('BC00002', DATE_SUB(NOW(), INTERVAL 4 DAY), 2, 1, 2, 144000, 28800, 172800, 1, DATE_SUB(NOW(), INTERVAL 4 DAY));

-- Reception pour BC00001 (Lead time = 2 days : J-9 to J-7)
INSERT INTO reception_fournisseur (reception_numero, reception_date, id_fournisseur, id_bon_commande_fournisseur, id_depot, created_by, created_at) VALUES
('REC00001', DATE_SUB(NOW(), INTERVAL 7 DAY), 1, 1, 1, 1, DATE_SUB(NOW(), INTERVAL 7 DAY));

-- Reception pour BC00002 (Lead time = 3 days : J-4 to J-1)
INSERT INTO reception_fournisseur (reception_numero, reception_date, id_fournisseur, id_bon_commande_fournisseur, id_depot, created_by, created_at) VALUES
('REC00002', DATE_SUB(NOW(), INTERVAL 1 DAY), 2, 2, 1, 1, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- ==========================================
-- DONNEES SUPPLEMENTAIRES POUR GRAPHIQUES
-- ==========================================

-- Plus de commandes pour avoir des données sur plusieurs mois
INSERT INTO bon_commande_fournisseur (bc_numero, bc_date, id_fournisseur, id_depot, montant_ht, montant_tva, montant_ttc, created_by, created_at) VALUES
-- Mois -3
('BC00003', DATE_SUB(NOW(), INTERVAL 90 DAY), 1, 1, 250000, 50000, 300000, 1, DATE_SUB(NOW(), INTERVAL 90 DAY)),
('BC00004', DATE_SUB(NOW(), INTERVAL 85 DAY), 2, 1, 180000, 36000, 216000, 1, DATE_SUB(NOW(), INTERVAL 85 DAY)),
('BC00005', DATE_SUB(NOW(), INTERVAL 80 DAY), 3, 1, 120000, 24000, 144000, 1, DATE_SUB(NOW(), INTERVAL 80 DAY)),

-- Mois -2
('BC00006', DATE_SUB(NOW(), INTERVAL 60 DAY), 1, 1, 280000, 56000, 336000, 1, DATE_SUB(NOW(), INTERVAL 60 DAY)),
('BC00007', DATE_SUB(NOW(), INTERVAL 55 DAY), 2, 1, 200000, 40000, 240000, 1, DATE_SUB(NOW(), INTERVAL 55 DAY)),
('BC00008', DATE_SUB(NOW(), INTERVAL 50 DAY), 3, 1, 150000, 30000, 180000, 1, DATE_SUB(NOW(), INTERVAL 50 DAY)),

-- Mois -1
('BC00009', DATE_SUB(NOW(), INTERVAL 30 DAY), 1, 1, 320000, 64000, 384000, 1, DATE_SUB(NOW(), INTERVAL 30 DAY)),
('BC00010', DATE_SUB(NOW(), INTERVAL 25 DAY), 2, 1, 220000, 44000, 264000, 1, DATE_SUB(NOW(), INTERVAL 25 DAY)),
('BC00011', DATE_SUB(NOW(), INTERVAL 20 DAY), 3, 1, 160000, 32000, 192000, 1, DATE_SUB(NOW(), INTERVAL 20 DAY)),

-- Mois actuel (en plus des 2 déjà créés)
('BC00012', DATE_SUB(NOW(), INTERVAL 15 DAY), 1, 1, 200000, 40000, 240000, 1, DATE_SUB(NOW(), INTERVAL 15 DAY)),
('BC00013', DATE_SUB(NOW(), INTERVAL 12 DAY), 2, 1, 160000, 32000, 192000, 1, DATE_SUB(NOW(), INTERVAL 12 DAY)),
('BC00014', DATE_SUB(NOW(), INTERVAL 8 DAY), 3, 1, 140000, 28000, 168000, 1, DATE_SUB(NOW(), INTERVAL 8 DAY));

-- Réceptions correspondantes pour calculer les lead times
INSERT INTO reception_fournisseur (reception_numero, reception_date, id_fournisseur, id_bon_commande_fournisseur, id_depot, created_by, created_at) VALUES
('REC00003', DATE_SUB(NOW(), INTERVAL 87 DAY), 1, 3, 1, 1, DATE_SUB(NOW(), INTERVAL 87 DAY)),
('REC00004', DATE_SUB(NOW(), INTERVAL 82 DAY), 2, 4, 1, 1, DATE_SUB(NOW(), INTERVAL 82 DAY)),
('REC00005', DATE_SUB(NOW(), INTERVAL 77 DAY), 3, 5, 1, 1, DATE_SUB(NOW(), INTERVAL 77 DAY)),
('REC00006', DATE_SUB(NOW(), INTERVAL 58 DAY), 1, 6, 1, 1, DATE_SUB(NOW(), INTERVAL 58 DAY)),
('REC00007', DATE_SUB(NOW(), INTERVAL 53 DAY), 2, 7, 1, 1, DATE_SUB(NOW(), INTERVAL 53 DAY)),
('REC00008', DATE_SUB(NOW(), INTERVAL 48 DAY), 3, 8, 1, 1, DATE_SUB(NOW(), INTERVAL 48 DAY)),
('REC00009', DATE_SUB(NOW(), INTERVAL 28 DAY), 1, 9, 1, 1, DATE_SUB(NOW(), INTERVAL 28 DAY)),
('REC00010', DATE_SUB(NOW(), INTERVAL 23 DAY), 2, 10, 1, 1, DATE_SUB(NOW(), INTERVAL 23 DAY)),
('REC00011', DATE_SUB(NOW(), INTERVAL 18 DAY), 3, 11, 1, 1, DATE_SUB(NOW(), INTERVAL 18 DAY)),
('REC00012', DATE_SUB(NOW(), INTERVAL 13 DAY), 1, 12, 1, 1, DATE_SUB(NOW(), INTERVAL 13 DAY)),
('REC00013', DATE_SUB(NOW(), INTERVAL 10 DAY), 2, 13, 1, 1, DATE_SUB(NOW(), INTERVAL 10 DAY)),
('REC00014', DATE_SUB(NOW(), INTERVAL 6 DAY), 3, 14, 1, 1, DATE_SUB(NOW(), INTERVAL 6 DAY));

-- ==========================================
-- SCHEMA MANQUANT POUR KPI (Simulation)
-- ==========================================

-- Table Litiges (si n'existe pas dans le module principal)
CREATE TABLE IF NOT EXISTS litige_fournisseur (
    id_litige INT AUTO_INCREMENT PRIMARY KEY,
    ref_litige VARCHAR(20) NOT NULL,
    date_litige DATE NOT NULL,
    id_fournisseur INT NOT NULL,
    type_litige VARCHAR(50) NOT NULL,
    description TEXT,
    montant_enjeu DECIMAL(15,2),
    priorite ENUM('Basse', 'Moyenne', 'Haute') DEFAULT 'Moyenne',
    statut ENUM('Nouveau', 'En cours', 'Analyse', 'Résolu', 'Cloturé') DEFAULT 'Nouveau',
    responsable VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Données Litiges
INSERT INTO litige_fournisseur (ref_litige, date_litige, id_fournisseur, type_litige, description, montant_enjeu, priorite, statut, responsable) VALUES
('L-2401', DATE_SUB(NOW(), INTERVAL 2 DAY), 1, 'Facture', 'Écart prix unitaire vs BC', 4200.00, 'Haute', 'En cours', 'J. Martin'),
('L-2398', DATE_SUB(NOW(), INTERVAL 5 DAY), 4, 'Qualité', 'Pièces non-conformes aux specs', 8500.00, 'Haute', 'Analyse', 'S. Dubois'),
('L-2387', DATE_SUB(NOW(), INTERVAL 12 DAY), 2, 'Quantité', 'Livraison partielle non signalée', 2100.00, 'Moyenne', 'En cours', 'J. Martin'),
('L-2375', DATE_SUB(NOW(), INTERVAL 20 DAY), 1, 'Délai', 'Retard de livraison > 5 jours', NULL, 'Faible', 'Résolu', 'S. Dubois');

-- Données Historique Prix (Simulation pour graphique)
-- On suppose que article_prix_historique existe (voir avis.sql), on injecte des données
INSERT INTO article_prix_historique (id_article, prix_vente, date_modification) VALUES
-- Article 1 Variation
(1, 100, DATE_SUB(NOW(), INTERVAL 12 MONTH)),
(1, 101, DATE_SUB(NOW(), INTERVAL 11 MONTH)),
(1, 102, DATE_SUB(NOW(), INTERVAL 10 MONTH)),
(1, 103, DATE_SUB(NOW(), INTERVAL 9 MONTH)),
(1, 104, DATE_SUB(NOW(), INTERVAL 8 MONTH)),
(1, 105, DATE_SUB(NOW(), INTERVAL 7 MONTH)),
(1, 103, DATE_SUB(NOW(), INTERVAL 6 MONTH)),
(1, 102, DATE_SUB(NOW(), INTERVAL 5 MONTH)),
(1, 101, DATE_SUB(NOW(), INTERVAL 4 MONTH)),
(1, 100, DATE_SUB(NOW(), INTERVAL 3 MONTH)),
(1, 99, DATE_SUB(NOW(), INTERVAL 2 MONTH)),
(1, 98, DATE_SUB(NOW(), INTERVAL 1 MONTH)),

-- Article 2 Variation
(2, 100, DATE_SUB(NOW(), INTERVAL 12 MONTH)),
(2, 102, DATE_SUB(NOW(), INTERVAL 11 MONTH)),
(2, 105, DATE_SUB(NOW(), INTERVAL 10 MONTH)),
(2, 108, DATE_SUB(NOW(), INTERVAL 9 MONTH)),
(2, 110, DATE_SUB(NOW(), INTERVAL 8 MONTH)),
(2, 112, DATE_SUB(NOW(), INTERVAL 7 MONTH)),
(2, 111, DATE_SUB(NOW(), INTERVAL 6 MONTH)),
(2, 109, DATE_SUB(NOW(), INTERVAL 5 MONTH)),
(2, 107, DATE_SUB(NOW(), INTERVAL 4 MONTH)),
(2, 105, DATE_SUB(NOW(), INTERVAL 3 MONTH)),
(2, 103, DATE_SUB(NOW(), INTERVAL 2 MONTH)),
(2, 101, DATE_SUB(NOW(), INTERVAL 1 MONTH));

