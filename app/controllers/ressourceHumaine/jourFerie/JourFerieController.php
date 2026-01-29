<?php

namespace app\controllers\ressourceHumaine\jourFerie;

use app\models\ressourceHumaine\jourFerie\JourFerieModel;
use Flight;

class JourFerieController
{
    private $jourFerieModel;

    public function __construct()
    {
        $this->jourFerieModel = new JourFerieModel();
    }

    public function showJourFeriePage()
    {
        $joursFeries = $this->jourFerieModel->getAllJoursFeries();
        Flight::render('ressourceHumaine/back/jourFerie/jourFerie', ['joursFeries' => $joursFeries]);
    }

    public function createJourFerie()
    {
        $date = Flight::request()->data->date;
        $description = Flight::request()->data->description;
        $recurrence = Flight::request()->data->recurrence;

        if (empty($date) || empty($description) || empty($recurrence)) {
            // Gérer l'erreur, peut-être rediriger avec un message d'erreur
            Flight::redirect('/backOffice/jourFerie?error=Tous+les+champs+sont+requis');
            return;
        }

        $success = $this->jourFerieModel->createJourFerie($date, $description, $recurrence);

        if ($success) {
            Flight::redirect('/backOffice/jourFerie?success=Jour+ferie+ajoute+avec+succes');
        } else {
            Flight::redirect('/backOffice/jourFerie?error=Erreur+lors+de+l+ajout+du+jour+ferie');
        }
    }

    public function deleteJourFerie($id)
    {
        if (empty($id)) {
            Flight::redirect('/backOffice/jourFerie?error=ID+manquant');
            return;
        }

        $success = $this->jourFerieModel->deleteJourFerie($id);

        if ($success) {
            Flight::redirect('/backOffice/jourFerie?success=Jour+ferie+supprime+avec+succes');
        } else {
            Flight::redirect('/backOffice/jourFerie?error=Erreur+lors+de+la+suppression');
        }
    }
}
