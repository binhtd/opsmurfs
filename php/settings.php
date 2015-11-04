<?php
$adminUserName = "admin";
$adminPassword = "D@ngha123456";
$categories = array(
    "1" => array("category_id"=> "1", "category_name" => "EUW - Level 30 - 30+ champions", "category_description" => "EUW - Level 30 - 30+ champions. Random skins. Price 30$", "price" => 30),
    "2" => array("category_id"=> "2", "category_name" => "EUW - Level 30 - 40+ champions", "category_description" => "EUW - Level 30 - 40+ champions. Random skins. Price 40$", "price" => 40),
    "3" => array("category_id"=> "3", "category_name" => "EUW - Level 30 - 50+ champions", "category_description" => "EUW - Level 30 - 50+ champions. Random skins. Price 50$", "price" => 50),
    "4" => array("category_id"=> "4", "category_name" => "NA - Level 30 - 30+ champions", "category_description" => "EUW - Level 30 - 30+ champions. Random skins. Price 30$", "price" => 30),
    "5" => array("category_id"=> "5", "category_name" => "NA - Level 30 - 40+ champions", "category_description" => "EUW - Level 30 - 40+ champions. Random skins. Price 40$", "price" => 40),
    "6" => array("category_id"=> "6", "category_name" => "NA - Level 30 - 50+ champions", "category_description" => "EUW - Level 30 - 30+ champions. Random skins. Price 50$", "price" => 50),
);

$databaseHostName = "localhost";
$databaseName = "opsmurfs";
$databaseUserName = "anh_binh";
$databasePassword = "123456";

$rowPerPage = 10;

// PayPal settings
$paypalEmail = 'binh.trinh-facilitator@niteco.se';
$returnUrl = 'http://cotuongvn.com/opsmurfs/payment-successful.html';
$cancelUrl = 'http://cotuongvn.com/opsmurfs/payment-cancelled.html';
$notifyUrl = 'http://cotuongvn.com/opsmurfs/php/payments.php';
$paypalUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
$paypalUrlVerify = 'ssl://www.sandbox.paypal.com';
