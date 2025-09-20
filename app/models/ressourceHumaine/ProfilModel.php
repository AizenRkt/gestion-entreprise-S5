<?php
namespace app\models\ressourceHumaine;

use Flight;
use PDO;

class ProfilModel {

    public static function getAll() {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT id_profil, nom FROM profil ORDER BY id_profil ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
