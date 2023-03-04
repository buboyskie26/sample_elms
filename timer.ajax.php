
<?php

    require_once('includes/config.php');

    // $subject_period_quiz_class_id = 6;
    $student_id = $_SESSION['student_id'];


    if(isset($_SESSION['subject_period_quiz_class_id'])){
        $subject_period_quiz_class_id = $_SESSION['subject_period_quiz_class_id'];
        // echo $subject_period_quiz_class_id;
    }

    if(isset($_SESSION['student_id'])){
        $student_id = $_SESSION['student_id'];
        // echo $student_id;
    }
    

    $query = $con->prepare("SELECT * FROM student_period_quiz
        WHERE student_id=:student_id
        AND subject_period_quiz_class_id=:subject_period_quiz_class_id");

    $query->bindValue(":student_id", $student_id);
    $query->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
    $query->execute();

    if($query->rowCount() > 0){

        $query = $query->fetch(PDO::FETCH_ASSOC);

        $quiz_time = $query['student_quiz_time'];

        $queryv2 = $con->prepare("SELECT * FROM subject_period_quiz_class
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id");
        
        $queryv2->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        $queryv2->execute();

        if($queryv2->rowCount() > 0){

            $queryv2 = $queryv2->fetch(PDO::FETCH_ASSOC);

            if($quiz_time <= $queryv2['quiz_time'] && $quiz_time > 0){

                $quiz_time_set = $query['student_quiz_time'] - 1;

                $queryUpdate = $con->prepare("UPDATE student_period_quiz
                    SET student_quiz_time=:student_quiz_time
                    WHERE student_id=:student_id
                    AND subject_period_quiz_class_id=:subject_period_quiz_class_id ");
                
                // $quiz_time_set = $quiz_time_set - 1;
                // echo $quiz_time_set;
                $queryUpdate->bindValue(":student_quiz_time", $quiz_time_set);
                $queryUpdate->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
                $queryUpdate->bindValue(":student_id", $student_id);
                $queryUpdate->execute();


                    $init = $quiz_time;
                    $minutes = floor(($init / 60) % 60);
                    $seconds = $init % 60;

                    // echo $init;

                    if($init > 59){		
                        echo "$minutes minutes and $seconds seconds";
                    } else if($init <= 59 && $init > 0){
                        echo "$seconds seconds";
                    }
                    // else if($init <= 0){
                    //     echo 0;
                    // }
            }
            else{
                // echo "NOT3";
                // 0 is the time out number.
                echo 0;
            }
        }else{
            echo "NOT2";
        }

    }else{
        echo "NOT";
    }

?>