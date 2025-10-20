<?php
namespace app\controllers\ressourceHumaine;
use app\models\ressourceHumaine\AuthModel;
use Flight;
use PDO;

class AuthController {

    public function log() {
        Flight::render('auth/log');
    }

    public function sign() {
        Flight::render('auth/sign');        
    }

    public function authVerif() {
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;
        $user = AuthModel::getActiveUserByUsername($username);

        if ($user && $password && $password === $user['pwd']) {
            $role = AuthModel::getUserRoleByUserId($user['id_user']);
            $service =AuthModel::getServiceByUserId($user['id_user']);
            $_SESSION['user'] = [
                'id_user' => $user['id_user'],
                'username' => $user['username'],
                'role' => $role,
                'id_service' => $service
            ]; 
            $mssg = "Bienvenue " . $user['username'] . "!";
            Flight::redirect('/backOffice?mssg=' . urlencode($mssg));
        } else {
            $mssg = "Nom d'utilisateur ou mot de passe incorrect ou compte inactif.";
            Flight::redirect('/log/?mssg=' . urlencode($mssg));
        }
    }

    public function authInscription() {
        $email = $_POST['email'] ?? null;
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$email || !$username || !$password) {
            $mssg = "Veuillez remplir tous les champs.";
            Flight::redirect('/sign?mssg=' . urlencode($mssg));
            return;
        }

        $result = AuthModel::registerUser($email, $username, $password);

        if ($result['success']) {
            $user = AuthModel::getByUsername($username);
            $role = AuthModel::getUserRoleByUserId($user['id_user']);
            $service =AuthModel::getServiceByUserId($user['id_user']);

            $_SESSION['user'] = [
                'id_user' => $user['id_user'],
                'username' => $user['username'],
                'role' => $role,
                'id_service' => $service
            ]; 
            $mssg = "Inscription réussie. Bienvenue " . $user['username'] . "!";
            Flight::redirect('/employes?mssg=' . urlencode($mssg));
        } else {
            Flight::redirect('/sign?mssg=' . urlencode($result['message']));
        }
    }

    public function authDeconnexion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        session_destroy();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        Flight::redirect('/log');
    }

}
?>