-- ==========================================================
-- Data for client types and clients
INSERT INTO client_type (libelle) VALUES
('Particulier'),
('Grossiste'),
('Détaillant'),
('Entreprise'),
('Administration publique');

INSERT INTO client (nom, telephone, email, adresse, id_client_type) VALUES
-- Particuliers
('Rakoto Jean', '0341122334', 'rakoto.jean@gmail.com', 'Analakely, Antananarivo', 1),
('Rasoanaivo Lala', '0324455667', 'lala.rasoanaivo@gmail.com', 'Anosy, Antananarivo', 1),

-- Grossistes
('Madagascar Trading SARL', '0202212345', 'contact@madatrading.mg', 'Zone industrielle Forello, Tanjombato', 2),
('Sava Distribution', '0202256789', 'info@savadistribution.mg', 'Antalaha, Région SAVA', 2),

-- Détaillants
('Supermarché Score Ankorondrano', '0202233445', 'ankorondrano@score.mg', 'Ankorondrano, Antananarivo', 3),
('Shoprite Toamasina', '0202267788', 'toamasina@shoprite.mg', 'Centre-ville, Toamasina', 3),

-- Entreprises
('JB Transport & Logistique', '0202245566', 'contact@jbtransport.mg', 'Route des Hydrocarbures, Toamasina', 4),
('AgroExport Madagascar', '0202278899', 'export@agromada.mg', 'Zone industrielle, Mahajanga', 4),

-- Administrations
('Commune Urbaine Antananarivo', '0202233999', 'mairie@cu-tnr.mg', 'Hôtel de Ville, Analakely', 5),
('Ministère de la Santé Publique', '0202201122', 'contact@sante.gov.mg', 'Ambohidahy, Antananarivo', 5);

-- ==========================================================
-- Data for sites and depots
INSERT INTO site (code, nom) VALUES
('SITE-TNR', 'Antananarivo'),
('SITE-TMM', 'Toamasina'),
('SITE-MJN', 'Mahajanga'),
('SITE-TLE', 'Toliara'),
('SITE-FNR', 'Fianarantsoa'),
('SITE-DIE', 'Antsiranana');

INSERT INTO depot (code, nom, adresse) VALUES
('DEP-CENTRAL-TNR', 'Dépôt Central Antananarivo', 'Zone Industrielle Forello Tanjombato, Antananarivo'),
('DEP-NORD-TNR', 'Dépôt Nord Antananarivo', 'Anosiala, Antananarivo'),
('DEP-PORT-TMM', 'Dépôt Portuaire Toamasina', 'Zone portuaire, Toamasina'),
('DEP-OUEST-MJN', 'Dépôt Régional Mahajanga', 'Route du Port, Mahajanga'),
('DEP-SUD-TLE', 'Dépôt Régional Toliara', 'Route de l''Aéroport, Toliara'),
('DEP-HAUTS-FNR', 'Dépôt Régional Fianarantsoa', 'Zone industrielle Andranomadio, Fianarantsoa'),
('DEP-NORD-DIE', 'Dépôt Régional Antsiranana', 'Zone portuaire, Antsiranana');

INSERT INTO site_depot (id_depot, id_site) VALUES
-- Antananarivo
(1, 1),
(2, 1),
-- Toamasina
(3, 2),
-- Mahajanga
(4, 3),
-- Toliara
(5, 4),
-- Fianarantsoa
(6, 5),
-- Antsiranana
(7, 6);

-- ==========================================================
-- data for article families, articles and suppliers
INSERT INTO article_famille (code, nom, description, necessite_lot) VALUES
('FAM-ALIM', 'Produits Alimentaires', 'Produits alimentaires et denrées', TRUE),
('FAM-HYGI', 'Produits d''hygiène', 'Savons, détergents, produits sanitaires', FALSE),
('FAM-BTP', 'Matériaux BTP', 'Matériaux de construction', FALSE),
('FAM-BUREAU', 'Fournitures de bureau', 'Articles bureautiques', FALSE),
('FAM-PHARM', 'Produits pharmaceutiques', 'Médicaments et consommables médicaux', TRUE);
INSERT INTO article_famille_status (libelle, date_status, id_article_famille) VALUES
('actif', NOW(), 1),
('actif', NOW(), 2),
('actif', NOW(), 3),
('actif', NOW(), 4),
('actif', NOW(), 5);

INSERT INTO article (
    code, designation, id_famille_article_famille, id_methode_valorisation,
    allocation_defaut, unite, prix_achat, prix_vente, stock_min, actif
) VALUES
-- Alimentaire
('ART-RIZ-001', 'Riz blanc 50kg', 1, 1, 'fifo', 'sac', 120000, 135000, 20, TRUE),
('ART-HUILE-001', 'Huile végétale 5L', 1, 1, 'fifo', 'bidon', 35000, 42000, 30, TRUE),
-- Hygiène
('ART-SAVON-001', 'Savon en barre 250g', 2, 3, 'fifo', 'pièce', 800, 1200, 200, TRUE),
('ART-JAVEL-001', 'Eau de javel 1L', 2, 3, 'fifo', 'bouteille', 1500, 2200, 150, TRUE),
-- BTP
('ART-CIMENT-001', 'Ciment CPJ 50kg', 3, 1, 'fifo', 'sac', 28000, 32000, 100, TRUE),
('ART-FER-001', 'Fer à béton 8mm', 3, 3, 'fifo', 'barre', 9000, 11000, 80, TRUE),
-- Bureau
('ART-PAPIER-001', 'Ramette papier A4', 4, 3, 'fifo', 'paquet', 18000, 22000, 50, TRUE),
-- Pharmaceutique
('ART-PARA-001', 'Paracétamol 500mg (boîte)', 5, 1, 'fefo', 'boîte', 2500, 3500, 100, TRUE);
INSERT INTO article_status (libelle, date_status, id_article) VALUES
('disponible', NOW(), 1),
('disponible', NOW(), 2),
('disponible', NOW(), 3),
('disponible', NOW(), 4),
('disponible', NOW(), 5),
('disponible', NOW(), 6),
('disponible', NOW(), 7),
('disponible', NOW(), 8);

-- fournisseur
INSERT INTO fournisseur (nom, adresse, telephone, email) VALUES
('Société Agricole Malgache', 'Alaotra Mangoro', '0345566778', 'contact@sama.mg'),
('Tiko Distribution', 'Ankorondrano, Antananarivo', '0202244556', 'distribution@tiko.mg'),
('Sanifer Madagascar', 'Zone industrielle Forello, Tanjombato', '0202233445', 'contact@sanifer.mg'),
('Batipro Madagascar', 'Route Digoin, Antananarivo', '0202277889', 'vente@batipro.mg'),
('Pharma Logistique MG', 'Anosizato, Antananarivo', '0202211223', 'appro@pharmalog.mg');

INSERT INTO fournisseur_article (id_fournisseur, id_article, prix_achat, delai_livraison) VALUES
-- Alimentaire
(1, 1, 118000, 5),
(1, 2, 34000, 4),
-- Hygiène
(2, 3, 780, 3),
(2, 4, 1400, 3),
-- BTP
(4, 5, 27500, 7),
(4, 6, 8800, 6),
-- Bureau
(3, 7, 17500, 4),
-- Pharmaceutique
(5, 8, 2400, 2);

-- ==========================================================
-- Data for documents
INSERT INTO document_type_avis (libelle) VALUES
('Bon de commande'),
('Bon de livraison'),
('Facture fournisseur'),
('Facture client'),
('Avis d''entrée en stock'),
('Avis de sortie de stock'),
('Procès-verbal d''inventaire');

INSERT INTO document_avis (id_document_type, reference, description, path) VALUES
-- Bons de commande
(1, 'BC-2025-001', 'Commande riz et huile auprès du fournisseur', '/documents/commandes/BC-2025-001.pdf'),
(1, 'BC-2025-002', 'Commande ciment et fer à béton', '/documents/commandes/BC-2025-002.pdf'),
-- Bons de livraison
(2, 'BL-2025-001', 'Livraison riz - Dépôt Central Antananarivo', '/documents/livraisons/BL-2025-001.pdf'),
(2, 'BL-2025-002', 'Livraison ciment - Dépôt Régional Mahajanga', '/documents/livraisons/BL-2025-002.pdf'),
-- Factures fournisseur
(3, 'FF-2025-001', 'Facture fournisseur Société Agricole Malgache', '/documents/factures/fournisseur/FF-2025-001.pdf'),
(3, 'FF-2025-002', 'Facture fournisseur Batipro Madagascar', '/documents/factures/fournisseur/FF-2025-002.pdf'),
-- Factures client
(4, 'FC-2025-001', 'Facture client Supermarché Score Ankorondrano', '/documents/factures/client/FC-2025-001.pdf'),
(4, 'FC-2025-002', 'Facture client Commune Urbaine Antananarivo', '/documents/factures/client/FC-2025-002.pdf'),
-- Avis de stock
(5, 'AE-2025-001', 'Avis d''entrée stock - Riz blanc 50kg', '/documents/avis/entree/AE-2025-001.pdf'),
(6, 'AS-2025-001', 'Avis de sortie stock - Savon 250g', '/documents/avis/sortie/AS-2025-001.pdf'),
-- Inventaire
(7, 'INV-2025-001', 'Inventaire annuel Dépôt Central Antananarivo', '/documents/inventaires/INV-2025-001.pdf');
