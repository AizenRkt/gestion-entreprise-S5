<?php

namespace app\controllers\ressourceHumaine\entretien;

use app\models\ressourceHumaine\entretien\EntretienModel;
use app\models\ressourceHumaine\candidat\CandidatModel;
use app\models\ressourceHumaine\scoring\ScoringModel;
use Dom\Entity;
use Flight;

class entretienController
{
    private $entretienModel;

    public function __construct()
    {
        $this->entretienModel = new EntretienModel();
    }

    /**
     * Afficher le formulaire d'organisation d'entretien
     */
    public function orgaEntretien()
    {
        // Récupérer tous les candidats pour le formulaire
        $candidats = $this->entretienModel->getTousCandidats();

        $id_candidat = $_GET['id_candidat'] ?? null;

        $candidat = null;
        if ($id_candidat) {
            $candidatModel = new CandidatModel();
            $candidat = $candidatModel->getById($id_candidat);
        }

        Flight::render('ressourceHumaine/back/orgaEntretien', [
            'candidat' => $candidat,
            'candidats' => $candidats
        ]);

    }


    public function getEntretiensPlanning()
    {
        try {
            // Récupérer le mois et l'année depuis les paramètres GET (optionnel)
            $month = $_GET['month'] ?? date('m');
            $year = $_GET['year'] ?? date('Y');

            // Récupérer tous les entretiens du mois
            $entretiens = $this->entretienModel->getEntretiensByMonth($month, $year);

            Flight::json($entretiens);

        } catch (\PDOException $e) {
            error_log("Erreur dans getEntretiensPlanning: " . $e->getMessage());
            Flight::json(['error' => 'Une erreur est survenue lors de la récupération des entretiens'], 500);
        }
    }

    /**
     * Noter un entretien
     */
    // public function noterEntretien()
    // {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         try {
    //             $id_entretien = $_POST['id_entretien'] ?? null;
    //             $note = $_POST['note'] ?? null;
    //             $evaluation = $_POST['evaluation'] ?? null;
    //             $commentaire = $_POST['commentaire'] ?? null;

    //             // Validation des données
    //             if (!$id_entretien || $note === null || !$evaluation) {
    //                 Flight::json(['error' => 'ID entretien, note et évaluation sont obligatoires'], 400);
    //                 return;
    //             }

    //             // Validation de la note (0-10)
    //             if (!is_numeric($note) || $note < 0 || $note > 10) {
    //                 Flight::json(['error' => 'La note doit être comprise entre 0 et 10'], 400);
    //                 return;
    //             }

    //             // Validation de l'évaluation
    //             $evaluationsValides = ['recommande', 'reserve', 'refuse'];
    //             if (!in_array($evaluation, $evaluationsValides)) {
    //                 Flight::json(['error' => 'Évaluation non valide'], 400);
    //                 return;
    //             }

    //             // Noter l'entretien
    //             $result = $this->entretienModel->noterEntretien(
    //                 $id_entretien,
    //                 $note,
    //                 $evaluation,
    //                 $commentaire
    //             );

    //             if ($result['success']) {
    //                 Flight::json([
    //                     'success' => true,
    //                     'message' => $result['message']
    //                 ]);
    //             } else {
    //                 Flight::json(['error' => $result['message']], 400);
    //             }

    //         } catch (\Exception $e) {
    //             error_log("Erreur dans noterEntretien: " . $e->getMessage());
    //             Flight::json(['error' => 'Une erreur est survenue lors de la notation'], 500);
    //         }
    //     } else {
    //         Flight::json(['error' => 'Méthode non autorisée'], 405);
    //     }
    // }

    /**
     * Récupérer les détails d'un entretien
     */
    public function getEntretienDetails()
    {
        $id_entretien = $_GET['id'] ?? null;

        if (!$id_entretien) {
            Flight::json(['error' => 'ID entretien manquant'], 400);
            return;
        }

        $entretien = $this->entretienModel->getEntretienById($id_entretien);

        if ($entretien) {
            Flight::json($entretien);
        } else {
            Flight::json(['error' => 'Entretien non trouvé'], 404);
        }
    }

    /**
     * Créer un nouvel entretien
     */
    public function creerEntretien()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Récupérer les données du formulaire
                $id_candidat = $_POST['id_candidat'] ?? null;
                $date_entretien = $_POST['date_entretien'] ?? null;
                $heure_entretien = $_POST['heure_entretien'] ?? null;
                $duree_entretien = $_POST['duree_entretien'] ?? null;

                // Validation des données
                if (!$id_candidat || !$date_entretien || !$heure_entretien || !$duree_entretien) {
                    Flight::json(['error' => 'Tous les champs sont obligatoires'], 400);
                    return;
                }

                // Combiner date et heure
                $datetime = $date_entretien . ' ' . $heure_entretien . ':00';

                // Récupérer l'ID de l'utilisateur connecté (si disponible)
                $id_user = $_SESSION['user_id'] ?? null;

                // Créer l'entretien avec vérification de conflit
                $result = $this->entretienModel->creerEntretien(
                    $id_candidat,
                    $datetime,
                    $duree_entretien,
                    $id_user
                );

                if ($result['success']) {
                    // Récupérer les informations du candidat
                    $candidat = $this->entretienModel->getCandidatById($id_candidat);

                    Flight::json([
                        'success' => true,
                        'message' => $result['message'],
                        'id_entretien' => $result['id'],
                        'candidat' => $candidat
                    ]);
                } else {
                    Flight::json(['error' => $result['message']], 400);
                }

            } catch (\PDOException $e) {
                error_log("Erreur dans creerEntretien: " . $e->getMessage());
                Flight::json(['error' => 'Une erreur est survenue'], 500);
            }
        } else {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
        }
    }

    public function getCandidatInfo()
    {
        $id_candidat = $_GET['id'] ?? null;

        if (!$id_candidat) {
            Flight::json(['error' => 'ID candidat manquant'], 400);
            return;
        }

        $candidat = $this->entretienModel->getCandidatById($id_candidat);

        if ($candidat) {
            Flight::json($candidat);
        } else {
            Flight::json(['error' => 'Candidat non trouvé'], 404);
        }
    }

    public function listerEntretiens()
    {
        $entretiens = $this->entretienModel->getTousEntretiens();

        Flight::render('ressourceHumaine/entretien/listeEntretiens', [
            'entretiens' => $entretiens
        ]);
    }

    public function modifierEntretien()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_entretien = $_POST['id_entretien'] ?? null;
                $date_entretien = $_POST['date_entretien'] ?? null;
                $heure_entretien = $_POST['heure_entretien'] ?? null;
                $duree_entretien = $_POST['duree_entretien'] ?? null;

                if (!$id_entretien || !$date_entretien || !$heure_entretien || !$duree_entretien) {
                    Flight::json(['error' => 'Tous les champs sont obligatoires'], 400);
                    return;
                }

                $datetime = $date_entretien . ' ' . $heure_entretien . ':00';

                $result = $this->entretienModel->modifierEntretien($id_entretien, $datetime, $duree_entretien);

                if ($result['success']) {
                    Flight::json(['success' => true, 'message' => $result['message']]);
                } else {
                    Flight::json(['error' => $result['message']], 400);
                }

            } catch (\PDOException $e) {
                error_log("Erreur dans modifierEntretien: " . $e->getMessage());
                Flight::json(['error' => 'Une erreur est survenue'], 500);
            }
        }
    }

    public function supprimerEntretien()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_entretien = $_POST['id_entretien'] ?? null;

                if (!$id_entretien) {
                    Flight::json(['error' => 'ID entretien manquant'], 400);
                    return;
                }

                $result = $this->entretienModel->supprimerEntretien($id_entretien);

                if ($result['success']) {
                    Flight::json(['success' => true, 'message' => $result['message']]);
                } else {
                    Flight::json(['error' => $result['message']], 400);
                }

            } catch (\PDOException $e) {
                error_log("Erreur dans supprimerEntretien: " . $e->getMessage());
                Flight::json(['error' => 'Une erreur est survenue'], 500);
            }
        }
    }

    // public function scoringEntretien() {
    //     $data = json_decode(file_get_contents('php://input'), true);

    //     if (!$data || !isset($data['id_candidat'], $data['id_entretien'], $data['score'], $data['duree'], $data['commentaire'])) {
    //         echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    //         return;
    //     }

    //     $id_candidat = (int) $data['id_candidat'];
    //     $id_entretien = (int) $data['id_entretien'];
    //     $score = (float) $data['score'];

    //     $duree = (int) $data['duree'];
    //     $commentaire = (string) $data['commentaire'];

    //     $id_typeScoring = 2; 

    //     try {
    //         ScoringModel::insertScore($id_candidat, $id_typeScoring, $score, $id_entretien);
    //         $this->entretienModel->creerDetailEntretien($id_entretien, $commentaire);
    //         echo json_encode(['success' => true, 'score' => $score]);
    //     } catch (\Exception $e) {
    //         echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    //     }
    // }

    // public function scoringEntretien() {
    //     $data = json_decode(file_get_contents('php://input'), true);

    //     if (!$data || !isset($data['id_entretien'], $data['note'], $data['evaluation'], $data['commentaire'])) {
    //         echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    //         return;
    //     }

    //     $id_entretien = (int) $data['id_entretien'];
    //     $note = (float) $data['note'];
    //     $evaluation = $data['evaluation'];
    //     $commentaire = $data['commentaire'] ?? null;

    //     try {
    //         // retrouver id_candidat via l’entretien
    //         $entretien = $this->entretienModel->getEntretienById($id_entretien);
    //         $id_candidat = $entretien['id_candidat'];

    //         $id_typeScoring = 2;

    //         // enregistrer le score
    //         ScoringModel::insertScore($id_candidat, $id_typeScoring, $note, $id_entretien);

    //         // enregistrer détail entretien (évaluation + commentaire)
    //         $this->entretienModel->creerDetailEntretien($id_entretien,$evaluation, $commentaire,);

    //         echo json_encode(['success' => true, 'score' => $note]);
    //     } catch (\Exception $e) {
    //         echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    //     }
    // }

    public function scoringEntretien() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id_candidat'], $data['id_entretien'], $data['score'], $data['evaluation'], $data['commentaire'])) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            return;
        }

        $id_candidat = (int) $data['id_candidat'];
        $id_entretien = (int) $data['id_entretien'];
        $score = (float) $data['score'];   
        $evaluation = (string) $data['evaluation'];
        $commentaire = (string) $data['commentaire'];

        $id_typeScoring = 2; 

        try {
            ScoringModel::insertScore($id_candidat, $id_typeScoring, $score, $id_entretien);
            $this->entretienModel->creerDetailEntretien($id_entretien, $evaluation, $commentaire);
            echo json_encode(['success' => true, 'score' => $score]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }



}