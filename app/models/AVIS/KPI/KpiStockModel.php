<?php
namespace app\models\AVIS\KPI;

use Flight;
use PDO;

/**
 * Modèle KPI Stock - Fonctions pour le tableau de bord stock/magasin
 */
class KpiStockModel
{
    /**
     * Récupère toutes les données KPI pour le dashboard stock
     */
    public static function getAllKpis(): array
    {
        return [
            'precision' => self::getStockPrecision(),
            'obsolescence' => self::getObsolescenceKpi(),
            'productivite' => self::getProductivityKpi(),
            'dockToStock' => self::getDockToStockKpi(),
            'valeurStock' => self::getTotalStockValue(),
            'referencesActives' => self::getActiveReferencesCount(),
            'rotationStock' => self::getStockRotation(),
            'tauxService' => self::getServiceRate(),
            'lotsRisque' => self::getLotsAtRisk(),
            'ecartsStock' => self::getStockVariances(),
            'charts' => self::getChartsData()
        ];
    }

    /**
     * KPI 1: Taux de précision stock (théorique vs physique)
     * Basé sur les campagnes d'inventaire validées
     */
    public static function getStockPrecision(): array
    {
        try {
            $db = Flight::db();
            
            // Dernière campagne d'inventaire clôturée
            $sql = "SELECT 
                        SUM(ic.quantite_theorique) as total_theorique,
                        SUM(ic.quantite_comptee) as total_comptee,
                        SUM(ABS(ic.ecart)) as total_ecart,
                        COUNT(*) as nb_lignes
                    FROM inventaire_comptage ic
                    INNER JOIN inventaire_campagne camp ON camp.id_inventaire_campagne = ic.id_inventaire_campagne
                    WHERE camp.statut = 'CLOTURE'
                    AND ic.date_comptage >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $theorique = (float)($data['total_theorique'] ?? 0);
            $comptee = (float)($data['total_comptee'] ?? 0);
            $taux = $theorique > 0 ? round(($comptee / $theorique) * 100, 1) : 100;
            
            // Comparaison mois précédent
            $sqlPrev = "SELECT 
                            SUM(ic.quantite_theorique) as total_theorique,
                            SUM(ic.quantite_comptee) as total_comptee
                        FROM inventaire_comptage ic
                        INNER JOIN inventaire_campagne camp ON camp.id_inventaire_campagne = ic.id_inventaire_campagne
                        WHERE camp.statut = 'CLOTURE'
                        AND ic.date_comptage >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
                        AND ic.date_comptage < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            $stmtPrev = $db->prepare($sqlPrev);
            $stmtPrev->execute();
            $dataPrev = $stmtPrev->fetch(PDO::FETCH_ASSOC);
            
            $theoriquePrev = (float)($dataPrev['total_theorique'] ?? 0);
            $compteePrev = (float)($dataPrev['total_comptee'] ?? 0);
            $tauxPrev = $theoriquePrev > 0 ? round(($compteePrev / $theoriquePrev) * 100, 1) : 100;
            
            $variation = round($taux - $tauxPrev, 1);
            
            return [
                'taux' => min($taux, 100),
                'ecart_moyen' => round(100 - $taux, 1),
                'variation' => $variation,
                'objectif' => 98,
                'nb_lignes' => (int)($data['nb_lignes'] ?? 0)
            ];
        } catch (\PDOException $e) {
            return [
                'taux' => 0,
                'ecart_moyen' => 0,
                'variation' => 0,
                'objectif' => 98,
                'nb_lignes' => 0
            ];
        }
    }

    /**
     * KPI 2: Obsolescence et Péremption
     * Lots proches de la date d'expiration
     */
    public static function getObsolescenceKpi(): array
    {
        try {
            $db = Flight::db();
            
            // Lots à risque (expiration dans les 90 jours)
            $sql = "SELECT 
                        COUNT(*) as nb_lots,
                        SUM(
                            (l.quantite_initiale - COALESCE(
                                (SELECT SUM(d.quantite) FROM mouvement_stock_lot_detail d WHERE d.id_lot = l.id_lot), 0
                            )) * l.cout_unitaire
                        ) as valeur_risque
                    FROM lot l
                    WHERE (l.date_limite_consommation IS NOT NULL AND l.date_limite_consommation <= DATE_ADD(CURDATE(), INTERVAL 90 DAY))
                       OR (l.date_limite_utilisation_optimale IS NOT NULL AND l.date_limite_utilisation_optimale <= DATE_ADD(CURDATE(), INTERVAL 90 DAY))";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Lots critiques (< 30 jours)
            $sqlCritique = "SELECT COUNT(*) as nb_critique
                           FROM lot l
                           WHERE (l.date_limite_consommation IS NOT NULL AND l.date_limite_consommation <= DATE_ADD(CURDATE(), INTERVAL 30 DAY))
                              OR (l.date_limite_utilisation_optimale IS NOT NULL AND l.date_limite_utilisation_optimale <= DATE_ADD(CURDATE(), INTERVAL 30 DAY))";
            $stmtCrit = $db->prepare($sqlCritique);
            $stmtCrit->execute();
            $dataCrit = $stmtCrit->fetch(PDO::FETCH_ASSOC);
            
            // Mois précédent
            $sqlPrev = "SELECT COUNT(*) as nb_lots
                       FROM lot l
                       WHERE (l.date_limite_consommation IS NOT NULL 
                              AND l.date_limite_consommation <= DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), INTERVAL 90 DAY)
                              AND l.date_limite_consommation > DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
            $stmtPrev = $db->prepare($sqlPrev);
            $stmtPrev->execute();
            $dataPrev = $stmtPrev->fetch(PDO::FETCH_ASSOC);
            
            $nbLots = (int)($data['nb_lots'] ?? 0);
            $nbPrev = (int)($dataPrev['nb_lots'] ?? 0);
            
            return [
                'valeur_risque' => (float)($data['valeur_risque'] ?? 0),
                'nb_lots' => $nbLots,
                'nb_critiques' => (int)($dataCrit['nb_critique'] ?? 0),
                'variation' => $nbLots - $nbPrev,
                'objectif' => 5
            ];
        } catch (\PDOException $e) {
            return [
                'valeur_risque' => 0,
                'nb_lots' => 0,
                'nb_critiques' => 0,
                'variation' => 0,
                'objectif' => 5
            ];
        }
    }

    /**
     * KPI 3: Productivité Préparation (Picking)
     * Nombre de lignes traitées par heure
     */
    public static function getProductivityKpi(): array
    {
        try {
            $db = Flight::db();
            
            // Mouvements de sortie (livraisons) du mois en cours
            $sql = "SELECT 
                        COUNT(*) as nb_lignes,
                        MIN(date_mouvement) as debut,
                        MAX(date_mouvement) as fin
                    FROM mouvement_stock ms
                    INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                    WHERE mst.code = 'VENTE_LIVRAISON'
                    AND ms.date_mouvement >= DATE_FORMAT(CURDATE(), '%Y-%m-01')";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $nbLignes = (int)($data['nb_lignes'] ?? 0);
            
            // Estimation heures travaillées (8h/jour, 22 jours/mois approximativement)
            $joursOuvres = self::getWorkingDays(date('Y-m-01'), date('Y-m-d'));
            $heuresTravail = max($joursOuvres * 8, 1);
            
            $lignesParHeure = round($nbLignes / $heuresTravail, 0);
            
            // Taux d'erreurs (mouvements annulés)
            $sqlErreurs = "SELECT COUNT(*) as nb_erreurs
                          FROM mouvement_stock_status mss
                          INNER JOIN mouvement_stock ms ON ms.id_mouvement_stock = mss.id_mouvement_stock
                          INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                          WHERE mss.libelle = 'annulé'
                          AND mst.code = 'VENTE_LIVRAISON'
                          AND mss.created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')";
            $stmtErr = $db->prepare($sqlErreurs);
            $stmtErr->execute();
            $dataErr = $stmtErr->fetch(PDO::FETCH_ASSOC);
            
            $nbErreurs = (int)($dataErr['nb_erreurs'] ?? 0);
            $tauxErreur = $nbLignes > 0 ? round(($nbErreurs / $nbLignes) * 100, 1) : 0;
            
            // Mois précédent
            $sqlPrev = "SELECT COUNT(*) as nb_lignes
                       FROM mouvement_stock ms
                       INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                       WHERE mst.code = 'VENTE_LIVRAISON'
                       AND ms.date_mouvement >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
                       AND ms.date_mouvement < DATE_FORMAT(CURDATE(), '%Y-%m-01')";
            $stmtPrev = $db->prepare($sqlPrev);
            $stmtPrev->execute();
            $dataPrev = $stmtPrev->fetch(PDO::FETCH_ASSOC);
            
            $joursPrev = self::getWorkingDays(
                date('Y-m-01', strtotime('-1 month')),
                date('Y-m-t', strtotime('-1 month'))
            );
            $heuresPrev = max($joursPrev * 8, 1);
            $lignesPrevHeure = round((int)($dataPrev['nb_lignes'] ?? 0) / $heuresPrev, 0);
            
            return [
                'lignes_par_heure' => $lignesParHeure,
                'taux_erreur' => $tauxErreur,
                'variation' => $lignesParHeure - $lignesPrevHeure,
                'objectif' => 50
            ];
        } catch (\PDOException $e) {
            return [
                'lignes_par_heure' => 0,
                'taux_erreur' => 0,
                'variation' => 0,
                'objectif' => 50
            ];
        }
    }

    /**
     * KPI 4: Temps de traitement réception (Dock-to-Stock)
     * Délai entre réception et mise en stock
     */
    public static function getDockToStockKpi(): array
    {
        try {
            $db = Flight::db();
            
            // Temps moyen entre réception et mouvement de stock
            $sql = "SELECT 
                        AVG(TIMESTAMPDIFF(HOUR, rf.reception_date, ms.date_mouvement)) as temps_moyen
                    FROM reception_fournisseur rf
                    INNER JOIN mouvement_stock ms ON ms.id_reference = rf.id_reception_fournisseur 
                        AND ms.table_reference = 'reception_fournisseur'
                    WHERE rf.reception_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $tempsMoyen = round((float)($data['temps_moyen'] ?? 0), 1);
            
            // Mois précédent
            $sqlPrev = "SELECT 
                            AVG(TIMESTAMPDIFF(HOUR, rf.reception_date, ms.date_mouvement)) as temps_moyen
                        FROM reception_fournisseur rf
                        INNER JOIN mouvement_stock ms ON ms.id_reference = rf.id_reception_fournisseur 
                            AND ms.table_reference = 'reception_fournisseur'
                        WHERE rf.reception_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
                        AND rf.reception_date < DATE_FORMAT(CURDATE(), '%Y-%m-01')";
            $stmtPrev = $db->prepare($sqlPrev);
            $stmtPrev->execute();
            $dataPrev = $stmtPrev->fetch(PDO::FETCH_ASSOC);
            
            $tempsPrev = round((float)($dataPrev['temps_moyen'] ?? 0), 1);
            
            return [
                'temps_moyen' => $tempsMoyen > 0 ? $tempsMoyen : 0,
                'variation' => round($tempsMoyen - $tempsPrev, 1),
                'objectif' => 6,
                'objectif_cible' => 5
            ];
        } catch (\PDOException $e) {
            return [
                'temps_moyen' => 0,
                'variation' => 0,
                'objectif' => 6,
                'objectif_cible' => 5
            ];
        }
    }

    /**
     * KPI Secondaire 1: Valeur totale du stock
     */
    public static function getTotalStockValue(): array
    {
        try {
            $db = Flight::db();
            
            $sql = "SELECT SUM(valeur_stock) as valeur_totale FROM stock_courant";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Valeur du mois précédent (depuis clôture si disponible)
            $sqlPrev = "SELECT SUM(valeur_cloture) as valeur_prev
                       FROM stock_cloture_detail scd
                       INNER JOIN stock_cloture_periode scp ON scp.id_stock_cloture_periode = scd.id_stock_cloture_periode
                       WHERE scp.annee = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                       AND scp.mois = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
            $stmtPrev = $db->prepare($sqlPrev);
            $stmtPrev->execute();
            $dataPrev = $stmtPrev->fetch(PDO::FETCH_ASSOC);
            
            $valeurActuelle = (float)($data['valeur_totale'] ?? 0);
            $valeurPrev = (float)($dataPrev['valeur_prev'] ?? 0);
            
            $variation = $valeurPrev > 0 ? round((($valeurActuelle - $valeurPrev) / $valeurPrev) * 100, 1) : 0;
            
            return [
                'valeur' => $valeurActuelle,
                'variation' => $variation
            ];
        } catch (\PDOException $e) {
            return ['valeur' => 0, 'variation' => 0];
        }
    }

    /**
     * KPI Secondaire 2: Nombre de références actives
     */
    public static function getActiveReferencesCount(): array
    {
        try {
            $db = Flight::db();
            
            $sql = "SELECT COUNT(*) as nb_refs FROM article WHERE actif = 1";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Variation estimée (nouveaux articles ce mois)
            $sqlNew = "SELECT COUNT(*) as nb_new 
                      FROM article 
                      WHERE actif = 1";
                      // Note: Si tu avais une colonne created_at, tu pourrais filtrer par mois
            
            return [
                'count' => (int)($data['nb_refs'] ?? 0),
                'variation' => 0 // À améliorer si tu ajoutes une date de création
            ];
        } catch (\PDOException $e) {
            return ['count' => 0, 'variation' => 0];
        }
    }

    /**
     * KPI Secondaire 3: Rotation des stocks
     * Coût des ventes / Stock moyen
     */
    public static function getStockRotation(): array
    {
        try {
            $db = Flight::db();
            
            // Coût des sorties (ventes) sur 12 mois
            $sql = "SELECT SUM(ms.quantite * ms.cout_unitaire) as cout_ventes
                   FROM mouvement_stock ms
                   INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                   WHERE mst.code = 'VENTE_LIVRAISON'
                   AND ms.date_mouvement >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Stock moyen
            $sqlStock = "SELECT SUM(valeur_stock) as stock_moyen FROM stock_courant";
            $stmtStock = $db->prepare($sqlStock);
            $stmtStock->execute();
            $dataStock = $stmtStock->fetch(PDO::FETCH_ASSOC);
            
            $coutVentes = (float)($data['cout_ventes'] ?? 0);
            $stockMoyen = (float)($dataStock['stock_moyen'] ?? 0);
            
            $rotation = $stockMoyen > 0 ? round($coutVentes / $stockMoyen, 1) : 0;
            
            return [
                'rotation' => $rotation,
                'variation' => 0 // À calculer si historique disponible
            ];
        } catch (\PDOException $e) {
            return ['rotation' => 0, 'variation' => 0];
        }
    }

    /**
     * KPI Secondaire 4: Taux de service
     * Commandes livrées complètes / Total commandes
     */
    public static function getServiceRate(): array
    {
        try {
            $db = Flight::db();
            
            // Commandes livrées ce mois
            $sql = "SELECT 
                        COUNT(DISTINCT cc.id_commande_client) as total_commandes,
                        COUNT(DISTINCT lc.id_livraison_client) as commandes_livrees
                    FROM commande_client cc
                    LEFT JOIN livraison_client lc ON lc.id_commande_client = cc.id_commande_client 
                        AND lc.statut = 'LIVREE'
                    WHERE cc.commande_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $total = (int)($data['total_commandes'] ?? 0);
            $livrees = (int)($data['commandes_livrees'] ?? 0);
            
            $taux = $total > 0 ? round(($livrees / $total) * 100, 1) : 100;
            
            return [
                'taux' => $taux,
                'variation' => 0,
                'objectif' => 98
            ];
        } catch (\PDOException $e) {
            return ['taux' => 100, 'variation' => 0, 'objectif' => 98];
        }
    }

    /**
     * Liste des lots à risque (obsolescence/péremption)
     */
    public static function getLotsAtRisk(int $limit = 10): array
    {
        try {
            $db = Flight::db();
            
            $sql = "SELECT 
                        l.id_lot,
                        l.lot_numero,
                        a.code as article_code,
                        a.designation as article_designation,
                        (l.quantite_initiale - COALESCE(
                            (SELECT SUM(d.quantite) FROM mouvement_stock_lot_detail d WHERE d.id_lot = l.id_lot), 0
                        )) as quantite_restante,
                        l.cout_unitaire,
                        (l.quantite_initiale - COALESCE(
                            (SELECT SUM(d.quantite) FROM mouvement_stock_lot_detail d WHERE d.id_lot = l.id_lot), 0
                        )) * l.cout_unitaire as valeur_totale,
                        COALESCE(l.date_limite_consommation, l.date_limite_utilisation_optimale) as date_expiration,
                        DATEDIFF(COALESCE(l.date_limite_consommation, l.date_limite_utilisation_optimale), CURDATE()) as jours_restants,
                        d.nom as depot_nom
                    FROM lot l
                    INNER JOIN article a ON a.id_article = l.id_article
                    INNER JOIN depot d ON d.id_depot = l.id_depot
                    WHERE (l.date_limite_consommation IS NOT NULL OR l.date_limite_utilisation_optimale IS NOT NULL)
                    AND COALESCE(l.date_limite_consommation, l.date_limite_utilisation_optimale) <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
                    AND (l.quantite_initiale - COALESCE(
                        (SELECT SUM(d2.quantite) FROM mouvement_stock_lot_detail d2 WHERE d2.id_lot = l.id_lot), 0
                    )) > 0
                    ORDER BY jours_restants ASC
                    LIMIT " . (int)$limit;
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $lots = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Ajouter le niveau de risque
            foreach ($lots as &$lot) {
                $jours = (int)$lot['jours_restants'];
                if ($jours <= 15) {
                    $lot['risque'] = 'critique';
                    $lot['risque_label'] = 'Critique';
                } elseif ($jours <= 30) {
                    $lot['risque'] = 'eleve';
                    $lot['risque_label'] = 'Élevé';
                } elseif ($jours <= 60) {
                    $lot['risque'] = 'moyen';
                    $lot['risque_label'] = 'Moyen';
                } else {
                    $lot['risque'] = 'faible';
                    $lot['risque_label'] = 'Faible';
                }
            }
            
            return $lots;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Écarts de stock (inventaire théorique vs physique)
     */
    public static function getStockVariances(float $seuilPourcent = 2.0, int $limit = 20): array
    {
        try {
            $db = Flight::db();
            
            $sql = "SELECT 
                        a.code as article_code,
                        a.designation as article_designation,
                        ic.quantite_theorique,
                        ic.quantite_comptee,
                        ic.ecart,
                        CASE WHEN ic.quantite_theorique > 0 
                            THEN ROUND((ic.ecart / ic.quantite_theorique) * 100, 1)
                            ELSE 0 
                        END as pourcent_ecart,
                        ic.ecart * COALESCE(a.prix_achat, 0) as valeur_ecart,
                        DATE(ic.date_comptage) as date_audit,
                        camp.code as campagne_code,
                        camp.libelle as campagne_libelle,
                        d.nom as depot_nom
                    FROM inventaire_comptage ic
                    INNER JOIN inventaire_campagne camp ON camp.id_inventaire_campagne = ic.id_inventaire_campagne
                    INNER JOIN article a ON a.id_article = ic.id_article
                    INNER JOIN depot d ON d.id_depot = ic.id_depot
                    WHERE camp.statut = 'CLOTURE'
                    AND ABS(ic.ecart) > 0
                    AND ic.quantite_theorique > 0
                    HAVING ABS(pourcent_ecart) >= " . (float)$seuilPourcent . "
                    ORDER BY ABS(pourcent_ecart) DESC
                    LIMIT " . (int)$limit;
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $ecarts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Ajouter priorité
            foreach ($ecarts as &$ecart) {
                $pct = abs((float)$ecart['pourcent_ecart']);
                if ($pct >= 10) {
                    $ecart['priorite'] = 'critique';
                    $ecart['priorite_label'] = 'Critique';
                } elseif ($pct >= 5) {
                    $ecart['priorite'] = 'moyen';
                    $ecart['priorite_label'] = 'Moyen';
                } else {
                    $ecart['priorite'] = 'faible';
                    $ecart['priorite_label'] = 'Faible';
                }
            }
            
            return $ecarts;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Données pour les graphiques (12 derniers mois)
     */
    public static function getChartsData(): array
    {
        return [
            'precision' => self::getMonthlyPrecisionData(),
            'obsolescence' => self::getMonthlyObsolescenceData(),
            'productivite' => self::getMonthlyProductivityData(),
            'dockToStock' => self::getMonthlyDockToStockData(),
            'pickingErrors' => self::getMonthlyPickingErrorsData(),
            'serviceRate' => self::getMonthlyServiceRateData()
        ];
    }

    /**
     * Données mensuelles de précision stock (12 mois)
     */
    private static function getMonthlyPrecisionData(): array
    {
        try {
            $db = Flight::db();
            $results = [];
            $labels = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m-01', strtotime("-$i months"));
                $endDate = date('Y-m-t', strtotime("-$i months"));
                $labels[] = self::getMonthLabel($date);
                
                $sql = "SELECT 
                            SUM(ic.quantite_theorique) as total_theo,
                            SUM(ic.quantite_comptee) as total_compt
                        FROM inventaire_comptage ic
                        INNER JOIN inventaire_campagne camp ON camp.id_inventaire_campagne = ic.id_inventaire_campagne
                        WHERE camp.statut = 'CLOTURE'
                        AND ic.date_comptage >= :start AND ic.date_comptage <= :end";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([':start' => $date, ':end' => $endDate]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $theo = (float)($data['total_theo'] ?? 0);
                $compt = (float)($data['total_compt'] ?? 0);
                $results[] = $theo > 0 ? round(($compt / $theo) * 100, 1) : 100;
            }
            
            return [
                'labels' => $labels,
                'data' => $results,
                'objectif' => array_fill(0, 12, 98)
            ];
        } catch (\PDOException $e) {
            return ['labels' => [], 'data' => [], 'objectif' => []];
        }
    }

    /**
     * Données mensuelles d'obsolescence (12 mois)
     */
    private static function getMonthlyObsolescenceData(): array
    {
        try {
            $db = Flight::db();
            $results = [];
            $labels = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m-01', strtotime("-$i months"));
                $labels[] = self::getMonthLabel($date);
                
                // Valeur des lots expirés durant ce mois
                $sql = "SELECT SUM(l.quantite_initiale * l.cout_unitaire) as valeur
                       FROM lot l
                       WHERE (l.date_limite_consommation IS NOT NULL 
                              AND YEAR(l.date_limite_consommation) = YEAR(:date1)
                              AND MONTH(l.date_limite_consommation) = MONTH(:date2))
                          OR (l.date_limite_utilisation_optimale IS NOT NULL 
                              AND YEAR(l.date_limite_utilisation_optimale) = YEAR(:date3)
                              AND MONTH(l.date_limite_utilisation_optimale) = MONTH(:date4))";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([':date1' => $date, ':date2' => $date, ':date3' => $date, ':date4' => $date]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $results[] = (float)($data['valeur'] ?? 0);
            }
            
            return [
                'labels' => $labels,
                'data' => $results
            ];
        } catch (\PDOException $e) {
            return ['labels' => [], 'data' => []];
        }
    }

    /**
     * Données mensuelles de productivité (12 mois)
     */
    private static function getMonthlyProductivityData(): array
    {
        try {
            $db = Flight::db();
            $results = [];
            $labels = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m-01', strtotime("-$i months"));
                $endDate = date('Y-m-t', strtotime("-$i months"));
                $labels[] = self::getMonthLabel($date);
                
                $sql = "SELECT COUNT(*) as nb_lignes
                       FROM mouvement_stock ms
                       INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                       WHERE mst.code = 'VENTE_LIVRAISON'
                       AND ms.date_mouvement >= :start AND ms.date_mouvement <= :end";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([':start' => $date, ':end' => $endDate]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $nbLignes = (int)($data['nb_lignes'] ?? 0);
                $joursOuvres = self::getWorkingDays($date, $endDate);
                $heures = max($joursOuvres * 8, 1);
                
                $results[] = round($nbLignes / $heures, 0);
            }
            
            return [
                'labels' => $labels,
                'data' => $results,
                'objectif' => array_fill(0, 12, 50)
            ];
        } catch (\PDOException $e) {
            return ['labels' => [], 'data' => [], 'objectif' => []];
        }
    }

    /**
     * Données mensuelles Dock-to-Stock (12 mois)
     */
    private static function getMonthlyDockToStockData(): array
    {
        try {
            $db = Flight::db();
            $results = [];
            $labels = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m-01', strtotime("-$i months"));
                $endDate = date('Y-m-t', strtotime("-$i months"));
                $labels[] = self::getMonthLabel($date);
                
                $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, rf.reception_date, ms.date_mouvement)) as temps_moyen
                       FROM reception_fournisseur rf
                       INNER JOIN mouvement_stock ms ON ms.id_reference = rf.id_reception_fournisseur 
                           AND ms.table_reference = 'reception_fournisseur'
                       WHERE rf.reception_date >= :start AND rf.reception_date <= :end";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([':start' => $date, ':end' => $endDate]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $results[] = round((float)($data['temps_moyen'] ?? 0), 1);
            }
            
            return [
                'labels' => $labels,
                'data' => $results,
                'objectif' => array_fill(0, 12, 6)
            ];
        } catch (\PDOException $e) {
            return ['labels' => [], 'data' => [], 'objectif' => []];
        }
    }

    /**
     * Données mensuelles taux d'erreurs picking (12 mois)
     */
    private static function getMonthlyPickingErrorsData(): array
    {
        try {
            $db = Flight::db();
            $results = [];
            $labels = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m-01', strtotime("-$i months"));
                $endDate = date('Y-m-t', strtotime("-$i months"));
                $labels[] = self::getMonthLabel($date);
                
                // Total lignes
                $sqlTotal = "SELECT COUNT(*) as nb FROM mouvement_stock ms
                            INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                            WHERE mst.code = 'VENTE_LIVRAISON'
                            AND ms.date_mouvement >= :start AND ms.date_mouvement <= :end";
                $stmt = $db->prepare($sqlTotal);
                $stmt->execute([':start' => $date, ':end' => $endDate]);
                $total = (int)($stmt->fetch(PDO::FETCH_ASSOC)['nb'] ?? 0);
                
                // Erreurs (annulées)
                $sqlErr = "SELECT COUNT(*) as nb FROM mouvement_stock_status mss
                          INNER JOIN mouvement_stock ms ON ms.id_mouvement_stock = mss.id_mouvement_stock
                          INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                          WHERE mss.libelle = 'annulé'
                          AND mst.code = 'VENTE_LIVRAISON'
                          AND mss.created_at >= :start AND mss.created_at <= :end";
                $stmtErr = $db->prepare($sqlErr);
                $stmtErr->execute([':start' => $date, ':end' => $endDate]);
                $erreurs = (int)($stmtErr->fetch(PDO::FETCH_ASSOC)['nb'] ?? 0);
                
                $results[] = $total > 0 ? round(($erreurs / $total) * 100, 1) : 0;
            }
            
            return [
                'labels' => $labels,
                'data' => $results,
                'objectif' => array_fill(0, 12, 1.5)
            ];
        } catch (\PDOException $e) {
            return ['labels' => [], 'data' => [], 'objectif' => []];
        }
    }

    /**
     * Données mensuelles taux de service (12 mois)
     */
    private static function getMonthlyServiceRateData(): array
    {
        try {
            $db = Flight::db();
            $results = [];
            $labels = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = date('Y-m-01', strtotime("-$i months"));
                $endDate = date('Y-m-t', strtotime("-$i months"));
                $labels[] = self::getMonthLabel($date);
                
                $sql = "SELECT 
                            COUNT(DISTINCT cc.id_commande_client) as total_cmd,
                            COUNT(DISTINCT lc.id_livraison_client) as cmd_livrees
                        FROM commande_client cc
                        LEFT JOIN livraison_client lc ON lc.id_commande_client = cc.id_commande_client 
                            AND lc.statut = 'LIVREE'
                        WHERE cc.commande_date >= :start AND cc.commande_date <= :end";
                
                $stmt = $db->prepare($sql);
                $stmt->execute([':start' => $date, ':end' => $endDate]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $total = (int)($data['total_cmd'] ?? 0);
                $livrees = (int)($data['cmd_livrees'] ?? 0);
                
                $results[] = $total > 0 ? round(($livrees / $total) * 100, 1) : 100;
            }
            
            return [
                'labels' => $labels,
                'data' => $results,
                'objectif' => array_fill(0, 12, 98)
            ];
        } catch (\PDOException $e) {
            return ['labels' => [], 'data' => [], 'objectif' => []];
        }
    }

    /**
     * Calcule le nombre de jours ouvrés entre deux dates
     */
    private static function getWorkingDays(string $startDate, string $endDate): int
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $count = 0;
        
        while ($start <= $end) {
            $dayOfWeek = $start->format('N');
            if ($dayOfWeek < 6) { // 1-5 = Lundi-Vendredi
                $count++;
            }
            $start->modify('+1 day');
        }
        
        return max($count, 1);
    }

    /**
     * Retourne le label du mois en français
     */
    private static function getMonthLabel(string $date): string
    {
        $mois = [
            1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Août',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
        ];
        $month = (int)date('n', strtotime($date));
        return $mois[$month] ?? '';
    }

    /**
     * Statistiques globales pour résumé
     */
    public static function getGlobalStats(): array
    {
        try {
            $db = Flight::db();
            
            // Nombre de dépôts
            $stmtDepot = $db->query("SELECT COUNT(*) as nb FROM depot");
            $nbDepots = (int)$stmtDepot->fetch(PDO::FETCH_ASSOC)['nb'];
            
            // Nombre de lots actifs
            $stmtLots = $db->query("SELECT COUNT(*) as nb FROM lot");
            $nbLots = (int)$stmtLots->fetch(PDO::FETCH_ASSOC)['nb'];
            
            // Nombre de mouvements ce mois
            $stmtMouvs = $db->query("SELECT COUNT(*) as nb FROM mouvement_stock 
                                    WHERE date_mouvement >= DATE_FORMAT(CURDATE(), '%Y-%m-01')");
            $nbMouvements = (int)$stmtMouvs->fetch(PDO::FETCH_ASSOC)['nb'];
            
            return [
                'nb_depots' => $nbDepots,
                'nb_lots' => $nbLots,
                'nb_mouvements_mois' => $nbMouvements
            ];
        } catch (\PDOException $e) {
            return [
                'nb_depots' => 0,
                'nb_lots' => 0,
                'nb_mouvements_mois' => 0
            ];
        }
    }
}
