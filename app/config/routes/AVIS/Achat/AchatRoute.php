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

// Tolérance si l'URL contient déjà le préfixe / avant le base_url
$router->get('/avis/achat', function () {
	Flight::redirect(Flight::base() . '/avis/achat');
});
$router->get('/avis/achat/', function () {
	Flight::redirect(Flight::base() . '/avis/achat');
});
$router->get('/avis/achat/saisie', function () {
	Flight::redirect(Flight::base() . '/avis/achat/saisie');
});
$router->post('/avis/achat/saisie', function () {
	Flight::redirect(Flight::base() . '/avis/achat/saisie');
});
$router->get('/avis/achat/demandes', function () {
	Flight::redirect(Flight::base() . '/avis/achat/demandes');
});
$router->get('/avis/achat/demandes/@id:[0-9]+', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id);
});
$router->get('/avis/achat/demandes/@id:[0-9]+/', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id);
});
$router->post('/avis/achat/demandes/@id:[0-9]+/valider', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id . '/valider');
});

// Tolérance pour les nouvelles routes avec préfixe doublé
$router->get('/avis/achat/dashboard', function () {
	Flight::redirect(Flight::base() . '/avis/achat/dashboard');
});
$router->get('/avis/achat/dashboard/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/dashboard');
});
$router->get('/avis/achat/bc', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->get('/avis/achat/bc/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->post('/avis/achat/bc', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->get('/avis/achat/bc/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->get('/avis/achat/bc/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->post('/avis/achat/bc/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->get('/avis/achat/receptions', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->get('/avis/achat/receptions/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->post('/avis/achat/receptions', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->get('/avis/achat/receptions/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->get('/avis/achat/receptions/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->post('/avis/achat/receptions/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->get('/avis/achat/factures', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->get('/avis/achat/factures/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->post('/avis/achat/factures', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->get('/avis/achat/factures/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->get('/avis/achat/factures/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->post('/avis/achat/factures/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->get('/avis/achat/paiements', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->get('/avis/achat/paiements/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->post('/avis/achat/paiements', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->get('/avis/achat/paiements/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});
$router->get('/avis/achat/paiements/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});
$router->post('/avis/achat/paiements/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});

// Tolérance si l'URL contient deux fois le préfixe /
$router->get('/avis/achat/dashboard', function () {
	Flight::redirect(Flight::base() . '/avis/achat/dashboard');
});
$router->get('/avis/achat/dashboard/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/dashboard');
});
$router->get('/avis/achat/bc', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->get('/avis/achat/bc/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->post('/avis/achat/bc', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc');
});
$router->get('/avis/achat/bc/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->get('/avis/achat/bc/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->post('/avis/achat/bc/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/bc/nouveau');
});
$router->get('/avis/achat/receptions', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->get('/avis/achat/receptions/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->post('/avis/achat/receptions', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions');
});
$router->get('/avis/achat/receptions/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->get('/avis/achat/receptions/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->post('/avis/achat/receptions/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/receptions/nouveau');
});
$router->get('/avis/achat/factures', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->get('/avis/achat/factures/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->post('/avis/achat/factures', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures');
});
$router->get('/avis/achat/factures/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->get('/avis/achat/factures/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->post('/avis/achat/factures/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/factures/nouveau');
});
$router->get('/avis/achat/paiements', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->get('/avis/achat/paiements/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->post('/avis/achat/paiements', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements');
});
$router->get('/avis/achat/paiements/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});
$router->get('/avis/achat/paiements/nouveau/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});
$router->post('/avis/achat/paiements/nouveau', function () {
	Flight::redirect(Flight::base() . '/avis/achat/paiements/nouveau');
});
$router->get('/avis/achat/demandes', function () {
	Flight::redirect(Flight::base() . '/avis/achat/demandes');
});
$router->get('/avis/achat/demandes/', function () {
	Flight::redirect(Flight::base() . '/avis/achat/demandes');
});
$router->get('/avis/achat/demandes/@id:[0-9]+', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id);
});
$router->get('/avis/achat/demandes/@id:[0-9]+/', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id);
});
$router->post('/avis/achat/demandes/@id:[0-9]+/valider', function ($id) {
	Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id . '/valider');
});
$router->get('/avis/achat/saisie', function () {
	Flight::redirect(Flight::base() . '/avis/achat/saisie');
});
$router->post('/avis/achat/saisie', function () {
	Flight::redirect(Flight::base() . '/avis/achat/saisie');
});
$router->get('/avis/achat', function () {
	Flight::redirect(Flight::base() . '/avis/achat');
});
$router->get('/avis/achat/', function () {
	Flight::redirect(Flight::base() . '/avis/achat');
});
$router->get('/avis/achat/*', function () {
	$splat = Flight::request()->splat ?? '';
	$normalized = $splat ? '/' . ltrim($splat, '/') : '';
	Flight::redirect(Flight::base() . '/avis/achat' . $normalized);
});
