<?php

use app\controllers\ressourceHumaine\absence\AbsenceController;
use app\controllers\ressourceHumaine\absence\DemandeAbsenceController;

use flight\net\Router;

/**
 * @var Router $router
 */

$absenceController = new AbsenceController();
$demandeAbsenceController = new DemandeAbsenceController();

$router->get('/backOffice/absence', [$absenceController, 'showAbsencePage']);
$router->post('/backOffice/absence/valider', [$absenceController, 'validerAbsence']);
$router->get('/backOffice/absence/refuser', [$absenceController, 'refuserAbsence']);
// API pour récupérer le solde avant validation
$router->get('/api/absence/solde', [$absenceController, 'getSoldeForAbsence']);

$router->get('/absence/demande', [$demandeAbsenceController, 'showDemandePage']);
$router->post('/absence/demande/submit', [$demandeAbsenceController, 'submitDemande']);