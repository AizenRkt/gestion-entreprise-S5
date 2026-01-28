<?php

use app\controllers\AVIS\Achat\AchatController;
use flight\net\Router;
use Flight;

/**
 * @var Router $router
 */

$controller = new AchatController();

$router->get('/avis/achat', [$controller, 'listRequests']);
$router->get('/avis/achat/', [$controller, 'listRequests']);

$router->get('/avis/achat/saisie', [$controller, 'showCreateForm']);
$router->post('/avis/achat/saisie', [$controller, 'storeRequest']);

$router->get('/avis/achat/demandes', [$controller, 'listRequests']);
$router->get('/avis/achat/demandes/@id:[0-9]+', [$controller, 'showRequest']);
$router->post('/avis/achat/demandes/@id:[0-9]+/valider', [$controller, 'validateRequest']);

// Tolérance si l'URL contient déjà le préfixe /gestion-entreprise-S5/ avant le base_url
$router->get('/gestion-entreprise-S5/avis/achat', function () {
	Flight::redirect(Flight::base() . '/avis/achat');
});
$router->get('/gestion-entreprise-S5/avis/achat/demandes', function () {
	Flight::redirect(Flight::base() . '/avis/achat/demandes');
});
$router->get('/gestion-entreprise-S5/avis/achat/demandes/@id:[0-9]+', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id);
});
