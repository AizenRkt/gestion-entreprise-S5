<?php

use app\controllers\AVIS\Achat\AchatController;
use flight\net\Router;

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
