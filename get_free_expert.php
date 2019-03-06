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

// // small validation check
if($startTime === "" || $endTime === ""){
    $res->success = false;
    $res->message = "Enter all the feilds";
    echo json_encode($res);
    return;
}


try {
    $sql = $conn->prepare('SELECT e.id, e.rating, e.name FROM expert AS e WHERE 
        startTime<=? AND endTime>=? AND id NOT IN (SELECT expert_id FROM booking WHERE 
        booking_date=? AND (start_time=? OR end_time=? OR (?  BETWEEN start_time AND end_time) 
        OR (?  BETWEEN start_time AND end_time))) ORDER BY rating'); 
    $sql->execute(array($date, $startTime, $endTime, $startTime, $endTime, $startTime, $endTime));
    if($sql->rowCount() > 0){
        $data = array();
        $ratings = array();
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $id = $row["id"];
            $rating = $row["rating"];
            $name = $row["name"];
            if(!in_array($rating, $ratings)){
                $category = array("id"=>$id, "rating"=>$rating, "name"=>$name);
                array_push($data, $category);
                array_push($ratings, $rating);
            }
        }
        $res->success = true;
        $res->data = $data;
        echo json_encode($res);
        return;
    }else{
        $res->success = false;
        $res->message = "No data found";
        echo json_encode($res);
    }
} catch (PDOException $e) {
    //throw $th;
    $res->success = false;
    $res->message = "Error:".$e;
    echo json_encode($res);
}
