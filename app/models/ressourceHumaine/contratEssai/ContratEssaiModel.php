<?php
namespace app\models\ressourceHumaine\contratEssai;

use Flight;
use PDO;
use FPDF;

class ContratEssaiModel {

    public function insert($id_candidat, $debut, $fin) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO contrat_essai (id_candidat, debut, fin) 
                                  VALUES (:id_candidat, :debut, :fin)");
            $stmt->execute([
                ':id_candidat' => $id_candidat,
                ':debut'       => $debut,
                ':fin'         => $fin
            ]);
            return "Contrat d'essai inséré avec succès.";
        } catch (\PDOException $e) {
            return "Erreur d'insertion : " . $e->getMessage();
        }
    }

    public function getAll() {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT * FROM contrat_essai ORDER BY debut DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getById($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT ce.*, c.nom, c.prenom 
                                  FROM contrat_essai ce 
                                  JOIN candidat c ON ce.id_candidat = c.id_candidat 
                                  WHERE ce.id_contrat_essai = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function update($id, $id_candidat, $debut, $fin) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE contrat_essai 
                                  SET id_candidat = :id_candidat, debut = :debut, fin = :fin 
                                  WHERE id_contrat_essai = :id");
            $stmt->execute([
                ':id'         => $id,
                ':id_candidat'=> $id_candidat,
                ':debut'      => $debut,
                ':fin'        => $fin
            ]);
            return "Mise à jour réussie.";
        } catch (\PDOException $e) {
            return "Erreur de mise à jour : " . $e->getMessage();
        }
    }

    public function delete($id) {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("DELETE FROM contrat_essai WHERE id_contrat_essai = ?");
            $stmt->execute([$id]);
            return "Suppression réussie.";
        } catch (\PDOException $e) {
            return "Erreur de suppression : " . $e->getMessage();
        }
    }


public function generatePdf($id) {
    // Chemin vers FPDF
    $baseUrl = Flight::get('flight.base_url'); 
    require_once $_SERVER['DOCUMENT_ROOT'] . $baseUrl . '/vendor/fpdf186/fpdf.php';

    // Exemple de données
    $contrat = [
        'entreprise'    => 'Mazer',
        'adresse_entreprise' => 'Antananarivo',
        'representant'  => 'Rohy Fifaliana',
        'titre'         => 'Directeur Général',
        'salarie'       => 'Rakoto Miora',
        'adresse_salarie' => 'Ambohibao',
        'secu'          => '123456789',
        'poste'         => 'Développeur Web',
        'date_entree'   => '01/10/2025'
    ];

    $pdf = new \FPDF();
    $pdf->AddPage();

    $logoPath = $_SERVER['DOCUMENT_ROOT'] . $baseUrl . "/public/template/assets/compiled/png/logo.png"; 
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 10, 6, 30); // (fichier, x, y, largeur)
    }

    // Décalage sous le logo
    $pdf->Ln(20);

    // Titre principal
    $pdf->SetFont('Arial','B',20);
    $pdf->Cell(0,10,utf8_decode("Contrat De Travail"),0,1,'C');
    $pdf->Cell(0,10,utf8_decode("Essai"),0,1,'C');
    $pdf->Ln(15);


    $pdf->SetFont('Arial','',11);
    $pdf->MultiCell(0,7,utf8_decode("Entre les soussignés :"));
    $pdf->Ln(3);

    // Partie employeur
    $pdf->SetFont('Arial','',11);
    $pdf->MultiCell(0,7,utf8_decode("1. L Employeur : ".$contrat['entreprise']));
    $pdf->MultiCell(0,7,utf8_decode($contrat['adresse_entreprise']));
    $pdf->MultiCell(0,7,utf8_decode("Représenté par ".$contrat['representant']));
    $pdf->MultiCell(0,7,utf8_decode("Titre : ".$contrat['titre']));
    $pdf->Ln(5);

    // Partie salarié
    $pdf->MultiCell(0,7,utf8_decode("2. Le Salarié : ".$contrat['salarie']));
    $pdf->MultiCell(0,7,utf8_decode($contrat['adresse_salarie']));
    $pdf->MultiCell(0,7,utf8_decode("Numéro de sécurité sociale : ".$contrat['secu']));
    $pdf->Ln(8);

    $pdf->MultiCell(0,7,utf8_decode("Il a été convenu ce qui suit :"));
    $pdf->Ln(5);

    // Articles
    $pdf->SetFont('Arial','B',12);
    $pdf->MultiCell(0,7,utf8_decode("Article 1 : Objet du contrat"));
    $pdf->SetFont('Arial','',11);
    $pdf->MultiCell(0,7,utf8_decode(
        "Le présent contrat a pour objet d établir les conditions dans lesquelles le Salarié sera employé "
        ."par l Employeur en tant que ".$contrat['poste']."."
    ));
    $pdf->Ln(5);

    $pdf->SetFont('Arial','B',12);
    $pdf->MultiCell(0,7,utf8_decode("Article 2 : Entrée en fonction"));
    $pdf->SetFont('Arial','',11);
    $pdf->MultiCell(0,7,utf8_decode(
        "Le Salarié commencera ses fonctions le ".$contrat['date_entree']."."
    ));

    // Signature
    $pdf->Ln(25);
    $pdf->Cell(90,10,utf8_decode("Signature employeur"),0,0,'C');
    $pdf->Cell(90,10,utf8_decode("Signature salarié"),0,1,'C');

    // Pied de page (copyright)
    $pdf->SetY(-15);
    $pdf->SetFont('Arial','I',10);

    $pdf->Output('I', "contrat_essai.pdf");
    exit;
}


}
