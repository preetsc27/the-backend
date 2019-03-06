<?php
require "db.php";
$x = 10;

function grd1(){
    return 20;
}


function grd(){
    return grd1();
}
echo "1";
echo grd();
echo "2";
?>