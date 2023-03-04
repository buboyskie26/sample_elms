
<?php

    
    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjectPeriodAssignmentHandout.php');
    require_once('includes/classes/Student.php');

    if(isset($_GET['handout_id']) &&
        isset($_SESSION['teacher_course_id'])){
    
        $subject_period_assignment_handout_id = $_GET['handout_id'];
        $teacher_course_id = $_SESSION['teacher_course_id'];

        // echo $subject_period_assignment_handout_id;

        $handout = new SubjectPeriodAssignmentHandout($con, $subject_period_assignment_handout_id,
            $teacherUserLoggedInObj);
        
        $create = $handout->CreateStudentViewedHandout($teacher_course_id, $subject_period_assignment_handout_id);
        echo "
            <div style='width: 100%' class='student_viewed_handout'>
                $create
            </div>
        ";
    } 

?>