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
