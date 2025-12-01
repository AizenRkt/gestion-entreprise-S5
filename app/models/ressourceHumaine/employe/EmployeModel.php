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
        
        return [
            'id_employe' => $id_employe,
            'id_statut' => $id_statut
        ];    
    }

    public static function getAllEmployes() {
        $db = Flight::db();
        $query = "
            SELECT e.*, es.activite, es.date_modification, p.titre AS poste_titre, p.id_poste
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            JOIN poste p ON es.id_poste = p.id_poste
            WHERE es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
            )
        ";
        $stmt = $db->query($query);
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
    
    public function getById($id) {
        try {
            $db = Flight::db();
            $query = "
                SELECT e.*, es.activite, es.date_modification, p.titre AS poste_titre, p.id_poste
                FROM employe e
                JOIN employe_statut es ON e.id_employe = es.id_employe
                JOIN poste p ON es.id_poste = p.id_poste
                WHERE es.date_modification = (
                    SELECT MAX(es2.date_modification)
                    FROM employe_statut es2
                    WHERE es2.id_employe = e.id_employe
                )
                AND e.id_employe = ?
            ";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
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
        try {
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
        } catch (\PDOException $e) {
            return null;
        }
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

