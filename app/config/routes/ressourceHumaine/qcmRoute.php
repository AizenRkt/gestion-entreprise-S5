<?php

use app\controllers\ressourceHumaine\qcm\QcmController;

use flight\net\Router;

/** 
 * @var Router $router
*/

$qcmController = new QcmController();

// QCM page
$router->get('/seeAllQcm', [$qcmController, 'seeAllQcm']);           
$router->get('/singleQcm', [$qcmController, 'singleQcm']);           
$router->get('/createQcm', [$qcmController, 'createQcm']);           
$router->get('/interviewQcm', [$qcmController, 'interviewQcm']);           
$router->get('/createQuestion', [$qcmController, 'createQuestion']);           

// QCM api 
$router->get('/qcm/all', [$qcmController, 'getAll']);           
$router->get('/qcm/@id', [$qcmController, 'getById']);
$router->delete('/qcm/@id', [$qcmController, 'delete']);
$router->post('/qcm/create', [$qcmController, 'createQcmApi']);

$router->post('/question/add', [$qcmController,'addQuestion']);
$router->get('/question/search', [$qcmController,'searchQuestion']);

// question api
$router->get('/question/all', [$qcmController, 'getAllQuestion']);           
$router->get('/question/@id', [$qcmController, 'getByIdQuestion']);
$router->delete('/question/@id', [$qcmController, 'deleteQuestion']);

// scoring
$router->POST('/scoringQcm', [$qcmController, 'scoringQcm']);
$router->GET('/resultatQcm', [$qcmController, 'qcmResult']);


