<?php

namespace app\models\ressourceHumaine;
use Flight;
use PDO;

class AuthModel {
    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }

    public static function getUserRoleByUserId($id_user) {
        $db = Flight::db();
        $stmt = $db->prepare(
            "SELECT r.nom AS role
            FROM user u
            JOIN employe_statut es ON u.id_employe = es.id_employe
            JOIN poste_role pr ON es.id_poste = pr.id_poste
            JOIN role r ON pr.id_role = r.id_role
            WHERE u.id_user = ?
            ORDER BY es.date_modification DESC, pr.date_role DESC
            LIMIT 1"
        );
        $stmt->execute([$id_user]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['role'] : null;
    }

    public static function getByUsername($username) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getActiveUserByUsername($username) {
        $db = Flight::db();
        $stmt = $db->prepare(
            "SELECT * FROM (
                SELECT u.*, es.activite
                FROM user u
                JOIN employe_statut es ON u.id_employe = es.id_employe
                WHERE u.username = ?
                ORDER BY es.date_modification DESC
                LIMIT 1
            ) AS latest_user_status
            WHERE latest_user_status.activite = 1"
        );
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getEmployeeByEmail($email) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM employe WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getMenuByRoleAndService($id_service, $role) {
        $db = Flight::db();
        $stmt = $db->prepare(
            "SELECT nom FROM menu_ui WHERE id_service = ? and role = ?"
        );
        $stmt->execute([$id_service, $role]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function userExistsForEmployee($id_employe) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM user WHERE id_employe = ?");
        $stmt->execute([$id_employe]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public static function isEmployeeActive($id_employe) {
        $db = Flight::db();
        $stmt = $db->prepare(
            "SELECT activite FROM employe_statut
             WHERE id_employe = ?
             ORDER BY date_modification DESC
             LIMIT 1"
        );
        $stmt->execute([$id_employe]);
        $status = $stmt->fetch(PDO::FETCH_ASSOC);

        return $status && $status['activite'] == 1;
    }

    public static function registerUser($email, $username, $password) {
        $db = Flight::db();

        // 1. Check if username already exists
        $stmt = $db->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Ce nom d\'utilisateur est déjà pris.'];
        }

        // 2. Find employee by email
        $employee = self::getEmployeeByEmail($email);
        if (!$employee) {
            return ['success' => false, 'message' => 'Aucun employé trouvé avec cet email.'];
        }

        $id_employe = $employee['id_employe'];

        // 2.5 Check if the employee is active
        if (!self::isEmployeeActive($id_employe)) {
            return ['success' => false, 'message' => 'Le compte de cet employé est inactif.'];
        }

        // 3. Check if a user account already exists for this employee
        if (self::userExistsForEmployee($id_employe)) {
            return ['success' => false, 'message' => 'Un compte utilisateur existe déjà pour cet employé.'];
        }

        // 4. Create the new user
        $stmt = $db->prepare("INSERT INTO user (username, pwd, id_employe) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $password, $id_employe])) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de la création du compte.'];
        }
    }
    
    public static function getServiceByUserId($id_user) {
        $db = Flight::db();
        $stmt = $db->prepare(
            "SELECT s.nom, s.id_service FROM user u
            JOIN employe_statut es ON u.id_employe = es.id_employe
            JOIN poste p ON es.id_poste = p.id_poste
            JOIN service s ON p.id_service = s.id_service
            WHERE u.id_user = ?"
        );
        $stmt->execute([$id_user]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function activerUser($id_user)
    {
        $sql = "INSERT INTO user_statut (id_user, activite, date_activite)
                VALUES (:id_user, 1, CURDATE())
                ON DUPLICATE KEY UPDATE activite = 1, date_activite = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
    }

    public function desactiverUser($id_user)
    {
        $sql = "INSERT INTO user_statut (id_user, activite, date_activite)
                VALUES (:id_user, 0, CURDATE())
                ON DUPLICATE KEY UPDATE activite = 0, date_activite = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
    }

    public function getDernierStatut($id_user)
    {
        $sql = "SELECT * FROM user_statut 
                WHERE id_user = :id_user 
                ORDER BY date_activite DESC 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function estActif($id_user): bool
    {
        $sql = "SELECT activite 
                FROM user_statut 
                WHERE id_user = :id_user 
                ORDER BY date_activite DESC 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['activite'] == 1;
    }

    public function getHistoriqueStatut($id_user)
    {
        $sql = "SELECT * FROM user_statut 
                WHERE id_user = :id_user 
                ORDER BY date_activite DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>