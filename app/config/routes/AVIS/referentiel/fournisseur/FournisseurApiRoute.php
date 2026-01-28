<?php

use app\controllers\AVIS\referentiel\fournisseur\FournisseurApiController;
use flight\net\Router;

/**
 * @var Router $router
 */

/* ==============================
 * FOURNISSEUR - API
 * ============================== */
$router->get('/api/referentiel/fournisseurs/all', [FournisseurApiController::class, 'getAllFournisseurs']);
$router->get('/api/referentiel/fournisseurs/@id', [FournisseurApiController::class, 'getFournisseurById']);
$router->post('/api/referentiel/fournisseurs/create', [FournisseurApiController::class, 'createFournisseur']);
$router->put('/api/referentiel/fournisseurs/@id', [FournisseurApiController::class, 'updateFournisseur']);
$router->delete('/api/referentiel/fournisseurs/@id', [FournisseurApiController::class, 'deleteFournisseur']);
$router->get('/api/referentiel/fournisseurs/search', [FournisseurApiController::class, 'searchFournisseur']);

/* ==============================
 * FOURNISSEUR ARTICLE - API
 * ============================== */
$router->get('/api/referentiel/fournisseur-articles/all', [FournisseurApiController::class, 'getAllFournisseurArticles']);
$router->get('/api/referentiel/fournisseur-articles/@id', [FournisseurApiController::class, 'getFournisseurArticleById']);
$router->get('/api/referentiel/fournisseur-articles/fournisseur/@id_fournisseur', [FournisseurApiController::class, 'getFournisseurArticlesByFournisseur']);
$router->get('/api/referentiel/fournisseur-articles/article/@id_article', [FournisseurApiController::class, 'getFournisseurArticlesByArticle']);
$router->post('/api/referentiel/fournisseur-articles/create', [FournisseurApiController::class, 'createFournisseurArticle']);
$router->put('/api/referentiel/fournisseur-articles/@id', [FournisseurApiController::class, 'updateFournisseurArticle']);
$router->delete('/api/referentiel/fournisseur-articles/@id', [FournisseurApiController::class, 'deleteFournisseurArticle']);
