<?php 

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjecPeriodAssignmentQuizQuestion.php');


    if(isset($_GET['subject_period_assignment_id'])){

        $subject_period_assignment_id = $_GET['subject_period_assignment_id'];

        $quizQuestion = new SubjecPeriodAssignmentQuizQuestion($con, $teacherUserLoggedInObj);

        $create = $quizQuestion->create($subject_period_assignment_id);

        echo "
            <div style='display: flex; flex: 1;' 
                class='assignment_quiz_question_container'>
                $create
            </div>
        ";
    }

?>

<script src="assets/js/common.js"></script>
