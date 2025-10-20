<?php
// app/models/ressourceHumaine/PermissionModel.php
namespace app\models\ressourceHumaine;

// Correction: Pas besoin d'étendre BaseSQL car c'est un cas d'usage spécifique
// qui ne correspond pas au modèle CRUD générique de BaseSQL.
use PDO;

class PermissionModel {

    protected $db;

    public function __construct() {
        // On établit une connexion directe, car le constructeur de BaseSQL
        // est trop spécifique pour ce modèle.
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=gestion_entreprise;charset=utf8", "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * Récupère toutes les permissions depuis la base de données
     * et les formate pour le middleware.
     *
     * @return array Format: ['/route' => ['Role1', 'Role2']]
     */
    // public function getPermissions(): array {
    //     $query = "SELECT route_pattern, role_name, id_service  FROM route_permissions";
    //     // On utilise directement la connexion $this->db
    //     $stmt = $this->db->query($query);
    //     $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     $permissions = [];
    //     foreach ($results as $row) {
    //         // On groupe les rôles par route
    //         $permissions[$row['route_pattern']][] = $row['role_name'];
    //     }

    //     return $permissions;
    // }

     public function getPermissions(): array {
        $query = "SELECT route_pattern, role_name, id_service FROM route_permissions";
        $stmt = $this->db->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $permissions = [];
        foreach ($results as $row) {
            $route = $row['route_pattern'];
            $role = $row['role_name'];
            $service = (int)$row['id_service'];

            if (!isset($permissions[$route])) {
                $permissions[$route] = [];
            }

            $permissions[$route][] = [
                'role' => $role,
                'id_service' => $service
            ];
        }

        return $permissions;
    }
}