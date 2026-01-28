<?php

use app\controllers\AVIS\referentiel\document\DocumentApiController;
use flight\net\Router;

/**
 * @var Router $router
 */

/* ==============================
 * DOCUMENT TYPE - API
 * ============================== */
$router->get('/api/referentiel/document-types/all', [DocumentApiController::class, 'getAllDocumentTypes']);
$router->get('/api/referentiel/document-types/@id', [DocumentApiController::class, 'getDocumentTypeById']);
$router->post('/api/referentiel/document-types/create', [DocumentApiController::class, 'createDocumentType']);
$router->put('/api/referentiel/document-types/@id', [DocumentApiController::class, 'updateDocumentType']);
$router->delete('/api/referentiel/document-types/@id', [DocumentApiController::class, 'deleteDocumentType']);

/* ==============================
 * DOCUMENT - API
 * ============================== */
$router->get('/api/referentiel/documents/all', [DocumentApiController::class, 'getAllDocuments']);
$router->get('/api/referentiel/documents/@id', [DocumentApiController::class, 'getDocumentById']);
$router->post('/api/referentiel/documents/create', [DocumentApiController::class, 'createDocument']);
$router->put('/api/referentiel/documents/@id', [DocumentApiController::class, 'updateDocument']);
$router->delete('/api/referentiel/documents/@id', [DocumentApiController::class, 'deleteDocument']);
$router->get('/api/referentiel/documents/search', [DocumentApiController::class, 'searchDocument']);
$router->get('/api/referentiel/documents/type/@id_document_type', [DocumentApiController::class, 'getDocumentByType']);

/* ==============================
 * DOCUMENT STATUS - API
 * ============================== */
$router->get('/api/referentiel/document-status/all', [DocumentApiController::class, 'getAllDocumentStatus']);
$router->get('/api/referentiel/document-status/@id', [DocumentApiController::class, 'getDocumentStatusById']);
