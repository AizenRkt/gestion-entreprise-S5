<?php
namespace app\models\ressourceHumaine\contratEssai;

use Flight;
use PDO;

class ContratEssaiStatutModel {

    public function insert($id_contrat_essai, $statut, $date_statut, $commentaire) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO contrat_essai_statut (id_contrat_essai, statut, date_statut, commentaire) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_contrat_essai, $statut, $date_statut, $commentaire]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM contrat_essai_statut ORDER BY date_statut DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getByContrat($id_contrat_essai) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM contrat_essai_statut WHERE id_contrat_essai = ?");
            $stmt->execute([$id_contrat_essai]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}