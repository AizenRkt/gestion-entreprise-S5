<?php

use app\controllers\AVIS\Achat\AchatController;
use flight\net\Router;

/**
 * @var Router $router
 */

$controller = new AchatController();

$router->get('/avis/achat', [$controller, 'dashboard']);

$router->post('/avis/achat/suppliers', [$controller, 'createSupplier']);
$router->post('/avis/achat/orders', [$controller, 'createOrder']);
$router->post('/avis/achat/receptions', [$controller, 'createReception']);
$router->post('/avis/achat/invoices', [$controller, 'createInvoice']);
$router->post('/avis/achat/payments', [$controller, 'createPayment']);
