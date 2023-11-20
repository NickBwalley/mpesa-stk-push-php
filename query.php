<?php
// Automatically refresh the page after 5 seconds. 
header("Refresh: 5");
// Include the access token file
include 'accessToken.php';

// Set the default timezone
date_default_timezone_set('Africa/Nairobi');

// M-Pesa API endpoint for STK push query
$query_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';

// M-Pesa credentials
$BusinessShortCode = '174379';
$passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";

// Generate the timestamp
$Timestamp = date('YmdHis');

// Encrypt data to get the password
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

// Get the CheckoutRequestID from the query parameters
$CheckoutRequestID = isset($_GET['checkout_request_id']) ? $_GET['checkout_request_id'] : '';

// Set the request headers
$queryheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

# Initiating the transaction query
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $query_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $queryheader);
$curl_post_data = [
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $Password,
    'Timestamp' => $Timestamp,
    'CheckoutRequestID' => $CheckoutRequestID
];
$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

// Execute the cURL request
$curl_response = curl_exec($curl);

// Decode the cURL response
$data_to = json_decode($curl_response);

// Check if ResultCode is set in the response
if (isset($data_to->ResultCode)) {
    $ResultCode = $data_to->ResultCode;

    // Determine the message based on the ResultCode
    switch ($ResultCode) {
        case '1037':
            $message = "1037 Timeout in completing transaction";
            break;
        case '1032':
            $message = "1032 Transaction cancelled by the user";
            break;
        case '1':
            $message = "1 Insufficient Balance for the transaction";
            break;
        case '0':
            $message = "0 Transaction was successful";
            break;
        default:
            $message = "Unexpected ResultCode: $ResultCode";
    }
}

// Close the cURL session
curl_close($curl);

// Output the cURL response (you might want to handle this differently in a production environment)
echo $curl_response;
