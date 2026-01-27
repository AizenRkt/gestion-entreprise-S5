<?php

namespace app\models\AVIS\Vente;

use Exception;
use Flight;
use PDO;
use PDOException;

/**
 * =============================================================================
 * ÉTAPE 5 - KPI DU MODULE VENTES
 * =============================================================================
 * 
 * Les KPI (Key Performance Indicators) permettent de piloter l'activité commerciale.
 * Ils sont calculés à partir des tables du module VENTES.
 */
class VenteKPIModel
{
    /**
     * KPI 1: COMMANDES EN RETARD
     * --------------------------
     * Objectif métier: Identifier les commandes validées non livrées dans les délais
     * Formule: Commandes VALIDEES où date_validation + délai_livraison < date_actuelle
     * Tables: commande_client, livraison_client
     */
    public function getCommandesEnRetard(int $delaiJours = 7): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT 
                        cc.*,
                        c.nom AS client_nom,
                        c.telephone AS client_telephone,
                        DATEDIFF(CURDATE(), cc.commande_date) AS jours_retard
                    FROM commande_client cc
                    LEFT JOIN client c ON cc.id_client = c.id_client
                    LEFT JOIN livraison_client lc ON cc.id_commande_client = lc.id_commande_client AND lc.statut = 'LIVREE'
                    WHERE cc.statut = 'VALIDE'
                    AND lc.id_livraison_client IS NULL
                    AND DATEDIFF(CURDATE(), cc.commande_date) > :delai
                    ORDER BY cc.commande_date ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute(['delai' => $delaiJours]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur KPI commandes en retard: ' . $e->getMessage());
        }
    }

    /**
     * KPI 2: CHIFFRE D'AFFAIRES PAR PÉRIODE
     * -------------------------------------
     * Objectif métier: Mesurer la performance commerciale sur une période
     * Formule: SUM(montant_ttc) des factures VALIDÉES ou PAYÉES
     * Tables: facture_client
     */
    public function getChiffreAffaires(string $dateDebut, string $dateFin): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT 
                        COUNT(*) AS nombre_factures,
                        COALESCE(SUM(montant_ht), 0) AS ca_ht,
                        COALESCE(SUM(montant_tva), 0) AS total_tva,
                        COALESCE(SUM(montant_ttc), 0) AS ca_ttc,
                        COALESCE(AVG(montant_ttc), 0) AS panier_moyen
                    FROM facture_client
                    WHERE statut IN ('VALIDE', 'PAYE')
                    AND date_facture BETWEEN :debut AND :fin";
            $stmt = $db->prepare($sql);
            $stmt->execute(['debut' => $dateDebut, 'fin' => $dateFin]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur KPI CA: ' . $e->getMessage());
        }
    }

    /**
     * CA par mois (pour graphiques)
     */
    public function getCAParMois(int $annee): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT 
                        MONTH(date_facture) AS mois,
                        COALESCE(SUM(montant_ttc), 0) AS ca_ttc,
                        COUNT(*) AS nb_factures
                    FROM facture_client
                    WHERE statut IN ('VALIDE', 'PAYE')
                    AND YEAR(date_facture) = :annee
                    GROUP BY MONTH(date_facture)
                    ORDER BY mois";
            $stmt = $db->prepare($sql);
            $stmt->execute(['annee' => $annee]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur KPI CA par mois: ' . $e->getMessage());
        }
    }

    /**
     * KPI 3: TAUX DE REMISE MOYENNE
     * -----------------------------
     * Objectif métier: Contrôler la politique commerciale de remises
     * Formule: AVG(remise_pourcent) des lignes de commande
     * Tables: ligne_commande_client, commande_client
     */
    public function getTauxRemiseMoyen(string $dateDebut = null, string $dateFin = null): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT 
                        COALESCE(AVG(lcc.remise_pourcent), 0) AS remise_moyenne,
                        COALESCE(MAX(lcc.remise_pourcent), 0) AS remise_max,
                        COALESCE(MIN(lcc.remise_pourcent), 0) AS remise_min,
                        COUNT(CASE WHEN lcc.remise_pourcent > 10 THEN 1 END) AS nb_remises_elevees,
                        COUNT(*) AS total_lignes
                    FROM ligne_commande_client lcc
                    JOIN commande_client cc ON lcc.id_commande_client = cc.id_commande_client
                    WHERE cc.statut IN ('VALIDE', 'CLOTURE')";
            
            $params = [];
            if ($dateDebut && $dateFin) {
                $sql .= " AND cc.commande_date BETWEEN :debut AND :fin";
                $params['debut'] = $dateDebut;
                $params['fin'] = $dateFin;
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur KPI remises: ' . $e->getMessage());
        }
    }

    /**
     * KPI 4: BACKLOG COMMANDES NON LIVRÉES
     * ------------------------------------
     * Objectif métier: Mesurer le carnet de commandes à livrer
     * Formule: Commandes VALIDÉES sans livraison complète
     * Tables: commande_client, livraison_client, ligne_commande_client, ligne_livraison_client
     */
    public function getBacklogCommandes(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT 
                        cc.*,
                        c.nom AS client_nom,
                        COALESCE(SUM(lcc.quantite), 0) AS qte_commandee,
                        COALESCE(SUM(delivered.qte_livree), 0) AS qte_livree,
                        COALESCE(SUM(lcc.quantite), 0) - COALESCE(SUM(delivered.qte_livree), 0) AS qte_restante
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
                    HAVING qte_restante > 0
                    ORDER BY cc.commande_date ASC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur KPI backlog: ' . $e->getMessage());
        }
    }

    /**
     * Résumé du backlog
     */
    public function getBacklogResume(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT 
                        COUNT(DISTINCT cc.id_commande_client) AS nb_commandes,
                        COALESCE(SUM(cc.montant_ttc), 0) AS valeur_totale
                    FROM commande_client cc
                    WHERE cc.statut = 'VALIDE'";
            $stmt = $db->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * KPI 5: FACTURES NON PAYÉES (CRÉANCES CLIENTS)
     * ---------------------------------------------
     * Objectif métier: Suivre les créances et le recouvrement
     * Formule: Factures VALIDÉES avec reste_à_payer > 0
     * Tables: facture_client, encaissement_client
     */
    public function getFacturesImpayees(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT 
                        fc.*,
                        c.nom AS client_nom,
                        c.telephone AS client_telephone,
                        fc.montant_ttc - COALESCE(encaisse.total_encaisse, 0) AS reste_a_payer,
                        DATEDIFF(CURDATE(), fc.date_facture) AS anciennete_jours
                    FROM facture_client fc
                    LEFT JOIN client c ON fc.client_id = c.id_client
                    LEFT JOIN (
                        SELECT facture_client_id, SUM(montant) AS total_encaisse
                        FROM encaissement_client
                        GROUP BY facture_client_id
                    ) encaisse ON fc.id_facture_client = encaisse.facture_client_id
                    WHERE fc.statut = 'VALIDE'
                    HAVING reste_a_payer > 0
                    ORDER BY anciennete_jours DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur KPI factures impayées: ' . $e->getMessage());
        }
    }

    /**
     * Résumé des créances par tranche d'ancienneté
     */
    public function getCreancesParAnciennete(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT 
                        CASE 
                            WHEN DATEDIFF(CURDATE(), fc.date_facture) <= 30 THEN '0-30 jours'
                            WHEN DATEDIFF(CURDATE(), fc.date_facture) <= 60 THEN '31-60 jours'
                            WHEN DATEDIFF(CURDATE(), fc.date_facture) <= 90 THEN '61-90 jours'
                            ELSE 'Plus de 90 jours'
                        END AS tranche,
                        COUNT(*) AS nb_factures,
                        COALESCE(SUM(fc.montant_ttc - COALESCE(encaisse.total_encaisse, 0)), 0) AS montant_total
                    FROM facture_client fc
                    LEFT JOIN (
                        SELECT facture_client_id, SUM(montant) AS total_encaisse
                        FROM encaissement_client
                        GROUP BY facture_client_id
                    ) encaisse ON fc.id_facture_client = encaisse.facture_client_id
                    WHERE fc.statut = 'VALIDE'
                    AND fc.montant_ttc - COALESCE(encaisse.total_encaisse, 0) > 0
                    GROUP BY tranche
                    ORDER BY 
                        CASE tranche
                            WHEN '0-30 jours' THEN 1
                            WHEN '31-60 jours' THEN 2
                            WHEN '61-90 jours' THEN 3
                            ELSE 4
                        END";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * KPI 6: TOP CLIENTS
     * ------------------
     * Objectif métier: Identifier les meilleurs clients
     */
    public function getTopClients(int $limit = 10, string $dateDebut = null, string $dateFin = null): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT 
                        c.id_client,
                        c.nom AS client_nom,
                        COUNT(DISTINCT fc.id_facture_client) AS nb_factures,
                        COALESCE(SUM(fc.montant_ttc), 0) AS ca_total
                    FROM client c
                    LEFT JOIN facture_client fc ON c.id_client = fc.client_id
                    WHERE fc.statut IN ('VALIDE', 'PAYE')";
            
            $params = [];
            if ($dateDebut && $dateFin) {
                $sql .= " AND fc.date_facture BETWEEN :debut AND :fin";
                $params['debut'] = $dateDebut;
                $params['fin'] = $dateFin;
            }
            
            $sql .= " GROUP BY c.id_client
                      ORDER BY ca_total DESC
                      LIMIT " . (int)$limit;
            
            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur KPI top clients: ' . $e->getMessage());
        }
    }

    /**
     * KPI 7: TOP ARTICLES VENDUS
     * --------------------------
     * Objectif métier: Identifier les produits les plus vendus
     */
    public function getTopArticles(int $limit = 10, string $dateDebut = null, string $dateFin = null): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT 
                        a.id_article,
                        a.code AS article_code,
                        a.designation,
                        COALESCE(SUM(llc.quantite_livree), 0) AS qte_vendue,
                        COALESCE(SUM(llc.quantite_livree * lcc.prix_unitaire), 0) AS ca_article
                    FROM article a
                    LEFT JOIN ligne_commande_client lcc ON a.id_article = lcc.id_article
                    LEFT JOIN ligne_livraison_client llc ON lcc.id_ligne_commande_client = llc.id_ligne_commande_client
                    LEFT JOIN livraison_client lc ON llc.id_livraison_client = lc.id_livraison_client
                    WHERE lc.statut = 'LIVREE'";
            
            $params = [];
            if ($dateDebut && $dateFin) {
                $sql .= " AND lc.livraison_date BETWEEN :debut AND :fin";
                $params['debut'] = $dateDebut;
                $params['fin'] = $dateFin;
            }
            
            $sql .= " GROUP BY a.id_article
                      HAVING qte_vendue > 0
                      ORDER BY qte_vendue DESC
                      LIMIT " . (int)$limit;
            
            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur KPI top articles: ' . $e->getMessage());
        }
    }

    /**
     * TABLEAU DE BORD GLOBAL
     * ----------------------
     * Agrège tous les KPI pour le dashboard
     */
    public function getDashboardData(): array
    {
        $annee = date('Y');
        $debutMois = date('Y-m-01');
        $finMois = date('Y-m-t');
        $debutAnnee = date('Y-01-01');
        $finAnnee = date('Y-12-31');

        return [
            'ca_mois' => $this->getChiffreAffaires($debutMois, $finMois),
            'ca_annee' => $this->getChiffreAffaires($debutAnnee, $finAnnee),
            'ca_par_mois' => $this->getCAParMois($annee),
            'backlog' => $this->getBacklogResume(),
            'commandes_retard' => count($this->getCommandesEnRetard()),
            'creances' => $this->getCreancesParAnciennete(),
            'total_creances' => array_sum(array_column($this->getCreancesParAnciennete(), 'montant_total')),
            'remises' => $this->getTauxRemiseMoyen(),
            'top_clients' => $this->getTopClients(5),
            'top_articles' => $this->getTopArticles(5)
        ];
    }
}
