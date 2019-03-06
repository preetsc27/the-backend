<?php

//connecting the database and filtering data function....
require 'db.php';

//getting the data from app in json format...
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$name = secure($_POST["name"]);
$email = secure($_POST["email"]);
$password = secure($_POST["password"]);
$rating = secure($_POST["rating"]);
$price = secure($_POST["price"]);
$startTime = secure($_POST["startTime"]);
$endTime = secure($_POST["endTime"]);

// small validation check
if($name === "" || $email === "" || $password === "" 
    || $rating === "" || $price === ""
    || $startTime === "" || $endTime === ""){
    $res->success = false;
    $res->message = "Enter all the feilds";
    echo json_encode($res);
    return;
}

// hashing the password
$passwordHash = md5($password.$salt);

try {
    $sql = $conn->prepare('INSERT INTO expert (name, email, password, rating, price, startTime, endTime) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $sql->execute(array($name, $email, $passwordHash, $rating, $price, $startTime, $endTime));
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