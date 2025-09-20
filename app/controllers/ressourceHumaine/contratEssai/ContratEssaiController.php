<?php

namespace app\controllers\ressourceHumaine\contratEssai;

use app\models\ressourceHumaine\contratEssai\ContratEssaiModel;
use app\models\ressourceHumaine\entretien\EntretienModel;
use Flight;

class ContratEssaiController
{
    private $contratEssaiModel;
    private $entretienModel;

    public function __construct()
    {
        $this->contratEssaiModel = new ContratEssaiModel();
        $this->entretienModel = new EntretienModel();
    }

    /**
     * Afficher la page des contrats d'essai avec les candidats recommandés
     */
    public function contratEssai()
    {
        try {
            // Récupérer tous les candidats avec entretien recommandé
            $candidatsRecommandes = $this->getCandidatsRecommandes();
            
            Flight::render('ressourceHumaine/back/contratEssai', [
                'candidatsRecommandes' => $candidatsRecommandes
            ]);
        } catch (\Exception $e) {
            error_log("Erreur dans contratEssai: " . $e->getMessage());
            Flight::render('ressourceHumaine/back/contratEssai', [
                'candidatsRecommandes' => [],
                'error' => 'Une erreur est survenue lors du chargement des données'
            ]);
        }
    }

    /**
     * Récupérer les candidats recommandés lors d'entretiens
     */
    private function getCandidatsRecommandes()
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT 
                    c.id_candidat,
                    c.nom,
                    c.prenom,
                    c.email,
                    c.telephone,
                    c.genre,
                    e.date as date_entretien,
                    e.note_entretien,
                    e.evaluation,
                    e.commentaire,
                    ce.contrat_accepte,
                    ce.date_acceptation
                FROM candidat c
                INNER JOIN entretien_candidat e ON c.id_candidat = e.id_candidat
                LEFT JOIN contrat_essai_acceptation cea ON c.id_candidat = cea.id_candidat
                LEFT JOIN contrat_essai ce ON c.id_candidat = ce.id_candidat
                WHERE e.evaluation = 'recommande'
                AND e.note_entretien IS NOT NULL
                ORDER BY e.date DESC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur SQL dans getCandidatsRecommandes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Marquer qu'un candidat a accepté le contrat
     */
    public function accepterContrat()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_candidat = $_POST['id_candidat'] ?? null;

                if (!$id_candidat) {
                    Flight::json(['error' => 'ID candidat manquant'], 400);
                    return;
                }

                $db = Flight::db();
                
                // Vérifier si une acceptation existe déjà
                $stmt = $db->prepare("SELECT id FROM contrat_essai_acceptation WHERE id_candidat = ?");
                $stmt->execute([$id_candidat]);
                $exists = $stmt->fetch();

                if (!$exists) {
                    // Créer une nouvelle entrée d'acceptation
                    $stmt = $db->prepare("
                        INSERT INTO contrat_essai_acceptation (id_candidat, contrat_accepte, date_acceptation) 
                        VALUES (?, 1, NOW())
                    ");
                    $stmt->execute([$id_candidat]);
                } else {
                    // Mettre à jour l'acceptation existante
                    $stmt = $db->prepare("
                        UPDATE contrat_essai_acceptation 
                        SET contrat_accepte = 1, date_acceptation = NOW() 
                        WHERE id_candidat = ?
                    ");
                    $stmt->execute([$id_candidat]);
                }

                Flight::json(['success' => true, 'message' => 'Contrat accepté avec succès']);

            } catch (\PDOException $e) {
                error_log("Erreur dans accepterContrat: " . $e->getMessage());
                Flight::json(['error' => 'Une erreur est survenue lors de l\'acceptation'], 500);
            }
        } else {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
        }
    }

    /**
     * Générer le PDF du contrat d'essai
     */
    public function generatePdf($id_candidat)
    {
        try {
            // Vérifier que le candidat existe et est recommandé
            $candidat = $this->getCandidatDetails($id_candidat);
            
            if (!$candidat) {
                Flight::redirect('/contratCrea?error=candidat_introuvable');
                return;
            }

            if ($candidat['evaluation'] !== 'recommande') {
                Flight::redirect('/contratCrea?error=candidat_non_recommande');
                return;
            }

            // Chemin vers FPDF
            $baseUrl = Flight::get('flight.base_url'); 
            require_once $_SERVER['DOCUMENT_ROOT'] . $baseUrl . '/vendor/fpdf186/fpdf.php';

            // Données du contrat avec les informations du candidat
            $contrat = [
                'entreprise'    => 'Mazer',
                'adresse_entreprise' => 'Antananarivo, Madagascar',
                'representant'  => 'Rohy Fifaliana',
                'titre'         => 'Directeur Général',
                'salarie'       => $candidat['prenom'] . ' ' . $candidat['nom'],
                'adresse_salarie' => 'Adresse du candidat', // À adapter selon vos données
                'secu'          => '123456789', // À adapter
                'poste'         => 'Poste à pourvoir', // À adapter selon le poste
                'date_entree'   => date('d/m/Y', strtotime('+7 days')) // Date d'entrée dans 7 jours
            ];

            $pdf = new \FPDF();
            $pdf->AddPage();

            // Logo
            $logoPath = $_SERVER['DOCUMENT_ROOT'] . $baseUrl . "/public/template/assets/compiled/png/logo.png"; 
            if (file_exists($logoPath)) {
                $pdf->Image($logoPath, 10, 6, 30);
            }

            // Décalage sous le logo
            $pdf->Ln(20);

            // Titre principal
            $pdf->SetFont('Arial','B',20);
            $pdf->Cell(0,10,$this->utf8_decode("Contrat De Travail"),0,1,'C');
            $pdf->Cell(0,10,$this->utf8_decode("Essai"),0,1,'C');
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
                "Le Salarié commencera ses fonctions le ".$contrat['date_entree']."."
            ));
            $pdf->Ln(5);

            $pdf->SetFont('Arial','B',12);
            $pdf->MultiCell(0,7,$this->utf8_decode("Article 3 : Période d'essai"));
            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,7,$this->utf8_decode(
                "Le présent contrat est conclu pour une période d'essai de 3 mois, renouvelable une fois. "
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

            // Enregistrer dans la base que le contrat a été généré
            $this->marquerContratGenere($id_candidat);

            $pdf->Output('I', "contrat_essai_".$candidat['nom']."_".$candidat['prenom'].".pdf");
            exit;

        } catch (\Exception $e) {
            error_log("Erreur dans generatePdf: " . $e->getMessage());
            Flight::redirect('/contratCrea?error=generation_pdf');
        }
    }

    /**
     * Récupérer les détails d'un candidat recommandé
     */
    private function getCandidatDetails($id_candidat)
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("
                SELECT 
                    c.*,
                    e.evaluation,
                    e.note_entretien,
                    e.date as date_entretien
                FROM candidat c
                INNER JOIN entretien_candidat e ON c.id_candidat = e.id_candidat
                WHERE c.id_candidat = ? 
                AND e.evaluation = 'recommande'
                ORDER BY e.date DESC
                LIMIT 1
            ");
            
            $stmt->execute([$id_candidat]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erreur dans getCandidatDetails: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Marquer qu'un contrat a été généré
     */
    private function marquerContratGenere($id_candidat)
    {
        try {
            $db = Flight::db();
            
            // Vérifier si un enregistrement existe déjà
            $stmt = $db->prepare("SELECT id FROM contrat_essai_generation WHERE id_candidat = ?");
            $stmt->execute([$id_candidat]);
            $exists = $stmt->fetch();

            if (!$exists) {
                $stmt = $db->prepare("
                    INSERT INTO contrat_essai_generation (id_candidat, date_generation) 
                    VALUES (?, NOW())
                ");
                $stmt->execute([$id_candidat]);
            }
        } catch (\PDOException $e) {
            error_log("Erreur dans marquerContratGenere: " . $e->getMessage());
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
