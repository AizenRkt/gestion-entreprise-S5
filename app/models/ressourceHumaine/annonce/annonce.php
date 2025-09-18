<?php
namespace app\models\ressourceHumaine\annonce;

use PDO;
use PDOException;

class Annonce {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }
        public function getAllAnnonces(): array {
    try {
            $sql = "SELECT a.id_annonce, a.titre, a.date_debut, a.date_fin, a.experience, a.age_min, a.age_max,
                        p.nom AS profil,
                        GROUP_CONCAT(DISTINCT d.nom SEPARATOR ', ') AS diplomes,
                        v.nom AS ville,
                        a.objectif
                    FROM annonce a
                    LEFT JOIN profil p ON a.id_profil = p.id_profil
                    LEFT JOIN detail_annonce da ON a.id_annonce = da.id_annonce AND da.type='ville'
                    LEFT JOIN ville v ON da.id_item = v.id_ville
                    LEFT JOIN detail_annonce dd ON a.id_annonce = dd.id_annonce AND dd.type='diplome'
                    LEFT JOIN diplome d ON dd.id_item = d.id_diplome
                    GROUP BY a.id_annonce
                    ORDER BY a.date_debut DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllDiplomes(): array {
        $stmt = $this->db->query("SELECT id_diplome, nom FROM diplome ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllVilles(): array {
        $stmt = $this->db->query("SELECT id_ville, nom FROM ville ORDER BY nom ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilteredAnnonces($keyword = null, $diplome = null, $ville = null): array {
        $sql = "SELECT a.id_annonce, a.titre, a.date_debut, a.date_fin, a.experience, a.age_min, a.age_max,
                    p.nom AS profil,
                    v.nom AS ville,
                    d.nom AS diplomes
                FROM annonce a
                LEFT JOIN profil p ON a.id_profil = p.id_profil
                LEFT JOIN detail_annonce da_ville ON a.id_annonce = da_ville.id_annonce AND da_ville.type='ville'
                LEFT JOIN ville v ON da_ville.id_item = v.id_ville
                LEFT JOIN detail_annonce da_diplome ON a.id_annonce = da_diplome.id_annonce AND da_diplome.type='diplome'
                LEFT JOIN diplome d ON da_diplome.id_item = d.id_diplome
                WHERE 1 ";

        $params = [];

        if ($keyword) {
            $sql .= " AND a.titre LIKE :keyword ";
            $params[':keyword'] = "%$keyword%";
        }

        if ($diplome) {
            $sql .= " AND d.id_diplome = :diplome ";
            $params[':diplome'] = $diplome;
        }

        if ($ville) {
            $sql .= " AND v.id_ville = :ville ";
            $params[':ville'] = $ville;
        }

        $sql .= " ORDER BY a.date_debut DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
