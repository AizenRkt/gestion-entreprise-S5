<?php

use app\controllers\ressourceHumaine\absence\AbsenceController;
use flight\net\Router;

/**
 * @var Router $router
 */

$absenceController = new AbsenceController();

$router->get('/backOffice/absence', [$absenceController, 'showAbsencePage']);
$router->get('/backOffice/absence/valider', [$absenceController, 'validerAbsence']);
$router->get('/backOffice/absence/refuser', [$absenceController, 'refuserAbsence']);
