<?php
header("Content-Type: application/json");
$stkCallbackResponse = file_get_contents('php://input');
$logFile = "Mpesastkresponse.json";
$log = fopen($logFile, "a");
fwrite($log, $stkCallbackResponse);
fclose($log);

$data = json_decode($stkCallbackResponse);

if ($data === null) {
    // JSON decoding failed
    echo "JSON decoding failed as a result of null values.";
} else {
    // Check if the expected properties exist
    if (isset($data->Body->stkCallback)) {
        $MerchantRequestID = $data->Body->stkCallback->MerchantRequestID;
        $CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID;
        $ResultCode = $data->Body->stkCallback->ResultCode;
        $ResultDesc = $data->Body->stkCallback->ResultDesc;

        if (isset($data->Body->stkCallback->CallbackMetadata->Item[0]->Value)) {
            $Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value;
        }

        if (isset($data->Body->stkCallback->CallbackMetadata->Item[1]->Value)) {
            $TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value;
        }

        if (isset($data->Body->stkCallback->CallbackMetadata->Item[4]->Value)) {
            $UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value;
        }

        // Check if the transaction was successful
        if ($ResultCode == 0) {
            // Store the transaction details in the database
        } else {
            echo "Transaction failed with ResultCode: $ResultCode, ResultDesc: $ResultDesc";
        }
    } else {
        echo "Missing stkCallback data in the JSON response.";
    }
}
