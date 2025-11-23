<?php
namespace app\models\ressourceHumaine\contratEssai;

use Flight;
use PDO;

class ContratEssaiRenouvellementModel {

    public function insert($id_contrat_essai, $nouvelle_date_fin, $date_renouvellement, $date_fin, $commentaire) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO contrat_essai_renouvellement (id_contrat_essai, nouvelle_date_fin, date_renouvellement, date_fin, commentaire) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_contrat_essai, $nouvelle_date_fin, $date_renouvellement, $date_fin, $commentaire]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM contrat_essai_renouvellement ORDER BY date_renouvellement DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getByContrat($id_contrat_essai) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM contrat_essai_renouvellement WHERE id_contrat_essai = ?");
            $stmt->execute([$id_contrat_essai]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}