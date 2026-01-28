<?php

namespace app\controllers\AVIS\referentiel\client;

use app\models\AVIS\referentiel\client\ClientModel;
use app\models\AVIS\referentiel\client\ClientTypeModel;
use Exception;
use Flight;

class ClientApiController {

    // ============ CLIENT TYPE ============

    public static function getAllClientTypes() {
        try {
            $types = ClientTypeModel::getAll();
            Flight::json(['success' => true, 'data' => $types]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getClientTypeById($id_client_type) {
        try {
            $type = ClientTypeModel::getById($id_client_type);
            if ($type) {
                Flight::json(['success' => true, 'data' => $type]);
            } else {
                Flight::json(['success' => false, 'message' => 'Type de client introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createClientType() {
        try {
            $data = Flight::request()->data;
            $model = new ClientTypeModel();
            $id = $model->insert($data->libelle ?? '');
            Flight::json(['success' => true, 'message' => 'Type de client créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateClientType($id_client_type) {
        try {
            $data = Flight::request()->data;
            ClientTypeModel::update($id_client_type, $data->libelle ?? '');
            Flight::json(['success' => true, 'message' => 'Type de client modifié']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteClientType($id_client_type) {
        try {
            ClientTypeModel::delete($id_client_type);
            Flight::json(['success' => true, 'message' => 'Type de client supprimé']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ CLIENT ============

    public static function getAllClients() {
        try {
            $clients = ClientModel::getAll();
            Flight::json(['success' => true, 'data' => $clients]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getClientById($id_client) {
        try {
            $client = ClientModel::getById($id_client);
            if ($client) {
                Flight::json(['success' => true, 'data' => $client]);
            } else {
                Flight::json(['success' => false, 'message' => 'Client introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createClient() {
        try {
            $data = Flight::request()->data;
            $model = new ClientModel();
            $id = $model->insert(
                $data->nom ?? '',
                $data->telephone ?? null,
                $data->email ?? null,
                $data->adresse ?? null,
                $data->id_type ?? null
            );
            Flight::json(['success' => true, 'message' => 'Client créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateClient($id_client) {
        try {
            $data = Flight::request()->data;
            ClientModel::update(
                $id_client,
                $data->nom ?? '',
                $data->telephone ?? null,
                $data->email ?? null,
                $data->adresse ?? null,
                $data->id_type ?? null
            );
            Flight::json(['success' => true, 'message' => 'Client modifié']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteClient($id_client) {
        try {
            ClientModel::delete($id_client);
            Flight::json(['success' => true, 'message' => 'Client supprimé']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function searchClient() {
        try {
            $search_term = Flight::request()->query->search ?? '';
            $results = ClientModel::search($search_term);
            Flight::json(['success' => true, 'data' => $results]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
