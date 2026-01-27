<?php

use app\controllers\AVIS\Vente\VenteController;
use flight\net\Router;

/**
 * =============================================================================
 * ROUTES DU MODULE VENTES
 * =============================================================================
 * 
 * Flux: COMMANDE -> LIVRAISON -> FACTURE -> ENCAISSEMENT
 * 
 * @var Router $router
 */

$controller = new VenteController();

// ============================================
// DASHBOARD
// ============================================
$router->get('/ventes', [$controller, 'dashboard']);
$router->get('/avis/vente', [$controller, 'dashboard']);

// ============================================
// PAGES SÉPARÉES
// ============================================
$router->get('/ventes/clients', [$controller, 'clientsPage']);
$router->get('/ventes/commandes', [$controller, 'commandesPage']);
$router->get('/ventes/livraisons', [$controller, 'livraisonsPage']);
$router->get('/ventes/factures', [$controller, 'facturesPage']);
$router->get('/ventes/encaissements', [$controller, 'encaissementsPage']);

// ============================================
// API ARTICLES
// ============================================
$router->get('/ventes/articles', [$controller, 'getArticles']);

// ============================================
// CLIENTS API
// ============================================
$router->post('/ventes/clients', [$controller, 'createClient']);
$router->get('/ventes/clients/@id', [$controller, 'getClient']);
$router->put('/ventes/clients/@id', [$controller, 'updateClient']);
$router->delete('/ventes/clients/@id', [$controller, 'deleteClient']);

// ============================================
// COMMANDES API
// ============================================
$router->get('/ventes/commandes/validees', [$controller, 'getCommandesValidees']);
$router->post('/ventes/commandes', [$controller, 'createCommande']);
$router->get('/ventes/commandes/@id', [$controller, 'getCommande']);
$router->post('/ventes/commandes/lignes', [$controller, 'addLigneCommande']);
$router->put('/ventes/commandes/@id/valider', [$controller, 'validerCommande']);
$router->post('/ventes/commandes/@id/annuler', [$controller, 'annulerCommande']);

// ============================================
// LIVRAISONS API
// ============================================
$router->get('/ventes/livraisons/sans-facture', [$controller, 'getLivraisonsSansFacture']);
$router->post('/ventes/livraisons', [$controller, 'createLivraison']);
$router->get('/ventes/livraisons/@id', [$controller, 'getLivraison']);
$router->post('/ventes/livraisons/lignes', [$controller, 'addLigneLivraison']);
$router->put('/ventes/livraisons/@id/valider', [$controller, 'validerLivraison']);

// ============================================
// FACTURES API
// ============================================
$router->get('/ventes/factures/impayees', [$controller, 'getFacturesImpayees']);
$router->post('/ventes/factures', [$controller, 'createFacture']);
$router->get('/ventes/factures/@id', [$controller, 'getFacture']);
$router->put('/ventes/factures/@id/valider', [$controller, 'validerFacture']);

// ============================================
// ENCAISSEMENTS
// ============================================
$router->post('/ventes/encaissements', [$controller, 'createEncaissement']);
$router->get('/ventes/encaissements/@id', [$controller, 'getEncaissement']);

// ============================================
// STOCK
// ============================================
$router->get('/ventes/articles/@depotId', [$controller, 'getArticlesByDepot']);
$router->get('/ventes/stock/@articleId/@depotId', [$controller, 'checkStock']);

// ============================================
// KPI
// ============================================
$router->get('/ventes/kpi', [$controller, 'getKPI']);
$router->get('/ventes/kpi/ca', [$controller, 'getCA']);
$router->get('/ventes/kpi/backlog', [$controller, 'getBacklog']);
$router->get('/ventes/kpi/retard', [$controller, 'getCommandesRetard']);
$router->get('/ventes/kpi/creances', [$controller, 'getCreances']);
