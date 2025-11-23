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
            $this->pointageModel->createOrUpdatePointage($id_employe, date('Y-m-d'));
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
            $this->pointageModel->createOrUpdatePointage($id_employe, date('Y-m-d'));
            Flight::json(['success' => true, 'message' => 'Check-out enregistré avec succès.']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du check-out.']);
        }
    }

    /**
     * Retourne tous les pointages (pour l'admin/backoffice) en JSON
     */
    public function getAllHistorique()
    {
        $historique = $this->pointageModel->getAllHistorique();
        // Render a backoffice view showing all pointages
        Flight::render('ressourceHumaine/back/pointage/pointageHistorique', [
            'pointages' => $historique
        ]);
    }

    /**
     * Endpoint pour mettre à jour un enregistrement de pointage (checkin/checkout)
     * Attendu en POST: id_pointage, datetime_checkin (Y-m-d H:i:s), datetime_checkout (Y-m-d H:i:s)
     */
    public function updatePointage()
    {
        $req = Flight::request();
        $data = $req->data;

        $id_pointage = $data['id_pointage'] ?? null;
        $datetime_checkin = $data['datetime_checkin'] ?? null;
        $datetime_checkout = $data['datetime_checkout'] ?? null;

        if (!$id_pointage) {
            Flight::json(['success' => false, 'message' => 'id_pointage manquant.'], 400);
            return;
        }

        $result = $this->pointageModel->updatePointageRecord($id_pointage, $datetime_checkin, $datetime_checkout);

        if ($result && is_array($result)) {
            Flight::json(['success' => true, 'message' => 'Pointage mis à jour.', 'updated' => $result]);
        } elseif ($result === true) {
            // fallback: success but no updated data
            Flight::json(['success' => true, 'message' => 'Pointage mis à jour.']);
        } else {
            Flight::json(['success' => false, 'message' => 'Erreur lors de la mise à jour du pointage.'], 500);
        }
    }
}
