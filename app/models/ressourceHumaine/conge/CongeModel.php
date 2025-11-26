<?php

namespace app\models\ressourceHumaine\conge;

use Flight;
use PDO;

class CongeModel
{
    /**
     * Récupère tous les détails des congés depuis la vue.
     * @return array
     */
    public function getAllCongeDetails(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM view_conge_details";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Gérer l'erreur, par exemple en loggant ou en retournant un tableau vide
            error_log($e->getMessage());
            return [];
        }
    }

    public function processValidation(int $id_demande_conge, string $statut, string $date_validation): bool
    {
        try {
            $db = Flight::db();
            $db->beginTransaction();

            // Supprimer toute validation existante pour cette demande
            $stmt_delete = $db->prepare("DELETE FROM validation_conge WHERE id_demande_conge = :id_demande_conge");
            $stmt_delete->execute(['id_demande_conge' => $id_demande_conge]);

            // Insérer la nouvelle validation
            $stmt_insert = $db->prepare(
                "INSERT INTO validation_conge (id_demande_conge, statut, date_validation)
                VALUES (:id_demande_conge, :statut, :date_validation)"
            );
            $stmt_insert->execute([
                'id_demande_conge' => $id_demande_conge,
                'statut' => $statut,
                'date_validation' => $date_validation
            ]);

            // Si le congé est validé, mettre à jour le statut dans la table de pointage
            if ($statut === 'valide') {
                $stmt_conge = $db->prepare("SELECT id_employe, date_debut, date_fin FROM demande_conge WHERE id_demande_conge = :id_demande_conge");
                $stmt_conge->execute(['id_demande_conge' => $id_demande_conge]);
                $conge_details = $stmt_conge->fetch(PDO::FETCH_ASSOC);

                if ($conge_details) {
                    $pointageModel = new \app\models\ressourceHumaine\pointage\PointageModel();
                    $pointageModel->updatePointageStatusForDateRange(
                        $conge_details['id_employe'],
                        $conge_details['date_debut'],
                        $conge_details['date_fin'],
                        'Congé'
                    );
                }
            }

            $db->commit();
            return true;
        } catch (\PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log($e->getMessage());
            return false;
        }
    }
}
