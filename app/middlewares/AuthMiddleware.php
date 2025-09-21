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

        // 2. Récupérer l'URL "propre", sans le sous-dossier de base de l'application
        $baseUrl = Flight::request()->base;
        $requestUrl = Flight::request()->url;
        $requestPath = $requestUrl;

        // Si l'URL demandée commence par l'URL de base (le sous-dossier), on la supprime
        if ($baseUrl && strpos($requestUrl, $baseUrl) === 0) {
            $requestPath = substr($requestUrl, strlen($baseUrl));
        }

        // S'assurer que le chemin commence par un / pour la comparaison
        if (empty($requestPath) || $requestPath[0] !== '/') {
            $requestPath = '/' . $requestPath;
        }

        // 3. Vérifier si cette URL nettoyée est protégée
        $isProtectedRoute = false;
        $allowedRoles = [];

        foreach ($permissions as $route => $roles) {
            if (strpos($requestPath, $route) === 0) {
                $isProtectedRoute = true;
                $allowedRoles = $roles;
                break;
            }
        }

        // Si la route n'est pas protégée, on laisse passer
        if (!$isProtectedRoute) {
            return; // Accès autorisé
        }

        // 4. Si la route est protégée, vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            Flight::redirect('/log');
            exit();
        }

        // 5. Vérifier si le rôle de l'utilisateur est dans la liste des rôles autorisés
        $userRole = $_SESSION['user']['role'];
        if (!in_array($userRole, $allowedRoles)) {
            Flight::halt(403, '<h1>403 - Accès Interdit</h1><p>Vous n\'avez pas les droits nécessaires pour accéder à cette page.</p>');
            exit();
        }
    }
}