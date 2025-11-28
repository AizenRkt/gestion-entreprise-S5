<?php

use app\controllers\ressourceHumaine\absence\AbsenceController;
use flight\net\Router;

/**
 * @var Router $router
 */

$absenceController = new AbsenceController();

$router->get('/backOffice/absence', [$absenceController, 'showAbsencePage']);
$router->post('/backOffice/absence/valider', [$absenceController, 'validerAbsence']);
$router->get('/backOffice/absence/refuser', [$absenceController, 'refuserAbsence']);
// API pour récupérer le solde avant validation
$router->get('/api/absence/solde', [$absenceController, 'getSoldeForAbsence']);
