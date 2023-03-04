<?php

    
    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjectPeriodQuizClass.php'); 
    require_once('includes/classes/SubjectPeriod.php'); 


    if(isset($_GET['teacher_id']) && isset($_GET['subject_period_id'])){

        $subject_period_id = $_GET['subject_period_id'];
        $teacher_id = $_GET['teacher_id'];

        $subjectPeriod = new SubjectPeriod($con, $subject_period_id,
            $teacherUserLoggedInObj, "teacher");
            
        $quizClass = new SubjectPeriodQuizClass($con, null, $teacherUserLoggedInObj);

        $createForm = $quizClass->createForm($subjectPeriod);

        // $subjectPeriodQuiz = new SubjectPeriodQuiz($con, $)

        echo "
            <div class='subject_period_quiz_class'>
                $createForm
            </div>
        ";

        if(isset($_POST['create_subject_period_quiz_class'])){

            $wasSuccess = $quizClass->SetQuizClass(
                $_POST['subject_period_quiz_id'],
                $_POST['subject_period_id'],
                $_POST['quiz_time'],
            );
            if($wasSuccess){
                header("Location: subject_period_quiz.php?teacher_id=$teacher_id&subject_period_id=$subject_period_id");
            }
        }
    }
?>