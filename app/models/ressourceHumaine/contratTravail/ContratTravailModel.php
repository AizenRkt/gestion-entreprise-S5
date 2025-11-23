<?php
namespace app\models\ressourceHumaine\contratTravail;

use Flight;
use PDO;

class ContratTravailModel {

    public function insert($id_type_contrat, $id_employe, $debut, $fin = null, $salaire_base = null, $date_signature = null, $id_poste = null, $pathPdf = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO contrat_travail 
                (id_type_contrat, id_employe, debut, fin, salaire_base, date_signature, id_poste, pathPdf)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_type_contrat, $id_employe, $debut, $fin, $salaire_base, $date_signature, $id_poste, $pathPdf]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM contrat_travail ORDER BY debut DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM contrat_travail WHERE id_contrat_travail = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAllWithTypeAndEmploye() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT ct.*, ctt.titre AS type_contrat, e.nom, e.prenom, e.email
                FROM contrat_travail ct
                JOIN contrat_travail_type ctt ON ct.id_type_contrat = ctt.id_type_contrat
                JOIN employe e ON ct.id_employe = e.id_employe
                ORDER BY ct.debut DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
