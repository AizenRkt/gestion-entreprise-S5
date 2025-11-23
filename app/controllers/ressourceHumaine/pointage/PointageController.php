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
}
