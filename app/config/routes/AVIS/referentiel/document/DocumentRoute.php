<?php

use app\controllers\AVIS\referentiel\document\DocumentController;
use flight\net\Router;

/**
 * @var Router $router
 */

$controller = new DocumentController();

/* ==============================
 * DOCUMENT TYPE - Pages
 * ============================== */
$router->get('/referentiel/document-type/list', [$controller, 'documentTypeList']);
$router->get('/referentiel/document-type/saisie', [$controller, 'documentTypeSaisie']);

/* ==============================
 * DOCUMENT - Pages
 * ============================== */
$router->get('/referentiel/document/list', [$controller, 'documentList']);
$router->get('/referentiel/document/saisie', [$controller, 'documentSaisie']);
$router->get('/referentiel/document/detail', [$controller, 'documentDetail']);
