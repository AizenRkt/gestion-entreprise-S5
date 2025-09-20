<?php

use app\controllers\ressourcesHumaines\contratEssai;
use flight\net\Router;

/** 
 * @var Router $router
*/

$contratController = new ContratEssaiController();

$router->get('/contrats', [$contratController, 'getAll']);           
$router->get('/contrats/@id', [$contratController, 'getById']);      
$router->post('/contrats/create', [$contratController, 'create']);   
$router->post('/contrats/update/@id', [$contratController, 'update']); 
$router->get('/contrats/delete/@id', [$contratController, 'delete']);  

// Route spéciale : génération PDF
$router->get('/contrats/pdf/@id', [$contratController, 'generatePdf']); 

