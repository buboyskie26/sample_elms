<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/AddAssignmentQuiz.php');
    require_once('includes/classes/SubjectPeriod.php');


    if(isset($_GET['subject_period_id'])){

        $subject_period_id = $_GET['subject_period_id'];

        $subjectPeriod = new SubjectPeriod($con, $subject_period_id, $teacherUserLoggedInObj, "teacher");

        $assignment = new AddAssignmentQuiz($con, $subjectPeriod, $teacherUserLoggedInObj);

        $createQuiz = $assignment->createFormQuiz();

        echo "
            <div class='column'>
                $createQuiz
            </div>
        ";

        if(isset($_POST['submit_add_assignmen_quiz'])){
            $wasSuccess = $assignment->AddQuizAssignment(
                $_POST['type_name'],
                $_POST['description'],
                $_POST['due_date'],
                $_POST['subject_period_id'],
                $_POST['subject_id'],
            );
            
            if($wasSuccess){
                header("Location: subject_period_assignment_quiz.php?subject_period_id=$subject_period_id");
            }
        }
    }

?>