<?php

use app\controllers\Controller;
use app\controllers\ressourceHumaine\candidat\CandidatController;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

$candidatController = new CandidatController();

$Controller = new Controller();

$router->get('/candidature', [ $Controller, 'candidature' ]);

// Routes pour les candidats
$router->get('/candidat', [$candidatController, 'annonce']);          
$router->post('/candidat/create', [$candidatController, 'create']);          
$router->get('/candidat/@id', [$candidatController, 'getById']);       
$router->post('/candidat/update/@id', [$candidatController, 'update']);
$router->get('/candidat/delete/@id', [$candidatController, 'delete']);
$router->get('/backOffice/candidat', [$candidatController, 'backOfficeCandidat']);
$router->post('/backOffice/candidat/filter', [$candidatController, 'filter']);
$router->get('/backOffice/candidat/detail', [$candidatController, 'detailCv']);

//api
$router->get('/candidat/detail/@id', [$candidatController, 'candidatDetail']);
$router->get('/candidat/excel/@id', [$candidatController, 'exportCvToExcel']);


$router->get('/test', [$candidatController, 'eli']);
