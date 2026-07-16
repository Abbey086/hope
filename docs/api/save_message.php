<?php

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false]);
    exit;
}

$form_type = $_POST['form_type'] ?? 'unknown';
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email) {
    echo json_encode(['success'=>false,'error'=>'Name and Email required']);
    exit;
}


$exclude = ['form_type','name','email','message'];
$extraDetails = [];

foreach($_POST as $key=>$val){

    if(!in_array($key,$exclude)){
        $extraDetails[$key] = $val;
    }

}

$detailsJson = json_encode($extraDetails);


/* FILE HANDLING */

$uploadedFiles = [];

if(!empty($_FILES)){

foreach($_FILES as $field=>$file){

if($file['error'] === 0){

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);

$filename = uniqid().".".$ext;

$target = __DIR__."/../uploads/".$filename;

move_uploaded_file($file['tmp_name'],$target);

$uploadedFiles[$field] = $filename;

}

}

}

if($uploadedFiles){
$extraDetails["files"] = $uploadedFiles;
$detailsJson = json_encode($extraDetails);
}


/* SAVE MESSAGE */

$stmt = $pdo->prepare("
INSERT INTO messages
(form_type,name,email,message,details)
VALUES (?,?,?,?,?)
");

$stmt->execute([
$form_type,
$name,
$email,
$message,
$detailsJson
]);


/* EMAIL */

$emailBody =
"New form submission\n\n".
"Form Type: $form_type\n".
"Name: $name\n".
"Email: $email\n\n".
"Message:\n$message\n\n";

foreach($extraDetails as $k=>$v){

if(is_array($v)){
$v = json_encode($v);
}

}

echo json_encode(["success"=>true]);