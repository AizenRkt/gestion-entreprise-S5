USE gestion_entreprise_test;

-- ====== Client Type ======
INSERT INTO client_type (libelle) VALUES
('Particulier'), ('Entreprise');

-- ====== Clients ======
INSERT INTO client (nom, telephone, email, adresse, id_client_type) VALUES
('Dupont SARL', '0341234567', 'contact@dupont.com', 'Antananarivo', 2),
('Rasoa', '0329876543', 'rasoa@email.com', 'Toamasina', 1);

-- ====== Articles Famille ======
INSERT INTO article_famille (code, nom, description) VALUES
('AF001', 'Boissons', 'Boissons en bouteille'),
('AF002', 'Snacks', 'Petits en-cas');

-- ====== Articles ======
INSERT INTO article (code, designation, id_famille_article_famille, id_methode_valorisation, unite, prix_achat, prix_vente, stock_min, actif) VALUES
('A001', 'Eau minérale 1L', 1, 1, 'bouteille', 0.50, 1.00, 10, TRUE),
('A002', 'Chips nature', 2, 3, 'paquet', 0.80, 1.50, 5, TRUE);

-- ====== Depots ======
INSERT INTO depot (code, nom, adresse) VALUES
('D001', 'Depot Central', 'Antananarivo'),
('D002', 'Depot Est', 'Toamasina');

-- ====== Fournisseurs ======
INSERT INTO fournisseur (nom, adresse, telephone, email) VALUES
('Fournisseur A', 'Antananarivo', '0321111111', 'fa@fournisseur.com'),
('Fournisseur B', 'Toamasina', '0322222222', 'fb@fournisseur.com');

-- ====== Lots ======
INSERT INTO lot (id_article, id_depot, lot_numero, date_entree, quantite_initiale, cout_unitaire) VALUES
(1, 1, 'LOT-001', '2026-01-30 08:00:00', 100, 0.50),
(2, 1, 'LOT-002', '2026-01-30 09:00:00', 50, 0.80);

-- ====== Mouvement Stock Type ======
-- déjà inséré dans ta base via ton script

-- ====== Stock Courant ======
INSERT INTO stock_courant (id_article, id_depot, quantite, valeur_stock, cout_moyen) VALUES
(1, 1, 100, 50, 0.50),
(2, 1, 50, 40, 0.80);

-- ====== Bon Commande Fournisseur ======
INSERT INTO bon_commande_fournisseur (bc_numero, bc_date, id_fournisseur, id_depot, montant_ht, created_by) VALUES
('BC-001', '2026-01-29 10:00:00', 1, 1, 100, 1);

-- ====== Reception Fournisseur ======
INSERT INTO reception_fournisseur (reception_numero, reception_date, id_fournisseur, id_bon_commande_fournisseur, id_depot, created_by) VALUES
('RC-001', '2026-01-30 10:00:00', 1, 1, 1, 1);

-- ====== Commande Client ======
INSERT INTO commande_client (commande_numero, commande_date, id_client, id_depot, montant_ht, created_by) VALUES
('CMD-001', '2026-01-30 14:00:00', 1, 1, 50, 1);

-- ====== Livraison Client ======
INSERT INTO livraison_client (livraison_numero, livraison_date, id_commande_client, id_depot, statut, cree_par) VALUES
('LIV-001', '2026-01-30 15:00:00', 1, 1, 'BROUILLON', 1);
