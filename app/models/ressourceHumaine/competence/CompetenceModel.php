<?php
namespace app\models\ressourceHumaine\competence;

use Flight;
use PDO;

class CompetenceModel {
    // Récupérer toutes les compétences
    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM competence ORDER BY nom ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getSkillsOverview() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT c.nom AS competence, COUNT(ec.id_employe) AS count
                FROM competence c
                LEFT JOIN employe_competence ec ON ec.id_competence = c.id_competence
                GROUP BY c.id_competence
                ORDER BY count DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return []; 
        }
    }

    public function getEmployeesSkill() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT
                    e.nom AS employe_name,
                    GROUP_CONCAT(DISTINCT c.nom ORDER BY c.nom ASC SEPARATOR ', ') AS skills_owned,
                    GROUP_CONCAT(DISTINCT p.titre ORDER BY p.titre ASC SEPARATOR ', ') AS related_posts,
                    GROUP_CONCAT(DISTINCT ms.missing_competence ORDER BY ms.missing_competence ASC SEPARATOR ', ') AS missing_skills,
                    GROUP_CONCAT(DISTINCT f.nom ORDER BY f.nom ASC SEPARATOR ', ') AS suggested_formations
                FROM employe e
                LEFT JOIN employe_competence ec ON ec.id_employe = e.id_employe
                LEFT JOIN competence c ON c.id_competence = ec.id_competence
                LEFT JOIN poste_competence pc ON pc.id_competence = c.id_competence
                LEFT JOIN poste p ON p.id_poste = pc.id_poste
                LEFT JOIN vw_missing_skills ms ON ms.id_employe = e.id_employe
                LEFT JOIN vw_formation_suggestion fts ON fts.id_employe = e.id_employe AND fts.id_competence = ms.id_competence
                LEFT JOIN formation f ON f.id_formation = fts.id_formation
                GROUP BY e.id_employe
                ORDER BY e.nom ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Do NOT echo anything here
            return []; // just return empty array if error
        }
    }
}
