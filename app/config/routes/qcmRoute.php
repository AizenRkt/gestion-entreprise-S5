<?php

use app\controllers\QcmController\QcmController;
use flight\net\Router;

/** 
 * @var Router $router
*/

$qcmController = new QcmController();

// Routes pour les QCM
$router->get('/qcm', [$qcmController, 'getAll']);           
$router->get('/qcm/@id', [$qcmController, 'getById']);      
$router->post('/qcm/create', [$qcmController, 'create']);   
$router->post('/qcm/update/@id', [$qcmController, 'update']); 
$router->get('/qcm/delete/@id', [$qcmController, 'delete']);  

