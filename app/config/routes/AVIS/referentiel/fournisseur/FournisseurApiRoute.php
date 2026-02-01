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
$router->get('/api/referentiel/fournisseurs/@id_fournisseur', [FournisseurApiController::class, 'getFournisseurById']);
$router->post('/api/referentiel/fournisseurs', [FournisseurApiController::class, 'createFournisseur']);
$router->put('/api/referentiel/fournisseurs/@id_fournisseur', [FournisseurApiController::class, 'updateFournisseur']);
$router->delete('/api/referentiel/fournisseurs/@id_fournisseur', [FournisseurApiController::class, 'deleteFournisseur']);
$router->get('/api/referentiel/fournisseurs/search', [FournisseurApiController::class, 'searchFournisseur']);

/* ==============================
 * FOURNISSEUR ARTICLE - API
 * ============================== */
$router->get('/api/referentiel/fournisseurs/@id_fournisseur/articles', [FournisseurApiController::class, 'getFournisseurArticlesByFournisseur']);
$router->post('/api/referentiel/fournisseurs/@id_fournisseur/articles', [FournisseurApiController::class, 'createFournisseurArticle']);
$router->get('/api/referentiel/fournisseurs/articles/all', [FournisseurApiController::class, 'getAllFournisseurArticles']);
$router->get('/api/referentiel/fournisseurs/articles/@id_fournisseur_article', [FournisseurApiController::class, 'getFournisseurArticleById']);
$router->get('/api/referentiel/articles/fournisseurs/@id_article', [FournisseurApiController::class, 'getFournisseurArticlesByArticle']);
$router->put('/api/referentiel/fournisseurs/articles/@id_fournisseur_article', [FournisseurApiController::class, 'updateFournisseurArticle']);
$router->delete('/api/referentiel/fournisseurs/articles/@id_fournisseur_article', [FournisseurApiController::class, 'deleteFournisseurArticle']);
