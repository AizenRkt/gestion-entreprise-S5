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

        /*
        // DEBUG : Afficher les données reçues
        echo '<pre style="color:blue">';
        echo "Données reçues :\n";
        print_r(['username' => $username, 'password' => $password]);

        // DEBUG : Tester la requête SQL avec le paramètre
        $user = AuthModel::getByUsername($username);
        echo "\nRésultat de la requête getByUsername :\n";
        print_r($user);

        // DEBUG : Tester la requête SQL en dur
        $db = \Flight::db();
        $stmt = $db->prepare("SELECT * FROM user WHERE username = 'jrakoto'");
        $stmt->execute();
        $userHard = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nRésultat de la requête en dur :\n";
        print_r($userHard);

        // DEBUG : Afficher la base courante
        $dbname = $db->query('SELECT DATABASE()')->fetchColumn();
        echo "\nBase courante : $dbname\n";
        echo '</pre>';
        exit;
        */

        $user = AuthModel::getByUsername($username);

        if ($user && $password && $password === $user['pwd']) { 
            $_SESSION['user'] = $user; 
            $mssg = "Bienvenue " . $user['username'] . "!";
            Flight::redirect('/annoncePage?mssg=' . urlencode($mssg));
        } else {
            $mssg = "Nom d'utilisateur ou mot de passe incorrect.";
            Flight::redirect('/?mssg=' . urlencode($mssg));
        }
    }

    public function authInscription() {
        $email = $_POST['email'] ?? null;
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        // DEBUG : Afficher les données reçues
        echo '<pre style="color:green">';
        echo "Données reçues (inscription) :\n";
        print_r(['email' => $email, 'username' => $username, 'password' => $password]);

        if (!$email || !$username || !$password) {
            $mssg = "Veuillez remplir tous les champs.";
            echo "\nErreur : $mssg\n";
            echo '</pre>';
            exit;
            // Flight::redirect('/sign?mssg=' . urlencode($mssg));
            // return;
        }

        $result = AuthModel::registerUser($email, $username, $password);
        echo "\nRésultat de registerUser :\n";
        print_r($result);

        if ($result['success']) {
            // Log the user in automatically
            $user = AuthModel::getByUsername($username);
            echo "\nUtilisateur créé :\n";
            print_r($user);
            echo '</pre>';
            exit;
            // $_SESSION['user'] = $user;
            // $mssg = "Inscription réussie. Bienvenue " . $user['username'] . "!";
            // Flight::redirect('/annoncePage?mssg=' . urlencode($mssg));
        } else {
            echo "\nErreur : " . $result['message'] . "\n";
            echo '</pre>';
            exit;
            // Flight::redirect('/sign?mssg=' . urlencode($result['message']));
        }
    }
}
?>