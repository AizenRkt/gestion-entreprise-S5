<?php
namespace app\models\AVIS\referentiel\site;
use Flight;
use PDO;
use Exception;

class SiteDepotModel {

    public static function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT sd.*, s.code AS site_code, s.nom AS site_nom, d.code AS depot_code, d.nom AS depot_nom 
                FROM site_depot sd 
                JOIN site s ON sd.id_site = s.id_site 
                JOIN depot d ON sd.id_depot = d.id_depot 
                ORDER BY sd.id_site_depot DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public static function getById($id_site_depot) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT sd.*, s.code AS site_code, s.nom AS site_nom, d.code AS depot_code, d.nom AS depot_nom 
                FROM site_depot sd 
                JOIN site s ON sd.id_site = s.id_site 
                JOIN depot d ON sd.id_depot = d.id_depot 
                WHERE sd.id_site_depot = :id_site_depot
            ");
            $stmt->execute([':id_site_depot' => $id_site_depot]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public static function getByIdSite($id_site) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT sd.*, s.code AS site_code, s.nom AS site_nom, d.code AS depot_code, d.nom AS depot_nom 
                FROM site_depot sd 
                JOIN site s ON sd.id_site = s.id_site 
                JOIN depot d ON sd.id_depot = d.id_depot 
                WHERE sd.id_site = :id_site
            ");
            $stmt->execute([':id_site' => $id_site]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function insert($id_site, $id_depot) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO site_depot (id_site, id_depot) VALUES (:id_site, :id_depot)");
            $stmt->execute([
                ':id_site' => $id_site,
                ':id_depot' => $id_depot
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            throw new Exception("Erreur d'insertion : " . $e->getMessage());
        }
    }

    public static function delete($id_site_depot) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM site_depot WHERE id_site_depot = :id_site_depot");
            $stmt->execute([':id_site_depot' => $id_site_depot]);
            return true;
        } catch (\PDOException $e) {
            throw new Exception("Erreur de suppression : " . $e->getMessage());
        }
    }
}
