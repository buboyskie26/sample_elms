<?php

    require_once('includes/studentHeader.php');


    $query = $con->prepare("SELECT * FROM table1");

    $query->execute();

    while($row = $query->fetch(PDO::FETCH_ASSOC)){

        $duration = $row['duration'];

        $_SESSION['duration'] = $duration;
        $_SESSION['start_time'] = date("Y-m-d H:i:s");

        $d = strtotime('+' .$_SESSION['duration'] . 'seconds');

        $end_time = date('Y-m-d H:i:s', strtotime('+' .$_SESSION['duration'] 
            . 'seconds', strtotime($_SESSION['start_time'])));

        $start_time  = $_SESSION['start_time'];

        // echo $start_time;
        // echo "<br>";
        // echo $end_time;
        $_SESSION['end_time'] = $end_time;

        echo $end_time;


        // $hours = floor($end_time/60);
        // $minutes = (int)($end_time/60);
        // $seconds = $end_time%60;
        
        // $show = $hours . ":" . $minutes . ":" . $seconds;

        // echo $show;
    }

?>

<!-- <script type="text/javascript">
    window.location = "durationIndex.php";
</script> -->