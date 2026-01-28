<?php

use app\controllers\AVIS\referentiel\article\ArticleController;
use flight\net\Router;

/**
 * @var Router $router
 */

$controller = new ArticleController();

/* ==============================
 * ARTICLE FAMILLE - Pages
 * ============================== */
$router->get('/referentiel/article-famille/list', [$controller, 'articleFamilleList']);
$router->get('/referentiel/article-famille/saisie', [$controller, 'articleFamilleSaisie']);

/* ==============================
 * ARTICLE - Pages
 * ============================== */
$router->get('/referentiel/article/list', [$controller, 'articleList']);
$router->get('/referentiel/article/saisie', [$controller, 'articleSaisie']);
$router->get('/referentiel/article/detail', [$controller, 'articleDetail']);
$router->get('/referentiel/article/prix-historique', [$controller, 'articlePrixHistorique']);
