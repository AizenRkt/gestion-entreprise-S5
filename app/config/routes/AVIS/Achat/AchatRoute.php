<?php

use app\controllers\AVIS\Achat\AchatController;
use flight\net\Router;
use Flight;

/**
 * @var Router $router
 */

$controller = new AchatController();

$router->get('/avis/achat/dashboard', [$controller, 'dashboard']);
$router->get('/avis/achat', [$controller, 'listRequests']);
$router->get('/avis/achat/', [$controller, 'listRequests']);

$router->get('/avis/achat/saisie', [$controller, 'showCreateForm']);
$router->post('/avis/achat/saisie', [$controller, 'storeRequest']);

$router->get('/avis/achat/bc', [$controller, 'listOrders']);
$router->get('/avis/achat/bc/nouveau', [$controller, 'showOrderForm']);
$router->post('/avis/achat/bc', [$controller, 'storeOrder']);

$router->get('/avis/achat/receptions', [$controller, 'listReceptions']);
$router->get('/avis/achat/receptions/nouveau', [$controller, 'showReceptionForm']);
$router->post('/avis/achat/receptions', [$controller, 'storeReception']);

$router->get('/avis/achat/factures', [$controller, 'listInvoices']);
$router->get('/avis/achat/factures/nouveau', [$controller, 'showInvoiceForm']);
$router->post('/avis/achat/factures', [$controller, 'storeInvoice']);

$router->get('/avis/achat/paiements', [$controller, 'listPayments']);
$router->get('/avis/achat/paiements/nouveau', [$controller, 'showPaymentForm']);
$router->post('/avis/achat/paiements', [$controller, 'storePayment']);

$router->get('/avis/achat/demandes', [$controller, 'listRequests']);
$router->get('/avis/achat/demandes/@id:[0-9]+', [$controller, 'showRequest']);
$router->post('/avis/achat/demandes/@id:[0-9]+/valider', [$controller, 'validateRequest']);

// Tolérance si l'URL contient déjà le préfixe /gestion-entreprise-S5/ avant le base_url
$router->get('/gestion-entreprise-S5/avis/achat', function () {
	Flight::redirect(Flight::base() . '/avis/achat');
});
$router->get('/gestion-entreprise-S5/avis/achat/', function () {
	Flight::redirect(Flight::base() . '/avis/achat');
});
$router->get('/gestion-entreprise-S5/avis/achat/saisie', function () {
	Flight::redirect(Flight::base() . '/avis/achat/saisie');
});
$router->post('/gestion-entreprise-S5/avis/achat/saisie', function () {
	Flight::redirect(Flight::base() . '/avis/achat/saisie');
});
$router->get('/gestion-entreprise-S5/avis/achat/demandes', function () {
	Flight::redirect(Flight::base() . '/avis/achat/demandes');
});
$router->get('/gestion-entreprise-S5/avis/achat/demandes/@id:[0-9]+', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id);
});
$router->get('/gestion-entreprise-S5/avis/achat/demandes/@id:[0-9]+/', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id);
});
$router->post('/gestion-entreprise-S5/avis/achat/demandes/@id:[0-9]+/valider', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id . '/valider');
});

// Tolérance pour les nouvelles routes avec préfixe doublé
$router->get('/gestion-entreprise-S5/avis/achat/dashboard', function () {
	Flight::redirect(Flight::base() . '/avis/achat/dashboard');
});
$router->get('/gestion-entreprise-S5/avis/achat/dashboard/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/dashboard');
});
$router->get('/gestion-entreprise-S5/avis/achat/bc', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->get('/gestion-entreprise-S5/avis/achat/bc/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->post('/gestion-entreprise-S5/avis/achat/bc', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->get('/gestion-entreprise-S5/avis/achat/bc/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->get('/gestion-entreprise-S5/avis/achat/bc/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->post('/gestion-entreprise-S5/avis/achat/bc/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->get('/gestion-entreprise-S5/avis/achat/receptions', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->get('/gestion-entreprise-S5/avis/achat/receptions/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->post('/gestion-entreprise-S5/avis/achat/receptions', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->get('/gestion-entreprise-S5/avis/achat/receptions/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->get('/gestion-entreprise-S5/avis/achat/receptions/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->post('/gestion-entreprise-S5/avis/achat/receptions/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->get('/gestion-entreprise-S5/avis/achat/factures', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->get('/gestion-entreprise-S5/avis/achat/factures/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->post('/gestion-entreprise-S5/avis/achat/factures', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->get('/gestion-entreprise-S5/avis/achat/factures/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->get('/gestion-entreprise-S5/avis/achat/factures/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->post('/gestion-entreprise-S5/avis/achat/factures/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->get('/gestion-entreprise-S5/avis/achat/paiements', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->get('/gestion-entreprise-S5/avis/achat/paiements/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->post('/gestion-entreprise-S5/avis/achat/paiements', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->get('/gestion-entreprise-S5/avis/achat/paiements/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});
$router->get('/gestion-entreprise-S5/avis/achat/paiements/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});
$router->post('/gestion-entreprise-S5/avis/achat/paiements/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});

// Tolérance si l'URL contient deux fois le préfixe /gestion-entreprise-S5/
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/dashboard', function () {
	Flight::redirect(Flight::base() . '/avis/achat/dashboard');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/dashboard/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/dashboard');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/bc', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/bc/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->post('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/bc', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/bc/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/bc/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->post('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/bc/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/receptions', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/receptions/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->post('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/receptions', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/receptions/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/receptions/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->post('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/receptions/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/factures', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/factures/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->post('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/factures', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/factures/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/factures/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->post('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/factures/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/paiements', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/paiements/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->post('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/paiements', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/paiements/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/paiements/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});
$router->post('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/paiements/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/demandes', function () {
	Flight::redirect(Flight::base() . '/avis/achat/demandes');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/demandes/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/demandes');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/demandes/@id:[0-9]+', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id);
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/demandes/@id:[0-9]+/', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id);
});
$router->post('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/demandes/@id:[0-9]+/valider', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id . '/valider');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/saisie', function () {
	Flight::redirect(Flight::base() . '/avis/achat/saisie');
});
$router->post('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/saisie', function () {
	Flight::redirect(Flight::base() . '/avis/achat/saisie');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat', function () {
	Flight::redirect(Flight::base() . '/avis/achat');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/', function () {
	Flight::redirect(Flight::base() . '/avis/achat');
});
$router->get('/gestion-entreprise-S5/gestion-entreprise-S5/avis/achat/*', function () {
	$splat = Flight::request()->splat ?? '';
	$normalized = $splat ? '/' . ltrim($splat, '/') : '';
	Flight::redirect(Flight::base() . '/avis/achat' . $normalized);
});
