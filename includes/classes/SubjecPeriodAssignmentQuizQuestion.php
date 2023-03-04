<?php

    class SubjecPeriodAssignmentQuizQuestion{

    private $con, $subjectPeriodQuizObj, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function create($subject_period_assignment_id){

        $table = "
            <section class='intro' style='flex: 1;' >
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

                                        <a  href='add_subject_period_assignment_quiz_question.php?subject_period_assignment_id=$subject_period_assignment_id'>
                                            <button class='btn btn-primary btn-sm'>Add Question</button>
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
                                                        <th scope='col'>Question Text</th>
                                                        <th scope='col'>Question Type</th>
                                                        <th scope='col'>Answer</th>
                                                        <th scope='col'>Creation</th>
                                                        <th scope='col'>Points</th>
                                                        <th scope='col'><th>
                                                    </tr>
                                                </thead>
                                            
        ";

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment_quiz_question
            WHERE subject_period_assignment_id=:subject_period_assignment_id");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
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

        $question_text = $row['question_text'];
        $question_type_id = $row['question_type_id'];
        $question_answer = $row['question_answer'];
        $points = $row['points'];
        $date_creation = $row['date_creation'];
 

        // $teacherCreationQuizId = $this->GetSubjectPeriodQuizTeacherId();

        $questions = "
            <a href='subject_period_assignment_quiz_question.php?subject_period_assignment_id=$subject_period_assignment_id'>
                Questions
            </a>";

        $body = "
            <tbody>
                <tr>
                    <th scope='row'>
                        <div class='form-check'>
                            <input class='form-check-input' type='checkbox' value='' id='flexCheckDefault1' checked/>
                        </div>
                    </th>
                    <td>$question_text</td>
                    <td>$question_type_id</td>
                    <td>$question_answer</td>

                    <td>$date_creation</td>
                    <td>$points</td>
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

    public function insert($question_text, $question_type_id, $question_answer,
        $points, $subject_period_assignment_id,
        $answer1, $answer2, $answer3, $answer4
        
        ){

        $query = $this->con->prepare("INSERT INTO subject_period_assignment_quiz_question
            (question_text, question_type_id, question_answer,points, subject_period_assignment_id)
            VALUES(:question_text, :question_type_id, :question_answer, :points, :subject_period_assignment_id)");
        
        $query->bindValue(":question_text", $question_text);
        $query->bindValue(":question_type_id", $question_type_id);
        $query->bindValue(":question_answer", $question_answer);
        $query->bindValue(":points", $points);
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);

        $queryExecute =  $query->execute();

        $queryId = $this->con->lastInsertId();

        if($answer1 != "" && $answer2 != "" && $answer3 != "" && $answer4 != ""){
            $this->insertMultipleChoice($answer1, $answer2, $answer3, $answer4,
                $queryId);
        }

        return $queryExecute;
        
    }

    public function insertMultipleChoice($answer1, $answer2, $answer3, $answer4,
        $subject_period_assignment_quiz_question_id){

        if($answer1 == "" && $answer2 == "" && $answer3 == "" && $answer4 == ""){
            return;
        }

        $arr = [];
        $letters = [];

        array_push($arr, $answer1);
        array_push($arr, $answer2);
        array_push($arr, $answer3);
        array_push($arr, $answer4);

        array_push($letters, "A");
        array_push($letters, "B");
        array_push($letters, "C");
        array_push($letters, "D");

        if(sizeof($arr) > 0){

            foreach($arr as $index => $value){
                
               $letter = $letters[$index];
                // echo $value;
                // echo "<br>";

                $queryA = $this->con->prepare("INSERT INTO subject_period_assignment_quiz_question_answer
                    (answer_text, choices, subject_period_assignment_quiz_question_id)
                    VALUES(:answer_text, :choices, :subject_period_assignment_quiz_question_id)");

                $queryA->bindValue(":answer_text", $value);
                $queryA->bindValue(":choices", $letter);
                $queryA->bindValue(":subject_period_assignment_quiz_question_id", $subject_period_assignment_quiz_question_id);
                $queryA->execute();

            }
        }

        // $subject_period_quiz_question_id = 0;

        // $queryA = $this->con->prepare("INSERT INTO subject_period_quiz_question_answer
        //     (answer_text, choices, subject_period_quiz_question_id)
        //     VALUES(:answer_text, :choices, :subject_period_quiz_question_id)");

        // $queryA->bindValue(":answer_text", $answer1);
        // $queryA->bindValue(":choices", "A");
        // $queryA->bindValue(":subject_period_quiz_question_id", $subject_period_quiz_question_id);
        // $queryA->execute();


        // $queryB = $this->con->prepare("INSERT INTO subject_period_quiz_question_answer
        //     (answer_text, choices, subject_period_quiz_question_id)
        //     VALUES(:answer_text, :choices, :subject_period_quiz_question_id)");

        // $queryB->bindValue(":answer_text", $answer2);
        // $queryB->bindValue(":choices", "B");
        // $queryB->bindValue(":subject_period_quiz_question_id", $subject_period_quiz_question_id);
        // $queryB->execute();


        // $queryC = $this->con->prepare("INSERT INTO subject_period_quiz_question_answer
        //     (answer_text, choices, subject_period_quiz_question_id)
        //     VALUES(:answer_text, :choices, :subject_period_quiz_question_id)");

        // $queryC->bindValue(":answer_text", $answer3);
        // $queryC->bindValue(":choices", "C");
        // $queryC->bindValue(":subject_period_quiz_question_id", $subject_period_quiz_question_id);
        // $queryC->execute();


        // $queryD = $this->con->prepare("INSERT INTO subject_period_quiz_question_answer
        //     (answer_text, choices, subject_period_quiz_question_id)
        //     VALUES(:answer_text, :choices, :subject_period_quiz_question_id)");

        // $queryD->bindValue(":answer_text", $answer4);
        // $queryD->bindValue(":choices", "D");
        // $queryD->bindValue(":subject_period_quiz_question_id", $subject_period_quiz_question_id);
        // $queryD->execute();


    }

    public function createForm($subject_period_assignment_id){

        $subject_period_id = 0;
        $teacher_id = 0;

        $questionTypeDrop = $this->createQuestionTypeDropdown($subject_period_assignment_id);

        return "
            <div style='text-align: right;'class='question_type_section'>
                <a href='add_question_type.php'></a>
                <button class='btn btn-secondary'>Add Question Type</button>
            </div>
            <form style='width:450px;' action='add_subject_period_assignment_quiz_question.php?subject_period_assignment_id=$subject_period_assignment_id' method='POST' enctype='multipart/form-data'>
                    <div class='form-group mt-2'>

                        <textarea placeholder='Quiz Description'
                            class='form-control mb-2 summernote' name='question_text'></textarea>

                        $questionTypeDrop
                        <div class='container'></div>
                        <input placeholder='Quiz Points' class='form-control mb-2' type='text' name='points'>

                        <input type='hidden' name='subject_period_assignment_id' value='$subject_period_assignment_id'>

                    </div>
                    <button type='submit' class='btn btn-primary' name='create_subject_period_assignment_quiz_question'>Save</button>
                </form>
        ";
    }


    private function createQuestionTypeDropdown($subject_period_assignment_id){

        $teacher_id = $this->userLoggedInObj->GetId();

        $onchange = "quizTypeChangeAssignment(this)";
        $query = $this->con->prepare("SELECT * FROM question_type");

        $query->execute();

            $html = "<div class='form-group mb-2'>
                    <select class='form-control' onchange='$onchange' name='question_type_id' id='question_type_id'>
                    <option value='' selected=''>Select Type</option>
                    
                    ";
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $html .= "
                    <option value='".$row['question_type_id']."'>".$row['type']."</option>
                ";
            }
            $html .= "</select>
                    </div>";
            
            return $html;
    }
}
?>