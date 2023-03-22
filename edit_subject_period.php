<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjectPeriod.php');
    require_once('includes/classes/TeacherCourse.php');


    if(isset($_GET['subject_period_id'])){
        $subject_period_id = $_GET['subject_period_id'];

        $teacher_course_id = 6;

        $subjectPeriod = new SubjectPeriod($con, null, 
            $teacherUserLoggedInObj, "teacher");

        $editForm = $subjectPeriod->editSubjectPeriodForm($subject_period_id);

        // echo $subject_period_id;
        if(isset($_POST['edit_subject_period_term'])){
            $wasSuccess = $subjectPeriod->editSubjectPeriod(
                $_POST['title'],
                $_POST['description'],
                $_POST['term'],
                $_POST['teacher_course_id'],
                $subject_period_id,
                $_POST['assignment_upload_value'],
                
            );

            // if($wasSuccess == true){
            //     header("Location: assignment.php?teacher_course_id=");
            // }

        }
        echo "
            <div class='column'>
                $editForm
            </div>
        ";
    }

?>

<script>
    $(document).ready(function () {
        $('.summernote').summernote({
            height:250
        });
    });
</script>