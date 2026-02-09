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
