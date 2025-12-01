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
    
}