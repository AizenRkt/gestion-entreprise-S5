<?php

namespace app\models\AVIS\KPI;

use Flight;
use PDO;

/**
 * Modèle KPI Direction - Fonctions pour le tableau de bord Direction Générale
 */
class KpiDirectionModel
{
    /**
     * Récupère toutes les données KPI pour le dashboard direction
     */
    public static function getAllKpis(): array
    {
        return [
            'chiffreAffaires' => self::getChiffreAffaires(),
            'margeBrute' => self::getMargeBrute(),
            'valeurStock' => self::getValeurStockTotal(),
            'rotationStock' => self::getRotationStock(),
            'performancesSites' => self::getPerformanceParSite(),
            'surstocks' => self::getSurstocksObsolescence(),
            'ecartsInventaire' => self::getEcartsInventaireParDepot(),
            'alertes' => self::getAlertesCritiques(),
            'obsolescenceAnalyse' => self::getObsolescenceAnalyse(),
            'charts' => self::getChartsData()
        ];
    }

    /**
     * KPI 1: Chiffre d'Affaires mensuel
     * Basé sur les livraisons client effectuées
     */
    public static function getChiffreAffaires(): array
    {
        try {
            $db = Flight::db();

            // CA du mois courant (basé sur les factures clients)
            $sqlCA = "SELECT COALESCE(SUM(fc.montant_ttc), 0) as ca_mois
                      FROM facture_client fc
                      WHERE MONTH(fc.facture_date) = MONTH(CURDATE())
                        AND YEAR(fc.facture_date) = YEAR(CURDATE())";
            $stmt = $db->prepare($sqlCA);
            $stmt->execute();
            $caMois = (float)$stmt->fetchColumn();

            // CA du mois précédent (M-1)
            $sqlPrev = "SELECT COALESCE(SUM(fc.montant_ttc), 0) as ca_prev
                        FROM facture_client fc
                        WHERE MONTH(fc.facture_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                          AND YEAR(fc.facture_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
            $stmtPrev = $db->prepare($sqlPrev);
            $stmtPrev->execute();
            $caPrev = (float)$stmtPrev->fetchColumn();

            // CA du même mois l'année dernière (M-12)
            $sqlYearAgo = "SELECT COALESCE(SUM(fc.montant_ttc), 0) as ca_year
                           FROM facture_client fc
                           WHERE MONTH(fc.facture_date) = MONTH(CURDATE())
                             AND YEAR(fc.facture_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))";
            $stmtYear = $db->prepare($sqlYearAgo);
            $stmtYear->execute();
            $caYear = (float)$stmtYear->fetchColumn();

            // Calcul des variations
            $variationM1 = $caPrev > 0 ? round((($caMois - $caPrev) / $caPrev) * 100, 1) : 0;
            $variationM12 = $caYear > 0 ? round((($caMois - $caYear) / $caYear) * 100, 1) : 0;

            // Objectif mensuel (moyenne des 3 derniers mois + 5%)
            $sqlObjectif = "SELECT COALESCE(AVG(monthly_ca), 0) * 1.05 as objectif
                            FROM (
                                SELECT SUM(fc.montant_ttc) as monthly_ca
                                FROM facture_client fc
                                WHERE fc.facture_date >= DATE_SUB(CURDATE(), INTERVAL 4 MONTH)
                                  AND fc.facture_date < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                                GROUP BY YEAR(fc.facture_date), MONTH(fc.facture_date)
                            ) sub";
            $stmtObj = $db->prepare($sqlObjectif);
            $stmtObj->execute();
            $objectif = (float)$stmtObj->fetchColumn();
            if ($objectif <= 0) $objectif = $caMois * 1.05; // fallback

            $progressObjectif = $objectif > 0 ? min(round(($caMois / $objectif) * 100, 0), 150) : 0;

            return [
                'valeur' => $caMois,
                'variationM1' => $variationM1,
                'variationM12' => $variationM12,
                'objectif' => $objectif,
                'progressObjectif' => $progressObjectif
            ];
        } catch (\PDOException $e) {
            return [
                'valeur' => 0,
                'variationM1' => 0,
                'variationM12' => 0,
                'objectif' => 0,
                'progressObjectif' => 0
            ];
        }
    }

    /**
     * KPI 2: Marge Brute
     * = CA - Coût des ventes (prix achat des articles vendus)
     */
    public static function getMargeBrute(): array
    {
        try {
            $db = Flight::db();

            // CA mois courant
            $sqlCA = "SELECT COALESCE(SUM(fc.montant_ht), 0) as ca_ht
                      FROM facture_client fc
                      WHERE MONTH(fc.facture_date) = MONTH(CURDATE())
                        AND YEAR(fc.facture_date) = YEAR(CURDATE())";
            $stmt = $db->prepare($sqlCA);
            $stmt->execute();
            $caHT = (float)$stmt->fetchColumn();

            // Coût des ventes (sortie de stock valorisée)
            $sqlCout = "SELECT COALESCE(SUM(ms.valeur_mouvement), 0) as cout_ventes
                        FROM mouvement_stock ms
                        INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                        WHERE mst.code = 'VENTE_LIVRAISON'
                          AND MONTH(ms.date_mouvement) = MONTH(CURDATE())
                          AND YEAR(ms.date_mouvement) = YEAR(CURDATE())";
            $stmtCout = $db->prepare($sqlCout);
            $stmtCout->execute();
            $coutVentes = (float)$stmtCout->fetchColumn();

            $margeBrute = $caHT - $coutVentes;
            $tauxMarge = $caHT > 0 ? round(($margeBrute / $caHT) * 100, 1) : 0;

            // Marge mois précédent pour variation
            $sqlPrevCA = "SELECT COALESCE(SUM(fc.montant_ht), 0) as ca_ht
                          FROM facture_client fc
                          WHERE MONTH(fc.facture_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                            AND YEAR(fc.facture_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
            $stmtPrevCA = $db->prepare($sqlPrevCA);
            $stmtPrevCA->execute();
            $caPrevHT = (float)$stmtPrevCA->fetchColumn();

            $sqlPrevCout = "SELECT COALESCE(SUM(ms.valeur_mouvement), 0) as cout_ventes
                            FROM mouvement_stock ms
                            INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                            WHERE mst.code = 'VENTE_LIVRAISON'
                              AND MONTH(ms.date_mouvement) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                              AND YEAR(ms.date_mouvement) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
            $stmtPrevCout = $db->prepare($sqlPrevCout);
            $stmtPrevCout->execute();
            $coutPrev = (float)$stmtPrevCout->fetchColumn();

            $margePrev = $caPrevHT - $coutPrev;
            $tauxPrev = $caPrevHT > 0 ? round(($margePrev / $caPrevHT) * 100, 1) : 0;
            $variationPts = round($tauxMarge - $tauxPrev, 1);

            // Objectif marge
            $objectifMarge = 35; // 35% objectif standard
            $progressObjectif = min(round(($tauxMarge / $objectifMarge) * 100, 0), 150);

            return [
                'valeur' => $margeBrute,
                'tauxMarge' => $tauxMarge,
                'variationPts' => $variationPts,
                'objectif' => $objectifMarge,
                'progressObjectif' => $progressObjectif
            ];
        } catch (\PDOException $e) {
            return [
                'valeur' => 0,
                'tauxMarge' => 0,
                'variationPts' => 0,
                'objectif' => 35,
                'progressObjectif' => 0
            ];
        }
    }

    /**
     * KPI 3: Valeur Stock Total
     */
    public static function getValeurStockTotal(): array
    {
        try {
            $db = Flight::db();

            // Valeur stock actuelle
            $sqlStock = "SELECT COALESCE(SUM(sc.quantite * sc.cout_unitaire_moyen), 0) as valeur_stock
                         FROM stock_courant sc";
            $stmt = $db->prepare($sqlStock);
            $stmt->execute();
            $valeurStock = (float)$stmt->fetchColumn();

            // Valeur stock M-1 (depuis clôture ou estimation)
            $sqlPrev = "SELECT COALESCE(SUM(scd.quantite * scd.cout_unitaire), 0) as valeur_prev
                        FROM stock_cloture_detail scd
                        INNER JOIN stock_cloture_periode scp ON scp.id_stock_cloture_periode = scd.id_stock_cloture_periode
                        WHERE scp.annee = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                          AND scp.mois = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
            $stmtPrev = $db->prepare($sqlPrev);
            $stmtPrev->execute();
            $valeurPrev = (float)$stmtPrev->fetchColumn();

            // Valeur stock M-12
            $sqlYear = "SELECT COALESCE(SUM(scd.quantite * scd.cout_unitaire), 0) as valeur_year
                        FROM stock_cloture_detail scd
                        INNER JOIN stock_cloture_periode scp ON scp.id_stock_cloture_periode = scd.id_stock_cloture_periode
                        WHERE scp.annee = YEAR(DATE_SUB(CURDATE(), INTERVAL 12 MONTH))
                          AND scp.mois = MONTH(DATE_SUB(CURDATE(), INTERVAL 12 MONTH))";
            $stmtYear = $db->prepare($sqlYear);
            $stmtYear->execute();
            $valeurYear = (float)$stmtYear->fetchColumn();

            $variationM1 = $valeurPrev > 0 ? round((($valeurStock - $valeurPrev) / $valeurPrev) * 100, 1) : 0;
            $variationM12 = $valeurYear > 0 ? round((($valeurStock - $valeurYear) / $valeurYear) * 100, 1) : 0;

            // Couverture en jours (valeur stock / CA journalier moyen)
            $sqlCAMoyen = "SELECT COALESCE(AVG(daily_ca), 0) as ca_jour
                           FROM (
                               SELECT DATE(facture_date) as jour, SUM(montant_ttc) as daily_ca
                               FROM facture_client
                               WHERE facture_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                               GROUP BY DATE(facture_date)
                           ) sub";
            $stmtCA = $db->prepare($sqlCAMoyen);
            $stmtCA->execute();
            $caJour = (float)$stmtCA->fetchColumn();
            $couvertureJours = $caJour > 0 ? round($valeurStock / $caJour, 1) : 0;

            return [
                'valeur' => $valeurStock,
                'variationM1' => $variationM1,
                'variationM12' => $variationM12,
                'valeurM1' => $valeurPrev,
                'valeurM12' => $valeurYear,
                'couvertureJours' => $couvertureJours
            ];
        } catch (\PDOException $e) {
            return [
                'valeur' => 0,
                'variationM1' => 0,
                'variationM12' => 0,
                'valeurM1' => 0,
                'valeurM12' => 0,
                'couvertureJours' => 0
            ];
        }
    }

    /**
     * KPI 4: Rotation du Stock
     * = Coût des ventes annuel / Stock moyen
     */
    public static function getRotationStock(): array
    {
        try {
            $db = Flight::db();

            // Coût des ventes sur 12 mois
            $sqlCoutVentes = "SELECT COALESCE(SUM(ms.valeur_mouvement), 0) as cout_ventes
                              FROM mouvement_stock ms
                              INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                              WHERE mst.code = 'VENTE_LIVRAISON'
                                AND ms.date_mouvement >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
            $stmt = $db->prepare($sqlCoutVentes);
            $stmt->execute();
            $coutVentes = (float)$stmt->fetchColumn();

            // Stock moyen (début + fin / 2, basé sur clôtures)
            $sqlStockCourant = "SELECT COALESCE(SUM(sc.quantite * sc.cout_unitaire_moyen), 0) as stock_actuel
                                FROM stock_courant sc";
            $stmtCourant = $db->prepare($sqlStockCourant);
            $stmtCourant->execute();
            $stockActuel = (float)$stmtCourant->fetchColumn();

            $sqlStockDebut = "SELECT COALESCE(SUM(scd.quantite * scd.cout_unitaire), 0) as stock_debut
                              FROM stock_cloture_detail scd
                              INNER JOIN stock_cloture_periode scp ON scp.id_stock_cloture_periode = scd.id_stock_cloture_periode
                              WHERE scp.annee = YEAR(DATE_SUB(CURDATE(), INTERVAL 12 MONTH))
                                AND scp.mois = MONTH(DATE_SUB(CURDATE(), INTERVAL 12 MONTH))";
            $stmtDebut = $db->prepare($sqlStockDebut);
            $stmtDebut->execute();
            $stockDebut = (float)$stmtDebut->fetchColumn();

            $stockMoyen = ($stockDebut + $stockActuel) / 2;
            $rotation = $stockMoyen > 0 ? round($coutVentes / $stockMoyen, 1) : 0;

            $objectif = 5;
            $ecartObjectif = $objectif > 0 ? round((($rotation - $objectif) / $objectif) * 100, 0) : 0;
            $progressObjectif = min(round(($rotation / $objectif) * 100, 0), 150);

            return [
                'valeur' => $rotation,
                'objectif' => $objectif,
                'ecartObjectif' => $ecartObjectif,
                'progressObjectif' => $progressObjectif,
                'sousCible' => $rotation < $objectif
            ];
        } catch (\PDOException $e) {
            return [
                'valeur' => 0,
                'objectif' => 5,
                'ecartObjectif' => -100,
                'progressObjectif' => 0,
                'sousCible' => true
            ];
        }
    }

    /**
     * Performance par Site/Dépôt
     */
    public static function getPerformanceParSite(): array
    {
        try {
            $db = Flight::db();

            $sql = "SELECT 
                        d.id_depot,
                        d.nom as site,
                        COALESCE(ca.ca_mois, 0) as ca,
                        COALESCE(ca.ca_prev, 0) as ca_prev,
                        COALESCE(marge.marge_mois, 0) as marge_brute,
                        COALESCE(stock.valeur_stock, 0) as valeur_stock
                    FROM depot d
                    LEFT JOIN (
                        SELECT 
                            cc.id_depot,
                            SUM(CASE WHEN MONTH(fc.facture_date) = MONTH(CURDATE()) AND YEAR(fc.facture_date) = YEAR(CURDATE()) 
                                     THEN fc.montant_ttc ELSE 0 END) as ca_mois,
                            SUM(CASE WHEN MONTH(fc.facture_date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) 
                                          AND YEAR(fc.facture_date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                                     THEN fc.montant_ttc ELSE 0 END) as ca_prev
                        FROM facture_client fc
                        INNER JOIN livraison_client lc ON fc.id_livraison_client = lc.id_livraison_client
                        INNER JOIN commande_client cc ON lc.id_commande_client = cc.id_commande_client
                        GROUP BY cc.id_depot
                    ) ca ON ca.id_depot = d.id_depot
                    LEFT JOIN (
                        SELECT 
                            cc.id_depot,
                            SUM(fc.montant_ht) - COALESCE(SUM(ms.valeur_mouvement), 0) as marge_mois
                        FROM facture_client fc
                        INNER JOIN livraison_client lc ON fc.id_livraison_client = lc.id_livraison_client
                        INNER JOIN commande_client cc ON lc.id_commande_client = cc.id_commande_client
                        LEFT JOIN mouvement_stock ms ON ms.id_document_origine = lc.id_livraison_client
                            AND ms.type_document_origine = 'LIVRAISON'
                        WHERE MONTH(fc.facture_date) = MONTH(CURDATE())
                          AND YEAR(fc.facture_date) = YEAR(CURDATE())
                        GROUP BY cc.id_depot
                    ) marge ON marge.id_depot = d.id_depot
                    LEFT JOIN (
                        SELECT id_depot, SUM(quantite * cout_unitaire_moyen) as valeur_stock
                        FROM stock_courant
                        GROUP BY id_depot
                    ) stock ON stock.id_depot = d.id_depot
                    ORDER BY ca.ca_mois DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultats = [];
            $totalCA = 0;
            $totalMarge = 0;
            $totalStock = 0;

            foreach ($sites as $site) {
                $ca = (float)$site['ca'];
                $caPrev = (float)$site['ca_prev'];
                $marge = (float)$site['marge_brute'];
                $stock = (float)$site['valeur_stock'];

                $variationCA = $caPrev > 0 ? round((($ca - $caPrev) / $caPrev) * 100, 1) : 0;
                $tauxMarge = $ca > 0 ? round(($marge / $ca) * 100, 1) : 0;

                // Rotation = (Coût ventes / Stock moyen) - simplifié par site
                $rotation = $stock > 0 ? round(($ca * 0.7) / $stock, 1) : 0; // Approximation avec 70% comme coût

                // Statut basé sur la rotation
                if ($rotation >= 4.5) {
                    $statut = 'Bon';
                } elseif ($rotation >= 3.5) {
                    $statut = 'Moyen';
                } else {
                    $statut = 'Alerte';
                }

                $resultats[] = [
                    'site' => $site['site'],
                    'ca' => $ca,
                    'variationCA' => $variationCA,
                    'margeBrute' => $marge,
                    'tauxMarge' => $tauxMarge,
                    'valeurStock' => $stock,
                    'rotation' => $rotation,
                    'statut' => $statut
                ];

                $totalCA += $ca;
                $totalMarge += $marge;
                $totalStock += $stock;
            }

            // Totaux
            $variationTotale = 0;
            $tauxMargeTotale = $totalCA > 0 ? round(($totalMarge / $totalCA) * 100, 1) : 0;
            $rotationTotale = $totalStock > 0 ? round(($totalCA * 0.7) / $totalStock, 1) : 0;

            return [
                'sites' => $resultats,
                'totaux' => [
                    'ca' => $totalCA,
                    'margeBrute' => $totalMarge,
                    'tauxMarge' => $tauxMargeTotale,
                    'valeurStock' => $totalStock,
                    'rotation' => $rotationTotale
                ]
            ];
        } catch (\PDOException $e) {
            return [
                'sites' => [],
                'totaux' => [
                    'ca' => 0,
                    'margeBrute' => 0,
                    'tauxMarge' => 0,
                    'valeurStock' => 0,
                    'rotation' => 0
                ]
            ];
        }
    }

    /**
     * Surstocks et Obsolescence
     * Top 10 articles avec le plus de surstock ou obsolescence
     */
    public static function getSurstocksObsolescence(int $limit = 10): array
    {
        try {
            $db = Flight::db();

            // Articles sans mouvement depuis longtemps + valeur stock
            $sql = "SELECT 
                        a.id_article,
                        a.designation as article,
                        a.code as reference,
                        COALESCE(SUM(sc.quantite), 0) as quantite_stock,
                        COALESCE(SUM(sc.quantite * sc.cout_unitaire_moyen), 0) as valeur,
                        MAX(ms.date_mouvement) as derniere_vente,
                        DATEDIFF(CURDATE(), MAX(ms.date_mouvement)) as jours_sans_vente
                    FROM article a
                    INNER JOIN stock_courant sc ON sc.id_article = a.id_article
                    LEFT JOIN mouvement_stock ms ON ms.id_article = a.id_article
                        AND ms.id_type_mouvement_stock IN (
                            SELECT id_type_mouvement_stock FROM mouvement_stock_type WHERE code = 'VENTE_LIVRAISON'
                        )
                    WHERE sc.quantite > 0
                    GROUP BY a.id_article, a.designation, a.code
                    HAVING jours_sans_vente > 90 OR jours_sans_vente IS NULL
                    ORDER BY valeur DESC
                    LIMIT " . (int)$limit;

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultats = [];
            $valeurTotale = 0;
            $nbObsolete = 0;
            $nbSurstock = 0;

            foreach ($articles as $i => $art) {
                $joursSansVente = (int)($art['jours_sans_vente'] ?? 999);
                $valeur = (float)$art['valeur'];

                // Statut basé sur les jours sans vente
                if ($joursSansVente > 365) {
                    $statut = 'Obsolète';
                    $nbObsolete++;
                } else {
                    $statut = 'Surstock';
                    $nbSurstock++;
                }

                // Format de la dernière vente
                $derniereVente = $art['derniere_vente'] ? 'Il y a ' . round($joursSansVente / 30) . ' mois' : 'Jamais';

                $resultats[] = [
                    'rang' => $i + 1,
                    'article' => $art['article'],
                    'reference' => $art['reference'],
                    'quantite' => (int)$art['quantite_stock'],
                    'valeur' => $valeur,
                    'derniereVente' => $derniereVente,
                    'statut' => $statut
                ];

                $valeurTotale += $valeur;
            }

            return [
                'articles' => $resultats,
                'valeurTotale' => $valeurTotale,
                'nbObsolete' => $nbObsolete,
                'nbSurstock' => $nbSurstock
            ];
        } catch (\PDOException $e) {
            return [
                'articles' => [],
                'valeurTotale' => 0,
                'nbObsolete' => 0,
                'nbSurstock' => 0
            ];
        }
    }

    /**
     * Écarts Inventaire par Dépôt
     */
    public static function getEcartsInventaireParDepot(): array
    {
        try {
            $db = Flight::db();

            // Dernière campagne d'inventaire
            $sqlCampagne = "SELECT id_inventaire_campagne, date_debut
                            FROM inventaire_campagne
                            WHERE statut = 'CLOTURE'
                            ORDER BY date_debut DESC
                            LIMIT 1";
            $stmtCamp = $db->prepare($sqlCampagne);
            $stmtCamp->execute();
            $campagne = $stmtCamp->fetch(PDO::FETCH_ASSOC);

            if (!$campagne) {
                return [
                    'depots' => [],
                    'totaux' => [
                        'valeurStock' => 0,
                        'ecartValeur' => 0,
                        'ecartPourcent' => 0,
                        'articlesEcart' => 0,
                        'fiabilite' => 100
                    ],
                    'dateInventaire' => null
                ];
            }

            $idCampagne = $campagne['id_inventaire_campagne'];
            $dateInventaire = $campagne['date_debut'];

            // Écarts par dépôt
            $sql = "SELECT 
                        d.id_depot,
                        d.nom as depot,
                        COALESCE(SUM(sc.quantite * sc.cout_unitaire_moyen), 0) as valeur_stock,
                        COALESCE(SUM(ic.ecart * sc.cout_unitaire_moyen), 0) as ecart_valeur,
                        COUNT(CASE WHEN ic.ecart != 0 THEN 1 END) as articles_ecart
                    FROM depot d
                    LEFT JOIN stock_courant sc ON sc.id_depot = d.id_depot
                    LEFT JOIN inventaire_comptage ic ON ic.id_depot = d.id_depot 
                        AND ic.id_article = sc.id_article
                        AND ic.id_inventaire_campagne = :id_campagne
                    GROUP BY d.id_depot, d.nom
                    ORDER BY valeur_stock DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute([':id_campagne' => $idCampagne]);
            $depots = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultats = [];
            $totalValeur = 0;
            $totalEcart = 0;
            $totalArticlesEcart = 0;

            foreach ($depots as $depot) {
                $valeur = (float)$depot['valeur_stock'];
                $ecart = (float)$depot['ecart_valeur'];
                $articlesEcart = (int)$depot['articles_ecart'];

                $ecartPourcent = $valeur > 0 ? round(($ecart / $valeur) * 100, 1) : 0;
                $fiabilite = 100 - abs($ecartPourcent);

                // Statut basé sur l'écart
                if (abs($ecartPourcent) <= 0.5) {
                    $statut = 'Conforme';
                } elseif (abs($ecartPourcent) <= 2) {
                    $statut = 'À surveiller';
                } else {
                    $statut = 'Critique';
                }

                $resultats[] = [
                    'depot' => $depot['depot'],
                    'valeurStock' => $valeur,
                    'ecartValeur' => $ecart,
                    'ecartPourcent' => $ecartPourcent,
                    'articlesEcart' => $articlesEcart,
                    'fiabilite' => round($fiabilite, 1),
                    'statut' => $statut
                ];

                $totalValeur += $valeur;
                $totalEcart += $ecart;
                $totalArticlesEcart += $articlesEcart;
            }

            $ecartPourcentTotal = $totalValeur > 0 ? round(($totalEcart / $totalValeur) * 100, 1) : 0;
            $fiabiliteTotal = 100 - abs($ecartPourcentTotal);

            return [
                'depots' => $resultats,
                'totaux' => [
                    'valeurStock' => $totalValeur,
                    'ecartValeur' => $totalEcart,
                    'ecartPourcent' => $ecartPourcentTotal,
                    'articlesEcart' => $totalArticlesEcart,
                    'fiabilite' => round($fiabiliteTotal, 1)
                ],
                'dateInventaire' => $dateInventaire
            ];
        } catch (\PDOException $e) {
            return [
                'depots' => [],
                'totaux' => [
                    'valeurStock' => 0,
                    'ecartValeur' => 0,
                    'ecartPourcent' => 0,
                    'articlesEcart' => 0,
                    'fiabilite' => 100
                ],
                'dateInventaire' => null
            ];
        }
    }

    /**
     * Alertes Critiques
     */
    public static function getAlertesCritiques(): array
    {
        try {
            $db = Flight::db();

            $alertes = [];

            // Alerte rotation stock
            $rotation = self::getRotationStock();
            if ($rotation['sousCible']) {
                $alertes[] = [
                    'type' => 'warning',
                    'message' => "Rotation stock ({$rotation['valeur']}) en dessous de l'objectif (≥{$rotation['objectif']})"
                ];
            }

            // Valeur immobilisée en surstock
            $surstocks = self::getSurstocksObsolescence(10);
            if ($surstocks['valeurTotale'] > 0) {
                $alertes[] = [
                    'type' => 'warning',
                    'message' => 'Valeur immobilisée en surstock : ' . number_format($surstocks['valeurTotale'], 0, ',', ' ') . ' €'
                ];
            }

            // Articles obsolètes
            if ($surstocks['nbObsolete'] > 0) {
                $alertes[] = [
                    'type' => 'danger',
                    'message' => $surstocks['nbObsolete'] . ' article(s) sans mouvement depuis +12 mois'
                ];
            }

            // Écarts inventaire critiques
            $ecarts = self::getEcartsInventaireParDepot();
            foreach ($ecarts['depots'] as $depot) {
                if ($depot['statut'] === 'Critique') {
                    $alertes[] = [
                        'type' => 'danger',
                        'message' => "Écart inventaire critique au dépôt {$depot['depot']} ({$depot['ecartPourcent']}%)"
                    ];
                }
            }

            return $alertes;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Analyse Obsolescence (pour le graphique pie)
     */
    public static function getObsolescenceAnalyse(): array
    {
        try {
            $db = Flight::db();

            // Stock actif (mouvement < 6 mois)
            $sqlActif = "SELECT COALESCE(SUM(sc.quantite * sc.cout_unitaire_moyen), 0) as valeur
                         FROM stock_courant sc
                         WHERE EXISTS (
                             SELECT 1 FROM mouvement_stock ms
                             INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                             WHERE ms.id_article = sc.id_article
                               AND mst.code = 'VENTE_LIVRAISON'
                               AND ms.date_mouvement >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                         )";
            $stmt = $db->prepare($sqlActif);
            $stmt->execute();
            $stockActif = (float)$stmt->fetchColumn();

            // Surstock (6-12 mois sans mouvement)
            $sqlSurstock = "SELECT COALESCE(SUM(sc.quantite * sc.cout_unitaire_moyen), 0) as valeur
                            FROM stock_courant sc
                            WHERE EXISTS (
                                SELECT 1 FROM mouvement_stock ms
                                INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                                WHERE ms.id_article = sc.id_article
                                  AND mst.code = 'VENTE_LIVRAISON'
                                  AND ms.date_mouvement >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                                  AND ms.date_mouvement < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                            )
                            AND NOT EXISTS (
                                SELECT 1 FROM mouvement_stock ms2
                                INNER JOIN mouvement_stock_type mst2 ON mst2.id_type_mouvement_stock = ms2.id_type_mouvement_stock
                                WHERE ms2.id_article = sc.id_article
                                  AND mst2.code = 'VENTE_LIVRAISON'
                                  AND ms2.date_mouvement >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                            )";
            $stmtSur = $db->prepare($sqlSurstock);
            $stmtSur->execute();
            $surstock = (float)$stmtSur->fetchColumn();

            // Obsolète (> 12 mois sans mouvement)
            $sqlObsolete = "SELECT COALESCE(SUM(sc.quantite * sc.cout_unitaire_moyen), 0) as valeur
                            FROM stock_courant sc
                            WHERE NOT EXISTS (
                                SELECT 1 FROM mouvement_stock ms
                                INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                                WHERE ms.id_article = sc.id_article
                                  AND mst.code = 'VENTE_LIVRAISON'
                                  AND ms.date_mouvement >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                            )";
            $stmtObs = $db->prepare($sqlObsolete);
            $stmtObs->execute();
            $obsolete = (float)$stmtObs->fetchColumn();

            return [
                'stockActif' => $stockActif,
                'surstock' => $surstock,
                'obsolete' => $obsolete
            ];
        } catch (\PDOException $e) {
            return [
                'stockActif' => 0,
                'surstock' => 0,
                'obsolete' => 0
            ];
        }
    }

    /**
     * Données pour les graphiques
     */
    public static function getChartsData(): array
    {
        try {
            $db = Flight::db();
            $labels = [];
            $caData = [];
            $margeData = [];
            $margePctData = [];
            $stockData = [];
            $rotationData = [];

            // Générer les 12 derniers mois
            for ($i = 11; $i >= 0; $i--) {
                $date = new \DateTime();
                $date->modify("-{$i} months");
                $mois = $date->format('Y-m');
                $labelMois = self::getMoisFr($date->format('n'));
                $labels[] = $labelMois;

                $annee = $date->format('Y');
                $numMois = $date->format('n');

                // CA du mois
                $sqlCA = "SELECT COALESCE(SUM(fc.montant_ttc), 0) as ca
                          FROM facture_client fc
                          WHERE YEAR(fc.facture_date) = :annee AND MONTH(fc.facture_date) = :mois";
                $stmtCA = $db->prepare($sqlCA);
                $stmtCA->execute([':annee' => $annee, ':mois' => $numMois]);
                $ca = (float)$stmtCA->fetchColumn();
                $caData[] = round($ca, 0);

                // Marge du mois
                $sqlMarge = "SELECT 
                                COALESCE(SUM(fc.montant_ht), 0) as ca_ht,
                                COALESCE((
                                    SELECT SUM(ms.valeur_mouvement)
                                    FROM mouvement_stock ms
                                    INNER JOIN mouvement_stock_type mst ON mst.id_type_mouvement_stock = ms.id_type_mouvement_stock
                                    WHERE mst.code = 'VENTE_LIVRAISON'
                                      AND YEAR(ms.date_mouvement) = :annee2 AND MONTH(ms.date_mouvement) = :mois2
                                ), 0) as cout
                             FROM facture_client fc
                             WHERE YEAR(fc.facture_date) = :annee AND MONTH(fc.facture_date) = :mois";
                $stmtMarge = $db->prepare($sqlMarge);
                $stmtMarge->execute([':annee' => $annee, ':mois' => $numMois, ':annee2' => $annee, ':mois2' => $numMois]);
                $row = $stmtMarge->fetch(PDO::FETCH_ASSOC);
                $caHT = (float)($row['ca_ht'] ?? 0);
                $cout = (float)($row['cout'] ?? 0);
                $marge = $caHT - $cout;
                $margePct = $caHT > 0 ? round(($marge / $caHT) * 100, 1) : 0;
                $margeData[] = round($marge, 0);
                $margePctData[] = $margePct;

                // Valeur stock (de la clôture ou estimation)
                $sqlStock = "SELECT COALESCE(SUM(scd.quantite * scd.cout_unitaire), 0) as valeur
                             FROM stock_cloture_detail scd
                             INNER JOIN stock_cloture_periode scp ON scp.id_stock_cloture_periode = scd.id_stock_cloture_periode
                             WHERE scp.annee = :annee AND scp.mois = :mois";
                $stmtStock = $db->prepare($sqlStock);
                $stmtStock->execute([':annee' => $annee, ':mois' => $numMois]);
                $stock = (float)$stmtStock->fetchColumn();

                // Si pas de clôture, utiliser stock courant pour le mois actuel
                if ($stock == 0 && $i == 0) {
                    $sqlCourant = "SELECT COALESCE(SUM(quantite * cout_unitaire_moyen), 0) FROM stock_courant";
                    $stock = (float)$db->query($sqlCourant)->fetchColumn();
                }
                $stockData[] = round($stock, 0);

                // Rotation (calculée sur base mensuelle annualisée)
                // Simplifié: (CA * 0.7 * 12) / Stock moyen
                $rotationMensuelle = $stock > 0 ? round(($ca * 0.7 * 12) / $stock, 1) : 0;
                $rotationData[] = $rotationMensuelle;
            }

            // Stock par site (donut chart)
            $sqlStockSite = "SELECT d.nom, COALESCE(SUM(sc.quantite * sc.cout_unitaire_moyen), 0) as valeur
                             FROM depot d
                             LEFT JOIN stock_courant sc ON sc.id_depot = d.id_depot
                             GROUP BY d.id_depot, d.nom
                             ORDER BY valeur DESC";
            $stockParSite = $db->query($sqlStockSite)->fetchAll(PDO::FETCH_ASSOC);

            $siteLabels = [];
            $siteData = [];
            $totalSite = array_sum(array_column($stockParSite, 'valeur'));

            foreach ($stockParSite as $site) {
                $valeur = (float)$site['valeur'];
                $pct = $totalSite > 0 ? round(($valeur / $totalSite) * 100, 0) : 0;
                $siteLabels[] = $site['nom'] . " ({$pct}%)";
                $siteData[] = round($valeur, 0);
            }

            // Obsolescence (pie chart)
            $obsolescence = self::getObsolescenceAnalyse();

            return [
                'caMargeEvolution' => [
                    'labels' => $labels,
                    'ca' => $caData,
                    'marge' => $margeData,
                    'margePct' => $margePctData
                ],
                'stockEvolution' => [
                    'labels' => $labels,
                    'valeurStock' => $stockData,
                    'rotation' => $rotationData
                ],
                'stockParSite' => [
                    'labels' => $siteLabels,
                    'data' => $siteData,
                    'total' => $totalSite
                ],
                'obsolescence' => [
                    'labels' => ['Obsolète (>12 mois)', 'Surstock (6-12 mois)', 'Stock Actif'],
                    'data' => [
                        round($obsolescence['obsolete'], 0),
                        round($obsolescence['surstock'], 0),
                        round($obsolescence['stockActif'], 0)
                    ]
                ]
            ];
        } catch (\PDOException $e) {
            return [
                'caMargeEvolution' => ['labels' => [], 'ca' => [], 'marge' => [], 'margePct' => []],
                'stockEvolution' => ['labels' => [], 'valeurStock' => [], 'rotation' => []],
                'stockParSite' => ['labels' => [], 'data' => [], 'total' => 0],
                'obsolescence' => ['labels' => [], 'data' => []]
            ];
        }
    }

    /**
     * Convertit un numéro de mois en nom français abrégé
     */
    private static function getMoisFr(int $mois): string
    {
        $noms = [
            1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Août',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
        ];
        return $noms[$mois] ?? '';
    }
}
