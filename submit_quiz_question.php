<?php 


    require_once('includes/studentHeader.php');
    require_once('includes/classes/SubjectPeriodQuizQuestion.php');
    require_once('includes/classes/SubjectPeriodQuiz.php');
    
    // if(isset($_GET['subject_period_quiz_id'])){

    //     $subject_period_quiz_id = $_GET['subject_period_quiz_id'];

    //     $i = 1;
    //     $question_answer = $_POST['question_answer_1'];
    //     echo $question_answer;

    //     if(isset($_POST['submit_quiz_question'])){

    //         $array = [];

    //         $query = $con->prepare("SELECT * FROM subject_period_quiz_question
    //             WHERE subject_period_quiz_id=:subject_period_quiz_id");

    //         $query->bindValue(":subject_period_quiz_id", $subject_period_quiz_id);
    //         $query->execute();

    //         while($row = $query->fetch(PDO::FETCH_ASSOC)){
    //             array_push($array, $row);
    //         }

    //         foreach($array as $index => $value){
    //             $subject_period_quiz_question_id = $value['subject_period_quiz_question_id'];

    //             $question_answer = "question_answer_$subject_period_quiz_question_id";


    //         }
    //     }
    // }
?>