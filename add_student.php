<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/DashboardTeacher.php');
    require_once('includes/classes/Teacher.php');
    require_once('includes/classes/TeacherCourse.php');
    require_once('includes/classes/MyStudent.php');

    if(isset($_GET['teacher_course_id'])){
        $teacher_course_id = $_GET['teacher_course_id'];
    }

    // echo "for teacher section ";
    // echo $_SESSION['teacherUserLoggedIn'];
    $teacher_course = new TeacherCourse($con, $teacher_course_id, $teacherUserLoggedInObj);
    $dashboard = new MyStudent($con, $teacherUserLoggedInObj, $teacher_course);

    $teacher = new Teacher($con, $teacherLoggedIn);
    $teacher_id = $teacher->getId();
    
    if(isset($_POST['submit_teacher_course_student'])){

        $wasSuccessful = $dashboard->AddStudent($teacher_course_id,
            $_POST['student_id'], $teacher_id);

        header("Location: my_student.php?teacher_course_id=$teacher_course_id");
    }
?>


<div class="column">
    <?php
        echo $dashboard->createForm();
    ?>
</div>