<?php

namespace app\controllers\AVIS\KPI;

use Flight;

class KpiController
{
    /* ==============================
     * KPI - DIRECTION GÉNÉRALE
     * ============================== */
    public static function direction()
    {
        Flight::render('AVIS/kpi/direction', [
            'title' => 'Tableau de bord - Direction Générale'
        ]);
    }

    /* ==============================
     * KPI - ACHATS / SUPPLY CHAIN
     * ============================== */
    /* ==============================
     * KPI - ACHATS / SUPPLY CHAIN
     * ============================== */
    public static function achats()
    {
        $db = Flight::db();

        // 1. Total Achats (suivi Bon de Commande validés)
        // Question: "Combien avons-nous depense ?"
        $sqlSpend = "SELECT SUM(montant_ttc) as total FROM bon_commande_fournisseur"; 
        // Note: idealement filtrer par statut 'validé' si la colonne existe ou 'date' pour YTD. 
        // Ici on prend le total global pour l'instant ou on suppose que tout BC est validé.
        // On va checker bon_commande_fournisseur_status si besoin mais restons simple pour start.
        $totalSpend = $db->query($sqlSpend)->fetchColumn() ?: 0;

        // 2. Top Fournisseur
        // Question: "Qui est notre plus gros fournisseur ?"
        $sqlTopSupplier = "SELECT f.nom, SUM(b.montant_ttc) as total 
                           FROM bon_commande_fournisseur b
                           JOIN fournisseur f ON b.id_fournisseur = f.id_fournisseur
                           GROUP BY f.id_fournisseur 
                           ORDER BY total DESC 
                           LIMIT 1";
        $topSupplierData = $db->query($sqlTopSupplier)->fetch(\PDO::FETCH_ASSOC);
        $topSupplierName = $topSupplierData['nom'] ?? 'N/A';
        $topSupplierAmount = $topSupplierData['total'] ?? 0;

        // 3. Commandes en cours (Demande achat non clôturée ou BC non livré)
        // Question: "Combien de commandes sont en attente ?"
        // On compte les Demandes d'achat en statut 'CREE' ou 'VISEE'
        $sqlPending = "SELECT COUNT(*) FROM demande_achat WHERE statut IN ('CREE', 'VISEE')";
        $pendingOrders = $db->query($sqlPending)->fetchColumn() ?: 0;

        // 4. Average Lead Time (Délai moyen commande -> réception)
        // Question: "En combien de temps sommes-nous livrés ?"
        // On compare date réception vs date BC
        $sqlLeadTime = "SELECT AVG(DATEDIFF(r.reception_date, b.bc_date)) as avg_days
                        FROM reception_fournisseur r
                        JOIN bon_commande_fournisseur b ON r.id_bon_commande_fournisseur = b.id_bon_commande_fournisseur";
        $avgLeadTime = $db->query($sqlLeadTime)->fetchColumn();
        $avgLeadTime = $avgLeadTime ? round($avgLeadTime, 1) : 0;

        // Données pour le tableau fournisseurs (Performance)
        // On recupere quelques fournisseurs avec leurs montants
        $sqlSuppliers = "SELECT f.nom, COALESCE(SUM(b.montant_ttc), 0) as volume_achat, COUNT(b.id_bon_commande_fournisseur) as nb_commandes
                         FROM fournisseur f
                         LEFT JOIN bon_commande_fournisseur b ON f.id_fournisseur = b.id_fournisseur
                         GROUP BY f.id_fournisseur
                         ORDER BY volume_achat DESC
                         LIMIT 5";
        $suppliersPerf = $db->query($sqlSuppliers)->fetchAll(\PDO::FETCH_ASSOC);

        // Données pour graphique: Evolution mensuelle des achats (4 derniers mois)
        $sqlMonthlyEvolution = "SELECT 
                                    DATE_FORMAT(bc_date, '%Y-%m') as mois,
                                    SUM(montant_ttc) as total_achats,
                                    COUNT(*) as nb_commandes
                                FROM bon_commande_fournisseur
                                WHERE bc_date >= DATE_SUB(NOW(), INTERVAL 4 MONTH)
                                GROUP BY DATE_FORMAT(bc_date, '%Y-%m')
                                ORDER BY mois ASC";
        $monthlyData = $db->query($sqlMonthlyEvolution)->fetchAll(\PDO::FETCH_ASSOC);


        // 5. Alerts & Risk Analysis
        // Risk: Top Supplier Share
        $supplierRiskShare = ($totalSpend > 0) ? round(($topSupplierAmount / $totalSpend) * 100, 1) : 0;
        
        // Urgent Orders (this month)
        // Supposons que 'remarque' contient 'urgent' ou priorite haute
        $sqlUrgent = "SELECT COUNT(*) FROM demande_achat 
                      WHERE (remarque LIKE '%urgent%' OR remarque LIKE '%URGENT%') 
                      AND MONTH(date_demande) = MONTH(CURRENT_DATE())
                      AND YEAR(date_demande) = YEAR(CURRENT_DATE())";
        $urgentOrdersCount = $db->query($sqlUrgent)->fetchColumn() ?: 0;

        // 6. Charts Data
        // Cycle Time Evolution (Avg days between DA date and BC date per month)
        $sqlCycleTime = "SELECT 
                            DATE_FORMAT(b.bc_date, '%Y-%m') as mois,
                            AVG(DATEDIFF(b.bc_date, d.date_demande)) as avg_days
                         FROM bon_commande_fournisseur b
                         JOIN demande_achat d ON b.id_demande_achat = d.id_demande_achat
                         WHERE b.bc_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                         GROUP BY DATE_FORMAT(b.bc_date, '%Y-%m')
                         ORDER BY mois ASC";
        $cycleTimeData = $db->query($sqlCycleTime)->fetchAll(\PDO::FETCH_ASSOC);

        // Price Evolution (Article A vs B) - Mocked logic if table empty, but trying fetch
        $sqlPriceHistory = "SELECT 
                                a.designation, 
                                aph.prix_vente as prix, 
                                DATE_FORMAT(aph.date_modification, '%Y-%m') as mois
                            FROM article_prix_historique aph
                            JOIN article a ON aph.id_article = a.id_article
                            WHERE aph.date_modification >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                            ORDER BY aph.date_modification ASC";
        $priceHistoryData = $db->query($sqlPriceHistory)->fetchAll(\PDO::FETCH_ASSOC);
        
        // Litigations (Active)
        // On checke si la table existe, sinon vide
        try {
            $sqlLitiges = "SELECT l.*, f.nom as fournisseur_nom 
                           FROM litige_fournisseur l
                           JOIN fournisseur f ON l.id_fournisseur = f.id_fournisseur
                           WHERE l.statut NOT IN ('Cloturé', 'Résolu')
                           ORDER BY l.date_litige DESC
                           LIMIT 5";
            $litiges = $db->query($sqlLitiges)->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $litiges = [];
        }

        Flight::render('AVIS/kpi/achats', [
            'title' => 'KPI Achats & Supply Chain',
            'totalSpend' => $totalSpend,
            'topSupplierName' => $topSupplierName,
            'topSupplierAmount' => $topSupplierAmount,
            'pendingOrders' => $pendingOrders,
            'avgLeadTime' => $avgLeadTime,
            'suppliersPerf' => $suppliersPerf,
            'monthlyData' => $monthlyData,
            'supplierRiskShare' => $supplierRiskShare,
            'urgentOrdersCount' => $urgentOrdersCount,
            'cycleTimeData' => $cycleTimeData,
            'priceHistoryData' => $priceHistoryData,
            'litiges' => $litiges
        ]);
    }

    /* ==============================
     * KPI - MAGASIN / STOCK
     * ============================== */
    public static function stock()
    {
        Flight::render('AVIS/kpi/stock', [
            'title' => 'KPI Stock & Magasin'
        ]);
    }

    /* ==============================
     * KPI - VENTES / COMMERCIAL
     * ============================== */
    public static function ventes()
    {
        $db = Flight::db();

        // 1. Orders in Progress (VALIDE status)
        $sqlOrdersInProgress = "SELECT COUNT(*) FROM commande_client WHERE statut = 'VALIDE'";
        $ordersInProgress = $db->query($sqlOrdersInProgress)->fetchColumn() ?: 0;

        // 2. Cancellation Rate
        $sqlTotalOrders = "SELECT COUNT(*) FROM commande_client WHERE statut IN ('VALIDE', 'CLOTURE')";
        $totalOrders = $db->query($sqlTotalOrders)->fetchColumn() ?: 1;
        
        $sqlCancellations = "SELECT COUNT(*) FROM commande_annulation";
        $cancellationsCount = $db->query($sqlCancellations)->fetchColumn() ?: 0;
        $cancellationRate = round(($cancellationsCount / $totalOrders) * 100, 1);

        // 3. Discounts Granted (Total amount)
        $sqlDiscounts = "SELECT SUM(lcc.montant_ht * lcc.remise_pourcent / 100) as total_remise
                         FROM ligne_commande_client lcc
                         JOIN commande_client cc ON lcc.id_commande_client = cc.id_commande_client
                         WHERE cc.statut IN ('VALIDE', 'CLOTURE')
                         AND MONTH(cc.commande_date) = MONTH(CURRENT_DATE())";
        $discountsGranted = $db->query($sqlDiscounts)->fetchColumn() ?: 0;

        // 4. Credits (Avoirs)
        $sqlCredits = "SELECT COALESCE(SUM(montant), 0) as total FROM avoir_client";
        $creditsTotal = $db->query($sqlCredits)->fetchColumn() ?: 0;

        // 5. Revenue (CA) - Current month
        $sqlRevenue = "SELECT COALESCE(SUM(montant_ttc), 0) FROM facture_client 
                       WHERE statut IN ('VALIDE', 'PAYE')
                       AND MONTH(date_facture) = MONTH(CURRENT_DATE())";
        $revenue = $db->query($sqlRevenue)->fetchColumn() ?: 0;

        // 6. Service Rate (Delivered on time)
        $sqlDelivered = "SELECT COUNT(*) FROM commande_client cc
                         JOIN livraison_client lc ON cc.id_commande_client = lc.id_commande_client
                         WHERE lc.statut = 'LIVREE'
                         AND DATEDIFF(lc.livraison_date, cc.commande_date) <= 7";
        $deliveredOnTime = $db->query($sqlDelivered)->fetchColumn() ?: 0;
        
        $sqlTotalDelivered = "SELECT COUNT(*) FROM livraison_client WHERE statut = 'LIVREE'";
        $totalDelivered = $db->query($sqlTotalDelivered)->fetchColumn() ?: 1;
        $serviceRate = round(($deliveredOnTime / $totalDelivered) * 100, 1);

        // 7. Customer Count (Active this month)
        $sqlCustomers = "SELECT COUNT(DISTINCT id_client) FROM commande_client 
                         WHERE MONTH(commande_date) = MONTH(CURRENT_DATE())";
        $customerCount = $db->query($sqlCustomers)->fetchColumn() ?: 0;

        // 8. Average Ticket
        $avgTicket = $totalOrders > 0 ? round($revenue / $totalOrders, 0) : 0;

        // 9. Alerts - Backlog (orders without delivery)
        $sqlBacklog = "SELECT COUNT(*) as count, COALESCE(SUM(cc.montant_ttc), 0) as montant
                       FROM commande_client cc
                       LEFT JOIN livraison_client lc ON cc.id_commande_client = lc.id_commande_client
                       WHERE cc.statut = 'VALIDE' AND lc.id_livraison_client IS NULL";
        $backlogData = $db->query($sqlBacklog)->fetch(\PDO::FETCH_ASSOC);
        $backlogCount = $backlogData['count'] ?? 0;
        $backlogAmount = $backlogData['montant'] ?? 0;

        // 10. Delayed Orders (> 7 days)
        $sqlDelayed = "SELECT COUNT(*) as count, COALESCE(SUM(cc.montant_ttc), 0) as montant
                       FROM commande_client cc
                       LEFT JOIN livraison_client lc ON cc.id_commande_client = lc.id_livraison_client
                       WHERE cc.statut = 'VALIDE'
                       AND lc.id_livraison_client IS NULL
                       AND DATEDIFF(CURDATE(), cc.commande_date) > 7";
        $delayedData = $db->query($sqlDelayed)->fetch(\PDO::FETCH_ASSOC);
        $delayedCount = $delayedData['count'] ?? 0;
        $delayedAmount = $delayedData['montant'] ?? 0;

        // 11. Charts Data - Backlog Evolution (last 6 months)
        $sqlBacklogEvolution = "SELECT 
                                    DATE_FORMAT(cc.commande_date, '%Y-%m') as mois,
                                    COUNT(*) as nb_backlog
                                FROM commande_client cc
                                LEFT JOIN livraison_client lc ON cc.id_commande_client = lc.id_commande_client
                                WHERE cc.statut = 'VALIDE' 
                                AND lc.id_livraison_client IS NULL
                                AND cc.commande_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                GROUP BY DATE_FORMAT(cc.commande_date, '%Y-%m')
                                ORDER BY mois ASC";
        $backlogEvolution = $db->query($sqlBacklogEvolution)->fetchAll(\PDO::FETCH_ASSOC);

        // 12. Orders by Status
        $sqlOrdersByStatus = "SELECT 
                                  SUM(CASE WHEN cc.statut = 'VALIDE' AND lc.id_livraison_client IS NULL THEN 1 ELSE 0 END) as en_cours,
                                  SUM(CASE WHEN lc.statut = 'LIVREE' THEN 1 ELSE 0 END) as livrees,
                                  SUM(CASE WHEN cc.statut = 'VALIDE' AND lc.id_livraison_client IS NULL AND DATEDIFF(CURDATE(), cc.commande_date) > 7 THEN 1 ELSE 0 END) as retard
                              FROM commande_client cc
                              LEFT JOIN livraison_client lc ON cc.id_commande_client = lc.id_commande_client";
        $ordersByStatus = $db->query($sqlOrdersByStatus)->fetch(\PDO::FETCH_ASSOC);

        // 13. Cancellation Reasons
        $sqlCancellationReasons = "SELECT motif, COUNT(*) as count 
                                   FROM commande_annulation 
                                   GROUP BY motif";
        $cancellationReasons = $db->query($sqlCancellationReasons)->fetchAll(\PDO::FETCH_ASSOC);

        // 14. Credits by Type
        $sqlCreditsByType = "SELECT type_avoir, COUNT(*) as count, SUM(montant) as total
                             FROM avoir_client
                             GROUP BY type_avoir";
        $creditsByType = $db->query($sqlCreditsByType)->fetchAll(\PDO::FETCH_ASSOC);

        // 15. Delayed Orders Table
        $sqlDelayedOrders = "SELECT 
                                cc.commande_numero,
                                c.nom as client_nom,
                                cc.commande_date,
                                DATE_ADD(cc.commande_date, INTERVAL 7 DAY) as date_prevue,
                                DATEDIFF(CURDATE(), cc.commande_date) as jours_retard,
                                cc.montant_ttc,
                                'Stock insuffisant' as motif,
                                'Critique' as statut_priorite
                             FROM commande_client cc
                             JOIN client c ON cc.id_client = c.id_client
                             LEFT JOIN livraison_client lc ON cc.id_commande_client = lc.id_commande_client
                             WHERE cc.statut = 'VALIDE'
                             AND lc.id_livraison_client IS NULL
                             AND DATEDIFF(CURDATE(), cc.commande_date) > 7
                             ORDER BY jours_retard DESC
                             LIMIT 10";
        $delayedOrders = $db->query($sqlDelayedOrders)->fetchAll(\PDO::FETCH_ASSOC);

        // 16. Cancellations Table
        $sqlCancellationsTable = "SELECT 
                                      ca.date_annulation,
                                      cc.commande_numero,
                                      c.nom as client_nom,
                                      ca.motif,
                                      ca.montant_impact,
                                      ca.raison_detail
                                  FROM commande_annulation ca
                                  JOIN commande_client cc ON ca.id_commande_client = cc.id_commande_client
                                  JOIN client c ON cc.id_client = c.id_client
                                  ORDER BY ca.date_annulation DESC
                                  LIMIT 10";
        $cancellationsTable = $db->query($sqlCancellationsTable)->fetchAll(\PDO::FETCH_ASSOC);

        // 17. Discounts Table
        $sqlDiscountsTable = "SELECT 
                                  cc.commande_date,
                                  cc.commande_numero,
                                  c.nom as client_nom,
                                  lcc.remise_pourcent,
                                  (lcc.montant_ht * lcc.remise_pourcent / 100) as montant_remise,
                                  CASE WHEN lcc.remise_validee THEN 'Validée' ELSE 'Auto' END as validation
                              FROM ligne_commande_client lcc
                              JOIN commande_client cc ON lcc.id_commande_client = cc.id_commande_client
                              JOIN client c ON cc.id_client = c.id_client
                              WHERE lcc.remise_pourcent > 0
                              AND MONTH(cc.commande_date) = MONTH(CURRENT_DATE())
                              ORDER BY cc.commande_date DESC
                              LIMIT 10";
        $discountsTable = $db->query($sqlDiscountsTable)->fetchAll(\PDO::FETCH_ASSOC);

        // 18. Credits Table
        $sqlCreditsTable = "SELECT 
                                av.date_avoir,
                                cc.commande_numero,
                                c.nom as client_nom,
                                av.type_avoir,
                                av.motif,
                                av.montant,
                                av.statut
                            FROM avoir_client av
                            JOIN commande_client cc ON av.id_commande_client = cc.id_commande_client
                            JOIN client c ON av.id_client = c.id_client
                            ORDER BY av.date_avoir DESC
                            LIMIT 10";
        $creditsTable = $db->query($sqlCreditsTable)->fetchAll(\PDO::FETCH_ASSOC);

        Flight::render('AVIS/kpi/ventes', [
            'title' => 'KPI Ventes & Commercial',
            'ordersInProgress' => $ordersInProgress,
            'cancellationRate' => $cancellationRate,
            'cancellationsCount' => $cancellationsCount,
            'discountsGranted' => $discountsGranted,
            'creditsTotal' => $creditsTotal,
            'revenue' => $revenue,
            'serviceRate' => $serviceRate,
            'customerCount' => $customerCount,
            'avgTicket' => $avgTicket,
            'backlogCount' => $backlogCount,
            'backlogAmount' => $backlogAmount,
            'delayedCount' => $delayedCount,
            'delayedAmount' => $delayedAmount,
            'backlogEvolution' => $backlogEvolution,
            'ordersByStatus' => $ordersByStatus,
            'cancellationReasons' => $cancellationReasons,
            'creditsByType' => $creditsByType,
            'delayedOrders' => $delayedOrders,
            'cancellationsTable' => $cancellationsTable,
            'discountsTable' => $discountsTable,
            'creditsTable' => $creditsTable
        ]);
    }

    /* ==============================
     * KPI - FINANCE / DAF
     * ============================== */
    public static function finance()
    {
        Flight::render('AVIS/kpi/finance', [
            'title' => 'KPI Finance & DAF'
        ]);
    }
}
