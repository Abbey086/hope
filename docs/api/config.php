<?php

session_start();

define("ADMIN_EMAIL", "info@hopeworldwideuganda.org");

define("UPLOAD_PATH", "../uploads/");

define("MAX_FILE_SIZE", 5 * 1024 * 1024);

$dbFile = __DIR__ . '/data/database.sqlite';

try {
    // Create (or open) the SQLite database
    $pdo = new PDO("sqlite:" . $dbFile);
    
    // Set error mode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("SQLite connection failed: " . $e->getMessage());
}

function dmail($x,$y,$z,$w){
   
    }

function sendMail($to,$subject,$message,$headers=""){

    $defaultHeaders =
    "MIME-Version: 1.0\r\n".
    "Content-type:text/html;charset=UTF-8\r\n".
    "From: Hope Worldwide <noreply@hopeworldwideuganda.org>\r\n";

    if($headers){
        $defaultHeaders .= $headers;
    }

    return mail($to,$subject,$message,$defaultHeaders);
}