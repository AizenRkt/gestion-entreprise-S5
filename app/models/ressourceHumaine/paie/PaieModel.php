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
    
    public static function getEmployesByFilters($nom_service = null, $nom_departement = null, $month = null, $year = null) {
    try {
        $db = Flight::db();

        // Build base query
        $query = "
            SELECT 
                e.id_employe,
                e.id_candidat,
                e.nom,
                e.prenom,
                e.email,
                e.telephone,
                e.genre,
                c.date_naissance,
                e.date_embauche,
                c.date_candidature,
                es.id_poste,
                p.titre AS titre_poste,
                s.nom AS nom_service,
                d.nom AS nom_departement,
                es.id_employe_statut as idEmpStatut,
                es.date_modification AS date_statut
            FROM employe e
            JOIN candidat c ON e.id_candidat = c.id_candidat
            LEFT JOIN (
                SELECT es1.*
                FROM employe_statut es1
                WHERE es1.activite = 1
                AND es1.date_modification = (
                    SELECT MAX(es2.date_modification)
                    FROM employe_statut es2
                    WHERE es2.id_employe = es1.id_employe
                    AND es2.activite = 1
                )
            ) es ON e.id_employe = es.id_employe
            LEFT JOIN poste p ON es.id_poste = p.id_poste
            LEFT JOIN service s ON p.id_service = s.id_service
            LEFT JOIN departement d ON s.id_dept = d.id_dept
            WHERE 1=1
        ";

        $params = [];

        // Optional filters
        if ($nom_service) {
            $query .= " AND s.nom = ?";
            $params[] = $nom_service;
        }

        if ($nom_departement) {
            $query .= " AND d.nom = ?";
            $params[] = $nom_departement;
        }

        if ($month && $year) {
            // Only employees whose hire date is <= selected month/year
            $query .= " AND e.date_embauche <= ?";
            $params[] = "$year-$month-31"; // last day of month
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $employes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Filter contrat for each employee
        foreach ($employes as $key => &$info) {
            if (isset($info['idEmpStatut'])) {
                $contrat = PaieModel::getInfoContratEmploye($info['idEmpStatut']);
                
                // Keep only if contract exists and is active for the selected month/year
                if (!$contrat) {
                    unset($employes[$key]);
                    continue;
                }

                $fin = $contrat['fin_reel'] ?? null;
                $selectedDate = "$year-$month-01";

                if ($fin && $fin < $selectedDate) {
                    unset($employes[$key]);
                    continue;
                }

                $info['contrat'] = $contrat;
            } else {
                unset($employes[$key]); // no statut => no contract
            }
        }

        // Reindex array
        return array_values($employes);

    } catch (\PDOException $e) {
        return null;
    }
}

    public static function getInfoContratEmploye($id_employe_statut) {
        try {
            $db = Flight::db();
            $query = "
                SELECT 
                    ct.*,
                    ctp.titre,
                    ces.id_employe_statut,
                    ces.date_ajout,
                    COALESCE(ctr.nouvelle_date_fin, ct.fin) AS fin_reel,
                    ctr.id_renouvellement,
                    ctr.date_renouvellement,
                    ctr.commentaire AS commentaire_renouvellement
                FROM contrat_employe_statut ces
                JOIN contrat_travail ct ON ces.id_contrat_travail = ct.id_contrat_travail
                JOIN contrat_travail_type ctp ON ctp.id_type_contrat = ct.id_type_contrat
                LEFT JOIN contrat_travail_renouvellement ctr 
                    ON ctr.id_contrat_travail = ct.id_contrat_travail
                WHERE ces.id_employe_statut = ?
                ORDER BY ctr.date_renouvellement DESC
                LIMIT 1
            ";
            $stmt = $db->prepare($query);
            $stmt->execute([$id_employe_statut]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAllDepartement(): array {
        try {
            $sql = "SELECT * FROM departement";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllService(): array {
        try {
            $sql = "SELECT * FROM service";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}