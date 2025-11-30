<?php
namespace app\models\ressourceHumaine\competence;

use Flight;
use PDO;

class CompetenceModel {
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
                p_current.titre AS current_poste,  -- current poste title
                GROUP_CONCAT(DISTINCT c.nom ORDER BY c.nom ASC SEPARATOR ', ') AS skills_owned,
                GROUP_CONCAT(DISTINCT rp.titre ORDER BY rp.titre ASC SEPARATOR ', ') AS related_posts,
                GROUP_CONCAT(DISTINCT ms.missing_competence ORDER BY ms.missing_competence ASC SEPARATOR ', ') AS missing_skills_current_post
            FROM employe e
            -- current poste of the employee
            LEFT JOIN employe_statut es ON es.id_employe = e.id_employe
            LEFT JOIN poste p_current ON p_current.id_poste = es.id_poste

            -- owned skills
            LEFT JOIN employe_competence ec ON ec.id_employe = e.id_employe
            LEFT JOIN competence c ON c.id_competence = ec.id_competence

            -- all related posts for their skills
            LEFT JOIN poste_competence pc2 ON pc2.id_competence = c.id_competence
            LEFT JOIN poste rp ON rp.id_poste = pc2.id_poste

            -- missing skills for current poste only
            LEFT JOIN (
                SELECT pc.id_poste, e.id_employe, c.nom AS missing_competence
                FROM poste_competence pc
                JOIN employe e ON 1=1  -- include all employees
                JOIN employe_statut es ON es.id_employe = e.id_employe
                LEFT JOIN employe_competence ec
                       ON ec.id_employe = e.id_employe
                      AND ec.id_competence = pc.id_competence
                JOIN competence c ON c.id_competence = pc.id_competence
                WHERE pc.id_poste = es.id_poste
                  AND ec.id_competence IS NULL
            ) ms ON ms.id_employe = e.id_employe

            GROUP BY e.id_employe
            ORDER BY e.nom ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\PDOException $e) {
        return [];
    }
}


}
