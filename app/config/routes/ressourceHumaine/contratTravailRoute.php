<?php

use app\controllers\ressourceHumaine\contratTravail\ContratTravailController;
use flight\net\Router;

/** 
 * @var Router $router
*/

$contratTravailController = new ContratTravailController();

$router->get('/contratTravailCrea', [ $contratTravailController, 'contratTravail' ]);

// api
$router->get('/contratTravail/CDI/creer/@id', [ $contratTravailController, 'creerCDI' ]);
$router->get('/contratTravail/CDD/creer/@id', [ $contratTravailController, 'creerCDD' ]);
