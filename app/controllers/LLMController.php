<?php

namespace app\controllers;

use app\models\LLMModel;

class LLMController extends Controller
{
    public function index()
    {
        $chatHistory = $_SESSION['chatHistory'] ?? [];
        $employeeId = $_SESSION['user']['id_employe'] ?? null;
        \Flight::render('LLMViews/index', ['chatHistory' => $chatHistory, 'employeeId' => $employeeId]);
    }

    /*public function ask()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $question = $_POST['question'] ?? '';
            $employeeId = $_POST['employee_id'] ?? null;

            if (empty($question)) {
                $chatHistory = $_SESSION['chatHistory'] ?? [];
                \Flight::render('LLMViews/index', ['error' => 'Question requise', 'chatHistory' => $chatHistory]);
                return;
            }

            $llmModel = new LLMModel();
            $response = $llmModel->callLLMService($question, $employeeId);

            // Ajout à l'historique
            if (!isset($_SESSION['chatHistory'])) {
                $_SESSION['chatHistory'] = [];
            }
            $_SESSION['chatHistory'][] = [
                'question' => $question,
                'response' => ($response['status'] === 'success') ? $response['data']['response'] : $response['error']
            ];

            $chatHistory = $_SESSION['chatHistory'];
            \Flight::render('LLMViews/index', [
                'question' => $question,
                'response' => $response,
                'chatHistory' => $chatHistory
            ]);
        } else {
            $this->index();
        }
    }*/

    public function ask()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $question = $_POST['question'] ?? '';
            $employeeId = $_POST['employee_id'] ?? null;

            if (empty($question)) {
                echo json_encode(['response' => 'Question requise']);
                return;
            }

            // Récupérer les infos utilisateur pour filtrage
            $userRole = $_SESSION['user']['role'] ?? null;
            $userServiceId = $_SESSION['user']['id_service'] ?? null;
            $employeeId = $_SESSION['user']['id_employe'] ?? null;

            $llmModel = new LLMModel();
            $response = $llmModel->callLLMService($question, $employeeId, $userRole, $userServiceId);

            // Préparer la réponse à renvoyer
            $assistantResponse = ($response['status'] === 'success') ? $response['data']['response'] : $response['error'];

            // Ajout à l'historique
            if (!isset($_SESSION['chatHistory'])) {
                $_SESSION['chatHistory'] = [];
            }
            $_SESSION['chatHistory'][] = [
                'question' => $question,
                'response' => $assistantResponse
            ];

            // Renvoie du JSON pour le JS
            header('Content-Type: application/json');
            echo json_encode(['response' => $assistantResponse]);
            return;
        }

        // GET ou autres → afficher la page normale
        $this->index();
    }

}
