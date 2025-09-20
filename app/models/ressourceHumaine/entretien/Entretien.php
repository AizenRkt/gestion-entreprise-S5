<?php
namespace app\models\ressourceHumaine\entretien;

use Flight;
use PDO;

class Entretien
{
    // Vérifier s'il y a un conflit d'horaire
    public function hasScheduleConflict($date, $duree, $ignoreId = null)
    {
        try {
            $db = Flight::db();

            // Convertir la date de début en datetime
            $startDateTime = $date;
            $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime . ' + ' . $duree . ' minutes'));

            $sql = "
                SELECT COUNT(*) as count 
                FROM entretien_candidat 
                WHERE (
                    (:start BETWEEN date AND DATE_ADD(date, INTERVAL duree MINUTE))
                    OR (:end BETWEEN date AND DATE_ADD(date, INTERVAL duree MINUTE))
                    OR (date BETWEEN :start AND :end)
                )
            ";

            $params = [
                ':start' => $startDateTime,
                ':end' => $endDateTime
            ];

            // Si on ignore un ID (pour les mises à jour)
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
            return true; // En cas d'erreur, on considère qu'il y a conflit pour éviter les doublons
        }
    }

    // Créer un entretien avec vérification de conflit
    public function create($id_candidat, $date, $id_user = null, $duree = null)
    {
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
            error_log("Erreur d'insertion d'entretien: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Erreur d'insertion : " . $e->getMessage()
            ];
        }
    }

    // Récupérer tous les entretiens avec info candidat
    public function getAll()
    {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT e.*, c.nom, c.prenom, c.email 
                FROM entretien_candidat e
                JOIN candidat c ON e.id_candidat = c.id_candidat
                ORDER BY e.date DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur de récupération des entretiens: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer les entretiens par mois pour le calendrier
    public function getByMonth($year, $month)
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT DATE(date) as date_only, COUNT(*) as nb_entretiens,
                       GROUP_CONCAT(CONCAT(c.nom, ' ', c.prenom) SEPARATOR ', ') as candidats
                FROM entretien_candidat e
                JOIN candidat c ON e.id_candidat = c.id_candidat
                WHERE YEAR(date) = :year AND MONTH(date) = :month
                GROUP BY DATE(date)
                ORDER BY date_only
            ");

            $stmt->execute([
                ':year' => $year,
                ':month' => $month
            ]);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Transformer en array associatif avec la date comme clé
            $entretiensByDate = [];
            foreach ($results as $row) {
                $entretiensByDate[$row['date_only']] = [
                    'count' => $row['nb_entretiens'],
                    'candidats' => $row['candidats']
                ];
            }

            return $entretiensByDate;

        } catch (\PDOException $e) {
            error_log("Erreur de récupération des entretiens par mois: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer un entretien par ID
    public function getById($id)
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM entretien_candidat WHERE id_entretien = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur de récupération d'entretien: " . $e->getMessage());
            return null;
        }
    }

    // Mettre à jour un entretien avec vérification de conflit
    public function update($id, $id_candidat, $date, $id_user = null, $duree = null)
    {
        try {
            // Vérifier s'il y a un conflit d'horaire (en ignorant l'entretien actuel)
            if ($this->hasScheduleConflict($date, $duree, $id)) {
                return [
                    'success' => false,
                    'message' => "Conflit d'horaire : Un autre entretien est déjà prévu à cette heure."
                ];
            }

            $db = Flight::db();
            $stmt = $db->prepare("
                UPDATE entretien_candidat 
                SET id_candidat = :id_candidat, date = :date, id_user = :id_user, duree = :duree
                WHERE id_entretien = :id
            ");

            $stmt->execute([
                ':id' => $id,
                ':id_candidat' => $id_candidat,
                ':date' => $date,
                ':id_user' => $id_user,
                ':duree' => $duree
            ]);

            return [
                'success' => true,
                'message' => "Mise à jour réussie."
            ];

        } catch (\PDOException $e) {
            error_log("Erreur de mise à jour d'entretien: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Erreur de mise à jour : " . $e->getMessage()
            ];
        }
    }

    // Supprimer un entretien
    public function delete($id)
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM entretien_candidat WHERE id_entretien = ?");
            $stmt->execute([$id]);

            return [
                'success' => true,
                'message' => "Suppression réussie."
            ];

        } catch (\PDOException $e) {
            error_log("Erreur de suppression d'entretien: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Erreur de suppression : " . $e->getMessage()
            ];
        }
    }

    // Vérifier si un candidat a déjà un entretien
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
            return true; // En cas d'erreur, on considère que le candidat a un entretien
        }
    }

    public function getByDay($date)
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
            SELECT e.*, c.nom, c.prenom, c.email, c.telephone, u.username
            FROM entretien_candidat e
            JOIN candidat c ON e.id_candidat = c.id_candidat
            LEFT JOIN user u ON e.id_user = u.id_user
            WHERE DATE(e.date) = :date
            ORDER BY e.date ASC
        ");

            $stmt->execute([':date' => $date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Erreur de récupération des entretiens du jour: " . $e->getMessage());
            return [];
        }
    }
}