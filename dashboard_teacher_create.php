<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/DashboardTeacher.php');
    require_once('includes/classes/Teacher.php');

    // echo "for teacher section ";
    // echo $_SESSION['teacherUserLoggedIn'];

    $dashboard = new DashboardTeacher($con, $teacherUserLoggedInObj);

    $teacher = new Teacher($con, $teacherLoggedIn);
    $id = $teacher->getId();
    
    if(isset($_POST['submit_teacher_course'])){
        $wasSuccessful = $dashboard->insertTeacherCourse(
            $_POST['course_id'],
            $teacher->getId(),
            $_POST['subject_id'],
            $_POST['school_year'],
            $_POST['school_year_id']);
    
        // echo $_POST['school_year_id'];
    }

?>


<div class="column">
    <?php
        echo $dashboard->createForm();
    ?>
</div>