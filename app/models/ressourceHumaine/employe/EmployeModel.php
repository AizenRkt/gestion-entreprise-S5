<?php

namespace app\models\ressourceHumaine\employe;
use Flight;
use PDO;

Class EmployeModel {

    public function __construct() {
    }

    // CRUD
    public static function createEmploye($nom, $prenom, $email, $telephone, $genre, $date_embauche, $activite, $id_poste, $id_candidat = null) {
        $db = Flight::db();
        
        // 1. Insérer dans la table employe
        $sql_employe = "INSERT INTO employe (id_candidat, nom, prenom, email, telephone, genre, date_embauche) VALUES (:id_candidat, :nom, :prenom, :email, :telephone, :genre, :date_embauche)";
        $stmt_employe = $db->prepare($sql_employe);
        $stmt_employe->execute([
            ':id_candidat' => $id_candidat,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':genre' => $genre,
            ':date_embauche' => $date_embauche
        ]);
        $id_employe = $db->lastInsertId();

        // 2. Insérer dans la table employe_statut
        $sql_statut = "INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES (:id_employe, :id_poste, :activite, NOW())";
        $stmt_statut = $db->prepare($sql_statut);
        $stmt_statut->execute([
            ':id_employe' => $id_employe,
            ':id_poste' => $id_poste,
            ':activite' => $activite
        ]);

        $id_statut = $db->lastInsertId();

        if ($id_candidat !== null) {
            $sql_migrate = "
                INSERT INTO employe_competence (id_employe, id_competence)
                SELECT :id_employe, dc.id_item
                FROM detail_cv dc
                JOIN cv c ON c.id_cv = dc.id_cv
                WHERE c.id_candidat = :id_candidat
                AND dc.type = 'competence'
                AND NOT EXISTS (
                    SELECT 1
                    FROM employe_competence ec
                    WHERE ec.id_employe = :id_employe
                        AND ec.id_competence = dc.id_item
                )
            ";
            $stmt_migrate = $db->prepare($sql_migrate);
            $stmt_migrate->execute([
                ':id_employe' => $id_employe,
                ':id_candidat' => $id_candidat
            ]);
        }

        return [
            'id_employe' => $id_employe,
            'id_statut' => $id_statut
        ];    
    }

    public static function getAllEmployes($filters = []) {
        $db = Flight::db();
        $query = "
            SELECT e.*, es.activite, es.date_modification, p.titre AS poste_titre, p.id_poste, s.nom AS service_nom, d.nom AS dept_nom, ct.fin AS contrat_fin
            FROM employe e
            LEFT JOIN employe_statut es ON e.id_employe = es.id_employe
            AND es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
            )
            LEFT JOIN poste p ON es.id_poste = p.id_poste
            LEFT JOIN service s ON p.id_service = s.id_service
            LEFT JOIN departement d ON s.id_dept = d.id_dept
            LEFT JOIN contrat_travail ct ON e.id_employe = ct.id_employe
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['genre'])) {
            $placeholders = [];
            foreach ($filters['genre'] as $index => $value) {
                $paramName = ":genre$index";
                $placeholders[] = $paramName;
                $params[$paramName] = $value;
            }
            $query .= " AND e.genre IN (" . implode(',', $placeholders) . ")";
        }
        
        if (!empty($filters['date_debut']) && !empty($filters['date_fin'])) {
            $query .= " AND e.date_embauche BETWEEN :date_debut AND :date_fin";
            $params[':date_debut'] = $filters['date_debut'];
            $params[':date_fin'] = $filters['date_fin'];
        }
        
        if (!empty($filters['id_dept'])) {
            $query .= " AND d.id_dept = :id_dept";
            $params[':id_dept'] = $filters['id_dept'];
        }
        
        if (!empty($filters['id_service'])) {
            $query .= " AND s.id_service = :id_service";
            $params[':id_service'] = $filters['id_service'];
        }
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateEmploye($id, $nom, $prenom, $email, $telephone, $genre, $date_embauche, $activite, $id_poste) {
        $db = Flight::db();
        
        // 1. Mettre à jour les informations principales de l'employé
        $sql_employe = "UPDATE employe SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, genre = :genre, date_embauche = :date_embauche WHERE id_employe = :id_employe";
        $stmt_employe = $db->prepare($sql_employe);
        $stmt_employe->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':genre' => $genre,
            ':date_embauche' => $date_embauche,
            ':id_employe' => $id
        ]);

        // 2. Insérer le nouveau statut pour conserver l'historique, en utilisant le id_poste du formulaire
        $sql_statut = "INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES (:id_employe, :id_poste, :activite, NOW())";
        $stmt_statut = $db->prepare($sql_statut);
        
        return $stmt_statut->execute([
            ':id_employe' => $id,
            ':id_poste' => $id_poste,
            ':activite' => $activite
        ]);
    
    }    
    
    // public static function getAllPostes() {
    //     $db = Flight::db();
    //     $stmt = $db->query("SELECT id_poste, titre FROM poste");
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    public static function getAllDepartements() {
        $db = Flight::db();
        $stmt = $db->query("SELECT id_dept, nom FROM departement");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllServices() {
        $db = Flight::db();
        $stmt = $db->query("SELECT id_service, nom FROM service");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            
            // Version simplifiée et plus fiable
            $query = "
                SELECT 
                    e.id_employe,
                    e.nom,
                    e.prenom,
                    e.email,
                    e.telephone,
                    e.genre,
                    e.date_embauche,
                    p.titre AS poste_titre,
                    es.activite
                FROM employe e
                LEFT JOIN employe_statut es ON e.id_employe = es.id_employe
                LEFT JOIN poste p ON es.id_poste = p.id_poste
                WHERE e.id_employe = ?
                ORDER BY es.date_modification DESC
                LIMIT 1
            ";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result;
        } catch (\PDOException $e) {
            error_log("Erreur EmployeModel getById: " . $e->getMessage());
            return null;
        }
    }

    public static function getCongesNonPris($idEmploye, $annee = null) {
        $db = Flight::db();
        $annee = $annee ?? date('Y');
        
        // Récupérer les droits annuels depuis type_conge (congé payé = 30 jours)
        $stmtDroits = $db->prepare("SELECT nb_jours_max FROM type_conge WHERE id_type_conge = 1"); // Congé payé
        $stmtDroits->execute();
        $droits = $stmtDroits->fetch(PDO::FETCH_ASSOC);
        $joursDroits = $droits ? $droits['nb_jours_max'] : 30; // Défaut 30 si pas trouvé
        
        // Calculer les jours pris (demandes validées pour congé payé)
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(dc.nb_jours), 0) AS jours_pris
            FROM demande_conge dc
            JOIN validation_conge vc ON dc.id_demande_conge = vc.id_demande_conge
            WHERE dc.id_employe = ? AND vc.statut = 'valide' AND dc.id_type_conge = 1 AND YEAR(dc.date_debut) = ?
        ");
        $stmt->execute([$idEmploye, $annee]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $joursPris = $result['jours_pris'];
        
        return max(0, $joursDroits - $joursPris);
    }

    public static function getAlertes() {
        $db = Flight::db();
        $employes = self::getAllEmployes();
        $alertes = [];

        foreach ($employes as $emp) {
            $alerte = [
                'id_employe' => $emp['id_employe'],
                'nom' => $emp['nom'],
                'prenom' => $emp['prenom'],
                'contrat' => null,
                'conge' => null,
                'score' => 0
            ];

            // Get contract
            $stmt = $db->prepare("SELECT fin FROM contrat_travail WHERE id_employe = ?");
            $stmt->execute([$emp['id_employe']]);
            $contrat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($contrat) {
                $alerte['contrat'] = $contrat['fin'];
                $fin = $contrat['fin'];
                if ($fin === null) {
                    $alerte['score'] += 0; // CDI low
                } else {
                    $diff = (new \DateTime($fin))->diff(new \DateTime());
                    $jours = $diff->invert ? -$diff->days : $diff->days;
                    if ($jours <= 0) {
                        $alerte['score'] += 100;
                    } elseif ($jours <= 30) {
                        $alerte['score'] += 50;
                    } elseif ($jours <= 90) {
                        $alerte['score'] += 20;
                    } elseif ($jours <= 180) {
                        $alerte['score'] += 10;
                    } else {
                        $alerte['score'] += 1;
                    }
                }
            }

            // Get conge
            $joursRestants = self::getCongesNonPris($emp['id_employe']);
            if ($joursRestants > 0) {
                $alerte['conge'] = $joursRestants;
                if ($joursRestants <= 5) {
                    $alerte['score'] += 50;
                } else {
                    $alerte['score'] += 10;
                }
            }

            $alertes[] = $alerte;
        }

        // Sort by score desc
        usort($alertes, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $alertes;
    }

    // Complexe
    public static function getAllPostes() {
        $db = Flight::db();
        $stmt = $db->query("SELECT id_poste, titre FROM poste");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // employe_statut
    public static function insertEmployeStatut($id_employe, $id_poste, $activite) {
        $db = Flight::db();
        
        $sql_statut = "INSERT INTO employe_statut (id_employe, id_poste, activite, date_modification) VALUES (:id_employe, :id_poste, :activite, NOW())";
        $stmt_statut = $db->prepare($sql_statut);
        $stmt_statut->execute([
            ':id_employe' => $id_employe,
            ':id_poste' => $id_poste,
            ':activite' => $activite
        ]);
        $id_statut = $db->lastInsertId();
        
        return $id_statut;  
    }

    public static function getEmployeStatutNow($id) {
        try {
            $db = Flight::db();
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
                        AND es1.id_employe = ?
                        ORDER BY es1.date_modification DESC
                        LIMIT 1
                    ) es ON e.id_employe = es.id_employe
                    LEFT JOIN poste p ON es.id_poste = p.id_poste
                    LEFT JOIN service s ON p.id_service = s.id_service
                    LEFT JOIN departement d ON s.id_dept = d.id_dept
                    WHERE e.id_employe = ?            
                    ";
            $stmt = $db->prepare($query);
            $stmt->execute([$id, $id]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
     
            if ($info && isset($info['idEmpStatut'])) {
                $contrat = EmployeModel::getInfoContratEmploye($info['idEmpStatut']);
                $info['contrat'] = $contrat;
            }
     
            return $info;
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

    // contrat_employe_statut
    public static function insertContratEmployeStatut($id_contrat, $id_employe_statut) {
        $db = Flight::db();

        $stmt = $db->prepare("
            INSERT INTO contrat_employe_statut (
                id_contrat_travail, id_employe_statut, date_ajout
            ) VALUES (:id_contrat, :id_statut, CURRENT_DATE)
        ");
        $stmt->execute([
            ':id_contrat' => $id_contrat,
            ':id_statut' => $id_employe_statut
        ]);

        return $db->lastInsertId();
    }


    // historique poste employe 
    public static function getEmployeHistoriqueStatut($id) {
        try {
            $db = Flight::db();
            $query = "
                SELECT 
                    es.id_employe_statut,
                    es.id_poste,
                    es.date_modification,
                    es.activite,
                    p.titre AS titre_poste,
                    s.nom AS nom_service,
                    d.nom AS nom_departement
                FROM employe_statut es
                LEFT JOIN poste p ON es.id_poste = p.id_poste
                LEFT JOIN service s ON p.id_service = s.id_service
                LEFT JOIN departement d ON s.id_dept = d.id_dept
                WHERE es.id_employe = ?
                ORDER BY es.id_employe_statut DESC
            ";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            $statuts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($statuts as &$statut) {
                if (isset($statut['id_employe_statut'])) {
                    $contrat = EmployeModel::getInfoContratEmploye($statut['id_employe_statut']);
                    $statut['contrat'] = $contrat;
                }
            }

            return $statuts;

        } catch (\PDOException $e) {
            return null;
        }
    }

}

