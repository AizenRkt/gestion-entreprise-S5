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

public function getFilteredAnnonces($keyword, $diplome, $ville): array {
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
            WHERE 1=1";   // 🔑 Important: start WHERE block cleanly

    $params = [];

    if ($keyword!=null) {
        $sql .= " AND a.titre LIKE :keyword ";
        $params[':keyword'] = "%$keyword%";
    }

    if ($diplome!=null) {
        // Use EXISTS so annonce still returns ALL diplomas
        $sql .= " AND EXISTS (
                      SELECT 1
                      FROM detail_annonce dd2
                      WHERE dd2.id_annonce = a.id_annonce
                        AND dd2.type='diplome'
                        AND dd2.id_item = :diplome
                  )";
        $params[':diplome'] = $diplome;
    }

    if ($ville!=null) {
        $sql .= " AND v.id_ville = :ville ";
        $params[':ville'] = $ville;
    }

    $sql .= " GROUP BY a.id_annonce ORDER BY a.date_debut DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function getDetailAnnonces($id): array {
    try {
            $sql = "SELECT a.id_annonce, a.titre, a.date_debut, a.date_fin, a.experience, a.age_min, a.age_max, a.qualite,
                        p.nom AS profil,
                        GROUP_CONCAT(DISTINCT d.nom SEPARATOR ', ') AS diplomes,
                        GROUP_CONCAT(DISTINCT c.nom SEPARATOR ', ') AS competences,
                        v.nom AS ville,
                        a.objectif
                    FROM annonce a
                    LEFT JOIN profil p ON a.id_profil = p.id_profil
                    LEFT JOIN detail_annonce da ON a.id_annonce = da.id_annonce AND da.type='ville'
                    LEFT JOIN ville v ON da.id_item = v.id_ville
                    LEFT JOIN detail_annonce dd ON a.id_annonce = dd.id_annonce AND dd.type='diplome'
                    LEFT JOIN diplome d ON dd.id_item = d.id_diplome
                    LEFT JOIN detail_annonce dc ON a.id_annonce = dc.id_annonce AND dc.type='competence'
                    LEFT JOIN competence c ON dc.id_item = c.id_competence
                    WHERE a.id_annonce = ?
                    GROUP BY a.id_annonce 
                    ORDER BY a.date_debut DESC";
            $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
