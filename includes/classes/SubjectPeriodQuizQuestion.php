<?php

    class SubjectPeriodQuizQuestion{

    private $con, $subjectPeriodQuizObj, $userLoggedInObj;

    public function __construct($con, $subjectPeriodQuizObj, $userLoggedInObj)
    {
        $this->con = $con;
        $this->subjectPeriodQuizObj = $subjectPeriodQuizObj;
        $this->userLoggedInObj = $userLoggedInObj;
    }
    public function createForm($subject_period_quiz_id){

        $subject_period_id = 0;
        $teacher_id = 0;

        $questionTypeDrop = $this->createQuestionTypeDropdown($subject_period_quiz_id);

        return "
            <div style='text-align: right;'class='question_type_section'>
                <a href='add_question_type.php'></a>
                <button class='btn btn-secondary'>Add Question Type</button>
            </div>
            <form style='width:450px;' action='add_subject_period_quiz_question.php?subject_period_quiz_id=$subject_period_quiz_id' method='POST' enctype='multipart/form-data'>
                    <div class='form-group mt-2'>

                        <textarea placeholder='Quiz Description'
                            class='form-control mb-2 summernote' name='question_text'></textarea>

                        $questionTypeDrop
                        <div class='container'></div>

                        <input type='hidden' name='subject_period_quiz_id' value='$subject_period_quiz_id'>

                    </div>
                    <button type='submit' class='btn btn-primary' name='create_subject_period_quiz_question'>Save</button>
                </form>
        ";
    }
    public function insert($question_text, $question_type_id, $question_answer,
        $subject_period_quiz_id, $answer1, $answer2, $answer3, $answer4){

        $query = $this->con->prepare("INSERT INTO subject_period_quiz_question
            (question_text, question_type_id, question_answer, subject_period_quiz_id)
            VALUES(:question_text, :question_type_id, :question_answer, :subject_period_quiz_id)");
        
        $query->bindValue(":question_text", $question_text);
        $query->bindValue(":question_type_id", $question_type_id);
        $query->bindValue(":question_answer", $question_answer);
        $query->bindValue(":subject_period_quiz_id", $subject_period_quiz_id);

        $queryExecute =  $query->execute();

        $queryId = $this->con->lastInsertId();

        if($answer1 != "" && $answer2 != "" && $answer3 != "" && $answer4 != ""){
            $this->insertMultipleChoice($answer1, $answer2, $answer3, $answer4,
                $queryId);
        }

        return $queryExecute;
        
    }
    
    public function insertMultipleChoice($answer1, $answer2, $answer3, $answer4,
        $subject_period_quiz_question_id){

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

                $queryA = $this->con->prepare("INSERT INTO subject_period_quiz_question_answer
                    (answer_text, choices, subject_period_quiz_question_id)
                    VALUES(:answer_text, :choices, :subject_period_quiz_question_id)");

                $queryA->bindValue(":answer_text", $value);
                $queryA->bindValue(":choices", $letter);
                $queryA->bindValue(":subject_period_quiz_question_id", $subject_period_quiz_question_id);
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
    private function createQuestionTypeDropdown($subject_period_quiz_id){

        $teacher_id = $this->userLoggedInObj->GetId();

        $onchange = "quizTypeChange(this, $subject_period_quiz_id, $teacher_id)";
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
    public function create($subject_period_quiz_id){

        $quizQuestionCreation = $this->GenerateQuizQuestion($subject_period_quiz_id);

        return "
            <div class='quiz_question_inner'>
                $quizQuestionCreation
            </div>
        ";
    }

    private function GenerateQuizQuestion($subject_period_quiz_id){

         $table = "
            <section class='intro'>
                <div class='bg-image h-100' style='background-color: transparent;'>
                    <div class='container'>
                        <div class='row justify-content-center'>
                            <div class='col-12'>
                                <div class='card shadow-2-strong' style='background-color: #333;'>
                                    <div style='background-color: transparent; text-align: right' class='form-control  '>
                                        <a href='add_subject_period_quiz_question.php?subject_period_quiz_id=$subject_period_quiz_id'>
                                            <button class='btn btn-primary btn-sm'>Add Question</button>
                                        </a>
                                    </div>
                                    <div class='card-body'>
                                        <div class='table-responsive'>
                                            
                                            <table class='table table-borderless mb-0'>
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
                                                        <th scope='col'>Date Added</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                            
        ";
        
        $query = $this->con->prepare("SELECT * FROM subject_period_quiz_question
            WHERE subject_period_quiz_id=:subject_period_quiz_id");

        $query->bindValue(":subject_period_quiz_id", $subject_period_quiz_id);
        $query->execute();

        if($query->rowCount() > 0){
            while($row = $query->fetch(PDO::FETCH_ASSOC)){

               $table .= $this->GenerateQuizQuestionBody($row);

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

    private function GenerateQuizQuestionBody($row){

        $question_text = $row['question_text'];
        $question_answer = $row['question_answer'];
        $date_creation = $row['date_creation'];
        $question_type = $this->GetQuestionType($row['question_type_id']);

        $body = "
            <tbody>
                <tr>
                    <th scope='row'>
                        <div class='form-check'>
                            <input class='form-check-input' type='checkbox' value='' id='flexCheckDefault1' checked/>
                        </div>
                    </th>
                    <td>$question_text</td>
                    <td>$question_type</td>
                    <td>$question_answer</td>

                    <td>$date_creation</td>
                    <td>

                        <button type='button' class='btn btn-success btn-sm px-3'>
                            <i class='fas fa-pencil'></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        ";

        return $body;
    }
    private function GetQuestionType($question_type_id){

        $query = $this->con->prepare("SELECT type FROM question_type
            WHERE question_type_id=:question_type_id
            LIMIT 1");

        $query->bindValue(":question_type_id",$question_type_id);
        $query->execute();

        return $query->fetchColumn();
    }

    // Taking Quiz
    public function createQuiz(){

        $generateQuiz = $this->GenerateStudentQuiz();

        $output = "
            <div class='generate_quiz'>
                $generateQuiz
            </div>
        ";

        return $output;
    }
    
    private function setTimer($mytime){

        // $mytime = 5;
        if(!isset($_SESSION['time'])){
            $_SESSION['time'] = time();

            // echo $_SESSION['time'];
        }else{

            $diff = time() - $_SESSION['time'];

            $diff = $mytime - $diff;

            $hours = floor($diff/60);
            $minutes = (int)($diff/60);
            $seconds = $diff%60;
            
            $show = $hours . ":" . $minutes . ":" . $seconds;

            // echo $seconds;

            // if($diff == 0 || $diff <= 0){
            //     echo "Quiz is over!";
            // }else{
            //     echo $show;
            // }
        }
    }
    
      
    public function createQuizForm($subject_period_quiz_class_id, $teacher_course_id){

        // $subject_period_quiz_id = $this->subjectPeriodQuizObj->GetQuizId();
        $generateStudentQuiz = $this->GenerateStudentQuiz();
        return "
            <div class='quiz_section_inner'>
                <form  action='quiz_question.php?subject_period_quiz_class_id=$subject_period_quiz_class_id&tc_id=$teacher_course_id' method='POST'>
                
                    $generateStudentQuiz
                    <button id='submitQuizAnswer' name='submit_quiz_question' type='submit' class='btn btn-primary btn-sm'>Submit Answer</button>
                </form>
            </div>

        ";
    }

    public function GenerateStudentQuiz(){

        $subject_period_quiz_id = $this->subjectPeriodQuizObj->GetQuizId();

        $subject_period_quiz_class_id = $_GET['subject_period_quiz_class_id'];
 
        $queryQuiz = $this->con->prepare("SELECT quiz_time FROM subject_period_quiz_class
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
            LIMIT 1");

        $queryQuiz->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        $queryQuiz->execute();
        $time = 0;

        if($queryQuiz->rowCount() > 0){
            $time = $queryQuiz->fetchColumn();
            // $this->setTimer($time);
            $_SESSION['timeStart'] = $time;
            $_SESSION['subject_period_quiz_class_id'] = $subject_period_quiz_class_id;
            // echo $_SESSION['timeStart'];
            // if($_SESSION['timeStart'] == 0){
            //    echo "qwe";
            // }
        }

        $query = $this->con->prepare("SELECT * FROM subject_period_quiz_question
                WHERE subject_period_quiz_id=:subject_period_quiz_id");

        $query->bindValue(":subject_period_quiz_id", $subject_period_quiz_id);
        $query->execute();

        // $ii = 1;

        $student_id = $this->userLoggedInObj->GetId();

        $outputQ = "";
        if($query->rowCount() > 0){

            $_SESSION['subject_period_quiz_class_id'] = $subject_period_quiz_class_id;
            $_SESSION['student_id'] = $student_id;

            $q = 1;
            while($rowQ = $query->fetch(PDO::FETCH_ASSOC)){
                $outputQ .= "
                    <div style='margin-left:5px;' id='select_$q'>
                        <button   onclick='return false;' se='$q' class='selectClick btn btn-sm btn-outline-primary'>$q</button>
                    </div>
                ";

                $q++;
            }
        }


        // $exam_star_time = $row['online_exam_datetime'];
		// $duration = $row['online_exam_duration'] . ' minute';
		// $exam_end_time = strtotime($exam_star_time . '+' . $duration);

		// $exam_end_time = date('Y-m-d H:i:s', $exam_end_time);
		// $remaining_minutes = strtotime($exam_end_time) - time();

        $exam_star_time = date('Y-m-d H:i:s', strtotime("2023-02-14 12:10:00"));
        $duration = '5' . ' minute';
        $exam_end_time = strtotime($exam_star_time . '+' . $duration);

        $exam_end_time = date('Y-m-d H:i:s', $exam_end_time);
        // $remaining_minutes = strtotime($exam_end_time) - time();

        $remaining_minutes = $this->GetStudentQuizTime($subject_period_quiz_class_id, $student_id);
        // To be exact on the countdown in timer.ajax.php
        $remaining_minutes = $remaining_minutes + 2;

        $table = "
            <div class='timer_section'>
                <div align='center'>
                    <div id='exam_timer' data-timer='$remaining_minutes' 
                    style='max-width:400px; width: 100%; height: 180px;'></div>
		        </div>

                <p id='timer'>50</p>
                <div id='question_nav' style='display: flex;'>
                    $outputQ
                </div>

                <div id='msg'></div>
            </div>

            <table class='questions-table table'>
                <tbody>
                    <tr>
                        <th>#</th>
                        <th>Questions</th>
                    </tr>
        ";

        // echo $subject_period_quiz_class_id;
        $doesStudentAnswerTheQuiz = $this->subjectPeriodQuizObj->
            CheckStudentDidntFinishTheQuiz($subject_period_quiz_class_id);
            
        if($doesStudentAnswerTheQuiz == true){
            // Populate the previous selected radio. if there`s something there
            $query = $this->con->prepare("SELECT * FROM subject_period_quiz_question
                WHERE subject_period_quiz_id=:subject_period_quiz_id");

            $query->bindValue(":subject_period_quiz_id", $subject_period_quiz_id);
            $query->execute();

            $ii = 1;

            $student_id = $this->userLoggedInObj->GetId();

            if($query->rowCount() > 0){
                while($row = $query->fetch(PDO::FETCH_ASSOC)){
                    
                    $question_type_id = $row['question_type_id'];
                    $subject_period_quiz_question_id = $row['subject_period_quiz_question_id'];
                    $question_text = $row['question_text'];
                    // $question_answer = $row['question_answer'];

                    $queryv2 = $this->con->prepare("SELECT * FROM student_period_quiz_question_answer
                        WHERE subject_period_quiz_question_id=:subject_period_quiz_question_id 
                        AND student_id=:student_id");

                    $queryv2->bindValue(":subject_period_quiz_question_id", $subject_period_quiz_question_id);
                    $queryv2->bindValue(":student_id", $student_id);
                    $queryv2->execute();

                    $my_answer = "";

                    if($queryv2->rowCount() > 0){
                        $queryv2 = $queryv2->fetch(PDO::FETCH_ASSOC);

                        $my_answer = $queryv2['my_answer'];
                    }

                    // echo "Have answered the quiz";
                    // Issue is you dont yet answer the quiz questions
                    // but its seems it act as already answered the quiz.

                    // It should be for every click of radio button
                    // not on submitting the Next Question Button.

                    $trueFalseButton = "nextQuestionSubmit(this,
                        $subject_period_quiz_question_id, $student_id, $subject_period_quiz_id)";

                    $trueFalseButtonRadio = "nextQuestionSubmitRadio(this,
                        $subject_period_quiz_question_id, $student_id, $subject_period_quiz_id)";
 
                    if($question_type_id === 2){

                        $radioForTrue = "
                            <input name='q-$subject_period_quiz_question_id' 
                                id='$subject_period_quiz_question_id' onclick='$trueFalseButtonRadio' type='radio' value='True'> True
                            ";

                        if($my_answer == "True"){
                            $radioForTrue = "
                                <input name='q-$subject_period_quiz_question_id' 
                                    id='$subject_period_quiz_question_id' onclick='$trueFalseButtonRadio' type='radio' checked value='True'> True
                            ";
                        }

                        $radioForFalse = "
                            <input name='q-$subject_period_quiz_question_id' 
                                id='$subject_period_quiz_question_id' onclick='$trueFalseButtonRadio' type='radio' value='False'> False
                        ";

                        if($my_answer == "False"){
                            $radioForFalse = "
                                <input name='q-$subject_period_quiz_question_id' 
                                    id='$subject_period_quiz_question_id' onclick='$trueFalseButtonRadio' type='radio' checked value='False'> False
                            ";
                        }

                        $table .= "
                            <tr id='q_$ii' class='questions'>
                                <td width='30'>$ii</td>
                                <td id='qa'>
                                    $question_text 

                                    $radioForTrue
                                    <br>

                                    $radioForFalse
                                    <br>

                                    <button onclick='return false;' pv=$ii id='prevBtn_$ii' class='prevq btn btn-primary btn-sm'>Prev Question</button>
                                    <button onclick='return false;' qn=$ii class='nextq btn btn-success btn-sm'>Next Question</button>
                                    <input type='hidden' name='x-$ii' value='$subject_period_quiz_question_id'>
                                
                                    <input type='hidden' name='x' value='$ii'>
                                </td>
                            </tr>
                        ";
                    }
                    // Multiplce Choices
                    if($question_type_id === 1){

                        $queryMult = $this->con->prepare("SELECT * FROM subject_period_quiz_question_answer
                            WHERE subject_period_quiz_question_id=:subject_period_quiz_question_id");

                        $queryMult->bindValue(":subject_period_quiz_question_id", $subject_period_quiz_question_id);
                        $queryMult->execute();

                        $answerText = "";
                        
                        $outputMultipleChoices = "";

                        if($queryMult->rowCount() > 0){
                            $arrayAlphabet = [];
                            $arrayMulti = [];

                            array_push($arrayAlphabet, "A");
                            array_push($arrayAlphabet, "B");
                            array_push($arrayAlphabet, "C");
                            array_push($arrayAlphabet, "D");

                            while($rowMul = $queryMult->fetch(PDO::FETCH_ASSOC)){
                                array_push($arrayMulti, $rowMul);
                            }
                            
                            foreach($arrayMulti as $index => $val){

                                $multiQuestionIndex = $index + 1;

                                $letter = $arrayAlphabet[$index];
                                
                                $subject_period_quiz_question_answer_id = $val['subject_period_quiz_question_answer_id'];
                                $answerText = $val['answer_text'];
                                $choices = $val['choices'];

                                $multipleRadioButtonSubmit = "multipleRadioButtonSubmit(this,
                                    $subject_period_quiz_question_id, $student_id,
                                    $subject_period_quiz_id, $subject_period_quiz_question_answer_id)";

                                $queryMultiMyAnswer = $this->con->prepare("SELECT * FROM student_period_quiz_multi_question_answer
                                    -- WHERE subject_period_quiz_question_answer_id=:subject_period_quiz_question_answer_id
                                    WHERE subject_period_quiz_question_answer_id=:subject_period_quiz_question_answer_id
                                    AND student_id=:student_id
                                    LIMIT 1");

                                // $queryMultiMyAnswer->bindValue(":subject_period_quiz_question_answer_id", $subject_period_quiz_question_answer_id);
                                $queryMultiMyAnswer->bindValue(":subject_period_quiz_question_answer_id", $subject_period_quiz_question_answer_id);
                                $queryMultiMyAnswer->bindValue(":student_id", $student_id);
                                $queryMultiMyAnswer->execute();
                                
                                $my_answerMul = "";
                                if($queryMultiMyAnswer->rowCount() > 0){
                                    $queryMultiMyAnswer = $queryMultiMyAnswer->fetch(PDO::FETCH_ASSOC);
                                    $my_answerMul = $queryMultiMyAnswer['my_answer'];
                                }
                                // $my_answer = $queryMultiMyAnswer['my_answer'];

                                $inputClick = " <input onclick='$multipleRadioButtonSubmit' 
                                    type='radio' name='q-$subject_period_quiz_question_id' value='$choices'>";

                                if($my_answerMul === $letter){
                                    $inputClick = "
                                        <input checked onclick='$multipleRadioButtonSubmit' type='radio'
                                        name='q-$subject_period_quiz_question_id' value='$choices'>
                                    ";
                                }
                                
                                $outputMultipleChoices .= "
                                    $letter: <input value='$answerText' name='answer_text_$multiQuestionIndex'
                                        type='text' size='40' class='mt-2 mb-2'> 
                                           $inputClick    
                                    <br>
                                ";
                            }
                        } 

                        $table .= "
                            <tr id='q_$ii' class='questions'>
                                <td width='30'>$ii</td>
                                <td id='qa'>
                                    $question_text
                                    
                                    $outputMultipleChoices

                                    <button onclick='return false;' pv=$ii id='prevBtn_$ii' class='prevq btn btn-primary btn-sm'>Prev Question</button>
                                    <button onclick='return false;' qn=$ii class='nextq btn btn-success btn-sm'>Next Question</button>
                                    <input type='hidden' name='x-$ii' value='$subject_period_quiz_question_id'>
                                
                                    <input type='hidden' name='x' value='$ii'>
                                </td>
                            </tr>
                        ";
                        }
                    

                    $ii++;
                }
            }
        }

        // else{
        //     echo "Not Answered the quiz yet";
        //     echo "FAF";

        //     $query = $this->con->prepare("SELECT * FROM subject_period_quiz_question
        //     WHERE subject_period_quiz_id=:subject_period_quiz_id");

        //     $query->bindValue(":subject_period_quiz_id", $subject_period_quiz_id);
        //     $query->execute();

        //     $student_id = $this->userLoggedInObj->GetId();
        //     $subject_period_quiz_id =  $this->subjectPeriodQuizObj->GetQuizId();

        //     if($query->rowCount() > 0){

        //         while($row = $query->fetch(PDO::FETCH_ASSOC)){
        //             array_push($array, $row);
        //         }

        //         $i = 1;
                
        //         foreach($array as $index => $value){

        //             $question_type_id = $value['question_type_id'];
        //             $subject_period_quiz_question_id = $value['subject_period_quiz_question_id'];
        //             $question_text = $value['question_text'];

        //             $trueFalseButton = "nextQuestionSubmit(this,
        //                 $subject_period_quiz_question_id, $student_id, $subject_period_quiz_id)";

        //             if($question_type_id === 2){
        //                 $table .= "
        //                     <tr>
        //                         <td width='30'>$i</td>
        //                         <td>

        //                             <p>$question_text</p>
        //                             <input name='q-$subject_period_quiz_question_id' id='$subject_period_quiz_question_id' type='radio' value='True'> True
        //                             <br>
        //                             <input name='q-$subject_period_quiz_question_id' id='$subject_period_quiz_question_id' type='radio' value='False'> False
        //                             <br>

        //                             <button onclick='$trueFalseButton' class='btn btn-success btn-sm'>Next Question</button>
        //                             <input type='hidden' name='x-$i' value='$subject_period_quiz_question_id'>
                                
        //                             <input type='hidden' name='x' value='$i'>
        //                         </td>
        //                     </tr>
        //                 ";
        //             }
        //             // if($question_type_id === 1){
        //             //     $queryMultiple = $this->con->prepare("SELECT * FROM subject_period_quiz_question_answer
        //             //         WHERE subject_period_quiz_question_id=:subject_period_quiz_question_id");

        //             //     $queryMultiple->bindValue(":subject_period_quiz_question_id", $subject_period_quiz_question_id);
        //             //     $queryMultiple->execute();
        //             //     // $queryMultiple = $queryMultiple->fetch(PDO::FETCH_ASSOC);

        //             //     $table .="
        //             //         <tr>
        //             //             <td width='30'>$i</td>
        //             //                 <td>
        //             //                     <p>$question_text</p>
        //             //     ";

        //             //     while($rowMul = $queryMultiple->fetch(PDO::FETCH_ASSOC)){

        //             //         $answer_text = $rowMul['answer_text'];
        //             //         $choices = $rowMul['choices'];

        //             //         $table .="
        //             //             <input name='q-$subject_period_quiz_question_id' type='radio' value='$choices'> $choices.)
        //             //             $answer_text
        //             //             <br>
        //             //         ";
        //             //     }
        //             //     $table .="
        //             //                 <button class='btn btn-success btn-sm'>Next Question</button>

        //             //                 <input type='hidden' name='x-$i' value='$subject_period_quiz_question_id'>
        //             //                 <input type='hidden' name='x' value='$i'>
        //             //             </td>
        //             //         </tr>
        //             //     ";
        //             // }


        //             $i++;
        //         }
        //         // while($row = $query->fetch(PDO::FETCH_ASSOC)){
        //         //     $table .= "
        //         //         <tr>
        //         //             <td width='30'>1</td>
        //         //             <td>
        //         //                 <p>Question One</p>
        //         //                 <br>
        //         //                 <br>
        //         //                 <input name='question_type_id_$question_type_id' type='radio' value='True'>True
        //         //                 <br>
        //         //                 <input name='question_type_id_$question_type_id' type='radio' value='False'>False

        //         //             </td>
        //         //         </tr>
        //         //     ";
        //         // }
        //     }
        // }


        $table.= "
            </tbody>
        </table>";

        return $table;
    }

    private function GetStudentQuizTime($subject_period_quiz_class_id, $student_id){

        $query = $this->con->prepare("SELECT student_quiz_time FROM student_period_quiz
                WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
                AND student_id=:student_id");

        $query->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->fetchColumn();


    }
}
?>