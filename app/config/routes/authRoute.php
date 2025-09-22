<?php

use app\controllers\ressourceHumaine\AuthController;
use flight\net\Router;

/** 
 * @var Router $router
*/
$AuthController = new AuthController();

$router->get('/auth/login', [ $AuthController, 'log' ]); // Affiche le formulaire de connexion
$router->post('/auth/login', [ $AuthController, 'authVerif' ]); // Vérifie la connexion

// Routes pour l'inscription
$router->get('/auth/sign', [ $AuthController, 'sign' ]); // Affiche le formulaire d'inscription
$router->post('/auth/sign', [ $AuthController, 'authInscription' ]); // Traite l'inscription

$router->get('/deconnexion', [$AuthController, 'authDeconnexion']);