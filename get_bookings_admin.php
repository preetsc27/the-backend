<?php

//connecting the database and filtering data function....
require 'db.php';

//getting the data from app in json format...
// $rest_json = file_get_contents("php://input");
// $_POST = json_decode($rest_json, true);

// // $name = secure($_POST["name"]);
// $startTime = secure($_POST["startTime"]);
// $endTime = secure($_POST["endTime"]);

// // // small validation check
// if($startTime === "" || $endTime === ""){
//     $res->success = false;
//     $res->message = "Enter all the feilds";
//     echo json_encode($res);
//     return;
// }


try {
    // WHERE startTime<=? AND endTime>=? ORDER BY rating
    $sql = $conn->prepare('SELECT b.id, e.rating, e.name AS expertName, s.name AS studentName, b.booking_date, b.start_time, 
        b.end_time FROM expert AS e INNER JOIN booking AS b ON b.expert_id=e.id INNER JOIN student AS s ON b.student_id=s.id
        ORDER BY b.booking_date DESC');
    $sql->execute();
    if($sql->rowCount() > 0){
        $pageNumbers = 1;
        $data->pageNumbers = $pageNumbers;
        $bookings = array();
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $bookingId = $row["id"];
            $expertName = $row["expertName"];
            $studentName = $row["studentName"];
            $expertRating = $row["rating"];
            $date = $row["booking_date"];
            $startTime = $row["start_time"];
            $endTime = $row["end_time"];
            $booking = array(
                "bookingId"=>$bookingId,
                "expertName"=>$expertName,
                "studentName"=>$studentName,
                "expertRating"=>$expertRating,
                "date"=>$date,
                "startTime"=>$startTime,
                "endTime"=>$endTime,
            );
            array_push($bookings, $booking);
        }
        $data->data = $bookings;

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

$res->success = false;
$res->message = "Error:";
echo json_encode($res);