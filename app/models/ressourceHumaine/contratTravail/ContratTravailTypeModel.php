<?php
namespace app\models\ressourceHumaine\contratTravail;

use Flight;
use PDO;

class ContratTravailTypeModel {

    public function insert($titre, $duree_min, $duree_max, $renouvelable, $max_duree_renouvellement, $max_nb_renouvellement) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO contrat_travail_type 
                (titre, duree_min, duree_max, renouvelable, max_duree_renouvellement, max_nb_renouvellement)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$titre, $duree_min, $duree_max, $renouvelable, $max_duree_renouvellement, $max_nb_renouvellement]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM contrat_travail_type ORDER BY titre ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM contrat_travail_type WHERE id_type_contrat = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getByTitre($titre) {
            try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM contrat_travail_type WHERE titre = ?");
            $stmt->execute([$titre]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }
}
