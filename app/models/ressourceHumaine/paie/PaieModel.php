<?php
namespace app\models\ressourceHumaine\paie;

use Flight;
use PDO;
use PDOException;

class PaieModel {

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function tauxAssurance(): array {
        try {
            $sql = "SELECT * FROM assurance";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function tauxHeureSup(): array {
        try {
            $sql = "SELECT * FROM taux_heures_sup";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPrime(int $id_employe, int $mois, int $annee): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM prime p
                    JOIN  employe_prime ep ON ep.id_prime = p.id_prime
                    WHERE ep.id_employe = :id_employe 
                    AND ep.mois = :mois 
                    AND ep.annee = :annee";
                    
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id_employe' => $id_employe,
                'mois' => $mois,
                'annee' => $annee
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC); 
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];  
        }
    }
    
    public function getAvance(int $id_employe, int $mois, int $annee): array
{
    try {
        $db = Flight::db();

        // Compute previous month + year
        $prevMonth = ($mois == 1) ? 12 : $mois - 1;
        $prevYear  = ($mois == 1) ? $annee - 1 : $annee;

        $sql = "SELECT a.*, pa.pourcentage
                FROM avance_salaire a
                JOIN pourcentage_avance pa 
                    ON pa.id_pourcentage = a.id_pourcentage
                WHERE a.id_employe = :id_employe
                AND MONTH(a.date_avance) = :mois 
                AND YEAR(a.date_avance) = :annee
                AND a.statut = 'accordee'
                ORDER BY a.date_avance DESC";

        $stmt = $db->prepare($sql);

        $stmt->execute([
            'id_employe' => $id_employe,
            'mois' => $mois,
            'annee' => $annee
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (\PDOException $e) {
        error_log($e->getMessage());
        return [];
    }
}


    
}