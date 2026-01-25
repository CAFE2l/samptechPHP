<?php
// Simple test endpoint to debug the response
header('Content-Type: text/plain');

echo "=== DEBUG API TEST ===\n";
echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Content Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set') . "\n";

$input = file_get_contents('php://input');
echo "Raw Input: " . $input . "\n";

$decoded = json_decode($input, true);
echo "Decoded Input: " . print_r($decoded, true) . "\n";

echo "Session Status: " . session_status() . "\n";

// Try to start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "Session Started: " . (session_status() === PHP_SESSION_ACTIVE ? 'Yes' : 'No') . "\n";

// Test JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Debug test successful',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>