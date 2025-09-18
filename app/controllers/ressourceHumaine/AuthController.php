<?php
namespace app\Controllers\ressourceHumaine;
use app\models\ressourceHumaine\AuthModel;
use Flight;
use PDO;

class AuthController {
    public function login() {
        // Utiliser la superglobale $_POST est plus direct pour les données de formulaire.
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        $user = AuthModel::getByUsername($username);

        // Utilisez password_verify pour une comparaison de mot de passe sécurisée
        if ($user && $password && password_verify($password, $user['pwd'])) {
            // Connexion réussie
            $_SESSION['user'] = $user; // Stocker les informations de l'utilisateur en session
            $mssg = "Bienvenue " . $user['username'] . "!";
            // Rediriger vers une page du back-office, par exemple /annonceCrea
            Flight::redirect('/annonceCrea?mssg=' . urlencode($mssg));
        } else {
            // Échec de la connexion
            $mssg = "Nom d'utilisateur ou mot de passe incorrect.";
            Flight::redirect('/log?mssg=' . urlencode($mssg));
        }
    }
}
?>