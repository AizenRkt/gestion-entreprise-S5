<?php

namespace app\models\ressourceHumaine;
use Flight;
use PDO;

class AuthModel {
    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }
    public static function getByUsername($username) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function getEmployeeByEmail($email) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM employe WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function userExistsForEmployee($id_employe) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM user WHERE id_employe = ?");
        $stmt->execute([$id_employe]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
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
}

?>