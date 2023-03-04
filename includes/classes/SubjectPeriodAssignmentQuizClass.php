<?php

    class SubjectPeriodAssignmentQuizClass{

    private $con, $userLoggedInObj, $sqlData;

    public function __construct($con, $input, $userLoggedInObj)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
        // $this->subjectPeriodObj = $subjectPeriodObj;
        $this->sqlData = $input;

        if($input == null){
            $this->sqlData = null;
        }
        if(!is_array($input)){

            $query = $this->con->prepare("SELECT * FROM subject_period_assignment_quiz_class 
                WHERE subject_period_assignment_quiz_class_id = :subject_period_assignment_quiz_class_id");

            $query->bindParam(":subject_period_assignment_quiz_class_id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }
    }

    public function GetSubjectPeriodAssignmentQuizClassId(){
        return isset($this->sqlData['subject_period_assignment_quiz_class_id']) 
            ? $this->sqlData['subject_period_assignment_quiz_class_id'] : 0;
    }

    public function GetSubjectPeriodAssignmentId(){
        return isset($this->sqlData['subject_period_assignment_id']) 
            ? $this->sqlData['subject_period_assignment_id'] : 0;
    }

    public function GetQuizTime(){
        return isset($this->sqlData['quiz_time']) 
            ? $this->sqlData['quiz_time'] : 0;
    }

    // public function GetStudentQuizClassTable($subject_period_assignment_quiz_class_id,
    //     $student_id){
            
    //     $query = $this->con->prepare("SELECT student_period_assignment_quiz_id FROM student_period_assignment_quiz
    //         WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
    //         AND student_id=:student_id");


    //     $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
    //     $query->bindValue(":student_id", $student_id);
    //     $query->execute();

    //     return $query->fetchColumn();
    // }
}

?>