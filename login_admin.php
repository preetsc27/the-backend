<?php

//connecting the database and filtering data function....
require 'db.php';

//getting the data from app in json format...
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$email = secure($_POST["email"]);
$password = secure($_POST["password"]);

// small validation check
if($email === "" || $password === "" ){
    $res->success = false;
    $res->message = "Enter all the feilds";
    echo json_encode($res);
    return;
}

// hashing the password
$passwordHash = md5($password.$salt);

try {
    $sql = $conn->prepare('SELECT id FROM admin WHERE email=? AND password=?');
    $sql->execute(array($email, $passwordHash));
    if($sql->rowCount() === 1){
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $studentId = $row["id"];
        $token = generateRandomString();
        $res->success = true;
        $res->token = $token;

        $loginSql = $conn->prepare('INSERT INTO login (token, role, user_id) VALUES (?, ?, ?)');
        $loginSql->execute(array($token, 1, $studentId));
        echo json_encode($res);
    }else{
        $res->success = false;
        $res->message = "Your email or password does not macth";
        echo json_encode($res);
    }
    die();
} catch (PDOException $e) {
    //throw $th;
    $res->success = false;
    $res->message = "Error:".$e;
    echo json_encode($res);
    die();
}

