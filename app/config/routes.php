<?php

use app\controllers\Controller;
use app\controllers\ressourceHumaine\pointage\PointageController;

//importation liée flight
use flight\Engine;
use flight\net\Router;

//Ressource humaine
require __DIR__ . '/routes/ressourceHumaine/qcmRoute.php';
require __DIR__ . '/routes/ressourceHumaine/authRoute.php';
require __DIR__ . '/routes/ressourceHumaine/employeRoute.php';
require __DIR__ . '/routes/ressourceHumaine/contratRoute.php';
require __DIR__ . '/routes/ressourceHumaine/contratTravailRoute.php';
require __DIR__ . '/routes/ressourceHumaine/entretienRoute.php';
require __DIR__ . '/routes/ressourceHumaine/back/creaAnnonce.php';
require __DIR__ . '/routes/ressourceHumaine/back/competenceRoute.php';
require __DIR__ . '/routes/ressourceHumaine/annonce.php';
require __DIR__ . '/routes/ressourceHumaine/candidatRoute.php';
require __DIR__ . '/routes/ressourceHumaine/scoringRoute.php';
require __DIR__ . '/routes/ressourceHumaine/pointageRoute.php';
require __DIR__ . '/routes/ressourceHumaine/absenceRoute.php';
require __DIR__ . '/routes/ressourceHumaine/heureSuppRoute.php';
require __DIR__ . '/routes/ressourceHumaine/congeRoute.php';
require __DIR__ . '/routes/ressourceHumaine/paieRoute.php';
require __DIR__ . '/routes/ressourceHumaine/jourFerieRoute.php';
require __DIR__ . '/routes/ressourceHumaine/LLMRoute.php';

// avis
require __DIR__ . '/routes/AVIS/KPI/KpiRoute.php';
require __DIR__ . '/routes/AVIS/stock/StockRoute.php';

// avis - referentiel
require __DIR__ . '/routes/AVIS/referentiel/client/ClientRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/client/ClientApiRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/article/ArticleRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/article/ArticleApiRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/site/SiteRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/site/SiteApiRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/fournisseur/FournisseurRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/fournisseur/FournisseurApiRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/document/DocumentRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/document/DocumentApiRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/valorisation/ValorisationRoute.php';
require __DIR__ . '/routes/AVIS/referentiel/valorisation/ValorisationApiRoute.php';

/** 
 * @var Router $router 
 * @var Engine $app
*/

$Controller = new Controller();
$pointageController = new PointageController(); // Instantiate PointageController

// $router->get('/planning2', [ $Controller, 'planning2' ]);
$router->get('/backOffice',function() use ($Controller, $pointageController){
    $pointageController->updatePointageStatusInSession(); // Update pointage status in session
    $Controller->backOffice();
});

?>