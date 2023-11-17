<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>MPESA-Payment</title>
</head>
<body>

<div class="container mt-5">
    <form method="POST" action="stkpush.php">
        <div class="form-group">
            <label for="PartyA">Enter Senders Phone Number</label>
            <input type="tel" class="form-control" name="PartyA" id="PartyA" placeholder="Enter phone number for Party A" required>
        </div>
        <div class="form-group">
            <label for="PartyB">Enter Recipient Phone Number</label>
            <input type="tel" class="form-control" name="PartyB" id="PartyB" placeholder="Enter phone number for Party B" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount to be sent:</label>
            <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter amount" required>
        </div>
        <button type="submit" name="pay" class="btn btn-primary">CLICK TO PAY</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- <script>
    function submitForm() {
        // Add your form submission logic here
        // For example, you can use JavaScript to retrieve input values and send them to a server.
        var PartyA = document.getElementById('PartyA').value;
        var PartyB = document.getElementById('PartyB').value;
        var amount = document.getElementById('amount').value;

        // Add your logic for form submission here
        console.log(PartyA);
        console.log(PartyB);
        console.log(amount);
    }
</script> -->

</body>
</html>


<!-- --------------------THIS IS THE BACKEND CODE-------------------------  -->


<?php
  if (isset($_POST['pay'])) {
    //INCLUDE THE ACCESS TOKEN FILE
    include 'accessToken.php';
    date_default_timezone_set('Africa/Nairobi');
    $processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $callbackurl = 'https://0a83-197-237-166-21.ngrok-free.app/mpesa-stk-push/callback.php';
    $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
    $BusinessShortCode = '174379';
    $Timestamp = date('YmdHis');
    // ENCRIPT  DATA TO GET PASSWORD
    $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp); 
    $PartyA = $_POST['PartyA']; // Phone Number to receive the stk push. 
    $PartyB = $_POST['PartyB']; 
    $AccountReference = 'NICK BWALLEY';
    $TransactionDesc = 'stkpush test';
    $Amount = $_POST['amount']; // Amount to be sent 
    $stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
    //INITIATE CURL
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader); //setting custom header
    $curl_post_data = array(
      //Fill in the request parameters with valid values
      'BusinessShortCode' => $BusinessShortCode,
      'Password' => $Password,
      'Timestamp' => $Timestamp,
      'TransactionType' => 'CustomerPayBillOnline',
      'Amount' => $Amount,
      'PartyA' => $PartyA,
      'PartyB' => $BusinessShortCode,
      'PhoneNumber' => $PartyA,
      'CallBackURL' => $callbackurl,
      'AccountReference' => $AccountReference,
      'TransactionDesc' => $TransactionDesc
    );

    $data_string = json_encode($curl_post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    echo $curl_response = curl_exec($curl);
    //ECHO  RESPONSE
    $data = json_decode($curl_response);
    $CheckoutRequestID = $data->CheckoutRequestID;
    $ResponseCode = $data->ResponseCode;
    if ($ResponseCode == "0") {
      echo "The CheckoutRequestID for this transaction is : " . $CheckoutRequestID;
      // Redirect to query.php with CheckoutRequestID as a query parameter
      header("Location: query.php?checkout_request_id=$CheckoutRequestID");
      exit();
    }
  }
?>




