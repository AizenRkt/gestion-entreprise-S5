<?php
namespace app\models\ressourceHumaine\ville;

use PDO;

class VilleModel {
    protected $db;

    public function __construct($db = null) {
        $this->db = $db ?? \Flight::db();
    }

    public function getAll() {
        $stmt = $this->db->query('SELECT id_ville, nom FROM ville ORDER BY nom ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
