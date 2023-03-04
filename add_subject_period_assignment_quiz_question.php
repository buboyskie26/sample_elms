<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjecPeriodAssignmentQuizQuestion.php'); 

    if(isset($_GET['subject_period_assignment_id'])){

        $subject_period_assignment_id = $_GET['subject_period_assignment_id'];

        // $subjectPeriosQuiz = new SubjectPeriodQuiz($con, $add_subject_period_assignment_quiz_question, $teacherUserLoggedInObj);

        $subQuizQuestion = new SubjecPeriodAssignmentQuizQuestion($con, $teacherUserLoggedInObj);

        $createForm = $subQuizQuestion->createForm($subject_period_assignment_id);
        echo "
            <div class='subject_period_quiz_question_form'>
                $createForm
            </div>
        ";

        if(isset($_POST['create_subject_period_assignment_quiz_question'])){
            $wasSuccessful = $subQuizQuestion->insert(
                $_POST['question_text'],
                $_POST['question_type_id'],
                $_POST['question_answer'],
                $_POST['points'],
                $_POST['subject_period_assignment_id'],
                $_POST['answer1'],
                $_POST['answer2'],
                $_POST['answer3'],
                $_POST['answer4']
            );
            
            header("Location: subject_period_assignment_quiz_question.php?subject_period_assignment_id=$subject_period_assignment_id");
        }
    }

?>


<script src="assets/js/common.js"></script>


<script>

    $(document).ready(function () {
            $('.summernote').summernote({
                height:250
            });
    });
</script>