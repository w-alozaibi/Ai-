<?php
class AIWrapper
{
    private $ingredients = [];
    private $response = '';
    private $apiKey;
    private $model;
    private $temperature;
    private $maxTokens;

    public function __construct()
    {
        // Controleer of config beschikbaar is
        if (!defined('API_KEY')) {
            require_once __DIR__ . '/../config/config.php';
        }
        $this->apiKey = API_KEY;
        $this->model = API_MODEL;
        $this->temperature = API_TEMPERATURE;
        $this->maxTokens = API_MAX_TOKENS;
    }

    public function processInput($ingredients)
    {
        if (empty($ingredients)) {
            throw new Exception("No ingredients provided");
        }
        $this->ingredients = $ingredients;

        try {
            $response = $this->callOpenAI();
            $this->response = $response;
            return true;
        } catch (Exception $e) {
            throw new Exception("Error processing recipe: " . $e->getMessage());
        }
    }

    private function callOpenAI()
    {
        $ingredientsList = implode(', ', $this->ingredients);
        $prompt = "Create a recipe using these ingredients: $ingredientsList. " .
            "Format the response as JSON with the following structure: " .
            '{"naam": "Recipe name", "ingrediënten": ["ingredient1", "ingredient2"], ' .
            '"bereidingstijd": "time", "stappen": ["step1", "step2"], ' .
            '"moeilijkheidsgraad": "difficulty"}';

        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful cooking assistant that creates recipes. Always respond in JSON format.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => (float)$this->temperature,
            'max_tokens' => (int)$this->maxTokens,
            'response_format' => ['type' => 'json_object']
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('API request failed: ' . $error);
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMessage = isset($errorData['error']['message']) 
                ? $errorData['error']['message'] 
                : "API returned error code: $httpCode";
            throw new Exception($errorMessage);
        }

        $result = json_decode($response, true);
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new Exception('Invalid API response format');
        }

        return $result['choices'][0]['message']['content'];
    }

    public function getResponse()
    {
        return $this->response;
    }
}

