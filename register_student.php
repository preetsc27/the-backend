<?php

//connecting the database and filtering data function....
require 'db.php';

//getting the data from app in json format...
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$name = secure($_POST["name"]);
$email = secure($_POST["email"]);
$password = secure($_POST["password"]);

// small validation check
if($name === "" || $email === "" || $password === ""){
    $res->success = false;
    $res->message = "Enter all the feilds";
    echo json_encode($res);
    return;
}

// hashing the password
$passwordHash = md5($password.$salt);

try {
    $sql = $conn->prepare('INSERT INTO student (name, email, password) VALUES (?, ?, ?)');
    $sql->execute(array($name, $email, $passwordHash));
} catch (PDOException $e) {
    //throw $th;
    $res->success = false;
    $res->message = "Error:".$e;
    echo json_encode($res);
    return;
}

$res->success = true;
$res->message = "Secret Token".$passwordHash;
echo json_encode($res);