<?php

use app\controllers\ressourceHumaine\absence\AbsenceController;
use flight\net\Router;

/**
 * @var Router $router
 */

$absenceController = new AbsenceController();

$router->get('/backOffice/absence', [$absenceController, 'showAbsencePage']);
