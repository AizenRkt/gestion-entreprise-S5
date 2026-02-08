<?php

namespace app\models\AVIS\stock;

use Flight;
use PDO;
use PDOException;

class InventoryCountingModel
{
    public static function listCounts(int $campagneId, ?int $depotId = null): array
    {
        $db = Flight::db();
        $sql = "SELECT ic.*, 
                   a.code AS article_code,
                   a.designation AS article_nom,
                   d.nom AS depot_nom,
                   l.lot_numero
            FROM inventaire_comptage ic
            INNER JOIN article a ON a.id_article = ic.id_article
            INNER JOIN depot d ON d.id_depot = ic.id_depot
            LEFT JOIN lot l ON l.id_lot = ic.id_lot
            WHERE ic.id_inventaire_campagne = :cid";
        $params = [':cid' => $campagneId];
        if ($depotId) {
            $sql .= " AND ic.id_depot = :depot";
            $params[':depot'] = $depotId;
        }
        $sql .= " ORDER BY ic.date_comptage DESC";
        $st = $db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function createCount(array $payload, int $userId): int
    {
        $db = Flight::db();
        if (empty($payload['id_inventaire_campagne']) || empty($payload['id_depot']) || empty($payload['id_article'])) {
            throw new PDOException('Campagne, dépôt et article requis');
        }
        if (!isset($payload['quantite_comptee'])) {
            throw new PDOException('Quantité comptée requise');
        }

        // Campaign existence / status check
        $campSt = $db->prepare("SELECT statut FROM inventaire_campagne WHERE id_inventaire_campagne = :cid");
        $campSt->execute([':cid' => $payload['id_inventaire_campagne']]);
        $camp = $campSt->fetch(PDO::FETCH_ASSOC);
        if (!$camp) {
            throw new PDOException('Campagne introuvable');
        }

        $quantiteTheorique = null;
        $stockSt = $db->prepare("SELECT quantite FROM stock_courant WHERE id_article = :art AND id_depot = :dep");
        $stockSt->execute([
            ':art' => (int)$payload['id_article'],
            ':dep' => (int)$payload['id_depot']
        ]);
        $row = $stockSt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $quantiteTheorique = (float)$row['quantite'];
        }
        $quantiteComptee = (float)$payload['quantite_comptee'];
        $ecart = is_null($quantiteTheorique) ? null : $quantiteComptee - $quantiteTheorique;

        $stmt = $db->prepare("INSERT INTO inventaire_comptage
            (id_inventaire_campagne, id_depot, id_article, id_lot, quantite_theorique, quantite_comptee, ecart, commentaire, created_by)
            VALUES (:cid, :depot, :article, :lot, :qtheo, :qcomptee, :ecart, :commentaire, :user)");
        $stmt->execute([
            ':cid' => (int)$payload['id_inventaire_campagne'],
            ':depot' => (int)$payload['id_depot'],
            ':article' => (int)$payload['id_article'],
            ':lot' => $payload['id_lot'] ?? null,
            ':qtheo' => $quantiteTheorique,
            ':qcomptee' => $quantiteComptee,
            ':ecart' => $ecart,
            ':commentaire' => $payload['commentaire'] ?? null,
            ':user' => $userId,
        ]);

        return (int)$db->lastInsertId();
    }

    public static function saveCount(array $payload, int $userId): int
    {
        $db = Flight::db();
        if (empty($payload['id_inventaire_campagne']) || empty($payload['id_depot']) || empty($payload['id_article'])) {
            throw new PDOException('Campagne, dépôt et article requis');
        }
        if (!isset($payload['quantite_comptee'])) {
            throw new PDOException('Quantité comptée requise');
        }

        $campId = (int)$payload['id_inventaire_campagne'];
        $depotId = (int)$payload['id_depot'];
        $articleId = (int)$payload['id_article'];
        $lotId = $payload['id_lot'] ?? null;

        // théorique
        $quantiteTheorique = null;
        $stockSt = $db->prepare("SELECT quantite FROM stock_courant WHERE id_article = :art AND id_depot = :dep");
        $stockSt->execute([':art' => $articleId, ':dep' => $depotId]);
        $row = $stockSt->fetch(PDO::FETCH_ASSOC);
        if ($row) { $quantiteTheorique = (float)$row['quantite']; }

        $quantiteComptee = (float)$payload['quantite_comptee'];
        $ecart = is_null($quantiteTheorique) ? null : $quantiteComptee - $quantiteTheorique;

        // upsert sur clé unique
        $existing = $db->prepare("SELECT id_inventaire_comptage FROM inventaire_comptage WHERE id_inventaire_campagne = :c AND id_depot = :d AND id_article = :a AND ((id_lot IS NULL AND :lot IS NULL) OR id_lot = :lot)");
        $existing->execute([':c' => $campId, ':d' => $depotId, ':a' => $articleId, ':lot' => $lotId]);
        $rowExist = $existing->fetch(PDO::FETCH_ASSOC);
        if ($rowExist) {
            $stmt = $db->prepare("UPDATE inventaire_comptage
                SET quantite_theorique = :qt, quantite_comptee = :qc, ecart = :ecart, commentaire = :commentaire, date_comptage = NOW(), created_by = :u
                WHERE id_inventaire_comptage = :id");
            $stmt->execute([
                ':qt' => $quantiteTheorique,
                ':qc' => $quantiteComptee,
                ':ecart' => $ecart,
                ':commentaire' => $payload['commentaire'] ?? null,
                ':u' => $userId,
                ':id' => (int)$rowExist['id_inventaire_comptage']
            ]);
            return (int)$rowExist['id_inventaire_comptage'];
        }

        $stmt = $db->prepare("INSERT INTO inventaire_comptage
            (id_inventaire_campagne, id_depot, id_article, id_lot, quantite_theorique, quantite_comptee, ecart, commentaire, created_by)
            VALUES (:cid, :depot, :article, :lot, :qtheo, :qcomptee, :ecart, :commentaire, :user)");
        $stmt->execute([
            ':cid' => $campId,
            ':depot' => $depotId,
            ':article' => $articleId,
            ':lot' => $lotId,
            ':qtheo' => $quantiteTheorique,
            ':qcomptee' => $quantiteComptee,
            ':ecart' => $ecart,
            ':commentaire' => $payload['commentaire'] ?? null,
            ':user' => $userId,
        ]);
        return (int)$db->lastInsertId();
    }

    public static function listSheet(int $campagneId, ?int $depotId = null, ?int $familleId = null, ?int $articleId = null): array
    {
        $db = Flight::db();

        // Depots concernés par la campagne
        $depotsStmt = $db->prepare("SELECT id_depot FROM inventaire_campagne_depot WHERE id_inventaire_campagne = :cid");
        $depotsStmt->execute([':cid' => $campagneId]);
        $depots = array_map('intval', array_column($depotsStmt->fetchAll(PDO::FETCH_ASSOC), 'id_depot'));
        if (empty($depots)) {
            return [];
        }
        if ($depotId !== null) {
            if (!in_array($depotId, $depots, true)) { return []; }
            $depots = [$depotId];
        }

        // Périmètre
        $scopeStmt = $db->prepare("SELECT type_cible, id_article, id_article_famille FROM inventaire_campagne_cible WHERE id_inventaire_campagne = :cid");
        $scopeStmt->execute([':cid' => $campagneId]);
        $scopes = $scopeStmt->fetchAll(PDO::FETCH_ASSOC);
        $scopeAll = empty($scopes);
        $scopeArticles = [];
        $scopeFamilles = [];
        foreach ($scopes as $s) {
            if ($s['type_cible'] === 'TOUS') { $scopeAll = true; }
            if ($s['type_cible'] === 'ARTICLE' && $s['id_article']) { $scopeArticles[] = (int)$s['id_article']; }
            if ($s['type_cible'] === 'FAMILLE' && $s['id_article_famille']) { $scopeFamilles[] = (int)$s['id_article_famille']; }
        }

        // Filtres explicites côté UI qui priment
        if ($articleId !== null) {
            $scopeAll = false;
            $scopeArticles = [$articleId];
            $scopeFamilles = [];
        } elseif ($familleId !== null) {
            $scopeAll = false;
            $scopeFamilles = [$familleId];
            $scopeArticles = [];
        }

        // Construction SQL
        $clauses = [];
        $params = [];

        $depPlaceholders = implode(',', array_fill(0, count($depots), '?'));
        $clauses[] = "st.id_depot IN ($depPlaceholders)";
        $params = array_merge($params, $depots);

        if (!$scopeAll) {
            $orParts = [];
            if (!empty($scopeArticles)) {
                $ph = implode(',', array_fill(0, count($scopeArticles), '?'));
                $orParts[] = "a.id_article IN ($ph)";
                $params = array_merge($params, $scopeArticles);
            }
            if (!empty($scopeFamilles)) {
                $ph = implode(',', array_fill(0, count($scopeFamilles), '?'));
                $orParts[] = "a.id_famille_article_famille IN ($ph)";
                $params = array_merge($params, $scopeFamilles);
            }
            if (empty($orParts)) {
                return [];
            }
            $clauses[] = '(' . implode(' OR ', $orParts) . ')';
        }

        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';

        $sql = "SELECT st.id_depot, d.nom AS depot_nom,
                       st.id_article, a.code, a.designation,
                       af.nom AS famille_nom,
                       st.quantite AS quantite_theorique,
                       st.cout_moyen,
                       ic.quantite_comptee,
                       (ic.quantite_comptee - st.quantite) AS ecart,
                       (st.quantite * st.cout_moyen) AS valeur_theorique,
                       (ic.quantite_comptee * st.cout_moyen) AS valeur_physique,
                       CASE WHEN ic.quantite_comptee IS NULL THEN NULL
                            WHEN (ic.quantite_comptee - st.quantite) >= 0 THEN 'SURPLUS'
                            ELSE 'PERTE' END AS type_ecart,
                       ic.date_comptage
                FROM stock_courant st
                INNER JOIN depot d ON d.id_depot = st.id_depot
                INNER JOIN article a ON a.id_article = st.id_article
                LEFT JOIN article_famille af ON af.id_article_famille = a.id_famille_article_famille
                LEFT JOIN (
                    SELECT t1.id_depot, t1.id_article, t1.date_comptage, t1.quantite_comptee
                    FROM inventaire_comptage t1
                    INNER JOIN (
                        SELECT id_depot, id_article, MAX(date_comptage) AS max_date
                        FROM inventaire_comptage
                        WHERE id_inventaire_campagne = ?
                        GROUP BY id_depot, id_article
                    ) t2 ON t2.id_depot = t1.id_depot AND t2.id_article = t1.id_article AND t2.max_date = t1.date_comptage
                    WHERE t1.id_inventaire_campagne = ?
                ) ic ON ic.id_depot = st.id_depot AND ic.id_article = st.id_article
                $where
                ORDER BY d.nom, a.designation";

        $st = $db->prepare($sql);
        // add campagneId twice for the subquery parameters
        $execParams = array_merge([$campagneId, $campagneId], $params);
        $st->execute($execParams);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}
