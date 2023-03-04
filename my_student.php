<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/Course.php');
    require_once('includes/classes/TeacherCourse.php');
    require_once('includes/classes/MyStudent.php');

    if(!isset($_GET['teacher_course_id'])){
        echo "ERROR MAP";
        exit();
    }

    $teacher_course_id = $_GET['teacher_course_id'];

    $course = new Course($con, $teacherUserLoggedInObj);
    $teacher_course = new TeacherCourse($con, $teacher_course_id,
        $teacherUserLoggedInObj);

    // print_r($teacherUserLoggedInObj);

    $my_student = new MyStudent($con, $teacherUserLoggedInObj, $teacher_course);

?>

<script src="assets/js/my_student.js"></script>

<div class="my_section">
    <?php 
        echo $my_student->create();
    ?>
</div>