<?php

namespace app\controllers\ressourceHumaine\contratEssai;

use app\models\ressourceHumaine\contratEssai\ContratEssaiModel;
use app\models\ressourceHumaine\entretien\EntretienModel;
use app\models\ressourceHumaine\annonce\Annonce;
use app\models\ressourceHumaine\candidat\CandidatModel;
use app\models\ressourceHumaine\employe\EmployeModel;

require __DIR__ . '/../../../../vendor/fpdf186/fpdf.php';

use Flight;

class ContratEssaiController
{
    private $contratEssaiModel;
    private $entretienModel;
    private $candidatModel;


    public function __construct()
    {
        $this->contratEssaiModel = new ContratEssaiModel();
        $this->entretienModel = new EntretienModel();
        $this->candidatModel = new CandidatModel();
    }

    public function contratEssai()
    {
        try {

            $annonceModel = new Annonce(Flight::db());
            $annonce = $annonceModel->getAllAnnonces();
            
            Flight::render('ressourceHumaine/back/contratEssai', [
                'annonce' => $annonce
            ]);
        } catch (\Exception $e) {
            error_log("Erreur dans contratEssai: " . $e->getMessage());
            Flight::render('ressourceHumaine/back/contratEssai', [
                'annonce' => [],
                'error' => 'Une erreur est survenue lors du chargement des données'
            ]);
        }
    }

    private function migrerCandidatVersEmploye($id_candidat, $date_embauche)
    {
        try {
            $db = Flight::db();
            
            $candidat = $this->candidatModel->getById($id_candidat);
            
            if (!$candidat) {
                Flight::redirect('/contratCrea?error=candidat_introuvable');
                return;
            }

            if ($candidat) {
                // Vérifier qu'un employé n'existe pas déjà
                $stmt = $db->prepare("SELECT id_employe FROM employe WHERE id_candidat = ?");
                $stmt->execute([$id_candidat]);
                $exists = $stmt->fetch();

                if (!$exists) {

                    EmployeModel::createEmploye(
                        $candidat['nom'],
                        $candidat['prenom'],
                        $candidat['email'],
                        $candidat['telephone'],
                        $candidat['genre'],
                        $date_embauche,
                        1,
                        1
                    );

                    return ['success' => true];
                }
            }

            return ['success' => false, 'message' => 'Candidat introuvable ou employé existant'];

        } catch (\PDOException $e) {
            error_log("Erreur dans creerEmployeDepuisCandidat: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la création de l\'employé'];
        }
    }

    private function insertContrat($id_candidat, $debut, $fin, $path)
    {
        $this->contratEssaiModel->insert($id_candidat, $debut, $fin, $path);
    }

    public function generatePdf($id_candidat, $date_debut, $date_fin, $duree_mois)
    {
        try {
            $candidat = $this->candidatModel->getById($id_candidat);
            
            if (!$candidat) {
                Flight::redirect('/contratCrea?error=candidat_introuvable');
                return;
            }

            // Chemin vers FPDF
            $baseUrl = Flight::base(); 
            require_once $_SERVER['DOCUMENT_ROOT'] . $baseUrl . '/vendor/fpdf186/fpdf.php';

            $contrat = [
                'entreprise'    => 'Mazer Enterprise',
                'adresse_entreprise' => 'Antananarivo, Madagascar',
                'representant'  => 'Rakoto Lita Mamy',
                'titre'         => 'Directeur Général',
                'salarie'       => $candidat['prenom'] . ' ' . $candidat['nom'],
                'adresse_salarie' => 'Rue du ' . $candidat['prenom'], 
                'secu'          => '123456789', 
                'poste'         => 'Poste à pourvoir', 
                'date_entree'   => $date_debut 
            ];

            $pdf = new \FPDF();
            $pdf->AddPage();

            $logoPath = $baseUrl . "/public/template/assets/compiled/png/logo.png"; 
            if (file_exists($logoPath)) {
                $pdf->Image($logoPath, 10, 6, 30);
            }

            // Décalage sous le logo
            $pdf->Ln(20);

            // Titre principal
            $pdf->SetFont('Arial','B',20);
            $pdf->Cell(0,10,$this->utf8_decode("Contrat D'Essai"),0,1,'C');
            $pdf->Ln(15);

            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,7,$this->utf8_decode("Entre les soussignés :"));
            $pdf->Ln(3);

            // Partie employeur
            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,7,$this->utf8_decode("1. L'Employeur : ".$contrat['entreprise']));
            $pdf->MultiCell(0,7,$this->utf8_decode($contrat['adresse_entreprise']));
            $pdf->MultiCell(0,7,$this->utf8_decode("Représenté par ".$contrat['representant']));
            $pdf->MultiCell(0,7,$this->utf8_decode("Titre : ".$contrat['titre']));
            $pdf->Ln(5);

            // Partie salarié
            $pdf->MultiCell(0,7,$this->utf8_decode("2. Le Salarié : ".$contrat['salarie']));
            $pdf->MultiCell(0,7,$this->utf8_decode($contrat['adresse_salarie']));
            $pdf->MultiCell(0,7,$this->utf8_decode("Email : ".$candidat['email']));
            $pdf->MultiCell(0,7,$this->utf8_decode("Téléphone : ".$candidat['telephone']));
            $pdf->Ln(8);

            $pdf->MultiCell(0,7,$this->utf8_decode("Il a été convenu ce qui suit :"));
            $pdf->Ln(5);

            // Articles
            $pdf->SetFont('Arial','B',12);
            $pdf->MultiCell(0,7,$this->utf8_decode("Article 1 : Objet du contrat"));
            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,7,$this->utf8_decode(
                "Le présent contrat a pour objet d'établir les conditions dans lesquelles le Salarié sera employé "
                ."par l'Employeur en tant que ".$contrat['poste']."."
            ));
            $pdf->Ln(5);

            $pdf->SetFont('Arial','B',12);
            $pdf->MultiCell(0,7,$this->utf8_decode("Article 2 : Entrée en fonction"));
            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,7,$this->utf8_decode(
                "Le Salarié entrera en fonction le ".$contrat['date_entree']."."
            ));
            $pdf->Ln(5);

            $pdf->SetFont('Arial','B',12);
            $pdf->MultiCell(0,7,$this->utf8_decode("Article 3 : Période d'essai"));
            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,7,$this->utf8_decode(
                "Le présent contrat est conclu pour une période d'essai de ".$duree_mois." mois, renouvelable une fois. "
                ."Durant cette période, chacune des parties peut rompre le contrat sans préavis ni indemnité."
            ));
            $pdf->Ln(5);

            $pdf->SetFont('Arial','B',12);
            $pdf->MultiCell(0,7,$this->utf8_decode("Article 4 : Rémunération"));
            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,7,$this->utf8_decode(
                "La rémunération sera définie selon la grille salariale de l'entreprise et les compétences du candidat."
            ));

            // Signature
            $pdf->Ln(25);
            $pdf->Cell(90,10,$this->utf8_decode("Signature employeur"),0,0,'C');
            $pdf->Cell(90,10,$this->utf8_decode("Signature salarié"),0,1,'C');
            $pdf->Ln(10);
            $pdf->Cell(90,10,"Date : ".date('d/m/Y'),0,0,'C');
            $pdf->Cell(90,10,"Date : ".date('d/m/Y'),0,1,'C');

            // Pied de page
            $pdf->SetY(-15);
            $pdf->SetFont('Arial','I',8);
            $pdf->Cell(0,10,$this->utf8_decode('Généré par le système RH Mazer - '.date('d/m/Y H:i')),0,0,'C');


            $nom_pdf = "contrat_essai_".$candidat['nom']."_".$candidat['prenom'].".pdf";
            $pdf->Output('I', $nom_pdf);
            
            return $nom_pdf;

        } catch (\Exception $e) {
            error_log("Erreur dans generatePdf: " . $e->getMessage());
            Flight::redirect('/contratCrea?error=generation_pdf');
        }
    }

    public function creerContratOfficiel()
    {
        $id = $_GET['id_candidat'] ?? null;
        $date_debut = $_GET['date_debut'] ?? null;      
        $nb_mois = $_GET['duree_mois'] ?? null;
        $date_fin = null;        

        if ($date_debut && $nb_mois) {
            $date_fin = date('Y-m-d', strtotime("+$nb_mois months", strtotime($date_debut)));
        } else {
            $date_fin = null;
        }

        try {
        
            // mettre le candidat en employé
            $this->migrerCandidatVersEmploye($id, $date_debut);

            // générer le PDF du contrat officiel
            $nom_pdf = $this->generatePdf($id, $date_debut, $date_fin, $nb_mois);

            // créer le contrat dans la base
            $this->insertContrat($id, $date_debut, $date_fin, $nom_pdf);
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Helper pour gérer l'encodage UTF-8 dans FPDF
     */
    private function utf8_decode($str)
    {
        return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
    }
}
