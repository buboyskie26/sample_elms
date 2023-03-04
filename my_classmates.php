
<?php
    require_once('includes/studentHeader.php');
    require_once('includes/classes/MyClassmate.php');
    require_once('includes/classes/MyClassmateItem.php');
    require_once('includes/classes/TeacherCourse.php');

    if(isset($_GET['teacher_course_id'])){
        $teacher_course_id = $_GET['teacher_course_id'];

        $teacherCourse = new TeacherCourse($con, $teacher_course_id, $studentUserLoggedInObj);

        $my_classmate = new MyClassmate($con, $teacherCourse, $studentUserLoggedInObj);

        $create = $my_classmate->create();

        echo "
           <div class='my_classmates'>
                echo $create
            </div>
        
        ";
    }
?>