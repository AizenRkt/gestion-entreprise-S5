<?php

use app\controllers\ressourceHumaine\contratEssai\ContratEssaiController;
use flight\net\Router;

/** 
 * @var Router $router
*/

/* Contrat d'Essai Routes - NOUVELLES ROUTES */
$contratEssaiController = new ContratEssaiController();
// Page principale des contrats d'essai
$router->get('/contratCrea', [ $contratEssaiController, 'contratEssai' ]);

$router->get('/contrat/creer', [ $contratEssaiController, 'creerContratOfficiel']);

$router->get('/contratListe', [ $contratEssaiController, 'contratEssaiList' ]);

// api 
$router->get('/contrat/all', [ $contratEssaiController, 'getAllContrat']);
$router->get('/contrat/valide/all', [ $contratEssaiController, 'getAllValider']);


$router->get('/contrat/valider/@id', [ $contratEssaiController, 'valider']);
$router->get('/contrat/rejeter/@id', [ $contratEssaiController, 'annuler']);

$router->get('/contrat/renouveller/@id', [ $contratEssaiController, 'renouvellerContratEssai']);


// // Accepter un contrat (AJAX)
// $router->post('/contrat/accepter', [ $contratEssaiController, 'accepterContrat' ]);
// // Générer le PDF du contrat
// $router->get('/contrat/generate/@id', [ $contratEssaiController, 'generatePdf' ]);
// // API pour récupérer les candidats recommandés
// $router->get('/api/candidats/recommandes', [ $contratEssaiController, 'getCandidatsRecommandesAPI' ]);
// // Créer un contrat d'essai officiel
// $router->post('/contrat/creer-officiel', [ $contratEssaiController, 'creerContratOfficiel' ]);