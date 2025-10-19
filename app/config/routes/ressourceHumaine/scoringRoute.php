<?php

use app\controllers\ressourceHumaine\scoring\ScoringController;

use flight\net\Router;

/** 
 * @var Router $router
*/

$scoringController = new ScoringController();

$router->get('/eligibleEssai/@id', [$scoringController, 'getEligibleEssaie']);           
