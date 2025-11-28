<?php

namespace app\controllers\ressourceHumaine\document;

use app\models\ressourceHumaine\document\DocumentModel;

use Flight;
use DateTime;

class DocumentController
{
    public function getDocumentsEmploye($id)
    {

        if (!$id) {
            Flight::json([
                "success" => false,
                "message" => "Paramètre id_employe manquant"
            ], 400);
            return;
        }

        $documents = DocumentModel::getEmployeDocuments($id);

        if (empty($documents)) {
            Flight::json([
                "success" => true,
                "message" => "l'employe n'a pas encore de documents"
            ]);
            return;

        }

        if ($documents === null) {
            Flight::json([
                "success" => false,
                "message" => "Erreur lors de la récupération des documents"
            ], 500);
            return;
        }

        Flight::json([
            "success" => true,
            "data" => $documents
        ]);
    }

    public function createDocumentAvecStatut($id)
    {
        $id_type_document = $_POST['id_type_document'] ?? null;
        $id_employe       = $id;
        $titre            = $_POST['titre'] ?? null;
        $statut           = $_POST['statut'] ?? 'valide';
        $commentaire      = $_POST['commentaire'] ?? null;
        $pathScan         = $_POST['pathScan'] ?? null;
        $date_expiration  = $_POST['date_expiration'] ?? null;

        if (!$id_type_document || !$id_employe || !$titre) {
            Flight::json([
                "success" => false,
                "message" => "Paramètres manquants : id_type_document, id_employe, titre requis."
            ], 400);
            return;
        }


        $id_document = DocumentModel::insertDocument(
            $id_type_document,
            $id_employe,
            $titre,
            $pathScan,
            $date_expiration
        );

        if ($id_document === null) {
            Flight::json([
                "success" => false,
                "message" => "Erreur lors de l'insertion du document."
            ], 500);
            return;
        }

        $insertStatut = DocumentModel::insertStatut(
            $id_document,
            $statut,
            $commentaire
        );

        if ($insertStatut === null) {
            Flight::json([
                "success" => false,
                "message" => "Document créé, mais échec lors de l'insertion du statut."
            ], 500);
            return;
        }

        Flight::json([
            "success" => true,
            "message" => "Document et statut créés avec succès.",
            "data" => [
                "id_document" => $id_document,
                "statut" => $statut
            ]
        ]);
    }

}