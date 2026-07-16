<?php

require "config.php";

header("Content-Type: application/json");

$email = $_POST['email'] ?? '';

if(!$email){
    echo json_encode(["status"=>"error","message"=>"Email required"]);
    exit;
}

$otp = rand(100000,999999);

$_SESSION['otp'] = $otp;
$_SESSION['otp_email'] = $email;

$message = "
<h2>Email Verification</h2>
<p>Your verification code is:</p>
<h1>$otp</h1>
<p>This code expires in 10 minutes.</p>
";

sendMail($email,"Your Verification Code",$message);

echo json_encode([
"status"=>"success"
]);