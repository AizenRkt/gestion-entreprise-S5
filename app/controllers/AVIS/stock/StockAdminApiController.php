<?php

namespace app\controllers\AVIS\stock;

use Flight;
use Exception;

class StockAdminApiController
{
    public static function listArticlesDefaults()
    {
        try {
            $db = Flight::db();
            $sql = "SELECT a.id_article, a.code, a.designation, a.id_methode_valorisation, mv.code AS methode_code, a.allocation_defaut
                    FROM article a
                    LEFT JOIN methode_valorisation mv ON mv.id_methode_valorisation = a.id_methode_valorisation
                    ORDER BY a.designation";
            $rows = $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            Flight::json(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function listMethods()
    {
        try {
            $db = Flight::db();
            $rows = $db->query("SELECT id_methode_valorisation, code, libelle FROM methode_valorisation ORDER BY id_methode_valorisation")->fetchAll(\PDO::FETCH_ASSOC);
            Flight::json(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateArticleDefaults($id)
    {
        try {
            $id = (int)$id;
            $req = Flight::request();
            $id_methode = isset($req->data->id_methode_valorisation) ? (int)$req->data->id_methode_valorisation : null;
            $allocation = isset($req->data->allocation_defaut) ? strtolower(trim($req->data->allocation_defaut)) : null;

            if ($id_methode === null && $allocation === null) {
                Flight::json(['success' => false, 'message' => 'Aucune mise à jour fournie'], 400);
                return;
            }

            $db = Flight::db();
            $sets = [];
            $params = [':id' => $id];
            if ($id_methode !== null) { $sets[] = 'id_methode_valorisation = :idm'; $params[':idm'] = $id_methode; }
            if ($allocation !== null) {
                if (!in_array($allocation, ['fifo','lifo','fefo'], true)) {
                    Flight::json(['success' => false, 'message' => 'Allocation invalide'], 400); return;
                }
                $sets[] = 'allocation_defaut = :alloc'; $params[':alloc'] = $allocation;
            }
            $sql = 'UPDATE article SET ' . implode(', ', $sets) . ' WHERE id_article = :id';
            $st = $db->prepare($sql);
            $st->execute($params);
            Flight::json(['success' => true, 'message' => 'Mise à jour effectuée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getClosureStatus()
    {
        try {
            $db = Flight::db();
            $rows = $db->query("SELECT id_stock_cloture_periode, annee, mois, statut, date_cloture FROM stock_cloture_periode ORDER BY annee DESC, mois DESC")->fetchAll(\PDO::FETCH_ASSOC);
            Flight::json(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function openClosure()
    {
        try {
            $req = Flight::request();
            $year = (int)($req->data->annee ?? $req->data->year ?? date('Y'));
            $month = (int)($req->data->mois ?? $req->data->month ?? date('n'));

            $db = Flight::db();
            $db->beginTransaction();
            $exists = $db->prepare("SELECT statut FROM stock_cloture_periode WHERE annee=:y AND mois=:m LIMIT 1");
            $exists->execute([':y' => $year, ':m' => $month]);
            $row = $exists->fetch(\PDO::FETCH_ASSOC);
            if ($row) {
                Flight::json(['success' => false, 'message' => 'Période déjà existante (' . $row['statut'] . ')'], 400); $db->rollBack(); return;
            }
            $ins = $db->prepare("INSERT INTO stock_cloture_periode(annee, mois, statut) VALUES (:y, :m, 'OUVERT')");
            $ins->execute([':y' => $year, ':m' => $month]);
            $periodeId = (int)$db->lastInsertId();

            // snapshot opening from stock_courant
            $cur = $db->query("SELECT id_article, id_depot, quantite, valeur_stock, cout_moyen FROM stock_courant")->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($cur)) {
                $insDet = $db->prepare("INSERT INTO stock_cloture_detail(id_stock_cloture_periode, id_article, id_depot, qty_ouverture, valeur_ouverture, cump_ouverture)
                                         VALUES (:pid, :art, :dep, :q, :v, :c)");
                foreach ($cur as $r) {
                    $insDet->execute([
                        ':pid' => $periodeId,
                        ':art' => (int)$r['id_article'],
                        ':dep' => (int)$r['id_depot'],
                        ':q' => (float)$r['quantite'],
                        ':v' => (float)$r['valeur_stock'],
                        ':c' => $r['cout_moyen'] !== null ? (float)$r['cout_moyen'] : null,
                    ]);
                }
            }
            $db->commit();
            Flight::json(['success' => true, 'message' => 'Période ouverte', 'periode_id' => $periodeId]);
        } catch (Exception $e) {
            try { Flight::db()->rollBack(); } catch (Exception $ignored) {}
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function closeClosure()
    {
        try {
            $req = Flight::request();
            $year = (int)($req->data->annee ?? $req->data->year ?? date('Y'));
            $month = (int)($req->data->mois ?? $req->data->month ?? date('n'));

            $db = Flight::db();
            $db->beginTransaction();
            $get = $db->prepare("SELECT id_stock_cloture_periode, statut FROM stock_cloture_periode WHERE annee=:y AND mois=:m LIMIT 1");
            $get->execute([':y' => $year, ':m' => $month]);
            $p = $get->fetch(\PDO::FETCH_ASSOC);
            if (!$p) { Flight::json(['success' => false, 'message' => 'Période introuvable'], 404); $db->rollBack(); return; }
            if ($p['statut'] === 'CLOTURE') { Flight::json(['success' => false, 'message' => 'Déjà clôturée'], 400); $db->rollBack(); return; }

            // update closing values from stock_courant
            $cur = $db->query("SELECT id_article, id_depot, quantite, valeur_stock, cout_moyen FROM stock_courant")->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($cur as $r) {
                // if detail exists, update closing; else insert with closing only
                $chk = $db->prepare("SELECT id_stock_cloture_detail FROM stock_cloture_detail WHERE id_stock_cloture_periode = :pid AND id_article = :art AND id_depot = :dep LIMIT 1");
                $chk->execute([':pid' => (int)$p['id_stock_cloture_periode'], ':art' => (int)$r['id_article'], ':dep' => (int)$r['id_depot']]);
                $d = $chk->fetch(\PDO::FETCH_ASSOC);
                if ($d) {
                    $upd = $db->prepare("UPDATE stock_cloture_detail SET qty_cloture = :q, valeur_cloture = :v, cump_cloture = :c WHERE id_stock_cloture_detail = :id");
                    $upd->execute([':q' => (float)$r['quantite'], ':v' => (float)$r['valeur_stock'], ':c' => $r['cout_moyen'] !== null ? (float)$r['cout_moyen'] : null, ':id' => (int)$d['id_stock_cloture_detail']]);
                } else {
                    $ins = $db->prepare("INSERT INTO stock_cloture_detail(id_stock_cloture_periode, id_article, id_depot, qty_cloture, valeur_cloture, cump_cloture)
                                          VALUES (:pid, :art, :dep, :q, :v, :c)");
                    $ins->execute([':pid' => (int)$p['id_stock_cloture_periode'], ':art' => (int)$r['id_article'], ':dep' => (int)$r['id_depot'], ':q' => (float)$r['quantite'], ':v' => (float)$r['valeur_stock'], ':c' => $r['cout_moyen'] !== null ? (float)$r['cout_moyen'] : null]);
                }
            }

            $updP = $db->prepare("UPDATE stock_cloture_periode SET statut='CLOTURE', date_cloture = NOW() WHERE id_stock_cloture_periode = :id");
            $updP->execute([':id' => (int)$p['id_stock_cloture_periode']]);
            $db->commit();
            Flight::json(['success' => true, 'message' => 'Période clôturée']);
        } catch (Exception $e) {
            try { Flight::db()->rollBack(); } catch (Exception $ignored) {}
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
