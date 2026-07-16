<?php

require "config.php";

header("Content-Type: application/json");

$otp = $_POST['otp'] ?? '';

if(!$otp){
 echo json_encode(["status"=>"error"]);
 exit;
}

if(!isset($_SESSION['otp'])){
 echo json_encode(["status"=>"expired"]);
 exit;
}

if($otp == $_SESSION['otp']){
 echo json_encode(["status"=>"verified"]);
}else{
 echo json_encode(["status"=>"invalid"]);
}