<?php
namespace app\models\AVIS\referentiel\site;
use Flight;
use PDO;
use Exception;

class SiteModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM site ORDER BY id_site DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_site) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM site WHERE id_site = :id_site");
            $stmt->execute([':id_site' => $id_site]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function getByCode($code) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM site WHERE code = :code");
            $stmt->execute([':code' => $code]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function insert($code, $nom = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO site (code, nom) VALUES (:code, :nom)");
            $stmt->execute([
                ':code' => $code,
                ':nom' => $nom
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function update($id_site, $code, $nom = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE site SET code = :code, nom = :nom WHERE id_site = :id_site");
            $stmt->execute([
                ':code' => $code,
                ':nom' => $nom,
                ':id_site' => $id_site
            ]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de mise à jour : " . $e->getMessage());
        }
    }

    public static function delete($id_site) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM site WHERE id_site = :id_site");
            $stmt->execute([':id_site' => $id_site]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }

    public static function getSiteWithDepots($id_site) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT s.*, d.id_depot, d.code, d.nom 
                FROM site s 
                LEFT JOIN site_depot sd ON s.id_site = sd.id_site 
                LEFT JOIN depot d ON sd.id_depot = d.id_depot 
                WHERE s.id_site = :id_site
            ");
            $stmt->execute([':id_site' => $id_site]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
}
