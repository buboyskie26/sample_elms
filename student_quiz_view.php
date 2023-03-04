

<?php

    
    require_once('includes/studentHeader.php');
    require_once('includes/classes/SubjectPeriodQuiz.php');
    require_once('includes/classes/SubjectPeriodQuizClass.php');
 
    
    // if(isset($_GET['subject_period_quiz_id'])
    //     && isset($_GET['tc_id'])){

    //     $teacher_course_id = $_GET['tc_id'];
    //     $subject_period_quiz_id = $_GET['subject_period_quiz_id'];

    //     $ass_quiz = new SubjectPeriodQuiz($con, $subject_period_quiz_id, $studentUserLoggedInObj);

    //     $showLayout = $ass_quiz->showQuizLayout($teacher_course_id);

    //     echo "
    //         <div class='assignmentStudentSection'>
    //             $showLayout 
    //         </div>
    //     ";
    // }

     if(isset($_GET['subject_period_quiz_class_id'])
        && isset($_GET['tc_id'])){

        $teacher_course_id = $_GET['tc_id'];
        $subject_period_quiz_class_id = $_GET['subject_period_quiz_class_id'];

        $subjectPeriodQuizClass = new SubjectPeriodQuizClass($con, $subject_period_quiz_class_id, $studentUserLoggedInObj);

        $subject_period_quiz_id = $subjectPeriodQuizClass->GetSubjectPeriodQuizId();

        $ass_quiz = new SubjectPeriodQuiz($con, $subject_period_quiz_id, $studentUserLoggedInObj);

        $showLayout = $ass_quiz->showQuizLayout($teacher_course_id);

        echo "
            <div class='assignmentStudentSection'>
                $showLayout 
            </div>
        ";
    }
?>
