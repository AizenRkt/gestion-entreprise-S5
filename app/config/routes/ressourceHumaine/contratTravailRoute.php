<?php

use app\controllers\ressourceHumaine\contratTravail\ContratTravailController;
use flight\net\Router;

/** 
 * @var Router $router
*/

$contratTravailController = new ContratTravailController();

$router->get('/contratTravailCrea', [ $contratTravailController, 'contratTravail' ]);
$router->get('/contratTravailList', [ $contratTravailController, 'contratTravailList' ]);

// api
$router->get('/contratTravail/CDI/creer/@id', [ $contratTravailController, 'creerCDI' ]);
$router->get('/contratTravail/CDD/creer/@id', [ $contratTravailController, 'creerCDD' ]);

$router->get('/contratTravail/all', [ $contratTravailController, 'getAllDetail' ]);

$router->get('/contratTravail/CDD/renouveller/@id', [ $contratTravailController, 'renouvellerCDD' ]);

$router->get('/contratTravail/CDD/toCDI/@id', [ $contratTravailController, 'basculerVersCDI' ]);
