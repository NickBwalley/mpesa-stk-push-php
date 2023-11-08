<?php
        function getAccessToken() {
        $consumerKey = "ZvPaIbYInxUIwq3fYG232UAG11q0BigH";
        $consumerSecret = "jYxWuEnaYJMhp7Ix";
        
        // Choose one depending on your development environment
        // sandbox
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
        // live
        // $url = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
        
        try {
            $encodedCredentials = base64_encode($consumerKey . ':' . $consumerSecret);

            $headers = [
                'Authorization: Basic ' . $encodedCredentials,
                'Content-Type: application/json'
            ];

            // Initialize cURL session and set options
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Send the request and parse the response
            $response = json_decode(curl_exec($ch), true);

            // Check for errors and return the access token
            if (curl_errno($ch)) {
                throw new Exception('Failed to get access token: ' . curl_error($ch));
            } elseif (isset($response['access_token'])) {
                return $response['access_token'];
            } else {
                throw new Exception('Failed to get access token: ' . $response['error_description']);
            }

            // Close the cURL session
            curl_close($ch);
        } catch (Exception $error) {
            throw new Exception('Failed to get access token.');
        }
    }

    try {
        // Call the getAccessToken function
        $accessToken = getAccessToken();
        echo 'Access Token: ' . $accessToken;
    } catch (Exception $error) {
        echo 'Error: ' . $error->getMessage();
    }

    $timestamp = date('YmdHis'); 


    // THIS IS A FUNCTION TO INITIATE THE MPESA-STK-PUSH-NOTIFICATION. 

      function sendStkPush() {
        $token = getAccessToken();
        $timestamp = date('YmdHis'); 
        
        $shortCode = "174379"; //sandbox -174379
        $passkey = "W3ar3l3g10n@sTk.pUsH";
        
        $stk_password = base64_encode($shortCode . $passkey . $timestamp);
        
        //choose one depending on you development environment
        //sandbox
        $url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
        //live
        // $url = "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
        
        $headers = [
          'Authorization: Bearer ' . $token,
          'Content-Type: application/json'
        ];
        
        $requestBody = array(
          "BusinessShortCode" => $shortCode,
          "Password" => $stk_password,
          "Timestamp" => $timestamp,
          "TransactionType" => "CustomerPayBillOnline", //till "CustomerBuyGoodsOnline"
          "Amount" => "1",
          "PartyA" => "254714394332",
          "PartyB" => $shortCode,
          "PhoneNumber" => "254714394332",
          "CallBackURL" => "https://5ce2-197-237-166-21.ngrok-free.app/mpesa-stk-push/mpesa-stk-push.php",
          "AccountReference" => "account",
          "TransactionDesc" => "test"
        );
        
        try {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $response = curl_exec($ch);
          curl_close($ch);
          echo $response;
          return $response;
        } catch (Exception $e) {
          echo 'Error: ',  $e->getMessage(), "
";
        }
      }
      ?>