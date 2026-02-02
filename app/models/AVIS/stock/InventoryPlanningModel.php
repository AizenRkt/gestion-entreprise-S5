<?php

namespace app\models\AVIS\stock;

use Flight;
use PDO;
use PDOException;

class InventoryPlanningModel
{
    public static function listCampaigns(): array
    {
        $db = Flight::db();
        $sql = "SELECT c.*, 
                COUNT(DISTINCT d.id_inventaire_campagne_depot) AS depots_count,
                COUNT(DISTINCT sc.id_inventaire_campagne_cible) AS scopes_count,
                COUNT(DISTINCT e.id_inventaire_equipe) AS equipes_count
            FROM inventaire_campagne c
            LEFT JOIN inventaire_campagne_depot d ON d.id_inventaire_campagne = c.id_inventaire_campagne
            LEFT JOIN inventaire_campagne_cible sc ON sc.id_inventaire_campagne = c.id_inventaire_campagne
            LEFT JOIN inventaire_equipe e ON e.id_inventaire_campagne = c.id_inventaire_campagne
            GROUP BY c.id_inventaire_campagne
            ORDER BY c.id_inventaire_campagne DESC";
        $st = $db->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getCampaign(int $id): ?array
    {
        $db = Flight::db();
        $campSt = $db->prepare("SELECT * FROM inventaire_campagne WHERE id_inventaire_campagne = :id");
        $campSt->execute([':id' => $id]);
        $campaign = $campSt->fetch(PDO::FETCH_ASSOC);
        if (!$campaign) { return null; }

        $depotsSt = $db->prepare("SELECT * FROM inventaire_campagne_depot WHERE id_inventaire_campagne = :id");
        $depotsSt->execute([':id' => $id]);
        $campaign['depots'] = $depotsSt->fetchAll(PDO::FETCH_ASSOC);

        $scopeSt = $db->prepare("SELECT * FROM inventaire_campagne_cible WHERE id_inventaire_campagne = :id");
        $scopeSt->execute([':id' => $id]);
        $campaign['cibles'] = $scopeSt->fetchAll(PDO::FETCH_ASSOC);

        $teamSt = $db->prepare("SELECT * FROM inventaire_equipe WHERE id_inventaire_campagne = :id");
        $teamSt->execute([':id' => $id]);
        $teams = $teamSt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($teams)) {
            $teamIds = array_column($teams, 'id_inventaire_equipe');
            $in = implode(',', array_fill(0, count($teamIds), '?'));
            $memSt = $db->prepare("SELECT * FROM inventaire_equipe_membre WHERE id_inventaire_equipe IN ($in)");
            $memSt->execute($teamIds);
            $members = $memSt->fetchAll(PDO::FETCH_ASSOC);
            $grouped = [];
            foreach ($members as $m) {
                $grouped[$m['id_inventaire_equipe']][] = $m;
            }
            foreach ($teams as &$t) {
                $t['membres'] = $grouped[$t['id_inventaire_equipe']] ?? [];
            }
        }
        $campaign['equipes'] = $teams;
        return $campaign;
    }

    public static function createCampaign(array $payload, int $userId): int
    {
        $db = Flight::db();
        $db->beginTransaction();
        try {
            $campStmt = $db->prepare("INSERT INTO inventaire_campagne
                (code, libelle, description, type_campagne, statut, date_planification, date_debut_prevue, date_fin_prevue, created_by, updated_by)
                VALUES (:code, :libelle, :description, :type_campagne, :statut, NOW(), :date_debut, :date_fin, :created_by, :updated_by)");
            $campStmt->execute([
                ':code' => $payload['code'] ?? null,
                ':libelle' => $payload['libelle'] ?? null,
                ':description' => $payload['description'] ?? null,
                ':type_campagne' => $payload['type_campagne'] ?? 'GENERAL',
                ':statut' => $payload['statut'] ?? 'PLANIFIE',
                ':date_debut' => $payload['date_debut_prevue'] ?? null,
                ':date_fin' => $payload['date_fin_prevue'] ?? null,
                ':created_by' => $userId,
                ':updated_by' => $userId
            ]);
            $campId = (int)$db->lastInsertId();

            if (!empty($payload['depots']) && is_array($payload['depots'])) {
                $depStmt = $db->prepare("INSERT INTO inventaire_campagne_depot (id_inventaire_campagne, id_depot, id_site, zone, commentaire)
                    VALUES (:cid, :depot, :site, :zone, :commentaire)");
                foreach ($payload['depots'] as $d) {
                    if (empty($d['id_depot'])) { continue; }
                    $depStmt->execute([
                        ':cid' => $campId,
                        ':depot' => (int)$d['id_depot'],
                        ':site' => $d['id_site'] ?? null,
                        ':zone' => $d['zone'] ?? null,
                        ':commentaire' => $d['commentaire'] ?? null,
                    ]);
                }
            }

            if (!empty($payload['cibles']) && is_array($payload['cibles'])) {
                $cibleStmt = $db->prepare("INSERT INTO inventaire_campagne_cible (id_inventaire_campagne, type_cible, id_article, id_article_famille, inclure_lots, commentaire)
                    VALUES (:cid, :type, :article, :famille, :inclure_lots, :commentaire)");
                foreach ($payload['cibles'] as $c) {
                    $cibleStmt->execute([
                        ':cid' => $campId,
                        ':type' => $c['type_cible'] ?? 'TOUS',
                        ':article' => $c['id_article'] ?? null,
                        ':famille' => $c['id_article_famille'] ?? null,
                        ':inclure_lots' => isset($c['inclure_lots']) ? (int)(bool)$c['inclure_lots'] : 1,
                        ':commentaire' => $c['commentaire'] ?? null,
                    ]);
                }
            }

            if (!empty($payload['equipes']) && is_array($payload['equipes'])) {
                $teamStmt = $db->prepare("INSERT INTO inventaire_equipe (id_inventaire_campagne, nom_equipe, id_responsable, commentaire)
                    VALUES (:cid, :nom, :resp, :commentaire)");
                $memberStmt = $db->prepare("INSERT INTO inventaire_equipe_membre (id_inventaire_equipe, id_employe, role_membre)
                    VALUES (:tid, :emp, :role)");
                foreach ($payload['equipes'] as $team) {
                    if (empty($team['nom_equipe'])) { continue; }
                    $teamStmt->execute([
                        ':cid' => $campId,
                        ':nom' => $team['nom_equipe'],
                        ':resp' => $team['id_responsable'] ?? null,
                        ':commentaire' => $team['commentaire'] ?? null,
                    ]);
                    $teamId = (int)$db->lastInsertId();
                    if (!empty($team['membres']) && is_array($team['membres'])) {
                        foreach ($team['membres'] as $m) {
                            if (empty($m['id_employe'])) { continue; }
                            $memberStmt->execute([
                                ':tid' => $teamId,
                                ':emp' => (int)$m['id_employe'],
                                ':role' => $m['role_membre'] ?? 'COMPTEUR',
                            ]);
                        }
                    }
                }
            }

            $db->commit();
            return $campId;
        } catch (PDOException $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
