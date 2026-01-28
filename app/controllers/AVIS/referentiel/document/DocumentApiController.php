<?php

namespace app\controllers\AVIS\referentiel\document;

use app\models\AVIS\referentiel\document\DocumentTypeModel;
use app\models\AVIS\referentiel\document\DocumentModel;
use app\models\AVIS\referentiel\document\DocumentStatusModel;
use Exception;
use Flight;

class DocumentApiController {

    // ============ DOCUMENT TYPE ============

    public static function getAllDocumentTypes() {
        try {
            $types = DocumentTypeModel::getAll();
            Flight::json(['success' => true, 'data' => $types]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getDocumentTypeById($id_document_type) {
        try {
            $type = DocumentTypeModel::getById($id_document_type);
            if ($type) {
                Flight::json(['success' => true, 'data' => $type]);
            } else {
                Flight::json(['success' => false, 'message' => 'Type de document introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createDocumentType() {
        try {
            $data = Flight::request()->data;
            $model = new DocumentTypeModel();
            $id = $model->insert($data->libelle ?? '');
            Flight::json(['success' => true, 'message' => 'Type de document créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateDocumentType($id_document_type) {
        try {
            $data = Flight::request()->data;
            DocumentTypeModel::update($id_document_type, $data->libelle ?? '');
            Flight::json(['success' => true, 'message' => 'Type de document modifié']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteDocumentType($id_document_type) {
        try {
            DocumentTypeModel::delete($id_document_type);
            Flight::json(['success' => true, 'message' => 'Type de document supprimé']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ DOCUMENT ============

    public static function getAllDocuments() {
        try {
            $documents = DocumentModel::getAll();
            Flight::json(['success' => true, 'data' => $documents]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getDocumentById($id_document) {
        try {
            $document = DocumentModel::getById($id_document);
            if ($document) {
                Flight::json(['success' => true, 'data' => $document]);
            } else {
                Flight::json(['success' => false, 'message' => 'Document introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createDocument() {
        try {
            $data = Flight::request()->data;
            $model = new DocumentModel();
            $id = $model->insert(
                $data->id_document_type ?? '',
                $data->reference ?? '',
                $data->description ?? null,
                $data->path ?? null
            );
            Flight::json(['success' => true, 'message' => 'Document créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateDocument($id_document) {
        try {
            $data = Flight::request()->data;
            DocumentModel::update(
                $id_document,
                $data->id_document_type ?? '',
                $data->reference ?? '',
                $data->description ?? null,
                $data->path ?? null
            );
            Flight::json(['success' => true, 'message' => 'Document modifié']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteDocument($id_document) {
        try {
            DocumentModel::delete($id_document);
            Flight::json(['success' => true, 'message' => 'Document supprimé']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function searchDocument() {
        try {
            $search_term = Flight::request()->query->search ?? '';
            $results = DocumentModel::search($search_term);
            Flight::json(['success' => true, 'data' => $results]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getDocumentByType($id_document_type) {
        try {
            $documents = DocumentModel::getByType($id_document_type);
            Flight::json(['success' => true, 'data' => $documents]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ DOCUMENT STATUS ============

    public static function getAllDocumentStatus() {
        try {
            $status = DocumentStatusModel::getAll();
            Flight::json(['success' => true, 'data' => $status]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getDocumentStatusById($id_document_status) {
        try {
            $status = DocumentStatusModel::getById($id_document_status);
            if ($status) {
                Flight::json(['success' => true, 'data' => $status]);
            } else {
                Flight::json(['success' => false, 'message' => 'Statut document introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
