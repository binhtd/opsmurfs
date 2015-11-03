<?php

// Strips nasty tags from code..
function cleanEvilTags($data) {
  $data = preg_replace("/javascript/i", "j&#097;v&#097;script",$data);
  $data = preg_replace("/alert/i", "&#097;lert",$data);
  $data = preg_replace("/about:/i", "&#097;bout:",$data);
  $data = preg_replace("/onmouseover/i", "&#111;nmouseover",$data);
  $data = preg_replace("/onclick/i", "&#111;nclick",$data);
  $data = preg_replace("/onload/i", "&#111;nload",$data);
  $data = preg_replace("/onsubmit/i", "&#111;nsubmit",$data);
  $data = preg_replace("/<body/i", "&lt;body",$data);
  $data = preg_replace("/<html/i", "&lt;html",$data);
  $data = preg_replace("/document\./i", "&#100;ocument.",$data);
  $data = preg_replace("/<script/i", "&lt;&#115;cript",$data);
  return strip_tags(trim($data));
}

// Cleans output data..
function cleanData($data) {
  $data = str_replace(' & ', ' &amp; ', $data);
  return (get_magic_quotes_gpc() ? stripslashes($data) : $data);
}

function multiDimensionalArrayMap($func,$arr) {
  $newArr = array();
  if (!empty($arr)) {
    foreach($arr AS $key => $value) {
      $newArr[$key] = (is_array($value) ? multiDimensionalArrayMap($func,$value) : $func($value));
    }
  }
  return $newArr;
}

function checkTxnid($tnxid){
    global $link;
    $valid_txnid = true;
    //get result set
    $sql = mysql_query("SELECT * FROM `payments` WHERE txnid = '$tnxid'", $link);
    if ($row = mysql_fetch_array($sql)) {
        $valid_txnid = false;
    }
    return $valid_txnid;
}

function updatePayments($data){
    global $link;

    if (is_array($data)) {
        $sql = mysql_query("INSERT INTO `payments` (txnid, payment_amount, payment_status, itemid, createdtime) VALUES (
				'".$data['txn_id']."' ,
				'".$data['payment_amount']."' ,
				'".$data['payment_status']."' ,
				'".$data['item_number']."' ,
				'".date("Y-m-d H:i:s")."'
				)", $link);

        return mysql_insert_id($link);
    }
}

function getAccountByCategoryId($categoryId){
    global $link;
    $sql = "SELECT * FROM account where categoryid=$categoryId and `sent` is null limit 1";
    $result = mysql_query($sql, $link);
    $row = mysql_fetch_assoc($result);

    if ($row) {
        $sql = "UPDATE account set `sent`=1 where `id`=". $row["id"];
        mysql_query($sql, $link);
    }

    return $row;
}

function writeLog($strMessage){
    $file = 'logs.txt';
    $current = file_get_contents($file);
    $current .= "$strMessage\n";
    file_put_contents($file, $current);
}