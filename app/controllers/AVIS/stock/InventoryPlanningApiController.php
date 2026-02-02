<?php

namespace app\controllers\AVIS\stock;

use app\models\AVIS\stock\InventoryPlanningModel;
use app\models\AVIS\stock\InventoryCountingModel;
use app\models\AVIS\stock\MouvStockModel;
use app\models\AVIS\stock\StockCourantModel;
use Exception;
use Flight;
use PDO;

class InventoryPlanningApiController
{
    private static function requireAuth(): int
    {
        $userId = (int)($_SESSION['user']['id_user'] ?? 0);
        if ($userId <= 0) {
            Flight::json(['success' => false, 'message' => 'Non authentifié'], 401);
            exit;
        }
        return $userId;
    }

    public static function listCampaigns()
    {
        try {
            // self::requireAuth();
            $rows = InventoryPlanningModel::listCampaigns();
            Flight::json(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getCampaign($id)
    {
        try {
            // self::requireAuth();
            $campaign = InventoryPlanningModel::getCampaign((int)$id);
            if (!$campaign) {
                Flight::json(['success' => false, 'message' => 'Introuvable'], 404);
                return;
            }
            Flight::json(['success' => true, 'data' => $campaign]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createCampaign()
    {
        try {
            // $userId = self::requireAuth();
            $userId = 0; // Temporary hardcoded user ID for testing
            $raw = Flight::request()->getBody();
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                $data = Flight::request()->data->getData() ?? [];
            }
            if (empty($data['code']) || empty($data['libelle'])) {
                Flight::json(['success' => false, 'message' => 'Code et libellé requis'], 400);
                return;
            }
            $campId = InventoryPlanningModel::createCampaign($data, $userId);
            $campaign = InventoryPlanningModel::getCampaign($campId);
            Flight::json(['success' => true, 'data' => $campaign]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function listSites()
    {
        try {
            // self::requireAuth();
            $db = Flight::db();
            $st = $db->query("SELECT id_site, code, nom FROM site ORDER BY nom");
            Flight::json(['success' => true, 'data' => $st->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function listArticleFamilies()
    {
        try {
            // self::requireAuth();
            $db = Flight::db();
            $st = $db->query("SELECT id_article_famille, code, nom FROM article_famille ORDER BY nom");
            Flight::json(['success' => true, 'data' => $st->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function listCounts()
    {
        try {
            // self::requireAuth();
            $campagneId = (int)(Flight::request()->query['campagne'] ?? Flight::request()->query['id_inventaire_campagne'] ?? 0);
            $depotId = Flight::request()->query['depot'] ?? Flight::request()->query['id_depot'] ?? null;
            if ($campagneId <= 0) {
                Flight::json(['success' => false, 'message' => 'Campagne requise'], 400);
                return;
            }
            $rows = InventoryCountingModel::listCounts($campagneId, $depotId ? (int)$depotId : null);
            Flight::json(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createCount()
    {
        try {
            // $userId = self::requireAuth();
            $userId = 0; // Temporary hardcoded user ID for testing
            $raw = Flight::request()->getBody();
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                $data = Flight::request()->data->getData() ?? [];
            }
            $countId = InventoryCountingModel::createCount($data, $userId);
            $campagneId = (int)$data['id_inventaire_campagne'];
            $depotId = isset($data['id_depot']) ? (int)$data['id_depot'] : null;
            $rows = InventoryCountingModel::listCounts($campagneId, $depotId);
            Flight::json(['success' => true, 'data' => $rows, 'id' => $countId]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function listCountSheet()
    {
        try {
            $campagneId = (int)(Flight::request()->query['campagne'] ?? Flight::request()->query['id_inventaire_campagne'] ?? 0);
            if ($campagneId <= 0) {
                Flight::json(['success' => false, 'message' => 'Campagne requise'], 400);
                return;
            }
            $depotId = Flight::request()->query['depot'] ?? Flight::request()->query['id_depot'] ?? null;
            $familleId = Flight::request()->query['famille'] ?? Flight::request()->query['id_article_famille'] ?? null;
            $articleId = Flight::request()->query['article'] ?? Flight::request()->query['id_article'] ?? null;
            $rows = InventoryCountingModel::listSheet(
                $campagneId,
                $depotId ? (int)$depotId : null,
                $familleId ? (int)$familleId : null,
                $articleId ? (int)$articleId : null
            );
            Flight::json(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function exportCountSheetCsv()
    {
        try {
            $campagneId = (int)(Flight::request()->query['campagne'] ?? 0);
            if ($campagneId <= 0) {
                Flight::json(['success' => false, 'message' => 'Campagne requise'], 400);
                return;
            }
            $depotId = Flight::request()->query['depot'] ?? null;
            $familleId = Flight::request()->query['famille'] ?? null;
            $articleId = Flight::request()->query['article'] ?? null;
            $rows = InventoryCountingModel::listSheet(
                $campagneId,
                $depotId ? (int)$depotId : null,
                $familleId ? (int)$familleId : null,
                $articleId ? (int)$articleId : null
            );

            $resp = Flight::response();
            $resp->header('Content-Type', 'text/csv; charset=utf-8');
            $resp->header('Content-Disposition', 'attachment; filename="fiche_comptage.csv"');
            $out = fopen('php://temp', 'r+');
            fputcsv($out, ['Depot', 'Article', 'Famille', 'Quantite_theorique', 'Quantite_physique', 'Ecart', 'Valeur_theorique', 'Valeur_physique', 'Type_ecart']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['depot_nom'] ?? '',
                    ($r['code'] ?? '') . ' ' . ($r['designation'] ?? ''),
                    $r['famille_nom'] ?? '',
                    $r['quantite_theorique'] ?? '',
                    $r['quantite_comptee'] ?? '',
                    $r['ecart'] ?? '',
                    $r['valeur_theorique'] ?? '',
                    $r['valeur_physique'] ?? '',
                    $r['type_ecart'] ?? ''
                ]);
            }
            rewind($out);
            $resp->write(stream_get_contents($out));
            fclose($out);
            $resp->send();
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function exportCountSheetPdf()
    {
        try {
            $campagneId = (int)(Flight::request()->query['campagne'] ?? 0);
            if ($campagneId <= 0) {
                Flight::json(['success' => false, 'message' => 'Campagne requise'], 400);
                return;
            }
            $depotId = Flight::request()->query['depot'] ?? null;
            $familleId = Flight::request()->query['famille'] ?? null;
            $articleId = Flight::request()->query['article'] ?? null;
            $rows = InventoryCountingModel::listSheet(
                $campagneId,
                $depotId ? (int)$depotId : null,
                $familleId ? (int)$familleId : null,
                $articleId ? (int)$articleId : null
            );

            require_once __DIR__ . '/../../../vendor/fpdf186/fpdf.php';
            $pdf = new \FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Fiche de comptage', 0, 1, 'C');
            $pdf->Ln(4);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(35, 8, 'Depot', 1);
            $pdf->Cell(55, 8, 'Article', 1);
            $pdf->Cell(35, 8, 'Famille', 1);
            $pdf->Cell(25, 8, 'Theo.', 1, 0, 'R');
            $pdf->Cell(25, 8, 'Phys.', 1, 0, 'R');
            $pdf->Cell(20, 8, 'Ecart', 1, 0, 'R');
            $pdf->Cell(25, 8, 'Type', 1, 1, 'C');
            $pdf->SetFont('Arial', '', 9);
            foreach ($rows as $r) {
                $pdf->Cell(35, 7, self::pdfText($r['depot_nom'] ?? ''), 1);
                $pdf->Cell(55, 7, self::pdfText(trim(($r['code'] ?? '') . ' ' . ($r['designation'] ?? ''))), 1);
                $pdf->Cell(35, 7, self::pdfText($r['famille_nom'] ?? ''), 1);
                $pdf->Cell(25, 7, isset($r['quantite_theorique']) ? $r['quantite_theorique'] : '', 1, 0, 'R');
                $pdf->Cell(25, 7, isset($r['quantite_comptee']) ? $r['quantite_comptee'] : '', 1, 0, 'R');
                $pdf->Cell(20, 7, isset($r['ecart']) ? $r['ecart'] : '', 1, 0, 'R');
                $pdf->Cell(25, 7, self::pdfText($r['type_ecart'] ?? ''), 1, 1, 'C');
            }

            $resp = Flight::response();
            $resp->header('Content-Type', 'application/pdf');
            $resp->header('Content-Disposition', 'attachment; filename="fiche_comptage.pdf"');
            $resp->write($pdf->Output('S'));
            $resp->send();
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private static function pdfText(string $text): string
    {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
    }

    public static function validateCampaign($id)
    {
        $campagneId = (int)$id;
        if ($campagneId <= 0) {
            Flight::json(['success' => false, 'message' => 'Campagne requise'], 400);
            return;
        }
        try {
            // $userId = self::requireAuth();
            $userId = (int)($_SESSION['user']['id_user'] ?? 0);
            $db = Flight::db();

            // Vérifier la campagne et son statut
            $campStmt = $db->prepare("SELECT id_inventaire_campagne, code, statut FROM inventaire_campagne WHERE id_inventaire_campagne = :id LIMIT 1");
            $campStmt->execute([':id' => $campagneId]);
            $camp = $campStmt->fetch(PDO::FETCH_ASSOC);
            if (!$camp) {
                Flight::json(['success' => false, 'message' => 'Campagne introuvable'], 404);
                return;
            }
            if ($camp['statut'] === 'CLOTURE') {
                Flight::json(['success' => false, 'message' => 'Campagne déjà clôturée'], 400);
                return;
            }

            // Récupérer les types de mouvement inventaire
            $typesStmt = $db->query("SELECT id_type_mouvement_stock, code FROM mouvement_stock_type WHERE code IN ('INVENTAIRE_PLUS','INVENTAIRE_MOINS')");
            $types = [];
            foreach ($typesStmt->fetchAll(PDO::FETCH_ASSOC) as $t) {
                $types[strtoupper($t['code'])] = (int)$t['id_type_mouvement_stock'];
            }
            if (empty($types['INVENTAIRE_PLUS']) || empty($types['INVENTAIRE_MOINS'])) {
                Flight::json(['success' => false, 'message' => 'Types de mouvement INVENTAIRE_PLUS / INVENTAIRE_MOINS manquants'], 500);
                return;
            }

            // Lignes à ajuster (périmètre campagne, derniers comptages)
            $rows = InventoryCountingModel::listSheet($campagneId, null, null, null);
            if (empty($rows)) {
                Flight::json(['success' => false, 'message' => 'Aucune ligne trouvée pour cette campagne'], 400);
                return;
            }

            $db->beginTransaction();
            $model = new MouvStockModel();
            $adjusted = 0;
            foreach ($rows as $r) {
                if (!isset($r['quantite_comptee'])) { continue; }
                $ecart = (float)$r['ecart'];
                if (abs($ecart) < 1e-9) { continue; }

                $isPlus = $ecart > 0;
                $typeId = $isPlus ? $types['INVENTAIRE_PLUS'] : $types['INVENTAIRE_MOINS'];
                $sens = $isPlus ? 1 : 0;
                $qty = abs($ecart);
                $cout = isset($r['cout_moyen']) ? (float)$r['cout_moyen'] : 0.0;

                $mvId = $model->insert([
                    'id_article' => (int)$r['id_article'],
                    'id_depot' => (int)$r['id_depot'],
                    'id_lot' => null,
                    'id_type_mouvement_stock' => $typeId,
                    'id_reference' => $campagneId,
                    'table_reference' => 'inventaire_campagne',
                    'sens' => $sens,
                    'quantite' => $qty,
                    'cout_unitaire' => $cout,
                    'motif' => 'Ajustement inventaire ' . ($camp['code'] ?? ''),
                    'date_mouvement' => date('Y-m-d H:i:s'),
                    'created_by' => $userId,
                ]);

                // Valider et impacter le stock
                if ($isPlus) {
                    StockCourantModel::applyEntry((int)$r['id_article'], (int)$r['id_depot'], $qty, $cout);
                } else {
                    StockCourantModel::applyExitCUMP((int)$r['id_article'], (int)$r['id_depot'], $qty);
                }
                MouvStockModel::updateFields($mvId, [
                    'date_validation' => date('Y-m-d H:i:s'),
                    'cout_unitaire' => $cout,
                ]);
                $st = $db->prepare("INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) VALUES (:id, 'validé', :user)");
                $st->execute([':id' => $mvId, ':user' => $userId]);
                $adjusted++;
            }

            // Clôturer la campagne
            $upd = $db->prepare("UPDATE inventaire_campagne SET statut = 'CLOTURE', updated_by = :u, updated_at = NOW() WHERE id_inventaire_campagne = :id");
            $upd->execute([':u' => $userId, ':id' => $campagneId]);

            $db->commit();
            Flight::json(['success' => true, 'message' => 'Campagne validée', 'ajustements' => $adjusted]);
        } catch (Exception $e) {
            try { Flight::db()->rollBack(); } catch (Exception $ignored) {}
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
