<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/Assignment.php');
    require_once('includes/classes/TeacherCourse.php');
    require_once('includes/classes/SubjectPeriod.php');
    require_once('includes/classes/SubjectPeriodAssignment.php');
    require_once('includes/classes/SubjectPeriodQuizClass.php');
    require_once('includes/classes/SubjectPeriodQuiz.php');
    require_once('includes/classes/SubjectPeriodAssignmentHandout.php');
    require_once('includes/classes/StudentPeriodAssignment.php');

    // if(isset($_GET['teacher_course_id'])){
    //     $teacher_course_id = $_GET['teacher_course_id'];
    //     $teacherCourse = new TeacherCourse($con, $teacher_course_id, $teacherUserLoggedInObj);
    //     $ass = new Assignment($con, $teacherCourse, $teacherUserLoggedInObj);

    // }
        
    if(isset($_GET['subject_period_id'])){
        $subject_period_id = $_GET['subject_period_id'];

        $subject_period = new SubjectPeriod($con, $subject_period_id,
            $teacherUserLoggedInObj, "teacher");

        echo $subject_period->EditSectionForm($subject_period_id);

    }
    
?>
<script src="assets/js/common.js"></script>
<div class="assignment_section">
    <?php
        if(isset($_GET['teacher_course_id'])){
            $teacher_course_id = $_GET['teacher_course_id'];
            $teacherCourse = new TeacherCourse($con, $teacher_course_id, $teacherUserLoggedInObj);
            $ass = new Assignment($con, $teacherCourse, $teacherUserLoggedInObj);
            
            echo $ass->create();
        }
        
         // Edit Subject Period Assignment
        if(isset($_GET['subject_period_assignment_id']) && isset($_GET['tc_id'])){

            $teacher_course_id = $_GET['tc_id'];
            $subject_period_assignment_id = $_GET['subject_period_assignment_id'];

            $subject_period_assignment = new SubjectPeriodAssignment($con, 
                $subject_period_assignment_id, $teacherUserLoggedInObj);
            
            echo $subject_period_assignment->EditSubjectPeriodAssignmentForm($teacher_course_id);

            if(isset($_POST['submit_edit_assignment'])){

                $wasSuccess = $subject_period_assignment->EditSubjectPeriodAssignment(
                    $subject_period_assignment_id,
                    $_POST['type_name'],
                    $_POST['max_submission'],
                    $_POST['due_date'],
                    $_POST['description'],
                    $_POST['subject_period_id'],
                    $_POST['subject_id'],
                    $_POST['assignment_upload_value']
                );
                header("Location: assignment.php?teacher_course_id=$teacher_course_id");
            }
        }
        
    ?>
</div>

<script src="assets/js/common.js"></script>

<script>
    $(document).ready(function () {
            $('.summernote').summernote({
                height:250
            });
    });
</script>