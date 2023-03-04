<?php 
 
    require_once('includes/teacherHeader.php');
    require_once('includes/classes/AddAssignmentQuiz.php'); 
    require_once('includes/classes/SubjectPeriod.php'); 


    if(isset($_GET['subject_period_id'])){

        $subject_period_id = $_GET['subject_period_id'];

        $subjectPeriod = new SubjectPeriod($con, $subject_period_id, $teacherUserLoggedInObj, "teacher");
        $assignment = new AddAssignmentQuiz($con, $subjectPeriod, $teacherUserLoggedInObj);

        $createQuiz = $assignment->createFormSetQuizToClass();

        echo "
            <div class='column'>
                $createQuiz
            </div>
        ";

        if(isset($_POST['set_subject_period_assignment_quiz_class'])){
            $wasSuccess = $assignment->settQuizToClass(
                $_POST['quiz_time'],
                $_POST['subject_period_assignment_id'],
                $_POST['subject_period_id'],
                $_POST['max_submission'],
                $_POST['show_correct_answer'],
                
            );
            
            if($wasSuccess == true){
                header("Location: subject_period_assignment_quiz.php?subject_period_id=$subject_period_id");
            }
        }
    }
?>