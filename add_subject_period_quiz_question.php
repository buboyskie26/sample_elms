<?php

    
    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjectPeriodQuizQuestion.php'); 
    require_once('includes/classes/SubjectPeriodQuiz.php'); 


    if(isset($_GET['subject_period_quiz_id'])){

        $subject_period_quiz_id = $_GET['subject_period_quiz_id'];

        $subjectPeriosQuiz = new SubjectPeriodQuiz($con, $subject_period_quiz_id, $teacherUserLoggedInObj);

        $subQuizQuestion = new SubjectPeriodQuizQuestion($con, $subjectPeriosQuiz, $teacherUserLoggedInObj);

        $createForm = $subQuizQuestion->createForm($subject_period_quiz_id);
        echo "
            <div class='subject_period_quiz_question_form'>
                $createForm
            </div>
        ";

        if(isset($_POST['create_subject_period_quiz_question'])){

            $wasSuccessful = $subQuizQuestion->insert(
                $_POST['question_text'],
                $_POST['question_type_id'],
                $_POST['question_answer'],
                $_POST['subject_period_quiz_id'],
                $_POST['answer1'],
                $_POST['answer2'],
                $_POST['answer3'],
                $_POST['answer4']
            );

            if($wasSuccessful){
                header("Location: subject_period_quiz_question.php?subject_period_quiz_id=$subject_period_quiz_id");
            }
            
            // echo "question_type_id " . $_POST['question_type_id'];
            // echo $_POST['question_type_id'];


            // echo $_POST['answer1'];
            // echo $_POST['question_answer'];
            // echo "<br>";
            // echo $_POST['answer2'];
            // echo $_POST['question_answer'];
            // echo "<br>";
            // echo $_POST['answer3'];
            // echo $_POST['question_answer'];
            // echo "<br>";
            // echo $_POST['answer4'];
            // echo $_POST['question_answer'];
            // echo "<br>";

            // $wasSuccessv2 = $subQuizQuestion->insertMultipleChoice(
            //     $_POST['answer1'],
            //     $_POST['answer2'],
            //     $_POST['answer3'],
            //     $_POST['answer4'], 0
            // );
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