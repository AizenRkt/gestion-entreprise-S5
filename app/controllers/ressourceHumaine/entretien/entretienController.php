<?php

namespace app\controllers\ressourceHumaine\entretien;

use app\models\ressourceHumaine\entretien\EntretienModel;
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

        Flight::render('ressourceHumaine/back/orgaEntretien', [
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
    public function noterEntretien()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_entretien = $_POST['id_entretien'] ?? null;
                $note = $_POST['note'] ?? null;
                $evaluation = $_POST['evaluation'] ?? null;
                $commentaire = $_POST['commentaire'] ?? null;

                // Validation des données
                if (!$id_entretien || $note === null || !$evaluation) {
                    Flight::json(['error' => 'ID entretien, note et évaluation sont obligatoires'], 400);
                    return;
                }

                // Validation de la note (0-10)
                if (!is_numeric($note) || $note < 0 || $note > 10) {
                    Flight::json(['error' => 'La note doit être comprise entre 0 et 10'], 400);
                    return;
                }

                // Validation de l'évaluation
                $evaluationsValides = ['recommande', 'reserve', 'refuse'];
                if (!in_array($evaluation, $evaluationsValides)) {
                    Flight::json(['error' => 'Évaluation non valide'], 400);
                    return;
                }

                // Noter l'entretien
                $result = $this->entretienModel->noterEntretien(
                    $id_entretien,
                    $note,
                    $evaluation,
                    $commentaire
                );

                if ($result['success']) {
                    Flight::json([
                        'success' => true,
                        'message' => $result['message']
                    ]);
                } else {
                    Flight::json(['error' => $result['message']], 400);
                }

            } catch (\Exception $e) {
                error_log("Erreur dans noterEntretien: " . $e->getMessage());
                Flight::json(['error' => 'Une erreur est survenue lors de la notation'], 500);
            }
        } else {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
        }
    }

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

    /**
     * Récupérer les informations d'un candidat (AJAX)
     */
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

    /**
     * Lister tous les entretiens
     */
    public function listerEntretiens()
    {
        $entretiens = $this->entretienModel->getTousEntretiens();

        Flight::render('ressourceHumaine/entretien/listeEntretiens', [
            'entretiens' => $entretiens
        ]);
    }

    /**
     * Modifier un entretien
     */
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

    /**
     * Supprimer un entretien
     */
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
}