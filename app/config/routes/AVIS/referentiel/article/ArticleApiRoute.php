<?php

use app\controllers\AVIS\referentiel\article\ArticleApiController;
use flight\net\Router;

/**
 * @var Router $router
 */

/* ==============================
 * ARTICLE FAMILLE - API
 * ============================== */
$router->get('/api/referentiel/articles/familles/all', [ArticleApiController::class, 'getAllArticleFamilles']);
$router->get('/api/referentiel/articles/familles/search', [ArticleApiController::class, 'searchArticleFamille']);
$router->get('/api/referentiel/articles/familles/@id', [ArticleApiController::class, 'getArticleFamilleById']);
$router->post('/api/referentiel/articles/familles', [ArticleApiController::class, 'createArticleFamille']);
$router->put('/api/referentiel/articles/familles/@id', [ArticleApiController::class, 'updateArticleFamille']);
$router->delete('/api/referentiel/articles/familles/@id', [ArticleApiController::class, 'deleteArticleFamille']);

/* ==============================
 * ARTICLE - API
 * ============================== */
$router->get('/api/referentiel/articles/all', [ArticleApiController::class, 'getAllArticles']);
$router->get('/api/referentiel/articles/search', [ArticleApiController::class, 'searchArticle']);
$router->get('/api/referentiel/articles/@id', [ArticleApiController::class, 'getArticleById']);
$router->post('/api/referentiel/articles', [ArticleApiController::class, 'createArticle']);
$router->put('/api/referentiel/articles/@id', [ArticleApiController::class, 'updateArticle']);
$router->delete('/api/referentiel/articles/@id', [ArticleApiController::class, 'deleteArticle']);
$router->get('/api/referentiel/articles/famille/@id_famille', [ArticleApiController::class, 'getArticleByFamille']);
$router->get('/api/referentiel/articles/active/list', [ArticleApiController::class, 'getActiveArticles']);

/* ==============================
 * ARTICLE STATUS - API
 * ============================== */
$router->get('/api/referentiel/articles/status/all', [ArticleApiController::class, 'getAllArticleStatus']);
$router->get('/api/referentiel/articles/status/@id', [ArticleApiController::class, 'getArticleStatusById']);

/* ==============================
 * ARTICLE PRIX HISTORIQUE - API
 * ============================== */
$router->get('/api/referentiel/articles/prix-historique/@id_article', [ArticleApiController::class, 'getArticlePrixHistorique']);
$router->get('/api/referentiel/articles/prix-dernier/@id_article', [ArticleApiController::class, 'getLastPriceArticle']);
