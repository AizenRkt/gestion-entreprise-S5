<?php

namespace app\models;

class LLMModel
{
    public function callLLMService($question, $employeeId = null)
    {
        $url = 'http://localhost:5000/api/llm';
        $data = [
            'question' => $question,
            'employee_id' => $employeeId
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === false) {
            return ['status' => 'error', 'error' => 'Impossible de contacter le service LLM'];
        }

        return json_decode($result, true);
    }
}
