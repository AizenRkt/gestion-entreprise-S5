<?php

use app\controllers\AVIS\referentiel\fournisseur\FournisseurController;
use flight\net\Router;

/**
 * @var Router $router
 */

$controller = new FournisseurController();

/* ==============================
 * FOURNISSEUR - Pages
 * ============================== */
$router->get('/referentiel/fournisseur/list', [$controller, 'fournisseurList']);
$router->get('/referentiel/fournisseur/saisie', [$controller, 'fournisseurSaisie']);
$router->get('/referentiel/fournisseur/detail', [$controller, 'fournisseurDetail']);
$router->get('/referentiel/fournisseur-article/list', [$controller, 'fournisseurArticleList']);
