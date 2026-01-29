<?php
namespace app\models\ressourceHumaine\contratTravail;

use app\models\ressourceHumaine\employe\EmployeModel;
use app\models\ressourceHumaine\contratTravail\ContratTravailTypeModel;
use app\models\ressourceHumaine\poste;
use app\models\ressourceHumaine\poste\PosteModel;
use app\models\ressourceHumaine\document\DocumentModel;

use Flight;
use PDO;
use Exception;

class ContratTravailModel {

    private $employeModel;
    private $contratTypeModel;

    public function __construct()
    {
        $this->employeModel = new EmployeModel();
        $this->contratTypeModel = new ContratTravailTypeModel();
    }

    public function insert($id_type_contrat, $id_employe, $debut, $fin = null, $salaire_base = null, $date_signature = null, $id_poste = null, $pathPdf = null) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                INSERT INTO contrat_travail 
                (id_type_contrat, id_employe, debut, fin, salaire_base, date_signature, id_poste, pathPdf)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_type_contrat, $id_employe, $debut, $fin, $salaire_base, $date_signature, $id_poste, $pathPdf]);
            return $db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM contrat_travail ORDER BY debut DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM contrat_travail WHERE id_contrat_travail = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAllWithTypeAndEmploye() {
        try {
            $db = Flight::db();
            $stmt = $db->query("
                SELECT ct.*, ctt.titre AS type_contrat, e.nom, e.prenom, e.email
                FROM contrat_travail ct
                JOIN contrat_travail_type ctt ON ct.id_type_contrat = ctt.id_type_contrat
                JOIN employe e ON ct.id_employe = e.id_employe
                ORDER BY ct.debut DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAllDetail()
    {
        try {
            $sql = "
                SELECT 
                    ct.id_contrat_travail,
                    ct.debut,
                    ct.fin,
                    ct.salaire_base,
                    ct.date_signature,
                    ct.date_creation,
                    ct.pathPdf,
                    
                    -- Type de contrat
                    t.titre AS type_contrat,
                    t.duree_max,

                    -- Employé
                    e.id_employe,
                    e.nom,
                    e.prenom,
                    e.email,
                    e.telephone,

                    -- Poste
                    p.titre AS poste,

                    -- Dernier renouvellement
                    r.id_renouvellement,
                    r.nouvelle_date_fin,
                    r.commentaire,
                    r.date_renouvellement,
                    r.pathPdf AS renouvellement_pdf

                FROM contrat_travail ct

                LEFT JOIN contrat_travail_type t 
                    ON t.id_type_contrat = ct.id_type_contrat

                LEFT JOIN employe e 
                    ON e.id_employe = ct.id_employe

                LEFT JOIN poste p 
                    ON p.id_poste = ct.id_poste

                LEFT JOIN contrat_travail_renouvellement r 
                    ON r.id_renouvellement = (
                        SELECT r2.id_renouvellement 
                        FROM contrat_travail_renouvellement r2
                        WHERE r2.id_contrat_travail = ct.id_contrat_travail
                        ORDER BY r2.date_renouvellement DESC
                        LIMIT 1
                    )

                ORDER BY ct.id_contrat_travail DESC
            ";

            $stmt = Flight::db()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;

        } catch (\Exception $e) {
            error_log("Erreur getAllDetail: " . $e->getMessage());
            return false;
        }
    }

    public function getByIdDetail($id)
    {
        try {
            $sql = "SELECT ct.*, t.titre AS type_titre, t.max_nb_renouvellement, t.max_duree_renouvellement
                    FROM contrat_travail ct
                    LEFT JOIN contrat_travail_type t ON t.id_type_contrat = ct.id_type_contrat
                    WHERE ct.id_contrat_travail = :id
                    LIMIT 1";
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            error_log("ContratTravailModel::getById error: " . $e->getMessage());
            return null;
        }
    }

    public function compteRenouvellement($id_contrat_travail)
    {
        try {
            $sql = "SELECT COUNT(*) as nb FROM contrat_travail_renouvellement WHERE id_contrat_travail = :id";
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute([':id' => $id_contrat_travail]);
            $r = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) ($r['nb'] ?? 0);
        } catch (\Exception $e) {
            error_log("ContratTravailModel::compteRenouvellement error: " . $e->getMessage());
            return false;
        }
    }

    public function InsertRenouvellement($id_contrat_travail, $nouvelle_date_fin, $commentaire, $date_renouvellement, $pathPdf = null)
    {
        try {
            $sql = "INSERT INTO contrat_travail_renouvellement 
                    (id_contrat_travail, nouvelle_date_fin, commentaire, date_renouvellement, date_creation, pathPdf)
                    VALUES (:id_contrat_travail, :nouvelle_date_fin, :commentaire, :date_renouvellement, :date_creation, :pathPdf)";
            $stmt = Flight::db()->prepare($sql);
            $nowDate = date('Y-m-d');
            $stmt->execute([
                ':id_contrat_travail' => $id_contrat_travail,
                ':nouvelle_date_fin' => $nouvelle_date_fin,
                ':commentaire' => $commentaire,
                ':date_renouvellement' => $date_renouvellement,
                ':date_creation' => $nowDate,
                ':pathPdf' => $pathPdf
            ]);
            return Flight::db()->lastInsertId();
        } catch (\Exception $e) {
            error_log("erreur de renouvellement: " . $e->getMessage());
            return false;
        }
    }

    public function compteMigrationCDDtoCDI($id_cdd)
    {
        try {
            $sql = "SELECT COUNT(*) as nb FROM contrat_migration_cdd_cdi WHERE id_cdd = :id";
            $stmt = Flight::db()->prepare($sql);
            $stmt->execute([':id' => $id_cdd]);
            $r = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) ($r['nb'] ?? 0);
        } catch (\Exception $e) {
            error_log("ContratTravailModel::compteRenouvellement error: " . $e->getMessage());
            return false;
        }
    }

    // hepler
    private function utf8_decode($str)
    {
        return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
    }

    private function genererContratTravailPDF($employe, $typeContrat, $debut, $fin, $salaire_base, $poste, $date_signature)
    {
        try {
            $baseUrl = Flight::base();

            $pdf = new \FPDF();
            $pdf->AddPage();
        

            $logoPath = $_SERVER['DOCUMENT_ROOT'] . "$baseUrl/public/template/assets/compiled/png/logo.png";
            if (file_exists($logoPath)) {
                $pdf->Image($logoPath, 10, 6, 30);
            }

            $pdf->Ln(20);

            // Titre
            $pdf->SetFont('Arial','B',20);
            $pdf->Cell(0,10, $this->utf8_decode("Contrat de Travail ($typeContrat)"),0,1,'C');
            $pdf->Ln(10);

            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,7, $this->utf8_decode("Entre les soussignés :"));
            $pdf->Ln(3);

            // ---- EMPLOYEUR ----
            $pdf->MultiCell(0,7,$this->utf8_decode("1. L'Employeur : Mazer Enterprise"));
            $pdf->MultiCell(0,7,$this->utf8_decode("Antananarivo, Madagascar"));
            $pdf->MultiCell(0,7,$this->utf8_decode("Représenté par Rakoto Lita Mamy, Directeur Général"));
            $pdf->Ln(5);

            // ---- SALARIÉ ----
            $pdf->MultiCell(0,7,$this->utf8_decode("2. Le Salarié : ".$employe['prenom']." ".$employe['nom']));
            $pdf->MultiCell(0,7,$this->utf8_decode("Email : ".$employe['email']));
            $pdf->MultiCell(0,7,$this->utf8_decode("Téléphone : ".$employe['telephone']));
            $pdf->Ln(5);

            // ---- ARTICLES ----
            $posteM = PosteModel::getById($poste);
            $titrePoste = $posteM['titre'];

            $pdf->SetFont('Arial','B',12);
            $pdf->MultiCell(0,7,$this->utf8_decode("Article 1 : Poste occupé"));
            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,7,$this->utf8_decode("Le salarié occupe le poste de $titrePoste."));
            $pdf->Ln(5);

            $pdf->SetFont('Arial','B',12);
            $pdf->MultiCell(0,7,$this->utf8_decode("Article 2 : Durée du contrat"));
            $pdf->SetFont('Arial','',11);

            $texteFin = ($typeContrat === "CDI") 
                ? "Ce contrat est conclu pour une durée indéterminée et débute le $debut."
                : "Ce contrat est conclu du $debut au $fin.";

            $pdf->MultiCell(0,7,$this->utf8_decode($texteFin));
            $pdf->Ln(5);

            $pdf->SetFont('Arial','B',12);
            $pdf->MultiCell(0,7,$this->utf8_decode("Article 3 : Rémunération"));
            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,7,$this->utf8_decode("La rémunération mensuelle brute est fixée à $salaire_base Ariary."));
            $pdf->Ln(5);

            // SIGNATURE
            $pdf->Ln(20);
            $pdf->Cell(90,10,$this->utf8_decode("Signature employeur"),0,0,'C');
            $pdf->Cell(90,10,$this->utf8_decode("Signature salarié"),0,1,'C');

            // Footer
            $pdf->SetY(-15);
            $pdf->SetFont('Arial','I',8);
            $pdf->Cell(0,10,$this->utf8_decode('Généré par le système RH Mazer - '.date('d/m/Y H:i')),0,0,'C');

            // --- Sauvegarde ---
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/S5/gestion-entreprise-S5/public/uploads/data/document/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $nom_pdf = "contrat_travail_".$employe['nom']."_".$employe['prenom']."_".uniqid().".pdf";
            $chemin = $uploadDir . $nom_pdf;

            $pdf->Output('F', $chemin);

            return $nom_pdf;

        } catch (\Exception $e) {
            error_log("Erreur PDF contrat travail : " . $e->getMessage());
            return null;
        }
    }

    //fonction version metier
    public function creerCDD($id, $debut, $fin, $salaire_base, $date_signature, $poste) {

        $employe = $this->employeModel->getById($id);
        if (!$employe) {
            throw new Exception("Employé introuvable");
        }

        $typeCDD = $this->contratTypeModel->getByTitre('CDD');
        if (!$typeCDD) {
            throw new Exception("Type de contrat CDD introuvable");
        }

        if (!$fin) {
            $fin = date('Y-m-d', strtotime("+{$typeCDD['duree_max']} months", strtotime($debut)));
        }

        $pdf = $this->genererContratTravailPDF(
            $employe,
            "CDD",
            $debut,
            $fin,
            $salaire_base,
            $poste,
            $date_signature
        );

        $idContrat = $this->insert(
            $typeCDD['id_type_contrat'],
            $id,
            $debut,
            $fin,
            $salaire_base,
            $date_signature,
            $poste,
            $pdf
        );

        DocumentModel::insertValableDocument(2, $id, $pdf, $pdf);

        if (!$idContrat) {
            throw new Exception("Erreur lors de la création du contrat CDD");
        }

        return $idContrat;
    }

    public function creerCDI($id, $debut, $fin, $salaire_base, $date_signature, $poste) {

        $employe = $this->employeModel->getById($id);
        if (!$employe) {
            throw new Exception("Employé introuvable");
        }

        $typeCDI = $this->contratTypeModel->getByTitre('CDI');
        if (!$typeCDI) {
            throw new Exception("Type de contrat CDI introuvable");
        }

        $pdf = $this->genererContratTravailPDF(
            $employe,
            "CDI",
            $debut,
            null,
            $salaire_base,
            $poste,
            $date_signature
        );

        $idContrat = $this->insert(
            $typeCDI['id_type_contrat'],
            $id,
            $debut,
            $fin,                // Peut rester NULL pour un CDI
            $salaire_base,
            $date_signature,
            $poste,
            $pdf
        );

        DocumentModel::insertValableDocument(2, $id, $pdf, $pdf);

        if (!$idContrat) {
            throw new Exception("Erreur lors de la création du contrat CDI");
        }

        return $idContrat; 
    }
}
