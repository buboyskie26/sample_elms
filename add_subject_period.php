<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjectPeriod.php');
    require_once('includes/classes/TeacherCourse.php');
    
    if(isset($_SESSION['teacher_course_id'])){
        $teacher_course_id = $_SESSION['teacher_course_id'];



        $subjectPeriod = new SubjectPeriod($con, null, 
            $teacherUserLoggedInObj, "teacher");

        $createForm = $subjectPeriod->createForm($teacher_course_id);

        if(isset($_POST['add_subject_period_term'])){

           $wasSuccess = $subjectPeriod->AddSubjectPeriod(
                $_POST['title'],
                $_POST['description'],
                $_POST['term'],
                $_POST['subject_id'],
                $teacher_course_id,
                // $_FILES['subject_period_upload']
           );
           
           if($wasSuccess == true){
                header("Location: assignment.php?teacher_course_id=$teacher_course_id");
           }
        }

        echo "
            <div class='column'>
                $createForm
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
