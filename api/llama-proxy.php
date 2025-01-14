<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['messages'][0]['content'];

// Comando para executar o modelo Llama 2 localmente
$command = 'llama-cpp-server --model models/llama-2-7b-chat.gguf --ctx-size 2048';

// Inicia o servidor Llama 2 em background (Windows)
pclose(popen('start /B ' . $command, 'r'));

// Aguarda o servidor iniciar
sleep(2);

// Faz a requisição para o servidor local
$ch = curl_init('http://localhost:8080/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'messages' => [
        [
            'role' => 'user',
            'content' => $prompt
        ]
    ],
    'temperature' => 0.7,
    'max_tokens' => 2048
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode([
        'error' => [
            'message' => 'Curl error: ' . curl_error($ch)
        ]
    ]);
    exit;
}

curl_close($ch);

// Encerra o servidor Llama 2
exec('taskkill /F /IM llama-cpp-server.exe');

http_response_code($httpCode);
echo $response;
