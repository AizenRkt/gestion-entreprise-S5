<?php

namespace app\controllers\AVIS\referentiel\valorisation;

use app\models\AVIS\referentiel\valorisation\MethodeValorisationModel;
use Exception;
use Flight;

class ValorisationApiController {

    // ============ METHODE VALORISATION ============

    public static function getAllMethodes() {
        try {
            $methodes = MethodeValorisationModel::getAll();
            Flight::json(['success' => true, 'data' => $methodes]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getMethodeById($id_methode_valorisation) {
        try {
            $methode = MethodeValorisationModel::getById($id_methode_valorisation);
            if ($methode) {
                Flight::json(['success' => true, 'data' => $methode]);
            } else {
                Flight::json(['success' => false, 'message' => 'Méthode de valorisation introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getMethodeByCode($code) {
        try {
            $methode = MethodeValorisationModel::getByCode($code);
            if ($methode) {
                Flight::json(['success' => true, 'data' => $methode]);
            } else {
                Flight::json(['success' => false, 'message' => 'Méthode de valorisation introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createMethode() {
        try {
            $data = Flight::request()->data;
            $model = new MethodeValorisationModel();
            $id = $model->insert(
                $data->code ?? '',
                $data->libelle ?? ''
            );
            Flight::json(['success' => true, 'message' => 'Méthode de valorisation créée', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateMethode($id_methode_valorisation) {
        try {
            $data = Flight::request()->data;
            MethodeValorisationModel::update(
                $id_methode_valorisation,
                $data->code ?? '',
                $data->libelle ?? ''
            );
            Flight::json(['success' => true, 'message' => 'Méthode de valorisation modifiée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteMethode($id_methode_valorisation) {
        try {
            MethodeValorisationModel::delete($id_methode_valorisation);
            Flight::json(['success' => true, 'message' => 'Méthode de valorisation supprimée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
