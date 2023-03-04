<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/MyStudentAssignmentView.php');
    require_once('includes/classes/SubjectPeriodAssignment.php');
    require_once('includes/classes/Student.php');
    require_once('includes/classes/TeacherCourse.php');

    if(isset($_GET['subject_period_assignment_id']) 
        && isset($_GET['student_period_assignment_id'])
        && isset($_GET['tc_id'])){
       
        $teacher_course_id = $_GET['tc_id'];
        $student_period_assignment_id = $_GET['student_period_assignment_id'];
        $subject_period_assignment_id = $_GET['subject_period_assignment_id'];

        $subject_period_assignment = new SubjectPeriodAssignment($con, $subject_period_assignment_id, $teacherUserLoggedInObj);
        
        $my_student_assignment_view = new MyStudentAssignmentView($con,
            $teacherUserLoggedInObj, $subject_period_assignment,
            $student_period_assignment_id);

        $create = $my_student_assignment_view->create($teacher_course_id);

        echo "
            <div class='my_section'>
                $create
            </div>
        ";

    }else{
        echo "Url does`nt exists";
        exit();
    }
    ?>
        <script>
            $(document).ready(function () {
                $('.summernote').summernote({
                    height:350
                });

                $("#summernoteDisabled").summernote("disable");
            });
        </script>
    <?php
?>
 

