<?php

namespace app\controllers\ressourceHumaine\absence;

use app\models\ressourceHumaine\absence\AbsenceModel;
use app\models\ressourceHumaine\conge\CongeModel;
use app\models\ressourceHumaine\AuthModel;
use Flight;
use PDO;

class AbsenceController
{
    private $absenceModel;
    private $congeModel;


    public function __construct()
    {
        $this->absenceModel = new AbsenceModel();
        $this->congeModel = new CongeModel();
    }


    public function showAbsencePage()
    {
        $absences = $this->absenceModel->getAllAbsenceDetails();
        Flight::render('ressourceHumaine/back/absence/absence', ['absences' => $absences]);
    }

    public function validerAbsence()
    {
        $data = Flight::request()->data;
        $id_absence = $data['id_absence'];
        $date_validation = $data['date_validation'];

        $result = $this->absenceModel->validerEtConvertirEnConge($id_absence, $date_validation);

        Flight::json($result);
    }

    public function refuserAbsence()
    {
        if (isset(Flight::request()->query['id_absence'])) {
            $id_absence = Flight::request()->query['id_absence'];
            $this->absenceModel->refuserAbsence($id_absence);
        }
        Flight::redirect('/backOffice/absence');
    }

    /**
     * API: retourne le solde de congé pour une absence donnée (utilisé avant validation)
     * GET param: id (id_absence)
     */
    public function getSoldeForAbsence()
    {
        $q = Flight::request()->query;
        $id = $q['id'] ?? null;
        if (!$id) {
            Flight::json(['success' => false, 'message' => 'id manquant'], 400);
            return;
        }

        $absence = $this->absenceModel->getAbsenceById((int)$id);
        if (!$absence) {
            Flight::json(['success' => false, 'message' => 'Absence introuvable'], 404);
            return;
        }

        // Utiliser la méthode qui calcule les jours ouvrés
        $days = $this->congeModel->calculateWorkingDays($absence['date_debut'], $absence['date_fin']);

        // La période prise en compte doit se terminer à la date de fin de la demande
        $solde = $this->congeModel->calculateSoldeConge((int)$absence['id_employe'], $absence['date_fin'], $absence['date_debut']);

        // Déterminer la répartition
        $paid_days = 0;
        $unpaid_days = 0;
        if ($solde['balance'] >= $days) {
            $paid_days = $days;
        } elseif ($solde['balance'] > 0) {
            $paid_days = $solde['balance'];
            $unpaid_days = $days - $solde['balance'];
        } else {
            $unpaid_days = $days;
        }

        $breakdown = ['paid_days' => $paid_days, 'unpaid_days' => $unpaid_days];

        Flight::json(['success' => true, 'data' => ['solde' => $solde, 'days' => $days, 'breakdown' => $breakdown]]);
    }
}
