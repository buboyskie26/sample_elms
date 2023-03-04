<?php 

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjectPeriodQuizQuestion.php');
    require_once('includes/classes/SubjectPeriodQuiz.php');


    if(isset($_GET['subject_period_quiz_id'])){

        $subject_period_quiz_id = $_GET['subject_period_quiz_id'];

        $quizQuestion = new SubjectPeriodQuizQuestion($con, $subject_period_quiz_id, $teacherUserLoggedInObj);

        $create = $quizQuestion->create($subject_period_quiz_id);

        echo "
            <div class='quiz_question_container'>
                $create
            </div>
        ";
    }

?>

<script src="assets/js/common.js"></script>