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
    /**
     * Récupère toutes les heures supplémentaires d'un employé pour un mois et une année donnés.
     *
     * @param int $id_employe L'identifiant de l'employé.
     * @param int $mois Le mois à filtrer (1-12).
     * @param int $annee L'année à filtrer.
     * @return array
     */
    public function getAllHeureSuppByEmployeAndDate(int $id_employe, int $mois, int $annee): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM view_total_heures_supp 
                    WHERE id_employe = :id_employe 
                    AND mois = :mois 
                    AND annee = :annee";
                    
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id_employe' => $id_employe,
                'mois' => $mois,
                'annee' => $annee
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Retourne tous les résultats
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];  // En cas d'erreur, retourne un tableau vide
        }
    }   
}