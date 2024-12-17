<?php
// image_generator.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// Check for POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['description']) && isset($input['image_count'])) {
        $description = $input['description'];
        $imageCount = (int)$input['image_count']; // Ensure it’s an integer
        $imageCount = max(1, min($imageCount, 10)); // Limit image count between 1 and 10

        // Your API endpoint
        $apiUrl = 'https://api.webihqsolutions.in/image-generator/generator.php';
        
        // API key (keep this secret)
        $apiKey = '111222';

        $responses = [];

        // Call the API multiple times based on the image count
        for ($i = 0; $i < $imageCount; $i++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "x-api-key: $apiKey",
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                "description" => $description
            ]));

            $response = curl_exec($ch);

            if (!curl_errno($ch)) {
                $data = json_decode($response, true);
                if (isset($data['imageUrl'])) {
                    $responses[] = $data['imageUrl'];
                }
            }
            curl_close($ch);
        }

        echo json_encode(["images" => $responses]);
    } else {
        echo json_encode(["error" => "Description and image count are required"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>
