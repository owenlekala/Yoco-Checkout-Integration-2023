<?php
// Set your Yoco secret key
$secretKey = '[YOUR LIVE SECRET KEY]';

$amount = isset($_GET['amount']) ? intval($_GET['amount']) : 0;

if ($amount <= 0) {
    echo 'Invalid amount.';
    exit;
}

// Create a data array with the payment details
$data = array(
    "amount" => $amount,
    "currency" => "ZAR",
    "cancelUrl" => "https://pay.domain.co.za/cancel.php",
    "successUrl" => "https://pay.domain.co.za/success.php",
    "failureUrl" => "https://pay.domain.co.za/failure.php"
    
);

// Convert the data array to JSON format
$jsonData = json_encode($data);

// Set the Yoco API endpoint URL
$apiUrl = 'https://payments.yoco.com/api/checkouts';

// Initialize cURL session
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $secretKey
));

// Execute cURL session and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
} else {
    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Check if the checkout was created successfully
    if ($responseData["status"] === "created" && isset($responseData["redirectUrl"])) {
        // Redirect the user to the Yoco checkout page
        header("Location: " . $responseData["redirectUrl"]);
        exit; // Make sure to exit after the redirection
    } else {
        echo 'Failed to create the checkout.';
    }

    // Close cURL session
    curl_close($ch);
}
?>
