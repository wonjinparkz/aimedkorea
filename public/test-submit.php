<?php
// Test script to submit a complete survey with all 25 responses

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Prepare test data
$testData = [
    '_token' => csrf_token(),
    'analysis_type' => 'simple',
    'responses' => []
];

// Generate 25 responses (all set to value 2 for testing)
for ($i = 0; $i < 25; $i++) {
    $testData['responses'][$i] = 2;
}

// Create a request
$request = Illuminate\Http\Request::create(
    '/surveys/1',
    'POST',
    $testData
);

// Handle the request
$response = $kernel->handle($request);

// Check if successful
if ($response->getStatusCode() == 302) {
    echo "✅ Survey submitted successfully!\n";
    
    // Get the redirect location
    $location = $response->headers->get('Location');
    echo "Redirected to: $location\n";
    
    // Extract response ID from redirect URL
    if (preg_match('/results\/(\d+)$/', $location, $matches)) {
        $responseId = $matches[1];
        echo "New response ID: $responseId\n";
        
        // Check the database
        $db = new PDO('mysql:host=localhost;dbname=laravel', 'root', 'root');
        $stmt = $db->prepare("SELECT id, total_score, JSON_LENGTH(responses_data) as response_count FROM survey_responses WHERE id = ?");
        $stmt->execute([$responseId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo "\nDatabase check:\n";
            echo "- Response ID: " . $result['id'] . "\n";
            echo "- Total responses saved: " . $result['response_count'] . "\n";
            echo "- Total score: " . $result['total_score'] . "\n";
            
            if ($result['response_count'] == 25) {
                echo "\n🎉 SUCCESS! All 25 responses were saved correctly!\n";
            } else {
                echo "\n⚠️ WARNING: Only " . $result['response_count'] . " responses were saved (expected 25)\n";
            }
        }
    }
} else {
    echo "❌ Error submitting survey. Status code: " . $response->getStatusCode() . "\n";
    echo "Response: " . $response->getContent() . "\n";
}