<?php

//connecting the database and filtering data function....
require 'db.php';

//getting the data from app in json format...
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$token = secure($_SERVER["token"]);
$password = secure($_POST["password"]);

// small validation check
if(empty(token)){
    $res->success = false;
    $res->message = "Enter all the feilds";
    echo json_encode($res);
    return;
}

try {
    $sql = $conn->prepare('DELETE FROM login WHERE token=?');
    $sql->execute(array($token));
    if($sql->rowCount() === 1){
        $res->success = true;
        $res->token = "Succesfully Logged Out";
        echo json_encode($res);
    }else{
        $res->success = false;
        $res->message = "Not able to delete";
        echo json_encode($res);
    }
} catch (PDOException $e) {
    //throw $th;
    $res->success = false;
    $res->message = "Error:".$e;
    echo json_encode($res);
    return;
}

