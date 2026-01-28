<?php
namespace app\models\AVIS\referentiel\valorisation;
use Flight;
use PDO;
use Exception;

class MethodeValorisationModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM methode_valorisation ORDER BY id_methode_valorisation DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_methode_valorisation) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM methode_valorisation WHERE id_methode_valorisation = :id_methode_valorisation");
            $stmt->execute([':id_methode_valorisation' => $id_methode_valorisation]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function getByCode($code) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM methode_valorisation WHERE code = :code");
            $stmt->execute([':code' => $code]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($code, $libelle) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO methode_valorisation (code, libelle) VALUES (:code, :libelle)");
            $stmt->execute([
                ':code' => $code,
                ':libelle' => $libelle
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_methode_valorisation, $code, $libelle) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE methode_valorisation SET code = :code, libelle = :libelle WHERE id_methode_valorisation = :id_methode_valorisation");
            $stmt->execute([
                ':code' => $code,
                ':libelle' => $libelle,
                ':id_methode_valorisation' => $id_methode_valorisation
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_methode_valorisation) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM methode_valorisation WHERE id_methode_valorisation = :id_methode_valorisation");
            $stmt->execute([':id_methode_valorisation' => $id_methode_valorisation]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }
}
