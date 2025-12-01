<?php
namespace app\models\ressourceHumaine\poste;
use Flight;
use PDO;
use Exception;

class PosteModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM poste WHERE titre != 'essaie'");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM poste WHERE id_poste = $id");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
