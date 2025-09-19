<?php
namespace app\models\ressourceHumaine\candidat;

use Flight;
use PDO;

class CandidatModel {
    /**
     * Filtrage dynamique des candidats selon les filtres fournis
     * $filters = [genre, age_min, age_max, diplome, competences, ville, profils, date_naissance]
     */
    public function getFiltered($filters = []) {
        $db = Flight::db();
        $where = [];
        $params = [];

        // Si aucun filtre n'est choisi, retourne tous les candidats (table candidat uniquement)
        $hasFilter = false;
        foreach (["genre","age_min","age_max","ville","diplome","competences","profils","date_naissance"] as $key) {
            if (!empty($filters[$key])) {
                $hasFilter = true;
                break;
            }
        }
        if (!$hasFilter) {
            $stmt = $db->query("SELECT * FROM candidat ORDER BY date_candidature DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Genre
        if (!empty($filters['genre'])) {
            $val = $filters['genre'];
            if (strtolower($val) === 'm' || strtolower($val) === 'homme') {
                $val = 'M';
            } elseif (strtolower($val) === 'f' || strtolower($val) === 'femme') {
                $val = 'F';
            }
            $where[] = 'genre = :genre';
            $params[':genre'] = $val;
        }

        // Age (calculé à partir de date_naissance)
        if (!empty($filters['age_min'])) {
            $where[] = 'TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) >= :age_min';
            $params[':age_min'] = $filters['age_min'];
        }
        if (!empty($filters['age_max'])) {
            $where[] = 'TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) <= :age_max';
            $params[':age_max'] = $filters['age_max'];
        }

        // Date de candidature avant
        if (!empty($filters['date_naissance'])) {
            $where[] = 'date_candidature <= :date_naissance';
            $params[':date_naissance'] = $filters['date_naissance'];
        }

        // Filtre ville, diplome, competence, profil via jointures
        $join = '';
        $having = [];

        // Ville
        if (!empty($filters['ville'])) {
            $join .= ' INNER JOIN cv ON cv.id_candidat = candidat.id_candidat';
            $join .= ' INNER JOIN detail_cv AS dcv_ville ON dcv_ville.id_cv = cv.id_cv AND dcv_ville.type = "ville"';
            $where[] = 'dcv_ville.id_item = :ville';
            $params[':ville'] = $filters['ville'];
        }
        // Diplome
        if (!empty($filters['diplome'])) {
            if (strpos($join, 'cv ON') === false) {
                $join .= ' INNER JOIN cv ON cv.id_candidat = candidat.id_candidat';
            }
            $join .= ' INNER JOIN detail_cv AS dcv_diplome ON dcv_diplome.id_cv = cv.id_cv AND dcv_diplome.type = "diplome"';
            $where[] = 'dcv_diplome.id_item = :diplome';
            $params[':diplome'] = $filters['diplome'];
        }
        // Competences (peut être plusieurs)
        if (!empty($filters['competences'])) {
            if (strpos($join, 'cv ON') === false) {
                $join .= ' INNER JOIN cv ON cv.id_candidat = candidat.id_candidat';
            }
            $join .= ' INNER JOIN detail_cv AS dcv_comp ON dcv_comp.id_cv = cv.id_cv AND dcv_comp.type = "competence"';
            $where[] = 'dcv_comp.id_item IN (' . implode(',', array_map(function($i){return (int)$i;}, $filters['competences'])) . ')';
        }
        // Profils (peut être plusieurs)
        if (!empty($filters['profils'])) {
            if (strpos($join, 'cv ON') === false) {
                $join .= ' INNER JOIN cv ON cv.id_candidat = candidat.id_candidat';
            }
            $where[] = 'cv.id_profil IN (' . implode(',', array_map(function($i){return (int)$i;}, $filters['profils'])) . ')';
        }

        $sql = 'SELECT DISTINCT candidat.* FROM candidat' . $join;
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY candidat.date_candidature DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($nom, $prenom, $email, $telephone, $genre, $date_naissance) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO candidat (nom, prenom, email, telephone, genre, date_naissance)
                VALUES (:nom, :prenom, :email, :telephone, :genre, :date_naissance)
            ");
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':genre' => $genre,
                ':date_naissance' => $date_naissance
            ]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM candidat ORDER BY date_candidature DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM candidat WHERE id_candidat = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function update($id, $nom, $prenom, $email, $telephone, $genre) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                UPDATE candidat 
                SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, genre = :genre
                WHERE id_candidat = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':genre' => $genre
            ]);
            return "Mise à jour réussie.";
        } catch (\PDOException $e) {
            return "Erreur de mise à jour : " . $e->getMessage();
        }
    }

    public function delete($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM candidat WHERE id_candidat = ?");
            $stmt->execute([$id]);
            return "Suppression réussie.";
        } catch (\PDOException $e) {
            return "Erreur de suppression : " . $e->getMessage();
        }
    }
}
// ...fin du fichier sans accolades inutiles
