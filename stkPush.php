<?php
// HERE WE GET THE VALUES FROM THE LARAVEL APP AND PASS IT DOWN TO OUR FIELDS 
$name = isset($_GET['name']) ? $_GET['name'] : null;
$employeeIdAuto = isset($_GET['employee_id_auto']) ? $_GET['employee_id_auto'] : null;
$employeeMpesaNumber = isset($_GET['employee_mpesa_number']) ? $_GET['employee_mpesa_number'] : null;
$sendersMpesaNumber = isset($_GET['senders_mpesa_number']) ? $_GET['senders_mpesa_number'] : null;
$amountPaid = isset($_GET['amount_paid']) ? $_GET['amount_paid'] : null;
?>


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
    <div class="card shadow-lg rounded-lg border-success">
         <div class="card-body p-4">
            <img src="./assets/img/lipa-na-mpesa.jpg" class="img-fluid mb-4" alt="Lipa na M-Pesa Image" style="max-width: 20%; height: auto;">
            
            <form method="POST" action="stkpush.php">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="PartyA">Employee Name</label>
                            <input type="tel" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" required readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="PartyA">Employee ID</label>
                            <input type="tel" class="form-control" name="employee_id_auto" id="employee_id_auto" value="<?php echo htmlspecialchars($employeeIdAuto); ?>" required readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="PartyA">Enter Employee M-Pesa Number</label>
                            <input type="tel" class="form-control" name="PartyB" id="PartyB" value="<?php echo htmlspecialchars($employeeMpesaNumber); ?>" required readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="PartyB">Enter Recipient M-Pesa Number</label>
                            <input type="tel" class="form-control" name="PartyA" id="PartyA" value="<?php echo htmlspecialchars($sendersMpesaNumber); ?>" required readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="amount">Amount to be sent:</label>
                            <input type="number" class="form-control" name="amount" id="amount" value="<?php echo htmlspecialchars($amountPaid); ?>" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="pay" class="btn btn-outline-success btn-block">LIPA NA M-PESA</button>
            </form>
        </div>
    </div>
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
  ob_start();

  if (isset($_POST['pay'])) {
    //INCLUDE THE ACCESS TOKEN FILE
    include 'accessToken.php';
    date_default_timezone_set('Africa/Nairobi');
    $processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $callbackurl = 'https://acers.xyz/callback.php';
    $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
    $BusinessShortCode = '174379';
    $Timestamp = date('YmdHis');
    // ENCRIPT  DATA TO GET PASSWORD
    $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp); 
    $PartyA = $_POST['PartyA']; // Phone Number to receive the stk push. 
    $PartyB = $_POST['PartyB']; 
    $AccountReference = 'KINYANJUI FARM';
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
        // Construct the URL with the CheckoutRequestID as a query parameter
        $redirectUrl = "query.php?checkout_request_id=" . urlencode($CheckoutRequestID);

        // Use header() to perform the redirection
        header("Location: $redirectUrl");
        exit();
    } else {
        // Handle the case where $ResponseCode is not "0"
        // You can include an error message or redirect to an error page
        echo "Error occurred with ResponseCode: $ResponseCode";
        // or redirect to an error page
        // header("Location: error.php");
        // exit();
    }


  }

  ob_end_flush();
?>




