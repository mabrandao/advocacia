<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Api-Key');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['messages'][0]['content'];

// Chave API do Google Gemini
$apiKey = 'AIzaSyAl0WC_Lh5YRCWDxa4n5QsP_TXO8hqP_VE';

$ch = curl_init('https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key=' . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 1024
    ]
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Log da resposta para debug
error_log("Gemini Response: " . $response);
error_log("HTTP Code: " . $httpCode);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode([
        'error' => [
            'message' => 'Curl error: ' . curl_error($ch)
        ]
    ]);
    exit;
}

$geminiResponse = json_decode($response, true);

// Se houver erro na resposta
if (isset($geminiResponse['error'])) {
    http_response_code(400);
    echo json_encode([
        'error' => [
            'message' => 'Erro na API do Gemini: ' . ($geminiResponse['error']['message'] ?? 'Erro desconhecido')
        ]
    ]);
    exit;
}

// Verifica se a resposta tem o formato esperado
if (!isset($geminiResponse['candidates'][0]['content']['parts'][0]['text'])) {
    http_response_code(500);
    echo json_encode([
        'error' => [
            'message' => 'Formato de resposta inválido. Resposta completa: ' . $response
        ]
    ]);
    exit;
}

// Converte para o formato esperado pelo frontend
$openAIFormat = [
    'choices' => [
        [
            'message' => [
                'content' => $geminiResponse['candidates'][0]['content']['parts'][0]['text']
            ]
        ]
    ]
];

curl_close($ch);
http_response_code($httpCode);
echo json_encode($openAIFormat);
