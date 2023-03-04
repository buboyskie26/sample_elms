


<?php

    require_once('includes/config.php');
    
    $mytime = 10;

    // if(isset($_GET['time'])){
    //     $a = $_GET['time'];
    //     echo $a;
    // }else{
    //     echo "not";
    // }

    if(!isset($_SESSION['time'])){
        $_SESSION['time'] = time();
        // echo $_SESSION['time'];
    }else{

        if(isset($_SESSION['timeStart'])){
            $timeStart = $_SESSION['timeStart'];
        }
        // echo $timeStart;

        $diff = time() - $_SESSION['time'];

        // echo $_SESSION['time'];
        // echo "<br>";
        // echo time();

        $diff = $timeStart - $diff;
        // echo $diff;

        $hours = floor($diff/60);
        $minutes = (int)($diff/60);
        $seconds = $diff%60;
        
        $show = $hours . ":" . $minutes . ":" . $seconds;

        // echo $seconds;

        if($diff == 0 || $diff <= 0){
            echo "Time out";
            // unset($_SESSION['timeStart']);
            // $subject_period_quiz_class_id = $_SESSION['subject_period_quiz_class_id'];
            // header("Location: student_quiz_view.php?subject_period_quiz_class_id=$subject_period_quiz_class_id&tc_id=6");
            exit();
        }else{
            echo $show;
        }

    }
?>


