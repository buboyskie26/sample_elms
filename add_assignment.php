<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/AddAssignment.php');
    require_once('includes/classes/SubjectPeriod.php');
    require_once('includes/classes/TeacherCourse.php');

    if(isset($_GET['subject_id'])){
        $subject_id = $_GET['subject_id'];
    }

    if(isset($_GET['subject_period_id'])){
        $subject_period_id = $_GET['subject_period_id'];
    }

    if(isset($_GET['teacher_course_id'])){
        $teacher_course_id = $_GET['teacher_course_id'];
    }

    $subjectPeriod = new SubjectPeriod($con, $subject_period_id, $teacherUserLoggedInObj, "teacher");
    $teacherCourse = new TeacherCourse($con, $teacher_course_id, $teacherUserLoggedInObj);

    $assignment = new AddAssignment($con, $subjectPeriod, $teacherUserLoggedInObj, $teacherCourse);

    $createForm = $assignment->createForm($subject_id, $subject_period_id);
    
    echo "
        <div class='column'>
            $createForm
        </div>
    ";
    if(isset($_POST['submit_add_assignment'])){

        $wasSuccessful = $assignment->AddAssignment(
            $_FILES['assignment_upload'],
            $_POST['type_name'],
            $_POST['subject_period_id'],
            $_POST['subject_id'],
            $_POST['max_submission'],
            $_POST['due_date'],
            $_POST['description'],
            $_POST['max_score'],
        );
        header("Location: assignment.php?teacher_course_id=$teacher_course_id");
    }
?>

<div class="column">
    <?php 
    ?>
</div>

<script>

    $(document).ready(function () {
            $('.summernote').summernote({
                height:250
            });
    });
</script>

