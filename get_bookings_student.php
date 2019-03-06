<?php

//connecting the database and filtering data function....
require 'db.php';
require 'authentication.php';

$studentId=authenticateStudent();

try {
    // WHERE startTime<=? AND endTime>=? ORDER BY rating
    $sql = $conn->prepare('SELECT b.id, e.rating, e.name AS expertName, s.name AS studentName, b.booking_date, b.start_time, 
        b.end_time FROM expert AS e INNER JOIN booking AS b ON b.expert_id=e.id INNER JOIN student AS s ON b.student_id=s.id
        WHERE student_id=? ORDER BY b.booking_date DESC');
    $sql->execute(array($studentId));
    if($sql->rowCount() > 0){
        $pageNumbers = 1;
        $data->pageNumbers = $pageNumbers;
        $bookings = array();
        while($row = $sql->fetch(PDO::FETCH_ASSOC)){
            $bookingId = $row["id"];
            $expertRating = $row["rating"];
            $expertName = $row["expertName"];
            $studentName = $row["studentName"];
            $date = $row["booking_date"];
            $startTime = $row["start_time"];
            $endTime = $row["end_time"];
            $booking = array(
                "bookingId"=>$bookingId,
                "expertRating"=>$expertRating,
                "expertName"=>$expertName,
                "studentName"=>$studentName,
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
        die();
    }else{
        $res->success = false;
        $res->message = "No data found:".$studentId;
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