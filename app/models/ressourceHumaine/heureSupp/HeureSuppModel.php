<?php

namespace app\models\ressourceHumaine\heureSupp;

use Flight;
use PDO;

class HeureSuppModel
{
    public function processValidation(int $id_demande_heure_sup, string $statut, string $date_validation, ?string $commentaire = null): bool
    {
        try {
            $db = Flight::db();
            $db->beginTransaction();

            // Supprimer toute validation existante pour cette demande
            $stmt_delete = $db->prepare("DELETE FROM validation_heure_sup WHERE id_demande_heure_sup = :id_demande_heure_sup");
            $stmt_delete->execute(['id_demande_heure_sup' => $id_demande_heure_sup]);

            // Insérer la nouvelle validation
            $stmt_insert = $db->prepare(
                "INSERT INTO validation_heure_sup (id_demande_heure_sup, commentaire, statut, date_validation)
                VALUES (:id_demande_heure_sup, :commentaire, :statut, :date_validation)"
            );
            $stmt_insert->execute([
                'id_demande_heure_sup' => $id_demande_heure_sup,
                'commentaire' => $commentaire,
                'statut' => $statut,
                'date_validation' => $date_validation
            ]);

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
        
    /**
     * Récupère tous les détails des heures supplémentaires depuis la vue.
     * @return array
     */
    public function getAllHeureSuppDetails(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM view_heure_sup_details";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Gérer l'erreur, par exemple en loggant ou en retournant un tableau vide
            error_log($e->getMessage());
            return [];
        }
    }
}