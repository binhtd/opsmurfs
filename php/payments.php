<?php
include_once("settings.php");
include_once("functions.php");

// Check if paypal request or response
if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])) {
    $categoryId = $_POST["categoryid"];
    $item_name = '';
    $item_amount = 30;

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        die("invalid email, please recheck data");
    }

    if (isset($categories)){
        $categoryData = $categories[$categoryId];
        $item_name = $categoryData["category_description"];
        $item_amount = (int)$categoryData["price"];
    }



    $querystring = '';

    // Firstly Append paypal account to querystring
    $querystring .= "?business=" . urlencode($paypalEmail) . "&";
    $querystring .= "item_name=" . urlencode($item_name) . "&";
    $querystring .= "amount=" . urlencode($item_amount) . "&";

    //loop for posted values and append to querystring
    foreach (array("cmd" => "_xclick","no_note" => "1", "lc" => "us", "currency_code" => "USD", "rm" => "1",
                 "bn" => "PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest", "first_name" => "Customer's First Name",
                 "last_name" => "Customer's Last Name", "payer_email"=> "customer@example.com", "item_number" => $categoryId) as $key => $value) {

        $value = urlencode(stripslashes($value));
        $querystring .= "$key=$value&";
    }

    // Append paypal return addresses
    $querystring .= "return=" . urlencode(stripslashes($returnUrl)) . "&";
    $querystring .= "cancel_return=" . urlencode(stripslashes($cancelUrl)) . "&";
    $querystring .= "notify_url=" . urlencode($notifyUrl);

    // Append querystring with custom field
    $querystring .= "&custom=" . urlencode(stripslashes($_POST["name"])) . "  -  " . urlencode(stripslashes($_POST["email"])) . "  -  " . urlencode(stripslashes($_POST["categoryid"]));
    $querystring .= "  -  $item_amount";

    // Redirect to paypal IPN
    header('location:' . $paypalUrl . $querystring);
    exit();
} else {
    //Database Connection
    $link = mysql_connect("$databaseHostName", "$databaseUserName", "$databasePassword");
    mysql_select_db($databaseName, $link);

    // read the post from PayPal system and add 'cmd'
    $req = 'cmd=_notify-validate';
    foreach ($_POST as $key => $value) {
        $value = urlencode(stripslashes($value));
        $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value);// IPN fix
        $req .= "&$key=$value";
    }

    // assign posted variables to local variables
    $data['item_name'] = $_POST['item_name'];
    $data['item_number'] = $_POST['item_number'];
    $data['payment_status'] = $_POST['payment_status'];
    $data['payment_amount'] = $_POST['mc_gross'];
    $data['payment_currency'] = $_POST['mc_currency'];
    $data['txn_id'] = $_POST['txn_id'];
    $data['receiver_email'] = $_POST['receiver_email'];
    $data['payer_email'] = $_POST['payer_email'];
    $data['custom'] = $_POST['custom'];

    // post back to PayPal system to validate
    $header  = "POST /cgi-bin/webscr HTTP/1.1\r\n";
    $header .= "Host: www.sanbox.paypal.com\r\n";
    $header .= "Accept: */*\r\n";
    $header .= "Connection: Close\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "\r\n";

    $fp = fsockopen($paypalUrlVerify, 443, $errno, $errstr, 30);

    if (!$fp) {
        writeLog("can not verify payment. Please contact admin");
    } else {
        fputs($fp, $header . $req);

        while (!feof($fp)) {
            $res = stream_get_contents($fp, 2048);

            if (preg_match("/VERIFIED/", $res)) {
                $customData = explode("  -  ", $data['custom']);
                $categoryId = (int)$customData[2];
                $customerEmail = $customData[1];
                $customerName = $customData[0];
                $valid_txnid = checkTxnid($data['txn_id']);
                $valid_price = (int)$data['payment_amount'] == (int)$customData[3] ;

                // PAYMENT VALIDATED & VERIFIED!
                if ($valid_txnid && $valid_price) {
                    $orderid = updatePayments($data);
                    if ($orderid) {
                        $accountData = getAccountByCategoryId($categoryId);
                        $userName = $accountData["username"];
                        $password =
                        $emailSubject = "OP SMURFS Account Information";
                        $message = "Hello $customerName,
                        You account information user/pass: (". $accountData["username"] . "/" . $accountData["password"]  . ") please contact if you have any problem";

                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html; charset=utf-8" . "\r\n";
                        $headers .= "From: <$emailFrom>" . "\r\n";
                        mail($customerEmail, $emailSubject, $message, $headers);

                        writeLog("successful creating payment and insert data into database");
                    } else {
                        writeLog("successful creating payment but fail to insert data");
                    }
                } else {
                    writeLog("Payment made but data has been changed");
                }

            } else if (preg_match("/INVALID/", $res)) {
                writeLog("PAYMENT INVALID & INVESTIGATE MANUALY!");
            }
        }
        fclose($fp);
    }
}
?>
