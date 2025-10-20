<?php
// app/middlewares/AuthMiddleware.php
namespace app\middlewares;

use Flight;
use app\models\ressourceHumaine\PermissionModel;

class AuthMiddleware {

    public static function checkAccess() {
        // 1. Récupérer les permissions depuis la base de données
        $permissionModel = new PermissionModel();
        $permissions = $permissionModel->getPermissions();

        // 2. Récupérer l'URL "propre", sans le sous-dossier de base
        $baseUrl = Flight::request()->base;
        $requestUrl = Flight::request()->url;
        $requestPath = $requestUrl;

        if ($baseUrl && strpos($requestUrl, $baseUrl) === 0) {
            $requestPath = substr($requestUrl, strlen($baseUrl));
        }

        if (empty($requestPath) || $requestPath[0] !== '/') {
            $requestPath = '/' . $requestPath;
        }

        // 3. Identifier si la route est protégée
        $matchedRules = null;

        foreach ($permissions as $route => $rules) {
            if (strpos($requestPath, $route) === 0) {
                $matchedRules = $rules; // tableau des rôles + services autorisés
                break;
            }
        }

        // Si route non protégée, accès libre
        if (!$matchedRules) {
            return;
        }

        // 4. Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            Flight::redirect('/log');
            exit();
        }

        // 5. Vérifier si l'utilisateur a le bon rôle ET appartient au bon service
        $userRole = $_SESSION['user']['role'];
        $userService = (int)$_SESSION['user']['id_service'];

        $hasAccess = false;
        foreach ($matchedRules as $rule) {
            if ($rule['role'] === $userRole && (int)$rule['id_service'] === $userService) {
                $hasAccess = true;
                break;
            }
        }

        // 6. Si non autorisé → erreur 403
        if (!$hasAccess) {
            session_destroy();
            Flight::render('ui/erreur403');
            exit();
        }
    }

    // public static function checkAccess() {
    //     // 1. Récupérer les permissions depuis la base de données
    //     $permissionModel = new PermissionModel();
    //     $permissions = $permissionModel->getPermissions();

    //     // 2. Récupérer l'URL "propre", sans le sous-dossier de base de l'application
    //     $baseUrl = Flight::request()->base;
    //     $requestUrl = Flight::request()->url;
    //     $requestPath = $requestUrl;

    //     // Si l'URL demandée commence par l'URL de base (le sous-dossier), on la supprime
    //     if ($baseUrl && strpos($requestUrl, $baseUrl) === 0) {
    //         $requestPath = substr($requestUrl, strlen($baseUrl));
    //     }

    //     // S'assurer que le chemin commence par un / pour la comparaison
    //     if (empty($requestPath) || $requestPath[0] !== '/') {
    //         $requestPath = '/' . $requestPath;
    //     }

    //     // 3. Vérifier si cette URL nettoyée est protégée
    //     $isProtectedRoute = false;
    //     $allowedRoles = [];

    //     foreach ($permissions as $route => $roles) {
    //         if (strpos($requestPath, $route) === 0) {
    //             $isProtectedRoute = true;
    //             $allowedRoles = $roles;
    //             break;
    //         }
    //     }

    //     // Si la route n'est pas protégée, on laisse passer
    //     if (!$isProtectedRoute) {
    //         return; // Accès autorisé
    //     }

    //     // 4. Si la route est protégée, vérifier si l'utilisateur est connecté
    //     if (!isset($_SESSION['user'])) {
    //         Flight::redirect('/log');
    //         exit();
    //     }

    //     // 5. Vérifier si le rôle de l'utilisateur est dans la liste des rôles autorisés
    //     $userRole = $_SESSION['user']['role'];
    //     if (!in_array($userRole, $allowedRoles)) {
    //         // Flight::halt(403, '<h1>403 - Accès Interdit</h1><p>Vous n\'avez pas les droits nécessaires pour accéder à cette page.</p>');
    //         Flight::render('ui/erreur403');
    //         exit();
    //     }
    // }
}