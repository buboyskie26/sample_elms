<?php

    // require_once('includes/studentHeader.php');

    require_once('includes/config.php');


    $timeNow = date('Y-m-d H:i:s');
    $toTime1 = $_SESSION['end_time'];

    echo $toTime1;

    $timeFirst = strtotime($timeNow);
    $timeSecond = strtotime($toTime1);

    $diff = $timeSecond - $timeFirst;

    $timeRemaining =  gmdate("H:i:s", $diff);

    // if($timeRemaining == "00:00:00"){

    //     echo "Time Out";
        
    //     echo "Time is Out";
    //     return;
    // }

    // echo $timeRemaining;
?>