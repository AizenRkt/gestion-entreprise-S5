<?php

namespace app\models\ressourceHumaine\employe;
use Flight;
use PDO;

class EmployeStatistiqueModel {

    public static function getEmployesByGenre() {
        $db = Flight::db();
        $stmt = $db->query("SELECT genre, COUNT(*) as count FROM employe GROUP BY genre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEmployesByService() {
        $db = Flight::db();
        $query = "
            SELECT s.nom AS service, COUNT(*) as count
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            JOIN poste p ON es.id_poste = p.id_poste
            JOIN service s ON p.id_service = s.id_service
            WHERE es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
            )
            GROUP BY s.id_service
        ";
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEmployesByDepartement() {
        $db = Flight::db();
        $query = "
            SELECT d.nom AS departement, COUNT(*) as count
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            JOIN poste p ON es.id_poste = p.id_poste
            JOIN service s ON p.id_service = s.id_service
            JOIN departement d ON s.id_dept = d.id_dept
            WHERE es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
            )
            GROUP BY d.id_dept
        ";
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEmployesByPoste() {
        $db = Flight::db();
        $query = "
            SELECT p.titre AS poste, COUNT(*) as count
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            JOIN poste p ON es.id_poste = p.id_poste
            WHERE es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
            )
            GROUP BY p.id_poste
        ";
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEmployesByActivite() {
        $db = Flight::db();
        $query = "
            SELECT CASE WHEN es.activite = 1 THEN 'Actif' ELSE 'Inactif' END as statut, COUNT(*) as count
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            WHERE es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
            )
            GROUP BY es.activite
        ";
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}