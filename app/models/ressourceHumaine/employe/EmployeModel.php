<?php

namespace app\models\ressourceHumaine\employe;

use Flight;
use PDO;

class EmployeModel {
    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }
    // Retourne tous les id_candidat present dans employe
    public function getAllEmployeIds() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT DISTINCT id_candidat FROM employe");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            return [];
        }
    }
    // Récupérer tous les résultats candidats
    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM employe ORDER BY id_employe ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    // Récupérer par employe
    public function getByEmploye($id_employe) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM employe WHERE id_employe = ?");
            $stmt->execute([$id_employe]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
        public static function getAllEmployes() {
        $db = Flight::db();
        $query = "
            SELECT e.*, es.activite, es.date_modification, p.titre AS poste_titre, p.id_poste
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            JOIN poste p ON es.id_poste = p.id_poste
            WHERE es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
            )
        ";
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateEmploye($id, $nom, $prenom, $email, $telephone, $genre, $date_embauche, $activite, $id_poste) {
        $db = Flight::db();
        
        // 1. Mettre à jour les informations principales de l'employé
        $sql_employe = "UPDATE employe SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, genre = :genre, date_embauche = :date_embauche WHERE id_employe = :id_employe";
        $stmt_employe = $db->prepare($sql_employe);
        $stmt_employe->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':genre' => $genre,
            ':date_embauche' => $date_embauche,
            ':id_employe' => $id
        ]);

        // 2. Insérer le nouveau statut pour conserver l'historique, en utilisant le id_poste du formulaire
        $sql_statut = "INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES (:id_employe, :id_poste, :activite, NOW())";
        $stmt_statut = $db->prepare($sql_statut);
        
        return $stmt_statut->execute([
            ':id_employe' => $id,
            ':id_poste' => $id_poste,
            ':activite' => $activite
        ]);
    }    public static function getAllPostes() {
        $db = Flight::db();
        $stmt = $db->query("SELECT id_poste, titre FROM poste");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}