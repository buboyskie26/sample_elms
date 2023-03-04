<?php 


    require_once('includes/studentHeader.php');
    require_once('includes/classes/SubjectPeriodQuizQuestion.php');
    require_once('includes/classes/SubjectPeriodQuiz.php');
    require_once('includes/classes/SubjectPeriodQuizClass.php');
    
    // if(isset($_GET['subject_period_quiz_id'])){
    //     $subject_period_quiz_id = $_GET['subject_period_quiz_id'];

    //     $subjectPeriodQuiz = new SubjectPeriodQuiz($con, $subject_period_quiz_id, $studentUserLoggedInObj);

    //     $subjectPeriodQuizQuestion = new SubjectPeriodQuizQuestion($con, $subjectPeriodQuiz, $studentUserLoggedInObj);
    //     $create = $subjectPeriodQuizQuestion->createQuizForm();
    //     echo "
        
    //         <div class='quiz_section'>
    //             $create
    //         </div>
    //     ";

    //     if(isset($_POST['submit_quiz_question'])){
    //         $loop = $_POST['x'];
    //         // echo $loop;
    //         $score = 0;
    //         for ($i=1; $i <= $loop ; $i++) { 
                
    //             $eachQuestionId = $_POST['x-'.$i];
    //             // echo $eachQuestionId;

    //             $questionValue = $_POST['q-'.$eachQuestionId];
    //             echo $questionValue;

    //             // echo $eachQuestionId;
    //             // echo "<br>";
    //             // echo $questionValue;
    //             // echo "<br>";

    //             $query = $con->prepare("SELECT * FROM subject_period_quiz_question
    //                 WHERE subject_period_quiz_question_id=:subject_period_quiz_question_id");
                
    //             $query->bindValue(":subject_period_quiz_question_id", $eachQuestionId);
    //             $query->execute();

    //             $query = $query->fetch(PDO::FETCH_ASSOC);

    //             if($query['question_answer'] == $questionValue){
    //                 echo "Number $i is Correct";
    //                 echo "<br>";
    //                 $score++;
    //             }else{
    //                 echo "Number $i is Wrong";
    //                 echo "<br>";
    //             }
    //         }

    //         echo "Total score is $score";
    //     }
      
        
    // }

    if(isset($_GET['subject_period_quiz_class_id']) &&
        isset($_GET['tc_id'])){

        $subject_period_quiz_class_id = $_GET['subject_period_quiz_class_id'];
        $teacher_course_id = $_GET['tc_id'];

        $subjectPeriodQuizClass = new SubjectPeriodQuizClass($con, $subject_period_quiz_class_id,
            $studentUserLoggedInObj);

        $subject_period_quiz_id = $subjectPeriodQuizClass->GetSubjectPeriodQuizId();

        $subjectPeriodQuiz = new SubjectPeriodQuiz($con, $subject_period_quiz_id, $studentUserLoggedInObj);

        $subjectPeriodQuizQuestion = new SubjectPeriodQuizQuestion($con, $subjectPeriodQuiz, $studentUserLoggedInObj);
        
        $createForm = $subjectPeriodQuizQuestion->createQuizForm($subject_period_quiz_class_id, $teacher_course_id);

        $student_id = $studentUserLoggedInObj->GetId();
        echo "
        
            <div class='quiz_section'>
                $createForm
            </div>
        ";

        $queryQuiz = $con->prepare("SELECT quiz_time FROM subject_period_quiz_class
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
            LIMIT 1");

        $queryQuiz->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        $queryQuiz->execute();

        $mytime = 0;

        if($queryQuiz->rowCount() > 0){
            $mytime = $queryQuiz->fetchColumn();
            // $this->setTimer($time);
        }

        if(isset($_POST['submit_quiz_question'])){

            $loop = $_POST['x'];
            // echo $loop;
            $score = 0;
            // echo $loop;
            for ($i=1; $i <= $loop ; $i++) { 
                
                $eachQuestionId = $_POST['x-'.$i];
                // echo $eachQuestionId;
                // echo "<br>";
                $questionValue = $_POST['q-'.$eachQuestionId];
                echo $questionValue;
 
                $query = $con->prepare("SELECT * FROM subject_period_quiz_question
                    WHERE subject_period_quiz_question_id=:subject_period_quiz_question_id");
                
                $query->bindValue(":subject_period_quiz_question_id", $eachQuestionId);
                $query->execute();

                $query = $query->fetch(PDO::FETCH_ASSOC);

                if($query['question_answer'] == $questionValue){
                    echo "Number $i is Correct";
                    echo "<br>";
                    
                    $score++;
                }else{
                    echo "Number $i is Wrong";
                    echo "<br>";
                }
            }
            echo "Total score is $score";

            $time_finish = date("Y-m-d H:i:s");

            $updateStudentQuiz = $con->prepare("UPDATE student_period_quiz
                SET total_score=:total_score, time_finish=:time_finish
                WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
                AND student_id=:student_id");

            $updateStudentQuiz->bindValue(":total_score", $score);
            $updateStudentQuiz->bindValue(":time_finish", $time_finish);
            // $updateStudentQuiz->bindValue(":set_final", "yes");
            $updateStudentQuiz->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
            $updateStudentQuiz->bindValue(":student_id", $student_id);
            // $updateStudentQuiz->bindValue(":is_final", "yes");

            if($updateStudentQuiz->execute()){
                echo "Total score is $score";
                header("Location: student_quiz_view.php?subject_period_quiz_class_id=$subject_period_quiz_class_id&tc_id=$teacher_course_id");
            }
        }
        
        
    }
?>

<script>
    $(document).ready(function(){	

        // $(".questions").each(function() {
        //     $(this).hide();
        // })

        // $("#q_1").show();
        // $("#prevBtn_1").hide();

        // $(".nextq").click(function() {

        //     var qn = $(this).attr('qn');

        //     var nextQuestion = parseInt(qn) + 1;

        //     var nextPrevQuestion = parseInt(qn) + 1;

        //     $("#q_" + qn).fadeOut();
        //     $("#q_" + nextQuestion).show();

        //     if(qn >= 1){
        //         // $("#q_1").show();
        //         $("#prevBtn_" + nextPrevQuestion).show();
        //     }
        // });

        // $(".selectClick").click(function() {

        //     var se = $(this).attr('se');
        //     console.log(se)
            
        //     $(".questions").each(function() {
        //         $(this).hide();
        //         return;
        //     });

        //     $("#q_" + se).fadeIn();
        // });



        $(".prevq").click(function() {

            var pv = $(this).attr('pv');
            var prevQuestion = parseInt(pv) - 1;

            $("#q_" + prevQuestion).fadeIn();
            $("#q_" + pv).hide();
            // console.log(pv)
        });
    });

</script>

 
<script>
    $(document).ready(function(){

        setMyInterval();

        function setMyInterval(){
            var timer = 1;
        // $(".questions-table input").hide();
            setInterval(function(){
                
                var timer = $("#timer").text();
                $("#timer").load("timer.ajax.php");	

                timer = parseInt(timer);
                
                console.log(timer);

                if(timer === 0){

                    $(".questions-table input[type='radio']").hide();
                    $("#question_nav").hide();
                    // $("#submit-test").show();
                    $("#msg").text("Time's up!!!\nPlease Submit your Answers\n It will Automatically submit your answer in a seconds\n If it not works. Just submit your answer");
                    $(".questions").each(function() {
                        $(this).hide();
                        return;
                    });

                    $("#exam_timer").hide();
                    
                    setTimeout(function(){ 
                        document.getElementById("submitQuizAnswer").click(); 
                    }, 3000);

                    // // Todo if it was refreshed, automatic submit the submit answer
                    // if (window.performance) {
                    //     window.location.href = 'student_quiz_view.php?subject_period_quiz_class_id=6&tc_id=6';
                    // }
                    //     console.info(performance.navigation.type);
                    // if (performance.navigation.type == performance.navigation.TYPE_RELOAD) {
                    //     window.location.href = 'student_quiz_view.php?subject_period_quiz_class_id=6&tc_id=6';

                    // } else {
                    //     console.info( "This page is not reloaded");
                    // }
                    // window.location.href = 'student_quiz_view.php?subject_period_quiz_class_id=6&tc_id=6';
                } else {
                    // $(".questions-table input").show();
                }
            }, 990);	
        }
        
        // 
        
        $("#exam_timer").TimeCircles({ 
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

            var remaining_second = $("#exam_timer").TimeCircles().getTime();

            // if(remaining_second < 1)
            // {
            //     return;
            // }
	    }, 1000);
        //
    });
        
</script>
<!-- 
<script>
    setTimeout(function(){ 

        document.getElementById("myForm").submit(); 

    }, 10000);
    
</script> -->



<!-- <script>

    setInterval(function () {
      var time = $('#showTimer').text();
       
      $('#showTimer').load('timer.php');
      if(time = "0:0:0"){
        // alert('q,we');
        window.href = "student_quiz_view.php?subject_period_quiz_class_id=6&tc_id=6";
      }
    }, 1000);
</script> -->
