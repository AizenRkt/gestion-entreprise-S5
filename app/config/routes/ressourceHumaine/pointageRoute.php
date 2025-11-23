<?php

use app\controllers\ressourceHumaine\pointage\PointageController;
use flight\net\Router;

/**
 * @var Router $router
 */

$pointageController = new PointageController();

$router->get('/backOffice/user/pointage', [$pointageController, 'showPointagePage']);
$router->get('/backOffice/user/checkin', [$pointageController, 'checkin']);
$router->get('/backOffice/user/checkout', [$pointageController, 'checkout']);
$router->get('/backOffice/user/pointage/historique', [$pointageController, 'getMyHistorique']);
$router->get('/pointageHistorique', [$pointageController, 'getAllHistorique']);
$router->post('/pointage/update', [$pointageController, 'updatePointage']);
