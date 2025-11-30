<?php

use app\controllers\Controller;

//importation liée flight
use flight\Engine;
use flight\net\Router;

//Ressource humaine
require __DIR__ . '/routes/ressourceHumaine/qcmRoute.php';
require __DIR__ . '/routes/ressourceHumaine/authRoute.php';
require __DIR__ . '/routes/ressourceHumaine/employeRoute.php';
require __DIR__ . '/routes/ressourceHumaine/contratRoute.php';
require __DIR__ . '/routes/ressourceHumaine/contratTravailRoute.php';
require __DIR__ . '/routes/ressourceHumaine/entretienRoute.php';
require __DIR__ . '/routes/ressourceHumaine/back/creaAnnonce.php';
require __DIR__ . '/routes/ressourceHumaine/annonce.php';
require __DIR__ . '/routes/ressourceHumaine/candidatRoute.php';
require __DIR__ . '/routes/ressourceHumaine/scoringRoute.php';
require __DIR__ . '/routes/ressourceHumaine/pointageRoute.php';
require __DIR__ . '/routes/ressourceHumaine/absenceRoute.php';
require __DIR__ . '/routes/ressourceHumaine/heureSuppRoute.php';
require __DIR__ . '/routes/ressourceHumaine/congeRoute.php';
require __DIR__ . '/routes/ressourceHumaine/jourFerieRoute.php';

/** 
 * @var Router $router 
 * @var Engine $app
*/

$Controller = new Controller();

// $router->get('/planning2', [ $Controller, 'planning2' ]);
$router->get('/backOffice',[$Controller,'backOffice']);

?>