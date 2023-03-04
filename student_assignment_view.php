

<?php

    
    require_once('includes/studentHeader.php');
    require_once('includes/classes/StudentAssignmentView.php');
    require_once('includes/classes/SubjectPeriodAssignment.php');
    require_once('includes/classes/TeacherCourse.php');
    require_once('includes/classes/SubjectPeriodAssignmentQuizClass.php');
    require_once('includes/classes/SubjectPeriodAssignmentHandout.php');
    require_once('includes/classes/SubjectPeriod.php');
    
    if(isset($_GET['subject_period_assignment_id']) 
        && isset($_GET['tc_id'])){

        $teacher_course_id = $_GET['tc_id'];
        $subject_period_assignment_id = $_GET['subject_period_assignment_id'];

        $subject_period_assignment = new SubjectPeriodAssignment($con,
                $subject_period_assignment_id, $studentUserLoggedInObj);

        $ass_student = new StudentAssignmentView($con, 
            $subject_period_assignment, $studentUserLoggedInObj);

        $create = $ass_student->create($teacher_course_id);  
        
        echo "
            <div class='assignmentStudentSection'>
                $create
            </div>
        ";
    }

    if(isset($_GET['handout'])){
        $subject_period_assignment_handout_id = $_GET['handout'];

        $handout = new SubjectPeriodAssignmentHandout($con, $subject_period_assignment_handout_id,
            $studentUserLoggedInObj);
        
        $create = $handout->create();

        echo "
            <div class='handout_section'>
                $create
            </div>
        ";
    }

    if(isset($_GET['handout'])){
        
    }

    

?>
<script src="assets/js/common.js"></script>
 

<script>
    $(document).ready(function () {
        $('.summernote').summernote({
            height:350
        });

        $("#summernoteDisabled").summernote("disable");
    });
</script>