<?php

namespace app\controllers\ressourceHumaine\pointage;

use app\models\ressourceHumaine\pointage\PointageModel;
use app\models\ressourceHumaine\AuthModel;
use Flight;

class PointageController
{
    private $pointageModel;

    public function __construct()
    {
        $this->pointageModel = new PointageModel();
    }
    protected function getEmployeId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        if (isset($_SESSION['user']['id_user'])) {
            $user = AuthModel::getById($_SESSION['user']['id_user']);
            return $user['id_employe'] ?? null;
        }
        return null;
    }

    public function showPointagePage()
    {
        $id_employe = $this->getEmployeId();
        if (!$id_employe) {
            Flight::redirect('/log?error=Veuillez+vous+connecter.');
            return;
        }

        // Remplir les jours de pointage manquants avant d'afficher la page
        $this->pointageModel->fillMissingPointages($id_employe);

        $hasCheckedIn = $this->pointageModel->hasCheckedInToday($id_employe);
        $hasCheckedOut = $this->pointageModel->hasCheckedOutToday($id_employe);

        Flight::render('auth/user/pointage', [
            'hasCheckedIn' => $hasCheckedIn,
            'hasCheckedOut' => $hasCheckedOut
        ]);
    }

    public function checkin()
    {
        $id_employe = $this->getEmployeId();
        if (!$id_employe) {
            Flight::json(['success' => false, 'message' => 'Employé non identifié.']);
            return;
        }

        if ($this->pointageModel->hasCheckedInToday($id_employe)) {
            Flight::json(['success' => false, 'message' => 'Vous avez déjà fait un check-in aujourd\'hui.']);
            return;
        }

        $checkinId = $this->pointageModel->saveCheckin($id_employe);

        if ($checkinId) {
            $this->pointageModel->createOrUpdatePointage($id_employe, date('Y-m-d'), $checkinId);
            Flight::json(['success' => true, 'message' => 'Check-in enregistré avec succès.']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du check-in.']);
        }
    }

    public function checkout()
    {
        $id_employe = $this->getEmployeId();
        if (!$id_employe) {
            Flight::json(['success' => false, 'message' => 'Employé non identifié.']);
            return;
        }
        
        if (!$this->pointageModel->hasCheckedInToday($id_employe)) {
            Flight::json(['success' => false, 'message' => 'Veuillez d\'abord faire un check-in.']);
            return;
        }

        if ($this->pointageModel->hasCheckedOutToday($id_employe)) {
            Flight::json(['success' => false, 'message' => 'Vous avez déjà fait un check-out aujourd\'hui.']);
            return;
        }

        $checkoutId = $this->pointageModel->saveCheckout($id_employe);

        if ($checkoutId) {
            $this->pointageModel->createOrUpdatePointage($id_employe, date('Y-m-d'), null, $checkoutId);
            Flight::json(['success' => true, 'message' => 'Check-out enregistré avec succès.']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du check-out.']);
        }
    }


    public function getMyHistorique()
    {
        $id_employe = $this->getEmployeId();
        if (!$id_employe) {
            Flight::json(['success' => false, 'message' => 'Employé non identifié.'], 403);
            return;
        }

        try {
            $historique = $this->pointageModel->getHistoriqueByEmployeId($id_employe);
            Flight::json(['success' => true, 'data' => $historique]);
        } catch (\Exception $e) {
            // Log the error if possible
            Flight::json(['success' => false, 'message' => 'Erreur du serveur lors de la récupération de l\'historique.'], 500);
        }
    }
    public function getAllHistorique()
    {
        // Get all employee IDs
        $employeIds = $this->pointageModel->getAllEmployeIds();

        // Fill missing pointages for each employee
        foreach ($employeIds as $id_employe) {
            $this->pointageModel->fillMissingPointages($id_employe);
        }

        $historique = $this->pointageModel->getAllHistorique();
        Flight::render('ressourceHumaine/back/pointage/pointageHistorique', ['pointages' => $historique]);
    }

    public function updatePointage()
    {
        $id_pointage = Flight::request()->data->id_pointage;
        $datetime_checkin = Flight::request()->data->datetime_checkin;
        $datetime_checkout = Flight::request()->data->datetime_checkout;

        if (empty($id_pointage)) {
            Flight::json(['success' => false, 'message' => 'ID de pointage manquant.'], 400);
            return;
        }

        // Sanitize inputs
        $id_pointage = (int) $id_pointage;
        $datetime_checkin = (string) $datetime_checkin ?: null;
        $datetime_checkout = (string) $datetime_checkout ?: null;

        try {
            $updated = $this->pointageModel->updatePointageRecord($id_pointage, $datetime_checkin, $datetime_checkout);

            if ($updated) {
                Flight::json(['success' => true, 'message' => 'Pointage mis à jour avec succès.', 'updated' => $updated]);
            } else {
                Flight::json(['success' => false, 'message' => 'Impossible de mettre à jour le pointage.'], 500);
            }
        } catch (\Exception $e) {
            Flight::json(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }
}
