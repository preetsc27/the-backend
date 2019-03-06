<?php

//connecting the database and filtering data function....
require 'db.php';
require 'authentication.php';

$studentId=authenticateStudent();

//getting the data from app in json format...
$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

$date = secure($_POST["date"]);
$startTime = secure($_POST["startTime"]);
$endTime = secure($_POST["endTime"]);
$rating = secure($_POST["selectedRating"]);


// // small validation check
if(empty($startTime) || empty($endTime) || empty($date) || empty($rating)){
    $res->success = false;
    $res->message = "Enter all the feilds";
    echo json_encode($res);
    die();
}


try {
    $sql = $conn->prepare('SELECT id, price FROM expert WHERE startTime<=? AND 
        endTime>=? AND rating=? ORDER BY price');
    $sql->execute(array($startTime, $endTime, $rating));
    if($sql->rowCount() > 0){
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        $expertId = $row["id"];
        $price = $row["price"];
        
        $bookingSql = $conn->prepare('INSERT INTO booking (student_id, expert_id, 
            booking_date, start_time, end_time) VALUES (?, ?, ?, ?, ?)');
        $bookingSql->execute(array($studentId, $expertId, $date, 
            $startTime, $endTime));
        

        $res->success = true;
        $res->message = "Successfully booked";
        echo json_encode($res);
        die();
    }else{
        $res->success = false;
        $res->message = "No data found";
        echo json_encode($res);
        die();
    }
} catch (PDOException $e) {
    //throw $th;
    $res->success = false;
    $res->message = "Error:".$e;
    echo json_encode($res);
    die();
}
