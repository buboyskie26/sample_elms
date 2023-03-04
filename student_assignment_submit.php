<?php

    require_once('includes/studentHeader.php');
    require_once('includes/classes/SubjectPeriodAssignment.php');
    require_once('includes/classes/StudentPeriodAssignment.php');
    require_once('includes/classes/SubjectPeriodAssignmentQuizQuestion.php');
    require_once('includes/classes/SubjectPeriodAssignmentQuizClass.php');

    if(isset($_GET['subject_period_assignment_id']) && isset($_GET['tc_id'])){

        $teacher_course_id = $_GET['tc_id'];
        
        $subject_period_assignment_id = $_GET['subject_period_assignment_id'];

        $subjectPeriodAss = new SubjectPeriodAssignment($con,
            $subject_period_assignment_id, $studentUserLoggedInObj);

        $student_period_ass = new StudentPeriodAssignment($con,
            $subjectPeriodAss, $studentUserLoggedInObj);

        $name = "student_assignment_submit_$subject_period_assignment_id";

        $hasReachedMaxSubmission = $student_period_ass->HasReachedMaxSubmission();
        // $hasReachedMaxSubmission = $subjectPeriodAss->HasReachedMaxSubmission();

        $create = $student_period_ass->create($hasReachedMaxSubmission, $teacher_course_id);

        echo "
            <div class='column-outer'>
               $create
            </div>
        ";

        if(isset($_POST[$name])){
            // Check if student reached the submission
            if($hasReachedMaxSubmission){
                echo "You have reached the max submission count";
                exit();
            }
            $imageArray = $_FILES['assignment_file']['name'];

            // Check if student will pass the assignment beyond the deadline
            $wasSuccess = $student_period_ass->insertStudentAssignment(
                $imageArray,
                $_POST['file_name'],
                $_POST['description'], 
                $_POST['student_id'],
                $_POST['subject_period_assignment_id']);
            
            if($wasSuccess){
                // header("Location: student_assignment_submit.php?subject_period_assignment_id=$subject_period_assignment_id");
                // header("Location: " . $_SERVER["HTTP_REFERER"]);
                header("Location: student_assignment_view.php?subject_period_assignment_id=$subject_period_assignment_id&tc_id=$teacher_course_id");
            }
        }
    }

    if(isset($_GET['subject_period_assignment_quiz_class_id']) 
        && $_SESSION['teacher_course_id']){

        $teacher_course_id = $_SESSION['teacher_course_id'];
        $subject_period_assignment_quiz_class_id = $_GET['subject_period_assignment_quiz_class_id'];

        $subPeriodAssQuizClass = new SubjectPeriodAssignmentQuizClass($con, 
            $subject_period_assignment_quiz_class_id, $studentUserLoggedInObj);

        $subjectPeriodAssQuizQuestion = new SubjectPeriodAssignmentQuizQuestion($con,
            $subPeriodAssQuizClass, $studentUserLoggedInObj);

        $createForm = $subjectPeriodAssQuizQuestion->createQuizForm($subject_period_assignment_quiz_class_id,
            $teacher_course_id);

        $student_id = $studentUserLoggedInObj->GetId();
            
        echo "
        
            <div class='quiz_section'>
                $createForm
            </div>
        ";

        if(isset($_POST['submit_ass_quiz_question'])
            && isset($_SESSION['token_quiz'])){
            

            $loop = $_POST['x'];
            // echo $loop;
            $score = 0;
            $finalScore = 0;

            // echo $loop;
            for ($i=1; $i <= $loop ; $i++) { 
                
                $eachQuestionId = $_POST['x-'.$i];
                // echo $eachQuestionId;
                // echo "<br>";
                $questionValue = $_POST['q-'.$eachQuestionId];
                echo $questionValue;
 
                $query = $con->prepare("SELECT * FROM subject_period_assignment_quiz_question
                    WHERE subject_period_assignment_quiz_question_id=:subject_period_assignment_quiz_question_id");
                
                $query->bindValue(":subject_period_assignment_quiz_question_id", $eachQuestionId);
                $query->execute();

                $query = $query->fetch(PDO::FETCH_ASSOC);

                if($query['question_answer'] == $questionValue){

                    $points = $query['points'];

                    echo "Number $i is Correct";
                    echo "You gain $points points";
                    echo "<br>";

                    $finalScore += (int)$points;  

                }else{
                    echo "Number $i is Wrong";
                    echo "<br>";
                }
            }
            echo "Final score is ".$finalScore;

            $time_finish = date("Y-m-d H:i:s");
            $updateStudentQuiz = $con->prepare("UPDATE student_period_assignment_quiz
                SET total_score=:total_score, time_finish=:time_finish
                WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
                AND student_id=:student_id
                AND time_finish IS NULL");

            $updateStudentQuiz->bindValue(":total_score", $finalScore);
            $updateStudentQuiz->bindValue(":time_finish", $time_finish);
            // $updateStudentQuiz->bindValue(":set_final", "yes");
            $updateStudentQuiz->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
            $updateStudentQuiz->bindValue(":student_id", $student_id);

            $subject_period_assignment_id = $subPeriodAssQuizClass->GetSubjectPeriodAssignmentId();

            if($updateStudentQuiz->execute()){
                unset($_SESSION['token_quiz']);
                header("Location: student_assignment_view.php?subject_period_assignment_id=$subject_period_assignment_id&tc_id=$teacher_course_id");
            }

            
        }

        ?>
        <script>
            $(document).ready(function(){

                setMyInterval();

                function setMyInterval(){
                    var timer = 1;
                    setInterval(function(){
                        
                        var timerv2 = $("#timerv2").text();

                        $("#timerv2").load("timer.ajaxv2.php");	

                        timerv2 = parseInt(timerv2);

                        console.log(timerv2);

                        if(timerv2 === 0){

                            $(".questions-table input[type='radio']").hide();
                            $("#question_nav").hide();
                            // $("#submit-test").show();
                            $("#msg").text("Time's up!!!\nPlease Submit your Answers\n It will Automatically submit your answer in a seconds\n If it not works. Just submit your answer");
                            $(".questions").each(function() {
                                $(this).hide();
                                return;
                            });

                            $("#exam_timerv2").hide();

                            setTimeout(function(){ 
                
                                document.getElementById("submitQuizAnswerv2").click(); 

                            }, 3000);

                        } else {
                            // $(".questions-table input").show();
                        }
                    }, 990);	
                }
                
                // 
                $("#exam_timerv2").TimeCircles({ 
                    time:{
                        Days:{
                            show: false
                        },
                        Hours:{
                            show: false
                        }
                    }
                });
                setInterval(function(){
                    var remaining_second = $("#exam_timerv2").TimeCircles().getTime();
                }, 1000);
                
            });
        </script>
        <?php
    }else{
        echo "Theres a problem on the GET and SESSION";
    }

    

?>
 




<script>
    $(document).ready(function () {
        
        $('.summernote').summernote({
            height:250
        });
    });

    $('#myForm').on('submit', function(e) {
        if($('.summernote').summernote('isEmpty')) {
            alert('Text Description is empty!');
            // cancel submit
            e.preventDefault();
        }
        else {
            // do action
        }
})
</script>
