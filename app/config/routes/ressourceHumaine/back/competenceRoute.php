<?php
use app\controllers\Controller;
use app\controllers\ressourceHumaine\back\CompetenceController;
use app\models\ressourceHumaine\back\competenceRoute;

$Controller = new Controller();

$router->get('/competence', [ $Controller, 'Cartecompetence' ]);

Flight::route('GET /api/skills-overview', function() {
    $db = Flight::db();                
    $controller = new CompetenceController($db); 
    $controller->getSkillsOverview();
});

Flight::route('GET /api/employees-skills', function(){
    $db = Flight::db();
    $controller = new CompetenceController($db); 
    $controller->getEmployeesSkill();
});





