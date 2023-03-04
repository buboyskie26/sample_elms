<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/Assignment.php');
    require_once('includes/classes/TeacherCourse.php');
    require_once('includes/classes/Student.php');
    require_once('includes/classes/SubjectPeriod.php');
    require_once('includes/classes/SubjectPeriodAssignment.php');

    if(isset($_SESSION['teacher_course_id'])){
        $teacher_course_id = $_SESSION['teacher_course_id'];

        $teacherCourse = new TeacherCourse($con, $teacher_course_id, $teacherUserLoggedInObj);
        $ass = new Assignment($con, $teacherCourse, $teacherUserLoggedInObj);
            
        $createStudentViewedList = $ass->createStudentsQuizView();

        echo "
            <div class='student_viewed_handout' style='width: 100%'>
                $createStudentViewedList
            </div>
        ";
        
        
    }
?>