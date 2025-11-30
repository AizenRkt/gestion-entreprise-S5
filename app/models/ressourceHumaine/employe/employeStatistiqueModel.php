<?php

namespace app\models\ressourceHumaine\employe;
use Flight;
use PDO;

class EmployeStatistiqueModel {

    public static function getEmployesByGenre($month = null, $year = null) {
        $db = Flight::db();
        $query = "
            SELECT e.genre, COUNT(*) as count
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            WHERE es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
        ";
        $params = [];
        if ($year) {
            if ($month) {
                $endDate = date('Y-m-t', strtotime("$year-$month-01"));
            } else {
                $endDate = "$year-12-31";
            }
            $query .= " AND es2.date_modification <= ?";
            $params[] = $endDate;
        }
        $query .= "
            ) AND es.activite = 1
            GROUP BY e.genre";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEmployesByService($month = null, $year = null) {
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
        ";
        $params = [];
        if ($year) {
            if ($month) {
                $endDate = date('Y-m-t', strtotime("$year-$month-01"));
            } else {
                $endDate = "$year-12-31";
            }
            $query .= " AND es2.date_modification <= ?";
            $params[] = $endDate;
        }
        $query .= "
            ) AND es.activite = 1
            GROUP BY s.id_service";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEmployesByDepartement($month = null, $year = null) {
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
        ";
        $params = [];
        if ($year) {
            if ($month) {
                $endDate = date('Y-m-t', strtotime("$year-$month-01"));
            } else {
                $endDate = "$year-12-31";
            }
            $query .= " AND es2.date_modification <= ?";
            $params[] = $endDate;
        }
        $query .= "
            ) AND es.activite = 1
            GROUP BY d.id_dept";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEmployesByPoste($month = null, $year = null) {
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
        ";
        $params = [];
        if ($year) {
            if ($month) {
                $endDate = date('Y-m-t', strtotime("$year-$month-01"));
            } else {
                $endDate = "$year-12-31";
            }
            $query .= " AND es2.date_modification <= ?";
            $params[] = $endDate;
        }
        $query .= "
            ) AND es.activite = 1
            GROUP BY p.id_poste";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEmployesByActivite($month = null, $year = null) {
        $db = Flight::db();
        $query = "
            SELECT CASE WHEN es.activite = 1 THEN 'Actif' ELSE 'Inactif' END as statut, COUNT(*) as count
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            WHERE es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
        ";
        $params = [];
        if ($year) {
            if ($month) {
                $endDate = date('Y-m-t', strtotime("$year-$month-01"));
            } else {
                $endDate = "$year-12-31";
            }
            $query .= " AND es2.date_modification <= ?";
            $params[] = $endDate;
        }
        $query .= "
            )
            GROUP BY es.activite";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}