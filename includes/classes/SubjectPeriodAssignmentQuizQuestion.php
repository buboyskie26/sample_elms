<?php

    class SubjectPeriodAssignmentQuizQuestion{

    private $con, $subjectPeriodAssQuizClassObj, $userLoggedInObj;

    public function __construct($con, $subjectPeriodAssQuizClassObj, $userLoggedInObj)
    {
        $this->con = $con;
        $this->subjectPeriodAssQuizClassObj = $subjectPeriodAssQuizClassObj;
        $this->userLoggedInObj = $userLoggedInObj;
    }



    public function createQuizForm($subject_period_assignment_quiz_class_id,
        $teacher_course_id){

        echo $_SESSION['token_quiz'];

        $generateStudentQuiz = $this->GenerateStudentQuiz($subject_period_assignment_quiz_class_id);
        // unset($_SESSION['teacher_course_id']);

        // echo $teacher_course_id;

        return "
            <div class='quiz_section_inner'>
                <form action='student_assignment_submit.php?subject_period_assignment_quiz_class_id=$subject_period_assignment_quiz_class_id' method='POST'>
                
                    $generateStudentQuiz

                    <button id='submitQuizAnswerv2' name='submit_ass_quiz_question' type='submit' class='btn btn-primary btn-sm'>
                        Submit Answer
                    </button>
                </form>
            </div>

        ";
    }

    private function GenerateStudentQuiz($subject_period_assignment_quiz_class_id){

        $subject_period_assignment_id = $this->subjectPeriodAssQuizClassObj
            ->GetSubjectPeriodAssignmentId();

        $subjectPeriodAssignment = new SubjectPeriodAssignment($this->con,
            $subject_period_assignment_id, $this->userLoggedInObj);

        $student_id = $this->userLoggedInObj->GetId();
        
        $doesQuizHadTaken = $subjectPeriodAssignment->DoesQuizHadTaken($subject_period_assignment_quiz_class_id, $student_id);
        $takeQuizCount = $subjectPeriodAssignment->GetTakeQuizCount($subject_period_assignment_quiz_class_id, $student_id);
        $max_submission = $subjectPeriodAssignment->GetMaxSubmission();
        // $teacher_course_id =  $_SESSION['teacher_course_id'];

        // if student doesnt have token quiz
        // they be would redirected to the given route.
        if(!isset($_SESSION['token_quiz'])){
            // Or give an message saying that the student
            // have been submit their answer.
            header("Location: student_assignment_view.php?subject_period_assignment_id=$subject_period_assignment_id&tc_id=6");
            // echo "qweqwe";
            exit(); 
        }

        if($doesQuizHadTaken == true && $takeQuizCount >= $max_submission){
            header("Location: student_assignment_view.php?subject_period_assignment_id=$subject_period_assignment_id&tc_id=6");
            // echo "qweqwe";
            exit();
        }


        // $exam_star_time = date('Y-m-d H:i:s', strtotime("2023-02-20 17:51:00"));
        // $duration = '5' . ' minute';
        // $exam_end_time = strtotime($exam_star_time . '+' . $duration);

        // $exam_end_time = date('Y-m-d H:i:s', $exam_end_time);
        // $remaining_minutes = strtotime($exam_end_time) - time();

        $remaining_minutes = $subjectPeriodAssignment->GetStudentQuizTime($subject_period_assignment_quiz_class_id,
            $student_id);

        // To be exact on the countdown in timer.ajax.php
        $remaining_minutes = $remaining_minutes + 2;

        // echo $remaining_minutes;
        // $remaining_minutes = 0;

        $query = $this->con->prepare("SELECT * FROM subject_period_assignment_quiz_question
                WHERE subject_period_assignment_id=:subject_period_assignment_id");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();

        // Generate necessary session for timer.

        $generateSession = "";

        if($query->rowCount() > 0){

            $_SESSION['subject_period_assignment_quiz_class_id'] = $subject_period_assignment_quiz_class_id;
            $_SESSION['student_id'] = $student_id;

            $q = 1;
            while($rowQ = $query->fetch(PDO::FETCH_ASSOC)){
                $generateSession .= "
                    <div style='margin-left:5px;' id='select_$q'>
                        <button onclick='return false;' se='$q' class='selectClick btn btn-sm btn-outline-primary'>$q</button>
                    </div>
                ";

                $q++;
            }
        }

        $table = "
            <table class='questions-table table'>
                <div class='timer_section'>
                    <div align='center'>
                        <div id='exam_timerv2' data-timer='$remaining_minutes' 
                        style='max-width:400px; width: 100%; height: 180px;'></div>
                    </div>

                    <p id='timerv2'></p>
                    <div id='question_nav' style='display: flex;'>
                        $generateSession
                    </div>
                <div id='msg'></div>
            </div>
                <tbody>
                    <tr>
                        <th>#</th>
                        <th>Questions</th>
                    </tr>
        ";

        // TODO
        $doesStudentAnswerTheQuiz = true;
        $student_id = $this->userLoggedInObj->GetId();

        if($doesStudentAnswerTheQuiz == true){

            $query = $this->con->prepare("SELECT * FROM subject_period_assignment_quiz_question
                WHERE subject_period_assignment_id=:subject_period_assignment_id");

            $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
            $query->execute();

            // Each student quiz class table. 
            $student_period_assignment_quiz_id = $this->GetStudentQuizClassTable($subject_period_assignment_quiz_class_id,
                $student_id);

            if($query->rowCount() > 0){

                $i=1;

                while($row = $query->fetch(PDO::FETCH_ASSOC)){

                    $subject_period_assignment_quiz_question_id = $row['subject_period_assignment_quiz_question_id'];
                    $question_type_id = $row['question_type_id'];
                    $question_text = $row['question_text'];


                    $queryv2 = $this->con->prepare("SELECT * FROM student_period_assignment_quiz_question_answer
                        WHERE subject_period_assignment_quiz_question_id=:subject_period_assignment_quiz_question_id
                        AND student_period_assignment_quiz_id=:student_period_assignment_quiz_id
                        AND student_id=:student_id");

                    $queryv2->bindValue(":subject_period_assignment_quiz_question_id", $subject_period_assignment_quiz_question_id);
                    $queryv2->bindValue(":student_period_assignment_quiz_id", $student_period_assignment_quiz_id);
                    $queryv2->bindValue(":student_id", $student_id);
                    $queryv2->execute();

                    $my_answer = "";

                    if($queryv2->rowCount() > 0){
                        $queryv2 = $queryv2->fetch(PDO::FETCH_ASSOC);

                        $my_answer = $queryv2['my_answer'];
                    }

                    // True or False
                    if($question_type_id == 2){
                        
                        $trueFalseButtonRadioAss = "clickRadioSubmitAssQuizTF(this,
                            $subject_period_assignment_quiz_question_id,
                            $student_id ,$subject_period_assignment_id,
                            $student_period_assignment_quiz_id)";
                        
                        $radioButtonSampTrue = $this->radioButtonTrue($subject_period_assignment_quiz_question_id,
                            $trueFalseButtonRadioAss, $my_answer, "True");

                        $radioButtonSampFalse = $this->radioButtonTrue($subject_period_assignment_quiz_question_id,
                            $trueFalseButtonRadioAss, $my_answer, "False");

                        $table .= "
                            <tr id='q_$i' class='questions'>
                                <td width='30'>$i</td>
                                <td id='qa'>
                                    $question_text
                                    $radioButtonSampTrue
                                    $radioButtonSampFalse

                                    <button onclick='return false;' pv=$i id='prevBtn_$i' class='prevq btn btn-primary btn-sm'>Prev Question</button>
                                    <button onclick='return false;' qn=$i class='nextq btn btn-success btn-sm'>Next Question</button>
                                    <input type='hidden' name='x-$i' value='$subject_period_assignment_quiz_question_id'>
                                
                                    <input type='hidden' name='x' value='$i'>
                                </td>
                            </tr>
                        ";
                    }

                    // Multiple
                    if($question_type_id == 1){

                        $multiple = $this->con->prepare("SELECT * FROM subject_period_assignment_quiz_question_answer
                            WHERE subject_period_assignment_quiz_question_id=:subject_period_assignment_quiz_question_id");

                        $multiple->bindValue(":subject_period_assignment_quiz_question_id", $subject_period_assignment_quiz_question_id);
                        $multiple->execute();

                        $multipleChoiceResult = "";

                        if($multiple->rowCount() > 0){

                            $arrayAlphabet = [];
                            $arrayMulti = [];

                            array_push($arrayAlphabet, "A");
                            array_push($arrayAlphabet, "B");
                            array_push($arrayAlphabet, "C");
                            array_push($arrayAlphabet, "D");

                            while($rowMul = $multiple->fetch(PDO::FETCH_ASSOC)){
                                array_push($arrayMulti, $rowMul);
                            }

                            foreach($arrayMulti as $index => $val){

                                $multiQuestionIndex = $index + 1;

                                $letter = $arrayAlphabet[$index];

                                $choices = $val['choices'];
                                $answer_text = $val['answer_text'];
                                $subject_period_assignment_quiz_question_answer_id = $val['subject_period_assignment_quiz_question_answer_id'];

                                // $trueFalseButtonRadioAss = "clickRadioSubmitAssQuizTF(this,
                                //     $subject_period_assignment_quiz_question_id, $student_id ,$subject_period_assignment_id)";

                                
                                //  
                                $multipleAnswerButton = "multipleRadioButtonSubmitv2(
                                    this, $subject_period_assignment_quiz_question_id,
                                    $student_id, $subject_period_assignment_id,
                                    $subject_period_assignment_quiz_question_answer_id,
                                    $student_period_assignment_quiz_id)";

                                $queryMultiMyAnswer = $this->con->prepare("SELECT * FROM student_period_assignment_multi_question_answer
                                    WHERE subject_period_assignment_quiz_question_id=:subject_period_assignment_quiz_question_id
                                    AND student_id=:student_id
                                    AND student_period_assignment_quiz_id=:student_period_assignment_quiz_id
                                    LIMIT 1");

                                // $queryMultiMyAnswer->bindValue(":subject_period_quiz_question_answer_id", $subject_period_quiz_question_answer_id);
                                $queryMultiMyAnswer->bindValue(":subject_period_assignment_quiz_question_id", $subject_period_assignment_quiz_question_id);
                                $queryMultiMyAnswer->bindValue(":student_id", $student_id);
                                $queryMultiMyAnswer->bindValue(":student_period_assignment_quiz_id", $student_period_assignment_quiz_id);
                                
                                $queryMultiMyAnswer->execute();

                                $myAnswerInMul = "";
                                if($queryMultiMyAnswer->rowCount() > 0){
                                    $queryMultiMyAnswer = $queryMultiMyAnswer->fetch(PDO::FETCH_ASSOC);
                                    $myAnswerInMul = $queryMultiMyAnswer['my_answer'];
                                }
                                $inputClick = "
                                    <input onclick='$multipleAnswerButton' type='radio' value='$choices' name='q-$subject_period_assignment_quiz_question_id'>

                                ";
                                if($myAnswerInMul == $letter){
                                    $inputClick = "
                                        <input onclick='$multipleAnswerButton' checked type='radio' value='$choices' name='q-$subject_period_assignment_quiz_question_id'>
                                    ";    
                                } 
                                $multipleChoiceResult .= "
                                    $letter: <input type='text' value='$answer_text' size='40' class='mt-2 mb-2'>
                                    $inputClick
                                    <br>

                                ";
                            }
                        }

                        $table .= "
                                <tr id='q_$i' class='questions'>
                                    <td width='30'>$i</td>
                                <td id='qa'>
                                    $question_text
                                    $multipleChoiceResult

                                    <button onclick='return false;' pv=$i id='prevBtn_$i' class='prevq btn btn-primary btn-sm'>Prev Question</button>
                                    <button onclick='return false;' qn=$i class='nextq btn btn-success btn-sm'>Next Question</button>
                                    <input type='hidden' name='x-$i' value='$subject_period_assignment_quiz_question_id'>
                                
                                    <input type='hidden' name='x' value='$i'>
                                </td>
                            </tr>
                        ";
                    }

                    $i++;
                }
            }
        }

        $table.= "
                </tbody>
            </table>";
        return $table;
    }

    private function GetStudentQuizTime($subject_period_quiz_class_id,
            $student_id){

            
    }


    private function GetStudentQuizClassTable($subject_period_assignment_quiz_class_id,
        $student_id){

        $query = $this->con->prepare("SELECT student_period_assignment_quiz_id FROM student_period_assignment_quiz
            WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
            AND student_id=:student_id");


        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->fetchColumn();
    }

    private function radioButtonTrue($subject_period_assignment_quiz_question_id,
        $trueFalseButtonRadioAss, $my_answer, $type){

        // $radioButtonTrue = "
        //     <input name='q-$subject_period_assignment_quiz_question_id' 
        //         id='$subject_period_assignment_quiz_question_id' onclick='$trueFalseButtonRadioAss' type='radio' value='True'> True
        //     <br>
        // ";

        // if($my_answer == "True"){
        //     $radioButtonTrue = "
        //         <input name='q-$subject_period_assignment_quiz_question_id' checked
        //             id='$subject_period_assignment_quiz_question_id' onclick='$trueFalseButtonRadioAss' type='radio' value='True'> True
        //         <br>
        //     ";
        // }
        
        $output = "
            <input name='q-$subject_period_assignment_quiz_question_id'
                id='$subject_period_assignment_quiz_question_id' onclick='$trueFalseButtonRadioAss' type='radio' value='$type'> $type
            <br>
        ";
        if($my_answer == $type){
            $output = "
                <input name='q-$subject_period_assignment_quiz_question_id' checked
                    id='$subject_period_assignment_quiz_question_id' onclick='$trueFalseButtonRadioAss' type='radio' value='$type'> $type
                <br>
            ";
        }

        // else{
        //     $output = "
        //         <input name='q-$subject_period_assignment_quiz_question_id'
        //             id='$subject_period_assignment_quiz_question_id' onclick='$trueFalseButtonRadioAss' type='radio' value='True'> True
        //         <br>
        //     ";
        // }

        return $output;
    }


}
?>