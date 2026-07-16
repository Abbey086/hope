<?php

require "config.php";

header("Content-Type: application/json");

if(!isset($_SESSION['otp'])){
 echo json_encode(["status"=>"otp_required"]);
 exit;
}

$data = $_POST;

$firstname = $data['firstname'] ?? '';
$lastname = $data['lastname'] ?? '';
$email = $data['email'] ?? '';
$type = $data['type'] ?? '';

if(!$firstname || !$lastname || !$email){
 echo json_encode(["status"=>"error","message"=>"Missing fields"]);
 exit;
}

$filePath = "";

if(!empty($_FILES['resume']['name'])){

 if($_FILES['resume']['size'] > MAX_FILE_SIZE){
   echo json_encode(["status"=>"error","message"=>"File too large"]);
   exit;
 }

 $ext = pathinfo($_FILES['resume']['name'],PATHINFO_EXTENSION);

 $allowed = ["pdf","doc","docx"];

 if(!in_array($ext,$allowed)){
   echo json_encode(["status"=>"error","message"=>"Invalid file type"]);
   exit;
 }

 $fileName = time()."_".rand(1000,9999).".".$ext;

 move_uploaded_file(
   $_FILES['resume']['tmp_name'],
   UPLOAD_PATH.$fileName
 );

 $filePath = $fileName;
}

$message = "
<h2>New Application</h2>

<b>Name:</b> $firstname $lastname<br>
<b>Email:</b> $email<br>
<b>Application Type:</b> $type<br>

";

if($filePath){
 $message .= "<b>Resume:</b> $filePath<br>";
}

sendMail(ADMIN_EMAIL,"New Application",$message);

sendMail($email,"Application Received",
"
<h2>Thank you for applying</h2>
<p>We received your application successfully.</p>
");

/* --- START: SAVE TO DB --- */
// Combine first and last name for the single 'name' column
$fullName = trim($firstname . ' ' . $lastname);

// Prepare the details JSON, including the uploaded resume if it exists
$extraDetails = [];
if ($filePath) {
    $extraDetails['files'] = ['resume' => $filePath];
}

// Dynamically grab any other POST data that wasn't explicitly defined
$exclude = ['firstname', 'lastname', 'email', 'type'];
foreach($_POST as $key => $val){
    if(!in_array($key, $exclude)){
        $extraDetails[$key] = $val;
    }
}

$detailsJson = json_encode($extraDetails);

// Set a default message since the application form doesn't seem to have a dedicated message field
$formMessage = "New application submitted.";

// Insert into the database (assumes $pdo is available via config.php)
$stmt = $pdo->prepare("
    INSERT INTO messages
    (form_type, name, email, message, details)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $type,          // Maps to form_type
    $fullName,      // Maps to name
    $email,         // Maps to email
    $formMessage,   // Maps to message
    $detailsJson    // Maps to details
]);
/* --- END: SAVE TO DB --- */

echo json_encode([
"status"=>"success"
]);