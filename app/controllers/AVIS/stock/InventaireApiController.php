<?php

namespace app\controllers\AVIS\stock;

use app\models\AVIS\stock\InventaireModel;
use app\models\AVIS\stock\InventaireLigneModel;
use Exception;
use Flight;
use PDO;

class InventaireApiController
{
    /* =========================
     * LIST & GET
     * ========================= */

    public static function listInventaires() {
        try {
            $data = InventaireModel::listAll();
            Flight::json(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getInventaire($id) {
        try {
            $inv = InventaireModel::getById((int)$id);
            if (!$inv) {
                Flight::json(['success' => false, 'message' => 'Inventaire introuvable'], 404);
                return;
            }

            $lignes = InventaireLigneModel::getByInventaire((int)$id);

            Flight::json([
                'success' => true,
                'data' => [
                    'inventaire' => $inv,
                    'lignes' => $lignes
                ]
            ]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* =========================
     * CREATE / UPDATE
     * ========================= */

    public static function createInventaire() {
        try {
            $req = json_decode(Flight::request()->getBody(), true) ?? [];

            $payload = [
                'inventaire_numero' => $req['inventaire_numero'] ?? ('INV-' . date('YmdHis')),
                'date_inventaire'   => $req['date_inventaire'] ?? date('Y-m-d'),
                'id_depot'          => (int)($req['id_depot'] ?? 0),
                'commentaire'       => $req['commentaire'] ?? null,
                'cree_par'          => (int)($_SESSION['user']['id_user'] ?? 1)
            ];

            if (!$payload['id_depot']) {
                Flight::json(['success' => false, 'message' => 'Dépôt requis'], 400);
                return;
            }

            $id = InventaireModel::insert($payload);

            Flight::json([
                'success' => true,
                'message' => 'Inventaire créé',
                'id' => $id
            ]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function saveInventaireLignes($id) {
        try {
            $id = (int)$id;
            $req = json_decode(Flight::request()->getBody(), true) ?? [];

            if (!is_array($req)) {
                Flight::json(['success' => false, 'message' => 'Format invalide'], 400);
                return;
            }

            $inv = InventaireModel::getById($id);
            if (!$inv) {
                Flight::json(['success' => false, 'message' => 'Inventaire introuvable'], 404);
                return;
            }

            if ($inv['statut'] !== 'BROUILLON') {
                Flight::json(['success' => false, 'message' => 'Inventaire déjà validé'], 400);
                return;
            }

            InventaireLigneModel::replaceLines($id, $req);

            Flight::json(['success' => true, 'message' => 'Lignes enregistrées']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* =========================
     * VALIDATION
     * ========================= */

    public static function validateInventaire($id) {
        try {
            $id = (int)$id;
            $userId = (int)($_SESSION['user']['id_user'] ?? 1);

            /*
            if (!$userId) {
                Flight::json(['success' => false, 'message' => 'Utilisateur non authentifié'], 401);
                return;
            }
            */

            InventaireModel::valider($id, $userId);

            Flight::json(['success' => true, 'message' => 'Inventaire validé']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* =========================
     * DELETE / CANCEL
     * ========================= */

    public static function cancelInventaire($id) {
        try {
            $id = (int)$id;
            $inv = InventaireModel::getById($id);

            if (!$inv) {
                Flight::json(['success' => false, 'message' => 'Inventaire introuvable'], 404);
                return;
            }

            if ($inv['statut'] === 'VALIDE') {
                Flight::json(['success' => false, 'message' => 'Impossible d’annuler un inventaire validé'], 400);
                return;
            }

            InventaireModel::updateStatut($id, 'ANNULE', (int)($_SESSION['user']['id_user'] ?? 1));

            Flight::json(['success' => true, 'message' => 'Inventaire annulé']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
