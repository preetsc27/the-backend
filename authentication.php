<?php
$token = $_SERVER['HTTP_TOKEN'];
if($conn === null){
    $resp->success = false;
    $resp->message =  "Include DB File";
    echo json_encode($resp);
    die();
}
if(!isset($token) || empty($token)){
    $resp->success = false;
    $resp->message =  "Token is missing.";
    echo json_encode($resp);
    die();
}

function authenticate($role){
    try {
        global $conn;
        global $token;
        $sqlAuth = $conn->prepare('SELECT user_id FROM login WHERE token=? AND role=?');
        $sqlAuth->execute(array($token, $role));
        if($sqlAuth->rowCount() === 0){
            $res->success = false;
            $res->message = "Not able to find you";
            $res->loggedIn = false;
            echo json_encode($res);
            die();
        }
        $row = $sqlAuth->fetch(PDO::FETCH_ASSOC);
        $id = $row["user_id"];
        return $id;
    } catch (PDOException $e) {
        $res->success = false;
        $res->message = "Error:".$e;
        echo json_encode($res);
        die();
    }
}

function authenticateAdmin(){
    return authenticate(1);
}

function authenticateStudent(){
    return authenticate(2);
}