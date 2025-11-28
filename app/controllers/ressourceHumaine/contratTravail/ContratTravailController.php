<?php

namespace app\controllers\ressourceHumaine\contratTravail;

use app\models\ressourceHumaine\employe\EmployeModel;
use app\models\ressourceHumaine\contratTravail\ContratTravailTypeModel;
use app\models\ressourceHumaine\contratTravail\ContratTravailModel;
use app\models\ressourceHumaine\poste;
use app\models\ressourceHumaine\poste\PosteModel;
use Flight;

use DateTime;

class ContratTravailController
{

    private $employeModel;
    private $contratTravailModel;
    private $contratTypeModel;

    public function __construct()
    {
        $this->employeModel = new EmployeModel();
        $this->contratTypeModel = new ContratTravailTypeModel();
        $this->contratTravailModel = new ContratTravailModel();
    }

    public function contratTravail() {
        $postes = PosteModel::getAll();
        Flight::render('ressourceHumaine/back/contratTravail/contratTravail', ['postes' => $postes]);    
    }

    public function contratTravailList() {
        Flight::render('ressourceHumaine/back/contratTravail/contratTravailList');    
    }

    public function creerCDI($id) {

        $debut = Flight::request()->query->debut ?? date('Y-m-d');
        $salaire_base = Flight::request()->query->salaire_base ?? null;
        $date_signature = Flight::request()->query->date_signature ?? null;
        $fin = null; 
        $poste = Flight::request()->query->poste ?? null;

        $employe = $this->employeModel->getById($id);
        if (!$employe) {
            Flight::json(['success' => false, 'message' => 'Employé introuvable']);
            return;
        }

        $typeCDI = $this->contratTypeModel->getByTitre('CDI');
        if (!$typeCDI) {
            Flight::json(['success' => false, 'message' => 'Type de contrat CDI introuvable']);
            return;
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

        $idContrat = $this->contratTravailModel->insert(
            $typeCDI['id_type_contrat'],
            $id,
            $debut,
            $fin,
            $salaire_base,
            $date_signature,
            $poste,
            $pdf
        );

        if ($idContrat) {
            Flight::json(['success' => true, 'message' => 'CDI créé avec succès', 'id_contrat' => $idContrat]);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la création du CDI']);
        }
    }

    public function creerCDD($id) {

        $debut = Flight::request()->query->debut ?? date('Y-m-d');
        $fin = Flight::request()->query->fin ?? null;
        $salaire_base = Flight::request()->query->salaire_base ?? null;
        $date_signature = Flight::request()->query->date_signature ?? null;
        $poste = Flight::request()->query->poste ?? null;

        $employe = $this->employeModel->getById($id);
        if (!$employe) {
            Flight::json(['success' => false, 'message' => 'Employé introuvable']);
            return;
        }

        $typeCDD = $this->contratTypeModel->getByTitre('CDD');
        if (!$typeCDD) {
            Flight::json(['success' => false, 'message' => 'Type de contrat CDD introuvable']);
            return;
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

        $idContrat = $this->contratTravailModel->insert(
            $typeCDD['id_type_contrat'],
            $id,
            $debut,
            $fin,
            $salaire_base,
            $date_signature,
            $poste,
            $pdf
        );

        if ($idContrat) {
            
            Flight::json(['success' => true, 'message' => 'CDD créé avec succès', 'id_contrat' => $idContrat]);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la création du CDD']);
        }
    }

    public function getAllDetail()
    {
        $details = $this->contratTravailModel->getAllDetail();

        if ($details === false) {
            Flight::json([
                'success' => false,
                'message' => 'Erreur interne lors de la récupération des contrats.'
            ], 500);
            return;
        }

        if (count($details) === 0) {
            Flight::json([
                'success' => true,
                'message' => 'Aucun contrat trouvé.',
                'data' => []
            ]);
            return;
        }

        Flight::json([
            'success' => true,
            'data' => $details
        ]);
    }

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

    public function renouvellerCDD($id)
    {
        try {
            $nouvelleDateFin = Flight::request()->query->nouvelle_date_fin ?? null;
            $commentaire = Flight::request()->query->commentaire ?? '';

            $dateRenouvellement = Flight::request()->query->date_renouvellement ?? date('Y-m-d');
            $c = \DateTime::createFromFormat('Y-m-d', $dateRenouvellement);


            if (!$nouvelleDateFin) {
                Flight::json(['success' => false, 'message' => 'La nouvelle date de fin est obligatoire.']);
                return;
            }

            $d = \DateTime::createFromFormat('Y-m-d', $nouvelleDateFin);
            if (!$d || $d->format('Y-m-d') !== $nouvelleDateFin) {
                Flight::json(['success' => false, 'message' => 'Format de date invalide (attendu YYYY-MM-DD).']);
                return;
            }

            $contrat = $this->contratTravailModel->getById($id);
            if (!$contrat) {
                Flight::json(['success' => false, 'message' => 'Contrat introuvable.'], 404);
                return;
            }

            if ($contrat['id_type_contrat'] != 2) {
                Flight::json(['success' => false, 'message' => 'Seuls les contrats de type CDD peuvent être renouvelés.']);
                return;
            }

            $finActuelle = $contrat['fin'];
            $dtFinActuelle = $finActuelle ? new \DateTime($finActuelle) : null;
            $dtNouvelle = new \DateTime($nouvelleDateFin);

            if ($dtFinActuelle && $dtNouvelle <= $dtFinActuelle) {
                Flight::json(['success' => false, 'message' => 'La nouvelle date de fin doit être postérieure à la date de fin actuelle.']);
                return;
            }

            $maxNb = 1;
            if ($maxNb !== null) {
                $nb = $this->contratTravailModel->compteRenouvellement($id);
                if ($nb === false) {
                    Flight::json(['success' => false, 'message' => 'Erreur interne lors du comptage des renouvellements.'], 500);
                    return;
                }
                if ($nb >= $maxNb) {
                    Flight::json(['success' => false, 'message' => 'Le nombre maximal de renouvellements a été atteint.']);
                    return;
                }
            }

            $maxDureeMois = 18;
            if ($maxDureeMois !== null) {
                $baseDate = $dtFinActuelle ?: new \DateTime($contrat['debut']);
                $interval = $baseDate->diff($dtNouvelle);
                $months = ($interval->y * 12) + $interval->m + ($interval->d > 0 ? 1 : 0);

                if ($months > $maxDureeMois) {
                    Flight::json(['success' => false, 'message' => "La durée du renouvellement excède la limite autorisée ({$maxDureeMois} mois)."]);
                    return;
                }
            }

            $pathPdfRenouv = $this->genererContratTravailPDF(
                $this->employeModel->getById($contrat['id_employe']),
                "CDD",
                $contrat['debut'],
                $nouvelleDateFin,
                $contrat['salaire_base'],
                $contrat['id_poste'],
                $dateRenouvellement
            );

            $idRenouv = $this->contratTravailModel->InsertRenouvellement($id, $nouvelleDateFin, $commentaire, $dateRenouvellement, $pathPdfRenouv);

            if ($idRenouv === false) {
                Flight::json(['success' => false, 'message' => 'Erreur lors de l\'insertion du renouvellement.'], 500);
                return;
            }

            Flight::json([
                'success' => true,
                'message' => 'Renouvellement enregistré avec succès.',
                'id_renouvellement' => $idRenouv
            ]);

        } catch (\Exception $e) {
            error_log("ContratTravailController::renouvellerCDD error: " . $e->getMessage());
            Flight::json(['success' => false, 'message' => 'Erreur interne : ' . $e->getMessage()], 500);
        }
    }

    public function basculerVersCDI($id) {
        try {

            if ($this->contratTravailModel->compteMigrationCDDtoCDI($id)) {
                Flight::json(['success' => false, 'message' => 'ce cdd a déjà été basculer vers un CDI'], 404);
                return;
            }

            $dateDebut = Flight::request()->query->date_debut ?? null;
            $dateSignature = Flight::request()->query->date_signature ?? date('Y-m-d');
            $ds = \DateTime::createFromFormat('Y-m-d', $dateSignature);

            $salaireBase =  Flight::request()->query->salaire_base ?? null;

            if (!$salaireBase) {
                Flight::json(['success' => false, 'message' => 'le salaire de base est obligatoire'], 404);
                return;
            }           

            $contratCDD = $this->contratTravailModel->getById($id);
            $emp = $this->employeModel->getById($contratCDD['id_employe']);

            $pdf = $this->genererContratTravailPDF(
                $emp,
                "CDI",
                $dateDebut,
                null,
                $salaireBase,
                $contratCDD['id_poste'],
                $dateSignature
            );

            $idContrat = $this->contratTravailModel->insert(
                1,
                $emp['id_employe'],
                $dateDebut,
                null,
                $salaireBase,
                $dateSignature,
                $contratCDD['id_poste'],
                $pdf
            );


            $stmt = Flight::db()->prepare("
                INSERT INTO contrat_migration_cdd_cdi 
                (id_cdd, id_cdi, date_migration)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$id, $idContrat, date('Y-m-d')]);

            Flight::json([
                'success' => true,
                'message' => 'Renouvellement enregistré avec succès.',
                'idContrat' => $idContrat
            ]);

        } catch (\Exception $e) {
            error_log("ContratTravailController::renouvellerCDD error: " . $e->getMessage());
            Flight::json(['success' => false, 'message' => 'Erreur interne : ' . $e->getMessage()], 500);
        }
    }
}