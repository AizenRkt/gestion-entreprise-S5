<?php

namespace app\controllers;

use app\models\LLMModel;

class LLMController extends Controller
{
    public function index()
    {
        $chatHistory = $_SESSION['chatHistory'] ?? [];
        \Flight::render('LLMViews/index', ['chatHistory' => $chatHistory]);
    }

    public function ask()
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
    }
}
