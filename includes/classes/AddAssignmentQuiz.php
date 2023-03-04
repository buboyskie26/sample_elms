<?php

    class AddAssignmentQuiz{

    private $con, $subjectPeriod, $teacherUserLoggedInObj, $teacherCourse;

    public function __construct($con, $subjectPeriod, $teacherUserLoggedInObj)
    {
        $this->con = $con;
        $this->subjectPeriod = $subjectPeriod;
        $this->teacherUserLoggedInObj = $teacherUserLoggedInObj;
    }

    public function createTable(){

        $subject_period_id = $this->subjectPeriod->GetSubjectPeriodId();
        $teacher_id = $this->teacherUserLoggedInObj->GetId();

        $table = "
            <section class='intro'>
                <div class='bg-image h-100' style='background-color: transparent;'>
                    <div class='container'>
                        
                        <div class='row justify-content-center'>
                            <div class='col-12'>
                                
                                <div class='card shadow-2-strong' style='background-color: #333;'>
                                    
                                    <div style='background-color: transparent; text-align: right' class='form-control  '>

                                        <button onclick='javascript:history.go(-1)' style='float: left; margin-left: 5px;' class='mr-2 btn btn-outline-primary btn-sm'>
                                            <i class='fas fa-arrow-left'></i>
                                        </button>
                                        <button type='button' id='add_button' class='mr-2 btn btn-info btn-sm'>Add (Bootsrap)</button>

                                        <a  href='add_subject_period_assignment_quiz_class.php?subject_period_id=$subject_period_id'>
                                            <button class='btn btn-success btn-sm'>Set Quiz to Class</button>
                                        </a>
                                        <a  href='add_subject_period_assignment_quiz.php?subject_period_id=$subject_period_id'>
                                            <button class='btn btn-primary btn-sm'>Add Quiz</button>
                                        </a>
                                    </div>
                                    <div class='card-body'>
                                        <div class='table-responsive'>
                                            
                                            <table id='subjectPeriodQuizTable' class='table table-borderless mb-0'>
                                                <thead>
                                                    <tr>
                                                        <th scope='col'>
                                                            <div class='form-check'>
                                                                <input class='form-check-input' type='checkbox' value='' id='flexCheckDefault' />
                                                            </div>
                                                        </th>
                                                        <th scope='col'>Quiz Title</th>
                                                        <th scope='col'>Quiz Description</th>
                                                        <th scope='col'>Date Added</th>
                                                        <th scope='col'>Questions</th>
                                                        <th scope='col'>Max Score</th>
                                                        <th>Set</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                            
        ";

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_period_id=:subject_period_id
            AND teacher_id=:teacher_id
            AND ass_type=:ass_type");

        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":teacher_id", $teacher_id);
        $query->bindValue(":ass_type", "Quiz");
        $query->execute();

        if($query->rowCount() > 0){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){

               $table .= $this->GenerateQuizBody($row);

            }
        }

        $table .= "
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        ";
        return $table;
    }
    private function GenerateQuizBody($row){

        $subject_period_assignment_id = $row['subject_period_assignment_id'];

        $quiz_title = $row['type_name'];
        $quiz_description = $row['description'];
        $date_creation = $row['dateCreation'];

        // $teacherCreationQuizId = $this->GetSubjectPeriodQuizTeacherId();

        $questions = "
            <a href='subject_period_assignment_quiz_question.php?subject_period_assignment_id=$subject_period_assignment_id'>
                Questions
            </a>";

        // $query = $this->con->prepare("SELECT SUM(points) as total_ponts FROM subject_period_assignment_quiz_question
        //     WHERE subject_period_assignment_id=:subject_period_assignment_id");

        // $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        // $query->execute();
        
        // $totalScore = 0;
        // if($query->rowCount() > 0){
        //     $totalScore = $query->fetchColumn();
        // }

        $totalScore = $this->GetTotalMaxScore($subject_period_assignment_id);

        $isQuizSetToYes = "
            <i style='color:green;' class='fas fa-check'></i>
        ";
        
        if($this->DoesQuizSetted($subject_period_assignment_id) == false){
            $isQuizSetToYes = "
                <i style='color:orange;' class='fas fa-times'></i>
            ";
        }
        $body = "
            <tbody>
                <tr>
                    <th scope='row'>
                        <div class='form-check'>
                            <input class='form-check-input' type='checkbox' value='' id='flexCheckDefault1' checked/>
                        </div>
                    </th>
                    <td>$quiz_title</td>
                    <td>$quiz_description</td>
                    <td>$date_creation</td>

                    <td>$questions</td>
                    <td>$totalScore</td>
                    <td>$isQuizSetToYes</td>
                    <td>

                        <button spq_id='$subject_period_assignment_id' type='button' class='subjectPeriozQuizEdit btn btn-success btn-sm px-3'>
                            <i class='fas fa-pencil'></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        ";

        return $body;
    }

    private function DoesQuizSetted($subject_period_assignment_id) {

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND ass_type=:ass_type
            AND set_quiz=:set_quiz
            LIMIT 1");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":ass_type", "Quiz");
        $query->bindValue(":set_quiz", "yes");
        $query->execute();
        
        $doesSet = false;

        if($query->rowCount() > 0){
            $doesSet = true;
        }
        return $doesSet;

    }
    private function GetTotalMaxScore($subject_period_assignment_id) {

        $query = $this->con->prepare("SELECT SUM(points) as total_ponts FROM subject_period_assignment_quiz_question
            WHERE subject_period_assignment_id=:subject_period_assignment_id");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();
        
        $totalScore = 0;

        if($query->rowCount() > 0){
            $totalScore = $query->fetchColumn();
        }

        return $totalScore;
    }
    public function AddQuizAssignment($type_name, $description,
        $due_date, $subject_period_id, $subject_id){
        
        if($type_name == "" || $description == "" || $due_date == "" || 
            $subject_period_id == 0 || $subject_id == 0){

            echo "All Parameters must be filled";
            return;
        }

        $teacher_id = $this->teacherUserLoggedInObj->GetId();

        $query = $this->con->prepare("INSERT INTO subject_period_assignment(
            type_name,description, due_date, subject_period_id,
            subject_id, ass_type, teacher_id)

            VALUES(:type_name, :description, :due_date, :subject_period_id,
            :subject_id, :ass_type, :teacher_id)");
        
        $query->bindValue(":type_name", $type_name);
        $query->bindValue(":description", $description);
        $query->bindValue(":due_date", $due_date);
        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":subject_id", $subject_id);
        $query->bindValue(":ass_type", "Quiz");
        $query->bindValue(":teacher_id", $teacher_id);
        
        return $query = $query->execute();
    }
    public function createFormQuiz(){

        // echo $teacher_course_id;
        $subject_id = $this->subjectPeriod->GetSubjectId();
        $subject_period_id = $this->subjectPeriod->GetSubjectPeriodId();

        return "
            <form action='add_subject_period_assignment_quiz.php?subject_period_id=$subject_period_id'
                method='POST'>
                    <div class='form-group'>

                        <input class='form-control mb-3' name='type_name' placeholder='Type' type='text'>

                        <textarea class='form-control mb-3 summernote' type='text'
                            name='description' placeholder='Description'></textarea>

                        <input class='form-control mb-3' name='due_date' placeholder='Due Date' type='text'>
                        
                        <input class='form-control' type='hidden' name='subject_period_id' value='$subject_period_id'>
                        <input class='form-control' type='hidden' name='subject_id' value='$subject_id'>
                    </div>

                    <button type='submit' class='btn btn-primary' name='submit_add_assignmen_quiz'>Save</button>
                </form>
        ";
    }

    public function createFormSetQuizToClass(){
           
        $subject_id = $this->subjectPeriod->GetSubjectId();
        $subject_period_id = $this->subjectPeriod->GetSubjectPeriodId();

        $quizQuestions = $this->createQuizQuestionDropdown($subject_period_id);

        return "
            <form action='add_subject_period_assignment_quiz_class.php?subject_period_id=$subject_period_id'
                method='POST'>
                    <div class='form-group mt-2'>
                       
                        $quizQuestions
                        <input type='text' name='quiz_time' class='mb-2 form-control mb-2' placeholder='Quiz Time In Minutes (Max: 10min)'>
                        <input type='number' name='max_submission' class='mb-2 form-control mb-2' placeholder='Max Submission'>
                        <input type='hidden' name='subject_period_id' value='$subject_period_id'>
                        <div style='margin-left: 10px;'>
                            <label>Allow Show Answer</label><br>  
                            <input type='radio' name='show_correct_answer' value='yes'> Yes <br>
                            <input type='radio'  name='show_correct_answer' value='no'> No
                        </div>
                        
                    </div>
                    <button type='submit' class='btn btn-primary mb-3' name='set_subject_period_assignment_quiz_class'>Save</button>
            </form>
        ";
    }
    public function settQuizToClass($quiz_time, $subject_period_assignment_id,
        $subject_period_id, $max_submission, $show_correct_answer) : bool{

        $SECONDS = 60;
        $max_submission = intval($max_submission);

        $quiz_time = intval($quiz_time) * $SECONDS;

        $query = $this->con->prepare("INSERT INTO subject_period_assignment_quiz_class 
            (quiz_time, subject_period_assignment_id, subject_period_id, show_correct_answer)
            VALUES(:quiz_time, :subject_period_assignment_id, :subject_period_id, :show_correct_answer)");

        $query->bindValue(":quiz_time", $quiz_time);
        // $query->bindValue(":due_date", $due_date);
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":show_correct_answer", $show_correct_answer);

        $isExecute =  $query->execute();

        $success = false;

        $max_score = $this->GetTotalMaxScore($subject_period_assignment_id);

        if($isExecute){
            // Update the set_quiz to true.
            $update = $this->con->prepare("UPDATE subject_period_assignment
                SET set_quiz=:set_quiz, max_score=:max_score, max_submission=:max_submission
                WHERE subject_period_assignment_id=:subject_period_assignment_id
                AND set_quiz=:set_quiz_no");
         
            $update->bindValue(":set_quiz", "yes");
            $update->bindValue(":max_score", $max_score);
            $update->bindValue(":max_submission", $max_submission);
            $update->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
            $update->bindValue(":set_quiz_no", "no");
            
            if($update->execute()){
                $success = true;
                return $isExecute;
            }
        }

        return $success;
    }
    private function createQuizQuestionDropdown($subject_period_id){

        $teacher_id = $this->teacherUserLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment
            WHERE ass_type=:ass_type
            AND teacher_id=:teacher_id
            AND subject_period_id=:subject_period_id
            -- set to 'no', to avoid set again,
            AND set_quiz=:set_quiz
            -- NOT WORKING
            -- AND due_date <= NOW()
            ORDER BY due_date DESC");

        $query->bindValue(":ass_type", "Quiz");
        $query->bindValue(":teacher_id", $teacher_id);
        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":set_quiz", "no");

        $onchange = "";

        $query->execute();

        $html = "<div class='form-group mb-2'>
                    <select class='form-control mb-2' name='subject_period_assignment_id' id='subject_period_assignment_id'>
                        <option value='' selected=''>Select Quiz</option>
                    
                    ";

        if($query->rowCount() > 0){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $html .= "
                    <option value='".$row['subject_period_assignment_id']."'>".$row['type_name']."</option>
                ";
            }
        }else{
            // echo "qwee";
        }
        
        
        $html .=    "</select>
                </div>";
        return $html;
    }
}
?>