<?php 
    require_once('includes/studentHeader.php');
    require_once('includes/classes/DashboardStudent.php');
    require_once('includes/classes/TeacherCourse.php');
    require_once('includes/classes/DashboardStudentItem.php');

    $dash_student = new DashboardStudent($con, $studentUserLoggedInObj);


?>

<div style="flex-direction: column;" class="dashboard_section">
    <?php 
        echo $dash_student->create();
    ?>
</div>