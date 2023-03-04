<?php

    class SubjectPeriodQuizClass{

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

            $query = $this->con->prepare("SELECT * FROM subject_period_quiz_class 
                WHERE subject_period_quiz_class_id = :subject_period_quiz_class_id");

            $query->bindParam(":subject_period_quiz_class_id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }
    }
    public function GetQuizTime(){
        return isset($this->sqlData['quiz_time']) ? $this->sqlData['quiz_time'] : 0;
    }
    public function GetSubjectPeriodQuizId(){
        return isset($this->sqlData['subject_period_quiz_id']) ? $this->sqlData['subject_period_quiz_id'] : 0;
    }
    public function GetMaxScore(){
        return isset($this->sqlData['max_score']) ? $this->sqlData['max_score'] : 0;
    }

    public function GetStudentAttemptOnQuiz(){
         

        $subject_period_quiz_class_id = $this->sqlData['subject_period_quiz_class_id'];
        $student_id = $this->userLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT take_quiz_count FROM student_period_quiz
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
            AND student_id=:student_id");

        $query->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->fetchColumn();
    }
    public function GetMaxAttempt(){
        return isset($this->sqlData['max_attempt']) ? $this->sqlData['max_attempt'] : 0;
    }
    public function GetDateCreation(){
        $date= isset($this->sqlData['date_creation']) ? $this->sqlData["date_creation"] : ""; 
        return date("M j Y", strtotime($date));
    }
    public function GetDueDate(){
        return isset($this->sqlData['due_date']) ? $this->sqlData["due_date"] : ""; 
    }
    public function IsAllowedLateSubmission(){
        return isset($this->sqlData['allow_late_submission']) ? $this->sqlData["allow_late_submission"] : ""; 
    }
    public function GetStudentQuizScore(){
        $subject_period_quiz_class_id = $this->sqlData['subject_period_quiz_class_id'];
        $student_id = $this->userLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT total_score FROM student_period_quiz
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
            AND student_id=:student_id");

        $query->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();
        
        // * 2 Because assumed all quiz questions 2points each by default.
        return $query->fetchColumn() * 2;
    }

    public function createForm($subjectPeriodObj){

        $subject_period_id = $subjectPeriodObj->GetSubjectPeriodId();
        $teacher_id = $this->userLoggedInObj->GetId();

        $quizQuestions = $this->createQuizQuestionDropdown();

        return "
            
            <form style='width:450px;' action='add_subject_period_quiz_class.php?subject_period_id=$subject_period_id&teacher_id=$teacher_id' method='POST' enctype='multipart/form-data'>
                    <div class='form-group mt-2'>
                       
                        $quizQuestions
                        <input type='text' name='quiz_time' class='form-control mb-2' placeholder='Quiz Time In Minutes (Max: 10min)'>
        
                        <input type='hidden' name='subject_period_id' value='$subject_period_id'>

                    </div>
                    <button type='submit' class='btn btn-primary mb-3' name='create_subject_period_quiz_class'>Save</button>
            </form>
        ";
    }
    public function SetQuizClass($subject_period_quiz_id,
        $subject_period_id, $quiz_time){
        // Inputted X 60 secs.
        $SECONDS = 60;
        $MINUTES = 0;

        $quiz_time = intval($quiz_time) * $SECONDS;

        $query = $this->con->prepare("INSERT INTO subject_period_quiz_class (subject_period_quiz_id, subject_period_id, quiz_time)
            VALUES(:subject_period_quiz_id, :subject_period_id, :quiz_time)");
        
        $query->bindValue(":subject_period_quiz_id", $subject_period_quiz_id);
        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":quiz_time", $quiz_time);

        return $query->execute();

    }
    private function createQuizQuestionDropdown(){

        $query = $this->con->prepare("SELECT * FROM subject_period_quiz");
        $onchange = "";

        $query->execute();

            $html = "<div class='form-group mb-2'>
                        <select class='form-control mb-2' onchange='$onchange'name='subject_period_quiz_id' id='subject_period_quiz_id'>
                            <option value='' selected=''>Select Quiz</option>
                        
                        ";
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $html .= "
                    <option value='".$row['subject_period_quiz_id']."'>".$row['quiz_title']."</option>
                ";
            }
            $html .=    "</select>
                    </div>";
            return $html;
    }

    public function CreateTeacherQuizSection(){

        $subject_period_quiz_id = 0;

        $query = $this->con->prepare("SELECT * FROM subject_period_quiz
            WHERE subject_period_quiz_id=:subject_period_quiz_id");
        
        $query->bindValue(":subject_period_quiz_id", "");
        $query->execute();

        $type_name = "";
        $subjectPeriodTypeName = "";
        $overStudentPassed = "";
        $overAllStudents = "";
        $due_date = "";

        return "
            <tbody>
                    <tr>
                        <td>
                            <a>
                            0$subjectPeriodTypeName $type_name
                            </a>
                        </td>
                        <td class='text-center'>$overStudentPassed/$overAllStudents</td>
                        <td class='text-center'>$due_date</td>
                        <td></td>
                        <td class='text-center'>
                            <a>
                                <buttonn
                                    <i class='fas fa-edit'></i>
                                </button>
                            </a>
                        </td>
                    </tr>
            </tbody>
        
        ";
    }
}

?>