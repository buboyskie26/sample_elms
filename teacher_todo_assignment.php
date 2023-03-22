<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/MyStudentAssignment.php');
    require_once('includes/classes/SubjectPeriodAssignment.php');
    require_once('includes/classes/Student.php');


    if(isset($_GET['teacher_course_id'])){

        $teacher_course_id = $_GET['teacher_course_id'];

        // $subject_period_assignment = new SubjectPeriodAssignment($con,
        //     0, $teacherUserLoggedInObj);
        
        // We done need the query of SubjectPeriodAssignment Obj
        $studentAssignment = new MyStudentAssignment($con, $teacherUserLoggedInObj
            ,null);

        $tableList = $studentAssignment->AllStudentPassedAssignmentOnTeacherCourse($teacher_course_id);
        
        echo "
            <div class='my_section'>
                $tableList
            </div>
        
        ";
        
       
    }
?>