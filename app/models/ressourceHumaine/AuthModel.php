<?php

namespace app\Models\ressourceHumaine;
use Flight;
use PDO;

class AuthModel {
    private $db;

    public function __construct() {
        $this->db = Flight::db();
    }
    public static function getByUsername($username) {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM user WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

?>