-- =============================================================================
-- MODULE VENTES - TABLES SUPPLÉMENTAIRES
-- =============================================================================
-- Ces tables complètent le schéma existant pour le module VENTES
-- À exécuter après le script mio.sql

USE gestion_entreprise;

-- ============================================
-- TABLE: ligne_commande_client
-- Détails des articles commandés
-- ============================================
CREATE TABLE IF NOT EXISTS ligne_commande_client (
    id_ligne_commande_client INT AUTO_INCREMENT PRIMARY KEY,
    id_commande_client INT NOT NULL,
    id_article INT NOT NULL,
    quantite DECIMAL(15,3) NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    remise_pourcent DECIMAL(5,2) DEFAULT 0,
    remise_validee BOOLEAN DEFAULT FALSE COMMENT 'True si remise > 10% validée par responsable',
    taux_tva DECIMAL(5,2) DEFAULT 20,
    montant_ht DECIMAL(15,2) NOT NULL,
    montant_tva DECIMAL(15,2) NOT NULL,
    montant_ttc DECIMAL(15,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_commande_client) REFERENCES commande_client(id_commande_client) ON DELETE CASCADE,
    FOREIGN KEY (id_article) REFERENCES article(id_article)
);

-- ============================================
-- TABLE: ligne_livraison_client
-- Quantités réellement livrées par ligne de commande
-- ============================================
CREATE TABLE IF NOT EXISTS ligne_livraison_client (
    id_ligne_livraison_client INT AUTO_INCREMENT PRIMARY KEY,
    id_livraison_client INT NOT NULL,
    id_ligne_commande_client INT NOT NULL,
    quantite_livree DECIMAL(15,3) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_livraison_client) REFERENCES livraison_client(id_livraison_client) ON DELETE CASCADE,
    FOREIGN KEY (id_ligne_commande_client) REFERENCES ligne_commande_client(id_ligne_commande_client)
);

-- ============================================
-- AJOUT COLONNES MANQUANTES (compatible MySQL)
-- ============================================

-- Ajouter statut, valide_par, date_validation à commande_client
-- Utiliser des procédures pour vérifier si les colonnes existent

DELIMITER //

DROP PROCEDURE IF EXISTS add_columns_commande_client//

CREATE PROCEDURE add_columns_commande_client()
BEGIN
    -- Ajouter colonne statut si elle n'existe pas
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'commande_client' 
        AND COLUMN_NAME = 'statut'
    ) THEN
        ALTER TABLE commande_client ADD COLUMN statut ENUM('BROUILLON','VALIDE','CLOTURE') DEFAULT 'BROUILLON';
    END IF;
    
    -- Ajouter colonne valide_par si elle n'existe pas
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'commande_client' 
        AND COLUMN_NAME = 'valide_par'
    ) THEN
        ALTER TABLE commande_client ADD COLUMN valide_par INT NULL;
    END IF;
    
    -- Ajouter colonne date_validation si elle n'existe pas
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'commande_client' 
        AND COLUMN_NAME = 'date_validation'
    ) THEN
        ALTER TABLE commande_client ADD COLUMN date_validation DATETIME NULL;
    END IF;
END//

DELIMITER ;

CALL add_columns_commande_client();
DROP PROCEDURE IF EXISTS add_columns_commande_client;

-- ============================================
-- TYPES DE MOUVEMENTS STOCK POUR VENTES
-- ============================================
INSERT IGNORE INTO mouvement_stock_type (id_categorie_mouvement_stock, code, libelle, impact_valorisation) VALUES
((SELECT id_categorie_mouvement_stock FROM mouvement_stock_categorie WHERE code = 'out'), 'RESERVATION_VENTE', 'Réservation pour vente', 0),
((SELECT id_categorie_mouvement_stock FROM mouvement_stock_categorie WHERE code = 'in'), 'LIBERATION_RESERVATION', 'Libération de réservation', 0),
((SELECT id_categorie_mouvement_stock FROM mouvement_stock_categorie WHERE code = 'out'), 'SORTIE_VENTE', 'Sortie pour vente', 1);

-- ============================================
-- MODES DE PAIEMENT
-- ============================================
INSERT IGNORE INTO mode_paiement (code, libelle) VALUES
('ESP', 'Espèces'),
('CHQ', 'Chèque'),
('VIR', 'Virement bancaire'),
('CB', 'Carte bancaire'),
('PRE', 'Prélèvement');

-- ============================================
-- TYPES DE CLIENTS
-- ============================================
INSERT IGNORE INTO client_type (libelle) VALUES
('Particulier'),
('Professionnel'),
('Grossiste'),
('Revendeur');

-- ============================================
-- VUES KPI (ÉTAPE 5)
-- ============================================

-- Vue: Commandes en retard
CREATE OR REPLACE VIEW v_commandes_retard AS
SELECT 
    cc.*,
    c.nom AS client_nom,
    c.telephone AS client_telephone,
    DATEDIFF(CURDATE(), cc.commande_date) AS jours_retard
FROM commande_client cc
LEFT JOIN client c ON cc.id_client = c.id_client
LEFT JOIN livraison_client lc ON cc.id_commande_client = lc.id_commande_client AND lc.statut = 'LIVREE'
WHERE cc.statut = 'VALIDE'
AND lc.id_livraison_client IS NULL
AND DATEDIFF(CURDATE(), cc.commande_date) > 7;

-- Vue: Chiffre d'affaires mensuel
CREATE OR REPLACE VIEW v_ca_mensuel AS
SELECT 
    YEAR(date_facture) AS annee,
    MONTH(date_facture) AS mois,
    COUNT(*) AS nombre_factures,
    SUM(montant_ht) AS ca_ht,
    SUM(montant_tva) AS total_tva,
    SUM(montant_ttc) AS ca_ttc,
    AVG(montant_ttc) AS panier_moyen
FROM facture_client
WHERE statut IN ('VALIDE', 'PAYE')
GROUP BY YEAR(date_facture), MONTH(date_facture)
ORDER BY annee DESC, mois DESC;

-- Vue: Backlog commandes
CREATE OR REPLACE VIEW v_backlog_commandes AS
SELECT 
    cc.*,
    c.nom AS client_nom,
    SUM(lcc.quantite) AS qte_commandee,
    COALESCE(SUM(delivered.qte_livree), 0) AS qte_livree,
    SUM(lcc.quantite) - COALESCE(SUM(delivered.qte_livree), 0) AS qte_restante
FROM commande_client cc
LEFT JOIN client c ON cc.id_client = c.id_client
LEFT JOIN ligne_commande_client lcc ON cc.id_commande_client = lcc.id_commande_client
LEFT JOIN (
    SELECT llc.id_ligne_commande_client, SUM(llc.quantite_livree) AS qte_livree
    FROM ligne_livraison_client llc
    JOIN livraison_client lc ON llc.id_livraison_client = lc.id_livraison_client
    WHERE lc.statut = 'LIVREE'
    GROUP BY llc.id_ligne_commande_client
) delivered ON lcc.id_ligne_commande_client = delivered.id_ligne_commande_client
WHERE cc.statut = 'VALIDE'
GROUP BY cc.id_commande_client
HAVING qte_restante > 0;

-- Vue: Factures impayées avec ancienneté
CREATE OR REPLACE VIEW v_factures_impayees AS
SELECT 
    fc.*,
    c.nom AS client_nom,
    c.telephone AS client_telephone,
    fc.montant_ttc - COALESCE(encaisse.total_encaisse, 0) AS reste_a_payer,
    DATEDIFF(CURDATE(), fc.date_facture) AS anciennete_jours,
    CASE 
        WHEN DATEDIFF(CURDATE(), fc.date_facture) <= 30 THEN '0-30 jours'
        WHEN DATEDIFF(CURDATE(), fc.date_facture) <= 60 THEN '31-60 jours'
        WHEN DATEDIFF(CURDATE(), fc.date_facture) <= 90 THEN '61-90 jours'
        ELSE 'Plus de 90 jours'
    END AS tranche_anciennete
FROM facture_client fc
LEFT JOIN client c ON fc.client_id = c.id_client
LEFT JOIN (
    SELECT facture_client_id, SUM(montant) AS total_encaisse
    FROM encaissement_client
    GROUP BY facture_client_id
) encaisse ON fc.id_facture_client = encaisse.facture_client_id
WHERE fc.statut = 'VALIDE'
HAVING reste_a_payer > 0;

-- Vue: Taux de remise
CREATE OR REPLACE VIEW v_taux_remise AS
SELECT 
    AVG(lcc.remise_pourcent) AS remise_moyenne,
    MAX(lcc.remise_pourcent) AS remise_max,
    MIN(lcc.remise_pourcent) AS remise_min,
    COUNT(CASE WHEN lcc.remise_pourcent > 10 THEN 1 END) AS nb_remises_elevees,
    COUNT(*) AS total_lignes
FROM ligne_commande_client lcc
JOIN commande_client cc ON lcc.id_commande_client = cc.id_commande_client
WHERE cc.statut IN ('VALIDE', 'CLOTURE');
