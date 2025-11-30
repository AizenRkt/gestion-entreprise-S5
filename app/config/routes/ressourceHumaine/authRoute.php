<?php

use app\controllers\Controller;
use app\controllers\ressourceHumaine\AuthController;
use app\controllers\ressourceHumaine\employe\EmployeController;
use flight\net\Router;

/** 
 * @var Router $router
*/

$Controller = new Controller();
$router->get('/', [ $Controller, 'acceuil' ]);
$router->get('/log', [ $Controller, 'log' ]);
$router->get('/sign', [ $Controller, 'sign' ]);

$AuthController = new AuthController();
$EmployeController = new EmployeController();

$router->get('/auth/login', [ $AuthController, 'log' ]); // Affiche le formulaire de connexion
$router->post('/auth/login', [ $AuthController, 'authVerif' ]); // Vérifie la connexion

// Routes pour l'inscription
$router->get('/auth/sign', [ $AuthController, 'sign' ]); // Affiche le formulaire d'inscription
$router->post('/auth/sign', [ $AuthController, 'authInscription' ]); // Traite l'inscription

// routes pour les paramètres d'user
$router->get('/auth/parametre',  [$EmployeController, 'getUserProfile']); 
 

// deco
$router->get('/deconnexion', [$AuthController, 'authDeconnexion']);
$router->get('/auth/disable/@iduser', [$AuthController, 'disableUser']);