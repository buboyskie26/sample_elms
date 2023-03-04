<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/MyStudentAssignment.php');
    require_once('includes/classes/SubjectPeriodAssignment.php');
    require_once('includes/classes/Student.php');
    require_once('includes/classes/TeacherCourse.php');


    if(isset($_GET['subject_period_assignment_quiz_id']) 
        && isset($_GET['tc_id'])){

        $teacher_course_id = $_GET['tc_id'];
        $subject_period_assignment_id = $_GET['subject_period_assignment_quiz_id'];
        
        $subject_period_assignment = new SubjectPeriodAssignment($con, $subject_period_assignment_id, $teacherUserLoggedInObj);

        $my_student_assignment = new MyStudentAssignment($con, $teacherUserLoggedInObj,
            $subject_period_assignment);
        
        $showQuizOfStudent = $my_student_assignment->ShowListStudentAnsweredQuiz($teacher_course_id);

        echo "
            <div style='width: 100%' class='student_quiz_list'>
                $showQuizOfStudent
            </div>
        ";
        
    }
    // If the URL doesnt have subject_period_assignment_id it show the code block
    // if(!isset($_GET['subject_period_assignment_id'])){
    //     echo "ERROR MAP";
    //     exit();
    // }
    if(isset($_GET['subject_period_assignment_id']) 
        && isset($_GET['tc_id']) 
        && !isset($_GET['student_id'] )){

        $teacher_course_id = $_GET['tc_id'];
        $subject_period_assignment_id = $_GET['subject_period_assignment_id'];

        $subject_period_assignment = new SubjectPeriodAssignment($con, $subject_period_assignment_id, $teacherUserLoggedInObj);

        $my_student_assignment = new MyStudentAssignment($con, $teacherUserLoggedInObj,
            $subject_period_assignment);

        if(isset($_POST['checked_student_assignment_btn'])){
            $wasSuccess = $my_student_assignment->CheckedStudentAssignment($_POST['grade_checked_number'],
                $_POST['student_period_assignment_id']);
            if($wasSuccess){
                header("Location: my_student_assignment.php?subject_period_assignment_id=$subject_period_assignment_id:");
            }
        } 
        
        $showStudentAnswer = $my_student_assignment->ShowListStudentAnsweredAssignment($teacher_course_id);
        
        echo "
            <div class='my_section'>
                $showStudentAnswer
            </div>
        ";
    }
    
    //
    if(isset($_GET['subject_period_assignment_id']) && isset($_GET['student_id'])){

        $subject_period_assignment_id = $_GET['subject_period_assignment_id'];

        $subject_period_assignment = new SubjectPeriodAssignment($con, $subject_period_assignment_id, $teacherUserLoggedInObj);

        $my_student_assignment = new MyStudentAssignment($con, $teacherUserLoggedInObj,
            $subject_period_assignment);

        $showStudentOtherAnswers = $my_student_assignment->OtherSubmissions($_GET['student_id']);
        echo "
            <div class='my_section'>
                $showStudentOtherAnswers
            </div>
        ";
    }
?>

<script src="assets/js/my_student.js"></script>
 