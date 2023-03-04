<?php

    
    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjectPeriodQuiz.php'); 
    require_once('includes/classes/SubjectPeriod.php'); 


    if(isset($_GET['teacher_id']) && isset($_GET['subject_period_id'])){


        $subject_period_id = $_GET['subject_period_id'];

        $teacher_id = $_GET['teacher_id'];

        $subjectPeriod = new SubjectPeriod($con, $subject_period_id,
            $teacherUserLoggedInObj, "teacher");

        $subjectPeriodQuizId = $subjectPeriod->GetSubjectPeriodQuizId($teacher_id);

        $periodQuiz = new SubjectPeriodQuiz($con, $subjectPeriodQuizId,  $teacherUserLoggedInObj);

        $createForm = $periodQuiz->createForm($subject_period_id, $teacher_id);
        
        echo "
            <div class='subject_period_quiz_form'>
                $createForm
            </div>

        ";
        
        if(isset($_POST['create_subject_period_quiz'])){
            $wasSuccess = $periodQuiz->insertPeriodQuiz(
                $_POST['quiz_title'],
                $_POST['quiz_description'],
                $_POST['due_date'],
                $_POST['subject_period_id'],
                $_POST['teacher_id']
            );
            if($wasSuccess){

                header("Location: subject_period_quiz.php?teacher_id=$teacher_id&subject_period_id=$subject_period_id'");
            }
        }
    }

   

?>






