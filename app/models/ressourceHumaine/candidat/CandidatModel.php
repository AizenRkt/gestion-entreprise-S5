<?php
namespace app\models\ressourceHumaine\candidat;

use Flight;
use PDO;

require __DIR__ . '/../../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CandidatModel {
    public function getAge($date_naissance) {
        if (empty($date_naissance)) return null;
        try {
            $dob = new \DateTime($date_naissance);
            $now = new \DateTime();
            return $now->diff($dob)->y;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFiltered($filters = []) {
        $db = Flight::db();
        $where = [];
        $params = [];
        // Filtre idsSousContrat prioritaire : on applique tous les autres filtres mais uniquement sur les candidats ayant ces ids
        if (!empty($filters['idsSousContrat']) && is_array($filters['idsSousContrat'])) {
            // Si aucun id, on renvoie vide
            if (count($filters['idsSousContrat']) === 1 && $filters['idsSousContrat'][0] === -1) {
                return [];
            }
            $whereSousContrat = 'candidat.id_candidat IN (' . implode(',', array_map('intval', $filters['idsSousContrat'])) . ')';
            $where[] = $whereSousContrat;
        }

        // Si aucun filtre n'est choisi, retourne tous les candidats (table candidat uniquement)
        $hasFilter = false;
        foreach (["genre","age_min","age_max","ville","diplome","competences","profils","date_naissance","idsSousContrat"] as $key) {
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

    public function getVilleByIdCv($idCv) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT dcv.id_item, v.nom
                FROM detail_cv dcv
                JOIN ville v ON dcv.id_item = v.id_ville
                WHERE dcv.id_cv = ? AND dcv.type = 'ville'
            ");
            $stmt->execute([$idCv]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getDiplomeByIdCv($idCv) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT dcv.id_item, d.nom
                FROM detail_cv dcv
                JOIN diplome d ON dcv.id_item = d.id_diplome
                WHERE dcv.id_cv = ? AND dcv.type = 'diplome'
            ");
            $stmt->execute([$idCv]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getCompetenceByIdCv($idCv) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT dcv.id_item, c.nom
                FROM detail_cv dcv
                JOIN competence c ON dcv.id_item = c.id_competence
                WHERE dcv.id_cv = ? AND dcv.type = 'competence'
            ");
            $stmt->execute([$idCv]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getCvById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT 
                    c.id_candidat,
                    c.nom,
                    c.prenom,
                    c.email,
                    c.telephone,
                    c.genre,
                    c.date_naissance,
                    c.date_candidature,
                    cv.id_cv,
                    p.nom as profil,
                    cv.date_soumission AS cv_date_soumission,
                    cv.photo
                FROM candidat c
                LEFT JOIN cv ON c.id_candidat = cv.id_candidat
                LEFT JOIN profil p ON cv.id_profil = p.id_profil
                WHERE c.id_candidat = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function cvAPI($id) {
        try {
            $cvs = $this->getCvById($id);

            if (!$cvs) {
                return null;
            }

            $result = [
                'id_candidat' => $cvs[0]['id_candidat'],
                'nom' => $cvs[0]['nom'],
                'prenom' => $cvs[0]['prenom'],
                'email' => $cvs[0]['email'],
                'telephone' => $cvs[0]['telephone'],
                'genre' => $cvs[0]['genre'],
                'date_naissance' => $cvs[0]['date_naissance'],
                'date_candidature' => $cvs[0]['date_candidature'],
                'cvs' => []
            ];

            foreach ($cvs as $cv) {
                $idCv = $cv['id_cv'];
                
                $result['cvs'][] = [
                    'id_cv' => $idCv,
                    'profil' => $cv['profil'], 
                    'date_soumission' => $cv['cv_date_soumission'],
                    'photo' => $cv['photo'],
                    'villes' => $this->getVilleByIdCv($idCv),
                    'diplomes' => $this->getDiplomeByIdCv($idCv),
                    'competences' => $this->getCompetenceByIdCv($idCv)
                ];
            }

            return $result;

        } catch (\PDOException $e) {
            return null;
        }
    }

    public function exportCvToExcel($candidatData) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1️⃣ Entêtes
        $sheet->setCellValue('A1', 'Nom');
        $sheet->setCellValue('B1', 'Prénom');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Téléphone');
        $sheet->setCellValue('E1', 'Genre');
        $sheet->setCellValue('F1', 'Date Naissance');
        $sheet->setCellValue('G1', 'Date Candidature');
        $sheet->setCellValue('H1', 'CV Profil');
        $sheet->setCellValue('I1', 'CV Date Soumission');
        $sheet->setCellValue('J1', 'CV Photo');
        $sheet->setCellValue('K1', 'Villes');
        $sheet->setCellValue('L1', 'Diplômes');
        $sheet->setCellValue('M1', 'Compétences');

        $row = 2; // Ligne de départ

        foreach ($candidatData['cvs'] as $cv) {
            $sheet->setCellValue('A'.$row, $candidatData['nom']);
            $sheet->setCellValue('B'.$row, $candidatData['prenom']);
            $sheet->setCellValue('C'.$row, $candidatData['email']);
            $sheet->setCellValue('D'.$row, $candidatData['telephone']);
            $sheet->setCellValue('E'.$row, $candidatData['genre']);
            $sheet->setCellValue('F'.$row, $candidatData['date_naissance']);
            $sheet->setCellValue('G'.$row, $candidatData['date_candidature']);
            $sheet->setCellValue('H'.$row, $cv['profil']);
            $sheet->setCellValue('I'.$row, $cv['date_soumission']);
            $sheet->setCellValue('J'.$row, $cv['photo']);

            // Colonnes avec plusieurs valeurs : on joint par une virgule
            $sheet->setCellValue('K'.$row, implode(', ', array_column($cv['villes'], 'nom')));
            $sheet->setCellValue('L'.$row, implode(', ', array_column($cv['diplomes'], 'nom')));
            $sheet->setCellValue('M'.$row, implode(', ', array_column($cv['competences'], 'nom')));

            $row++;
        }

        // 2️⃣ Création du fichier Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'candidat_'.$candidatData['nom'].'-'.$candidatData['prenom'].'.xlsx';

        // 3️⃣ Téléchargement direct
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }



}