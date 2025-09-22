<?php
namespace app\models\ressourceHumaine\entretien;

use Flight;
use PDO;

class EntretienModel {
    
    // correction
    public function hasScheduleConflict($date, $duree, $ignoreId = null)
    {
        try {
            $db = Flight::db();
            
            $startDateTime = $date;
            $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime . ' + ' . $duree . ' minutes'));
            
            $sql = "
                SELECT COUNT(*) as count 
                FROM entretien_candidat 
                WHERE (
                    (:start BETWEEN date AND DATE_ADD(date, INTERVAL $duree MINUTE))
                    OR (:end BETWEEN date AND DATE_ADD(date, INTERVAL $duree MINUTE))
                    OR (date BETWEEN :start AND :end)
                )
            ";
            
            $params = [
                ':start' => $startDateTime,
                ':end'   => $endDateTime
            ];
            
            if ($ignoreId) {
                $sql .= " AND id_entretien != :ignore_id";
                $params[':ignore_id'] = $ignoreId;
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (\PDOException $e) {
            error_log("Erreur de vérification de conflit: " . $e->getMessage());
            return true; // on suppose un conflit en cas d'erreur
        }
    }

    // public function hasScheduleConflict($date, $duree, $ignoreId = null)
    // {
    //     try {
    //         $db = Flight::db();
            
    //         $startDateTime = $date;
    //         $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime . ' + ' . $duree . ' minutes'));
            
    //         $sql = "
    //             SELECT COUNT(*) as count 
    //             FROM entretien_candidat 
    //             WHERE (
    //                 (:start BETWEEN date AND DATE_ADD(date, INTERVAL duree MINUTE))
    //                 OR (:end BETWEEN date AND DATE_ADD(date, INTERVAL duree MINUTE))
    //                 OR (date BETWEEN :start AND :end)
    //             )
    //         ";
            
    //         $params = [
    //             ':start' => $startDateTime,
    //             ':end' => $endDateTime
    //         ];
            
    //         if ($ignoreId) {
    //             $sql .= " AND id_entretien != :ignore_id";
    //             $params[':ignore_id'] = $ignoreId;
    //         }
            
    //         $stmt = $db->prepare($sql);
    //         $stmt->execute($params);
            
    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
    //         return $result['count'] > 0;
            
    //     } catch (\PDOException $e) {
    //         error_log("Erreur de vérification de conflit: " . $e->getMessage());
    //         return true;
    //     }
    // }

    public function creerEntretien($id_candidat, $date, $duree, $id_user = null) {
        try {
            // Vérifier s'il y a un conflit d'horaire
            if ($this->hasScheduleConflict($date, $duree)) {
                return [
                    'success' => false,
                    'message' => "Conflit d'horaire : Un entretien est déjà prévu à cette heure."
                ];
            }
            
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO entretien_candidat (id_candidat, date, id_user, duree)
                VALUES (:id_candidat, :date, :id_user, :duree)
            ");
            
            $stmt->execute([
                ':id_candidat' => $id_candidat,
                ':date' => $date,
                ':id_user' => $id_user,
                ':duree' => $duree
            ]);
            
            return [
                'success' => true,
                'message' => "Entretien créé avec succès.",
                'id' => $db->lastInsertId()
            ];
            
        } catch (\PDOException $e) {
            error_log("Erreur lors de la création de l'entretien: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Erreur d'insertion : " . $e->getMessage()
            ];
        }
    }

    public function noterEntretien($id_entretien, $note, $evaluation, $commentaire = null) {
        try {
            $db = Flight::db();
            
            $stmt = $db->prepare("
                UPDATE entretien_candidat 
                SET note_entretien = :note, 
                    evaluation = :evaluation, 
                    commentaire = :commentaire,
                    date_evaluation = NOW()
                WHERE id_entretien = :id_entretien
            ");
            
            $result = $stmt->execute([
                ':note' => $note,
                ':evaluation' => $evaluation,
                ':commentaire' => $commentaire,
                ':id_entretien' => $id_entretien
            ]);
            
            return [
                'success' => $result,
                'message' => $result ? "Entretien noté avec succès." : "Erreur lors de la notation."
            ];
            
        } catch (\PDOException $e) {
            error_log("Erreur lors de la notation de l'entretien: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Erreur de notation : " . $e->getMessage()
            ];
        }
    }

    public function getTousCandidats() {
        try {
            $db = Flight::db();
            $sql = "SELECT id_candidat, nom, prenom, email FROM candidat ORDER BY nom, prenom";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des candidats: " . $e->getMessage());
            return [];
        }
    }

    public function getCandidatById($id_candidat) {
        try {
            $db = Flight::db();
            $sql = "SELECT id_candidat, nom, prenom, email, telephone FROM candidat WHERE id_candidat = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_candidat]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération du candidat: " . $e->getMessage());
            return null;
        }
    }


    public function getEntretiensByCandidatId($id_candidat) {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM entretien_candidat WHERE id_candidat = ? ORDER BY date DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id_candidat]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des entretiens: " . $e->getMessage());
            return [];
        }
    }

    public function getTousEntretiens() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT e.*, c.nom, c.prenom, c.email, u.username 
                FROM entretien_candidat e 
                JOIN candidat c ON e.id_candidat = c.id_candidat 
                LEFT JOIN user u ON e.id_user = u.id_user 
                ORDER BY e.date DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des entretiens: " . $e->getMessage());
            return [];
        }
    }

    public function modifierEntretien($id_entretien, $date, $duree) {
        try {
            if ($this->hasScheduleConflict($date, $duree, $id_entretien)) {
                return [
                    'success' => false,
                    'message' => "Conflit d'horaire : Un autre entretien est déjà prévu à cette heure."
                ];
            }
            
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE entretien_candidat SET date = ?, duree = ? WHERE id_entretien = ?");
            $result = $stmt->execute([$date, $duree, $id_entretien]);
            
            return [
                'success' => $result,
                'message' => $result ? "Entretien modifié avec succès." : "Erreur lors de la modification."
            ];
        } catch (\PDOException $e) {
            error_log("Erreur lors de la modification de l'entretien: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Erreur de modification : " . $e->getMessage()
            ];
        }
    }

    public function supprimerEntretien($id_entretien) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM entretien_candidat WHERE id_entretien = ?");
            $result = $stmt->execute([$id_entretien]);
            
            return [
                'success' => $result,
                'message' => $result ? "Entretien supprimé avec succès." : "Erreur lors de la suppression."
            ];
        } catch (\PDOException $e) {
            error_log("Erreur lors de la suppression de l'entretien: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Erreur de suppression : " . $e->getMessage()
            ];
        }
    }

    public function candidateHasInterview($id_candidat, $ignoreId = null)
    {
        try {
            $db = Flight::db();
            
            $sql = "SELECT COUNT(*) as count FROM entretien_candidat WHERE id_candidat = :id_candidat";
            $params = [':id_candidat' => $id_candidat];
            
            if ($ignoreId) {
                $sql .= " AND id_entretien != :ignore_id";
                $params[':ignore_id'] = $ignoreId;
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (\PDOException $e) {
            error_log("Erreur de vérification candidat: " . $e->getMessage());
            return true;
        }
    }

    public function getEntretiensByMonth($month, $year) {
        try {
            $db = Flight::db();
            
            $sql = "
                SELECT 
                    e.id_entretien,
                    e.date,
                    e.duree,
                    e.note_entretien,
                    e.evaluation,
                    e.commentaire,
                    e.date_evaluation,
                    c.nom,
                    c.prenom,
                    c.email,
                    u.username
                FROM entretien_candidat e 
                JOIN candidat c ON e.id_candidat = c.id_candidat 
                LEFT JOIN user u ON e.id_user = u.id_user 
                WHERE MONTH(e.date) = :month 
                AND YEAR(e.date) = :year
                ORDER BY e.date ASC
            ";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':month' => $month,
                ':year' => $year
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération des entretiens par mois: " . $e->getMessage());
            return [];
        }
    }

    public function getEntretienById($id_entretien) {
        try {
            $db = Flight::db();
            
            $sql = "
                SELECT 
                    e.*,
                    c.nom,
                    c.prenom,
                    c.email,
                    u.username
                FROM entretien_candidat e 
                JOIN candidat c ON e.id_candidat = c.id_candidat 
                LEFT JOIN user u ON e.id_user = u.id_user 
                WHERE e.id_entretien = :id_entretien
            ";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([':id_entretien' => $id_entretien]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("Erreur lors de la récupération de l'entretien: " . $e->getMessage());
            return null;
        }
    }

    public function creerDetailEntretien($id_entretien, $duree, $commentaire = null) {
        try {
            $db = Flight::db();
            $sql = "
                INSERT INTO detail_entretien (id_entretien, duree, commentaire)
                VALUES (:id_entretien, :duree, :commentaire)
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_entretien' => $id_entretien,
                ':duree'        => $duree,
                ':commentaire'  => $commentaire
            ]);

            return $db->lastInsertId();

        } catch (\PDOException $e) {
            error_log("Erreur lors de la création du détail entretien : " . $e->getMessage());
            return false;
        }
    }

}