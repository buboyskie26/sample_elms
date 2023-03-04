<?php

    class StudentAssignment{

    private $con, $teacherCourse, $studentUserLoggedInObj;

    public function __construct($con, $teacherCourse, $studentUserLoggedInObj)
    {
        $this->con = $con;
        $this->studentUserLoggedInObj = $studentUserLoggedInObj;
        $this->teacherCourse = $teacherCourse;
    }

    public function create(){
        
        $generateStudentAssignment = $this->GenerateStudentAssignment();

        return "
            <div class='subjectPeriodContainer'>
                $generateStudentAssignment
            </div>
        ";
    }

    public function createProgress(){

        $subject_id = $this->teacherCourse->GetCourseSubjectId();
        $student_id = $this->studentUserLoggedInObj->GetId();

        $queryx= $this->con->prepare("SELECT * FROM subject_period
            WHERE subject_id=:subject_id");

        $queryx->bindValue(":subject_id", $subject_id);
        $queryx->execute();

        $query= $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_id=:subject_id");

        $query->bindValue(":subject_id", $subject_id);
        $query->execute();

        $array = [];
        // <span>Total: 10</span>
        // <span>Completed: 6</span>
        // <span>Percentage: 60%</span>
        $totalAssignments = 0;
        $totalPassedAss = 0;
        $totalHandoutViewed = 0;

        if($query->rowCount() > 0){
            $totalAssignments = $query->rowCount();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                // $subjectPeriodAssignment = new SubjectPeriodAssignment($this->con,
                //     $row, $this->studentUserLoggedInObj);

                // $subjectPeriodAssId = $row['subject_period_assignment_id'];
 
                // $totalPassedAss += $this->NumberOfPassedDropboxAssignment($subjectPeriodAssId);
                
                // $subject_period_assignment_quiz_class_id = $subjectPeriodAssignment->GetSubjectPeriodAssignmentQuizClassId();

                // $totalPassedAss += $this->GetStudentQuizClassTable($subject_period_assignment_quiz_class_id, $student_id);
                // $totalAssignments += $this->GetStudentQuizClassTable($subject_period_assignment_quiz_class_id, $student_id);


                // echo $student_period_assignment_quiz_id;
               
                // echo $subject_period_id;

            }
        }

        $array = [];
        // <span>Total: 10</span>
        // <span>Completed: 6</span>
        // <span>Percentage: 60%</span>
        $totalTasks = 0;
        $totalCompletedAss = 0;
        $totalHandoutViewed = 0;

        $join = $this->con->prepare("SELECT * FROM subject_period sp
            INNER JOIN subject_period_assignment spa ON spa.subject_period_id = sp.subject_period_id
            -- INNER JOIN subject s ON s.subject_id = sp.subject_id
            -- WHERE subject_id=5
            ");

        $join->execute();

        if($join->rowCount() > 0){

            $totalTasks = $query->rowCount();

            while($row = $join->fetch(PDO::FETCH_ASSOC)){
                // echo $row['type_name'];
                $subject_period_id =  $row['subject_period_id'];
                $title =  $row['title'];

                // echo $subject_period_id;

                $subjectPeriodAssignment = new SubjectPeriodAssignment($this->con,
                    $row, $this->studentUserLoggedInObj);

                $subjectPeriodAssId = $row['subject_period_assignment_id'];
 
                $totalCompletedAss += $this->NumberOfPassedDropboxAssignment($subjectPeriodAssId);
                
                $subject_period_assignment_quiz_class_id = $subjectPeriodAssignment
                    ->GetSubjectPeriodAssignmentQuizClassId();

                $totalCompletedAss += $this->NumberOfPassedQuizAssignment($subject_period_assignment_quiz_class_id, $student_id);
                // $totalTasks += $this->GetStudentQuizClassTable($subject_period_assignment_quiz_class_id, $student_id);
                
                if(!in_array($subject_period_id, $array)){
                    array_push($array, intval($subject_period_id));
                }
                
                // $student_idx = $this->GetHandoutStudentId($subject_period_assignment_handout_id, $student_id);
                // $totalCompletedAss += $student_idx;
                
                // echo $subject_period_id;
            }
        }

        $arraySubAssHandoutId = [];

        foreach ($array as $key => $value) {
            // $ff = $this->GetSubjectPeriodAssignmentHandoutId($value);

            $queryAssignmentHandout = $this->con->prepare("SELECT * FROM subject_period_assignment_handout
                WHERE subject_period_id=:subject_period_id");

            $queryAssignmentHandout->bindValue(":subject_period_id", $value);
            $queryAssignmentHandout->execute();

            if($queryAssignmentHandout->rowCount() > 0){

                $totalHandoutInTheSubjectPeriod = $queryAssignmentHandout->rowCount();
                $totalTasks += $totalHandoutInTheSubjectPeriod;

                while($row = $queryAssignmentHandout->fetch(PDO::FETCH_ASSOC)){
                    $subject_period_assignment_handout_id = $row['subject_period_assignment_handout_id'];
                    // echo "<br>";
                    array_push($arraySubAssHandoutId, $subject_period_assignment_handout_id);
                }
            }else{
            }

            // array_push($subarr, $ff);

            // echo $subject_period_assignment_handout_id;
            // $subject_period_assignment_handout_id = intval($subject_period_assignment_handout_id);

            // $x = $this->GetHandoutStudentId($subject_period_assignment_handout_id, $student_id);

        }
 
        foreach ($arraySubAssHandoutId as $key => $subject_period_assignment_handout_id) {

            // echo $subject_period_assignment_handout_id;
            $queryHandoutViewed = $this->con->prepare("SELECT * FROM handout_viewed
                WHERE subject_period_assignment_handout_id=:subject_period_assignment_handout_id
                AND student_id=:student_id");

            $queryHandoutViewed->bindValue(":subject_period_assignment_handout_id", $subject_period_assignment_handout_id);
            $queryHandoutViewed->bindValue(":student_id", $student_id);
            $queryHandoutViewed->execute();

            if($queryHandoutViewed->rowCount() > 0){
                $totalHandoutViewed++;
                // while($row = $queryHandoutViewed->fetch(PDO::FETCH_ASSOC)){
                //     echo $row['handout_viewed_id'];
                // }
            }
        }
        $totalCompletedAss += $totalHandoutViewed;
 
        // echo $totalHandoutViewed;

        $percentage = 0;

        if($totalTasks >= $totalCompletedAss && $totalCompletedAss != 0){
            $percentage = intval(($totalCompletedAss/$totalTasks) * 100);
        }

        return "
            <div class='progress_section' style='margin-bottom: 15px;'>
                <span>Total: $totalTasks</span>
                <span>Completed: $totalCompletedAss</span>
                <span>Percentage: $percentage%</span>
            </div>
            <div class='progress'>
                <div class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' 
                aria-valuenow='$percentage' aria-valuemin='0' aria-valuemax='100'
                style='width: $percentage%'>$percentage%</div>
            </div>
        ";
    }

    private function GetHandoutStudentId($subject_period_assignment_handout_id,
        $student_id){

        $query = $this->con->prepare("SELECT student_id FROM handout_viewed
            WHERE subject_period_assignment_handout_id=:subject_period_assignment_handout_id
            AND student_id=:student_id
            LIMIT 1");

        $query->bindValue(":subject_period_assignment_handout_id", $subject_period_assignment_handout_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();
        
        return $query->fetchColumn();

    }
    private function GetSubjectPeriodAssignmentHandoutId($subject_period_id){

        $query = $this->con->prepare("SELECT subject_period_assignment_handout_id FROM subject_period_assignment_handout
            WHERE subject_period_id=:subject_period_id");

        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->execute();

        $array = [];
        // $output = "";

        if($query->rowCount() > 0){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                // echo $row['handout_name'];
                // echo "<br>";
                // $output .= $row['subject_period_assignment_handout_id'];  
                array_push($array, $row['subject_period_assignment_handout_id']);
                
            }
        }
        return $array;
    }
    private function NumberOfPassedQuizAssignment($subject_period_assignment_quiz_class_id,
        $student_id){
        
        $query = $this->con->prepare("SELECT * FROM student_period_assignment_quiz
            WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
            AND student_id=:student_id
            AND time_finish IS NOT NULL");

        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->rowCount();
    }

    private function NumberOfPassedDropboxAssignment($subjectPeriodAssId){

        $student_id = $this->studentUserLoggedInObj->GetId();
        // echo $subjectPeriodAssId;
        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id
            AND is_final=:is_final");
 
        $query->bindValue(":subject_period_assignment_id", $subjectPeriodAssId);
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":is_final", "yes");

        $query->execute();
        $output = 0;
        if($query->rowCount() > 0){
            $output = $query->rowCount();
            // while($row = $query->fetch(PDO::FETCH_ASSOC)){
            //     $output .= $row['file_name'];
            //     // $output++;
            // }
        }

        return $query->rowCount();
    }

    public function GenerateStudentAssignment(){

        $output = "";
        $subject_id = $this->teacherCourse->GetCourseSubjectId();
        $teacherCourseId =  $this->teacherCourse->GetTeacherCourseId();

        $query= $this->con->prepare("SELECT * FROM subject_period
            WHERE subject_id=:subject_id
            AND teacher_course_id=:teacher_course_id");
        
        $query->bindValue(":subject_id", $subject_id);
        $query->bindValue(":teacher_course_id", $teacherCourseId);
        $query->execute();

        if($query->rowCount() > 0){

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                // 1st
                $subjectPeriod = new SubjectPeriod($this->con, $row,
                    $this->studentUserLoggedInObj, "student");
                
                $output .= $subjectPeriod->create($teacherCourseId);
            }
        }else{
            // $output = "Check administrator to fix the issue.";
            $output = "Your Teacher did not create yet the subject.";
        }

        return $output;
    }
}
?>