<?php

use app\controllers\AVIS\referentiel\client\ClientController;
use flight\net\Router;

/**
 * @var Router $router
 */

$controller = new ClientController();

/* ==============================
 * CLIENT TYPE - Pages
 * ============================== */
$router->get('/referentiel/client-type/list', [$controller, 'clientTypeList']);
$router->get('/referentiel/client-type/saisie', [$controller, 'clientTypeSaisie']);

/* ==============================
 * CLIENT - Pages
 * ============================== */
$router->get('/referentiel/client/list', [$controller, 'clientList']);
$router->get('/referentiel/client/saisie', [$controller, 'clientSaisie']);
$router->get('/referentiel/client/detail', [$controller, 'clientDetail']);
