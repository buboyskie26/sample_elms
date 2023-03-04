<?php

    class StudentAssignmentView{

    private $con, $subjectPeriodAssignment, $studentUserLoggedInObj;

    public function __construct($con, $subjectPeriodAssignment,
        $studentUserLoggedInObj)
    {
        $this->con = $con;
        $this->subjectPeriodAssignment = $subjectPeriodAssignment;
        $this->studentUserLoggedInObj = $studentUserLoggedInObj;
    }

    public function create($teacher_course_id){
        
        $generateStudentViewAssignment = $this->ViewStudentAssignmentv2($teacher_course_id);
        $tabsSection = $this->createTabs();

        $subject_title = $this->subjectPeriodAssignment->GetSubjectTitle();

        $teacherCourse = new TeacherCourse($this->con, $teacher_course_id, $this->studentUserLoggedInObj);
        $schoolYear = $teacherCourse->GetSchoolYear();

        return "
            <div class='assignmentStudentSectionHeader'>
                <div class='left'>
                    $subject_title / School Year: $schoolYear
                </div>
            </div>

            <div class='content_outer'>
                $tabsSection
                <div class='tab-content channelContent' id='myTabContent'>
                    $generateStudentViewAssignment
                </div>
            </div>
        ";
    }
    
    private function HasReachedMaxSubmission(){

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();
        $max_submission = $this->subjectPeriodAssignment->GetMaxSubmission();

        $student_id = $this->studentUserLoggedInObj->GetId();

        $hasReached = false;

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
                WHERE subject_period_assignment_id=:subject_period_assignment_id
                AND student_id=:student_id");
            
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();

        $my_submission_count = $query->rowCount();

        if($my_submission_count >= $max_submission){
            $hasReached = true;
        }
        return $hasReached;
    }
    private function ViewStudentAssignmentv2($teacher_course_id){

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();
        // $student_period_assignment_id = $this->student_period_assignment_id;

        $student_id = $this->studentUserLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id
            AND is_final=:is_final
            LIMIT 1");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":is_final", "yes");
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        $output = "";
        $currentGrade = 0;
        $description = "";
        $submission_pic = "";
        
        $passed_date = "";
            
        if($query->rowCount() > 0){
            $query = $query->fetch(PDO::FETCH_ASSOC);

            $currentGrade = $query['grade'];
            $description = $query['description'];
            $submission_pic = $query['assignment_file'];
            
            $passed_date = $query['passed_date'];
            $passed_date = date("F j, g:i a", strtotime($passed_date));
        }

        
            
        // $description = $query['description'];
        // $submission_pic = $query['assignment_file'];

        $assignment_url = $this->subjectPeriodAssignment->GetAssignmentFileUpload();
        $subject_description = $this->subjectPeriodAssignment->GetSubjectPeriodDescription();
        $max_submission = $this->subjectPeriodAssignment->GetMaxSubmission();
        $allow_late_submission = $this->subjectPeriodAssignment->AllowLateSubmission();
        $totalAttempts = $this->TotalAttempts($subject_period_assignment_id, $student_id);

        // $currentGrade = $query['grade'];
        $dateCreation = $this->subjectPeriodAssignment->GetSubjectPeriodCreation();
        $dateCreation = date("M j", strtotime($dateCreation));

        $due_date = $this->subjectPeriodAssignment->GetDueDate();
        $due_date = date("F j, g:i a", strtotime($due_date));

        // $passed_date = $query['passed_date'];
        // $passed_date = date("F j, g:i a", strtotime($passed_date));

        // Todo: Allow Late Submission to the student Validation
        // Delete after you see.

        $maxScore = $this->subjectPeriodAssignment->GetMaxScore();
        $percentageScore = ($currentGrade/$maxScore) * 100;
        // $percentageScore = 0;

        $doesHaveTextBased = "";
        if($description !== ""){
            $doesHaveTextBased = "
                <div class='student_answer'>
                    <h3 class='mb-3'>Text based Answer</h3>
                    <textarea id='summernoteDisabled' class='form-control summernote' name='assignment_description'>$description</textarea>
                </div>
            ";
        }

        $style = "block";

        $hasReachedMaxSubmission = $this->HasReachedMaxSubmission();
        // 1.
        $doesAssignmentChecked = $this->DoesAssignmentBeenChecked();
        $submitOnTime = $this->subjectPeriodAssignment->CheckStudentSubmittedOnTime();
 
        // Enable style for real
        if($hasReachedMaxSubmission == true ||
            $doesAssignmentChecked ||
            $submitOnTime == false
            ){
            $style = "none";
        }

        $assignmentType = $this->subjectPeriodAssignment->GetAssignmentType();
        $getSetQuiz = $this->subjectPeriodAssignment->GetSetQuiz();

        // $mySubmission = "Please pass your assignment!";
        $mySubmission = "Please pass your assignment! TEXT BASED ASSIGNMENT";
        
        $ifStudentSubmittedAss = $this->subjectPeriodAssignment
            ->CheckStudentSubmittedAssignment($subject_period_assignment_id);

        if($ifStudentSubmittedAss == true && $assignmentType != "Quiz"){
            // 
            // jojo
            $queryStudentPeriodAss = $this->con->prepare("SELECT student_period_assignment_id FROM student_period_assignment
                WHERE subject_period_assignment_id=:subject_period_assignment_id
                AND is_final=:is_final
                LIMIT 1");

            $queryStudentPeriodAss->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
            $queryStudentPeriodAss->bindValue(":is_final", "yes");
            $queryStudentPeriodAss->execute();

            $images = "No Image";
            if($queryStudentPeriodAss->rowCount() > 0){
                $student_period_assignment_id = $queryStudentPeriodAss->fetchColumn();
                $images = $this->PopulateStudentImagesAnswer($student_period_assignment_id, $student_id);
            }

            $ouput = "
                <div class='submission_picture'>
                    <img style='height: 270px; 
                        width:500px; max-width:100%;' src='$submission_pic'>
                    <h3 class='mb-3'>Download Links</h3>
                    <a href='$submission_pic' download='$submission_pic'>    
                        <span class='mt-3'>$submission_pic</span>
                    </a>
                </div>
            ";
            $mySubmission = "
                $doesHaveTextBased

                $images

                <div style='margin-left: 1px; margin-top: 12px;' class='form-group row col-3'>
                        <button type='submit' onclick='btnClick' class='btn btn-sm btn-success'>
                            Add to portfolio
                        o</button>
                </div>
            "; 
        }
        else if($assignmentType == "Quiz" && $getSetQuiz == "yes"){
            $mySubmission = "Here are your latest answers";
            $mySubmission .= $this->ShowQuizSubmission($subject_period_assignment_id);
        }


        // $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();
        // $student_period_assignment_id = $this->student_period_assignment_id;

        $student_id = $this->studentUserLoggedInObj->GetId();

        $queryAssQuizClass = $this->con->prepare("SELECT subject_period_assignment_quiz_class_id FROM subject_period_assignment_quiz_class
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            -- AND subject_period_id=:subject_period_id
            LIMIT 1");

        $queryAssQuizClass->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $queryAssQuizClass->execute();
        
        // $subjectPeriodAssQuizClass = new SubjectPeriodAssignmentQuizClass($this->con, 0,
        //     $this->studentUserLoggedInObj);

        $subject_period_assignment_quiz_class_id = $queryAssQuizClass->fetchColumn();


        $assignmentType = $this->subjectPeriodAssignment->GetAssignmentType();

        $prepareAnswerLink = "";
        $subjectDescription = "";
        if($assignmentType == "Dropbox" && $getSetQuiz == "no"){
            $prepareAnswerLink = "
                <a style='display: $style' href='student_assignment_submit.php?subject_period_assignment_id=$subject_period_assignment_id&tc_id=$teacher_course_id'>
                    <button class='btn btn-sm btn-primary' title='Pass your assignment'>
                    <i class='fa-solid fa-plus-circle'></i> Prepare Answer</button>
                </a>
            ";
            $subjectDescription = "
                <a target='_blank' rel='noopener noreferrer' href='$assignment_url'>
                    <p class='description'>
                        $subject_description
                    </p>
                </a>
            ";
        }

        else if($assignmentType == "Quiz" && $getSetQuiz == "yes"){

            $subjectDescription = "
                <p class='description'>
                    $subject_description
                </p>
            ";
            $_SESSION['teacher_course_id'] = $teacher_course_id;
          
            $subject_period_assignment_quiz_class_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentQuizClassId();
            
            
            if($subject_period_assignment_quiz_class_id != 0){

                $_SESSION['teacher_course_id'] = $_GET['tc_id'];

                

                // This link will generate a $_SESSION['teacher_course_id']

                // Check If student does not took the quiz.

                // $doesQuizHadTaken = $this->DoesQuizHadTaken($subject_period_assignment_quiz_class_id, $student_id);
                $doesQuizHadTaken = $this->subjectPeriodAssignment->DoesQuizHadTaken($subject_period_assignment_quiz_class_id, $student_id);
                $doesQuizHadTakenNull = $this->subjectPeriodAssignment->DoesQuizHadTakenTimeFinishNull($subject_period_assignment_quiz_class_id, $student_id);
                $takeQuizCount = $this->subjectPeriodAssignment->GetTakeQuizCount($subject_period_assignment_quiz_class_id, $student_id);

                
                if($doesQuizHadTaken == true){
                    // Check If student taken the quiz.
                    // If taken, check the takequizcount and numberof submission

                    // $takeQuizCount = $this->GetTakeQuizCount($subject_period_assignment_quiz_class_id, $student_id);

                    if($takeQuizCount < $max_submission){

                        // This serves as token to take the quiz
                        // If this was voided. it should prevent them to access
                        // the certain quiz route.
                        // $_SESSION['token_quiz'] = "token_quiz";

                        $takeQuizv2 = "takeQuizv2($subject_period_assignment_quiz_class_id,
                            $student_id, $subject_period_assignment_id, $teacher_course_id)";

                        $subject_description = "Your previous quiz score will be voided. It will start a new fresh start.";
                        
                        $prepareAnswerLink = "
                            <a style='display: $style' onclick='$takeQuizv2' href='student_assignment_submit.php?subject_period_assignment_quiz_class_id=$subject_period_assignment_quiz_class_id'>
                                <button class='btn btn-sm btn-outline-primary' title='Pass your assignment'>
                                <i class='fa-solid fa-plus-circle'></i> Prepare Another Quiz-x1</button>
                            </a>
                        ";
                    }
                    
                    else if($takeQuizCount == $max_submission){
                        $subject_description = "You have used all of your remaining submission on this quiz.";
                    }
                    
                }  
                elseif($doesQuizHadTakenNull == true && $takeQuizCount <= $max_submission){

                    // This is for student who suddenly left the quiz
                    // All of their remaining quiz is now voided.
                    // As long as the deadline did not reached, this can be retakeable. 
                    $subject_description = "Your previous quiz score will be voided. It will start a new fresh start.";
                    // $_SESSION['token_quiz'] = "token_quiz";


                    $takeQuizv2 = "takeQuizv2($subject_period_assignment_quiz_class_id,
                            $student_id, $subject_period_assignment_id, $teacher_course_id)";

                    $prepareAnswerLink = "
                        <a style='display: $style' onclick='$takeQuizv2' href='student_assignment_submit.php?subject_period_assignment_quiz_class_id=$subject_period_assignment_quiz_class_id'>
                            <button class='btn btn-sm btn-outline-primary' title='Pass your assignment'>
                            <i class='fa-solid fa-plus-circle'></i> Prepare Another Quiz-x2</button>
                        </a>";
                }

                else if($doesQuizHadTaken == false){
                    $subject_description = "This is timed quiz.";

                    $takeQuizv2 = "takeQuizv2($subject_period_assignment_quiz_class_id,
                            $student_id, $subject_period_assignment_id, $teacher_course_id)";

                    $prepareAnswerLink = "
                        <a style='display: $style' onclick='$takeQuizv2' href='student_assignment_submit.php?subject_period_assignment_quiz_class_id=$subject_period_assignment_quiz_class_id'>
                            <button class='btn btn-sm btn-primary' title='Pass your assignment'>
                            <i class='fa-solid fa-plus-circle'></i> Prepare Quiz</button>
                        </a>
                    ";
                }

            }else{
                echo "subject_period_assignment_quiz_class_id is not valid. Check";
            }
            
            // For Quiz Type
            $query1 = $this->con->prepare("SELECT subject_period_assignment_quiz_class_id FROM subject_period_assignment_quiz_class
                    WHERE subject_period_assignment_id=:subject_period_assignment_id");

            $query1->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
            $query1->execute();

            if($query1->rowCount() > 0){
                $subject_period_assignment_quiz_class_id = $query1->fetchColumn();

                // echo $subject_period_assignment_quiz_class_id;
                // echo "<br>";

                $query2 = $this->con->prepare("SELECT * FROM student_period_assignment_quiz
                    WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
                    AND student_id=:student_id
                    LIMIT 1");

                $query2->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
                $query2->bindValue(":student_id", $student_id);
                $query2->execute();

                if($query2->rowCount() > 0){
                    $query2 = $query2->fetch(PDO::FETCH_ASSOC);

                    $totalAttempts = $query2['take_quiz_count'];

                    $total_score = $query2['total_score'];

                    $currentGrade = $total_score;

                    $percentageScore = ($currentGrade/$maxScore) * 100;

                    if($query2['time_finish'] != NULL){
                        $passed_date = $query2['time_finish'];
                        $passed_date = date("F j, g:i a", strtotime($passed_date));
                    }
                }
            }
        }

        // rrr
        $rightColumn = $this->rightColumn($maxScore, $dateCreation,
            $due_date, $currentGrade, $percentageScore, $max_submission,
            $totalAttempts, $allow_late_submission, $passed_date, $doesAssignmentChecked);
       
        // yuyu
        
        if($assignmentType == "Dropbox" && $getSetQuiz == "no"){
            $a = "
                <a target='_blank' rel='noopener noreferrer' href='$assignment_url'>
                    <p class='description'>
                        $subject_description
                    </p>
                </a>
            ";
        }
        else if($assignmentType == "Quiz" && $getSetQuiz == "yes"){
            $a = "
                <p class='description'>
                    $subject_description
                </p>
            ";
        }

        
        $output = "
            <div class='tab-pane fade show active' id='instructions' role='tabpanel' aria-labelledby='home-tab'>
                <div class='student_assignment_table_content'>
                    <div class='leftColumn_assignment'>
                        <h3>Instructions</h3>
                            <div class='description_container'>
                                $subjectDescription
                            </div>
                            $prepareAnswerLink
                    </div>  
                    $rightColumn
                </div>  
            </div>  

            <div class='tab-pane fade' id='submission' role='tabpanel' aria-labelledby='profile-tab'>
                <div class='student_assignment_table_content' >
                    <div class='leftColumn_assignment'>

                        <h3>Submissions</h3>
                        $mySubmission
                    </div>

                    $rightColumn
                </div>
            </div>  
        ";

        return $output;
    }

    private function PopulateStudentImagesAnswer($student_period_assignment_id, $student_id){

        $queryStudentAnswers = $this->con->prepare("SELECT * FROM student_period_assignment_file
                WHERE student_period_assignment_id=:student_period_assignment_id
                AND student_id=:student_id");
            
        $queryStudentAnswers->bindValue(":student_period_assignment_id", $student_period_assignment_id);
        $queryStudentAnswers->bindValue(":student_id", $student_id);
        $queryStudentAnswers->execute();

        $images = "No image answer.";

        if($queryStudentAnswers->rowCount() > 0){
            while($rowAns = $queryStudentAnswers->fetch(PDO::FETCH_ASSOC)){
                $submission_pic = $rowAns['assignment_file_path'];

                $images .= "
                    <div class='submission_picture'>
                        <img style='height: 270px; width:500px;' src='$submission_pic'>
                        <a href='$submission_pic' download='$submission_pic'>    
                            <span class='mt-3'>$submission_pic</span>
                        </a>
                    </div>
                ";
            }
        }

        return $images;
    }


    private function ShowQuizSubmission($subject_period_assignment_id){

        $output = "";

        $queryAssQuizClass = $this->con->prepare("SELECT subject_period_assignment_id FROM subject_period_assignment_quiz_class
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            LIMIT 1");

        $queryAssQuizClass->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $queryAssQuizClass->execute();

        if($queryAssQuizClass->rowCount() > 0){

            $subject_period_assignment_id = $queryAssQuizClass->fetchColumn();

            $query = $this->con->prepare("SELECT * FROM subject_period_assignment_quiz_question
                WHERE subject_period_assignment_id=:subject_period_assignment_id
                LIMIT 1");

            $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
            $query->execute();

            $query2 = $this->con->prepare("SELECT * FROM subject_period_assignment_quiz_question
                WHERE subject_period_assignment_id=:subject_period_assignment_id
                 ");

            $query2->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
            $query2->execute();

            $student_id = $this->studentUserLoggedInObj->GetId();

            if($query2->rowCount() > 0){
                $i= 1;

                $subject_period_assignment_quiz_class_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentQuizClassId($subject_period_assignment_id);

                // Each student quiz class table. 
                $student_period_assignment_quiz_id = $this->GetStudentQuizClassTable($subject_period_assignment_quiz_class_id,
                    $student_id);

                while($row = $query2->fetch(PDO::FETCH_ASSOC)){

                    $subject_period_assignment_quiz_question_id = $row['subject_period_assignment_quiz_question_id'];

                    $question_text = $row['question_text'];
                    $question_answer = $row['question_answer'];
                    $question_type_id = $row['question_type_id'];
                    $points = $row['points'];

                    $my_answer_in_TF = "";
                    $my_answer_in_multipleQuestion = "";

                    $queryMulti = $this->con->prepare("SELECT * FROM student_period_assignment_multi_question_answer
                        WHERE subject_period_assignment_quiz_question_id=:subject_period_assignment_quiz_question_id
                        AND student_id=:student_id 
                        AND student_period_assignment_quiz_id=:student_period_assignment_quiz_id");
                        
                    $queryMulti->bindValue(":subject_period_assignment_quiz_question_id", $subject_period_assignment_quiz_question_id);
                    $queryMulti->bindValue(":student_id", $student_id);
                    $queryMulti->bindValue(":student_period_assignment_quiz_id", $student_period_assignment_quiz_id);

                    $queryMulti->execute();

                    if($queryMulti->rowCount() > 0){
                        $queryMulti = $queryMulti->fetch(PDO::FETCH_ASSOC);

                        if($question_type_id == 1){
                            $my_answer_in_multipleQuestion = $queryMulti['my_answer'];
                        }
                        
                    }

                    $queryTF = $this->con->prepare("SELECT * FROM student_period_assignment_quiz_question_answer
                        WHERE subject_period_assignment_quiz_question_id=:subject_period_assignment_quiz_question_id
                        AND student_id=:student_id
                        AND student_period_assignment_quiz_id=:student_period_assignment_quiz_id
                        
                        LIMIT 1");

                    $queryTF->bindValue(":subject_period_assignment_quiz_question_id", $subject_period_assignment_quiz_question_id);
                    $queryTF->bindValue(":student_id", $student_id);
                    $queryTF->bindValue(":student_period_assignment_quiz_id", $student_period_assignment_quiz_id);
                    $queryTF->execute();

                    if($queryTF->rowCount() > 0){
                        $queryTF = $queryTF->fetch(PDO::FETCH_ASSOC);

                        if($question_type_id == 2){
                            $my_answer_in_TF = $queryTF['my_answer'];
                        }
                    }

                    if($question_type_id == "2"){
                        $myAnswerOutput="
                            <div style='display: flex'>
                                <b>Response:</b>
                                <span style='margin-left: 10px'>$my_answer_in_TF</span>
                            </div>
                        ";
                    }
                    else if($question_type_id == "1"){
                        $myAnswerOutput="
                            <div style='display: flex'> 
                                <b>Response:</b>
                                <span style='margin-left: 10px'>$my_answer_in_multipleQuestion</span>
                            </div>
                        ";
                    }

                    $enableShowAnswer = $this->subjectPeriodAssignment->DoesShowCorrectAnswer(); 
                    
                    // Default is it wont show the correct answer. Teacher can set it
                    $showingCorrectAnswer = "";
                    if($enableShowAnswer == true){
                        // Correct Answer
                        $showingCorrectAnswer = "
                        <div style='display: flex'>
                            <b>Correct Answer:</b>
                            <span style='margin-left: 10px'>$question_answer</span>
                        </div>";
                    }

                    // Showing out of score
                    $correctAnswer = "";

                    if($question_type_id == "2"){
                        if($my_answer_in_TF == $question_answer){
                            $correctAnswer = "
                                    <b style='color: green;'>Score</b><span> $points out of $points</span> <i style='color: green;' class='fas fa-check'></i>
                            ";
                        }else{
                            $correctAnswer = "
                                <b style='color: orange;'>Score</b><span> 0 out of $points</span> <i style='color: orange;' class='fas fa-times'></i>
                            ";
                        }
                    }
                    else if($question_type_id == "1"){
                        if($my_answer_in_multipleQuestion == $question_answer){
                            $correctAnswer = "
                                    <b style='color: green;'>Score</b><span> $points out of $points</span> <i style='color: green;' class='fas fa-check'></i>
                            ";
                        }else{
                            $correctAnswer = "
                                <b style='color: orange;'>Score</b><span> 0 out of $points</span> <i style='color: orange;' class='fas fa-times'></i>
                            ";
                        }
                    }
                    
                    $output .= "
                        <div class='my_quiz_answer_container'>
                                <h3>Question $i</h3>

                                <div style='display: flex'>
                                    $question_text
                                </div>

                                $myAnswerOutput
                                $showingCorrectAnswer
                                <div class='my_quiz_score mt-2'>
                                    $correctAnswer
                                </div>
                        </div>
                    ";

                    $i++;
                }
            }
        }

        return $output;
    }
 
    private function GetTakeQuizCount($subject_period_assignment_quiz_class_id, $student_id){

        $query = $this->con->prepare("SELECT take_quiz_count FROM student_period_assignment_quiz
            WHERE  subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
            AND student_id=:student_id
            AND time_taken IS NOT NULL
            LIMIT 1");
        
        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();
        return $query->fetchColumn();
    }
    private function DoesQuizHadTaken($subject_period_assignment_quiz_class_id, $student_id) : bool{

        $query = $this->con->prepare("SELECT * FROM student_period_assignment_quiz
                WHERE  subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
                AND student_id=:student_id
                AND time_taken IS NOT NULL
                LIMIT 1");

        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        if($query->fetchColumn() > 0){
            return true;
        }
        return false;
    }

    private function GetStudentQuizClassTable($subject_period_assignment_quiz_class_id,
        $student_id){
        
        // $subject_period_assignment_quiz_class_id = $this->con->prepare("SELECT subject_period_assignment_quiz_class_id FROM subject_period_assignment_quiz_class
        //     WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
        //     AND student_id=:student_id");

        $query = $this->con->prepare("SELECT student_period_assignment_quiz_id FROM student_period_assignment_quiz
            WHERE subject_period_assignment_quiz_class_id=:subject_period_assignment_quiz_class_id
            AND student_id=:student_id");


        $query->bindValue(":subject_period_assignment_quiz_class_id", $subject_period_assignment_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->fetchColumn();

    }
    private function DoesAssignmentBeenChecked() : bool{

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();

        $student_id = $this->studentUserLoggedInObj->GetId();

        $isChecked = false;
        // echo "hehey";
        $query = $this->con->prepare("SELECT * FROM student_period_assignment
                WHERE subject_period_assignment_id=:subject_period_assignment_id
                AND student_id=:student_id
                AND is_final=:is_final
                AND grade > 0
                LIMIT 1");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":is_final", "yes");

        $query->execute();

        $asd = $query->fetch(PDO::FETCH_ASSOC);
        // echo $asd['file_name'];
        // echo $query->rowCount();

        return $query->rowCount() > 0;
    }

    private function TotalAttempts($subject_period_assignment_id, $student_id){

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id
            AND student_id=:student_id");

        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->bindValue(":student_id", $student_id);

        $query->execute();

        return $query->rowCount();
    }

    private function rightColumn($maxScore, $dateCreation, $due_date,
        $currentGrade, $percentageScore, $max_submission, $totalAttempts,
        $allow_late_submission, $passed_date, $doesAssignmentChecked){

        $doesAllow = "
            <p>Allow late submissions: <i style='color: red;' class='fas fa-times'></i></p>
        ";
        if($allow_late_submission === "yes"){
            $doesAllow = "
                <p>Allow late submissions: <i style='color: green;' class='fas fa-check'></i></p>
            ";
        }
            
        // $isChecked = $this->DoesAssignmentBeenChecked();
        $assignmentType = $this->subjectPeriodAssignment->GetAssignmentType();

        $waitingForGrade = "
            <h4>Score: <span>$currentGrade/$maxScore</span></h4> <h3>$percentageScore%</h3>
        ";

        if($doesAssignmentChecked == false){
           $waitingForGrade = "
                <h4>Score</h4>
                <div>
                    <i class='fas fa-hourglass'></i> <span>Waiting for grade</span>
                </div>
            "; 
        }

        $assignmentType = $this->subjectPeriodAssignment->GetAssignmentType();
        $getSetQuiz = $this->subjectPeriodAssignment->GetSetQuiz();

        if($assignmentType == "Quiz" && $getSetQuiz == "yes"){
            
            $waitingForGrade = "
            <h4>Score: <span>$currentGrade/$maxScore</span></h4> <h3>$percentageScore%</h3>
            ";
        }



        if($passed_date === ""){
            $passed_date = "
                <i style='color: red;'class='fas fa-times'></i>
            ";
        }

        $output = "
            <div class='rightColumn_assignment'>
                <div class='assignment_container'>
                        <div class='assignment_first'>
                            <h3>Assignment</h3>
                            <p>Type: $assignmentType</p>
                            <p>Max score: $maxScore</p>
                            <p>Start: $dateCreation</p>
                            <p>Due: $due_date</p>
                        </div>
                        <div class='assignment_score'>
                            $waitingForGrade
                        </div>
                        <div class='submission_details'>
                            <div class='submission_details_inner'>
                                <h3>Submission</h3>
                                <p>Submitted: $passed_date</p>
                                <p>Attempts: $totalAttempts</p>
                                <p>Max attempts: $max_submission</p>
                                $doesAllow
                            </div>
                        </div>
                </div>
            </div>    
        ";
        return $output;
    }

    private function ViewStudentAssignment(){

        $type_name = $this->subjectPeriodAssignment->GetTypeName();
        $due_date = $this->subjectPeriodAssignment->GetDueDate();
        $creationDate = $this->subjectPeriodAssignment->GetSubjectPeriodCreation();
        $description = $this->subjectPeriodAssignment->GetSubjectPeriodDescription();
        $max_submission = $this->subjectPeriodAssignment->GetMaxSubmission();
        $submitOnTime = $this->subjectPeriodAssignment->CheckStudentSubmittedOnTime();

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();


        // Get your number of submission then compare
        $query = $this->con->prepare("SELECT * FROM student_period_assignment
            WHERE subject_period_assignment_id=:subject_period_assignment_id");
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);
        $query->execute();

        $my_submission_count = $query->rowCount();

        $style = "block";
        // $date_now = date("Y-m-d H:i:s");

        if($my_submission_count > $max_submission || $submitOnTime == false){
            // Enabled style variable for real.
            // $style = "none";
        }

        $table = "
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th class='text-center'>Date Upload</th>
                        <th class='text-center'>File Name</th>
                        <th class='text-center'>Description</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>$creationDate</td>
                        <td>$type_name</td>
                        <td>$description</td>
                        <td>
                            <button  class='btn btn-sm btn-primary' title='Download'>
                                <i class='fas fa-download'></i>
                            </button>
                        </td>
                        <td>
                            <a style='display: $style' href='student_assignment_submit.php?subject_period_assignment_id=$subject_period_assignment_id'>
                                <button class='btn btn-sm btn-success' title='Pass your assignment'>
                                <i class='fa-solid fa-plus-circle'></i> Submit Assignment</button>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        ";
        return $table;
    }

    private function createTabs(){

        $display = "block";

        $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();
        $ifStudentSubmittedAss = $this->subjectPeriodAssignment
            ->CheckStudentSubmittedAssignment($subject_period_assignment_id);
       
        $assignmentType = $this->subjectPeriodAssignment->GetAssignmentType();
        $getSetQuiz = $this->subjectPeriodAssignment->GetSetQuiz();

        if($ifStudentSubmittedAss == false && $assignmentType != "Quiz" && $getSetQuiz != "yes"){
            $display = "none";

        }

        $subject_period_assignment_quiz_class_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentQuizClassId();

        $student_id = $this->studentUserLoggedInObj->GetId();

        $doesQuizHadTakenNull = $this->subjectPeriodAssignment
            ->DoesQuizHadTakenTimeFinishNull($subject_period_assignment_quiz_class_id,
            $student_id);


        $submission = "
            <a class='nav-link' id='submission-tab' data-bs-toggle='tab' href='#submission' role='tab' 
                aria-controls='submission' aria-selected='false'>
                Submissions
            </a>
        ";
        if($assignmentType == "Quiz" && $getSetQuiz == "yes" 
            && $doesQuizHadTakenNull == true){
            $display = "none";
            // This will reload the page if someone change the display none.
            $notAllowedBtn = "notAllowedToSeeSubmission()";
            $submission = "
                 <a onclick='$notAllowedBtn' class='nav-link' id='submission-tab' data-bs-toggle='tab' href='#submission' role='tab' 
                        aria-controls='submission' aria-selected='false'>
                        Submissions
                </a>
            ";
        }



        return "
            <ul class='nav nav-tabs' role='tablist'>
                <li class='nav-item'>
                    <a class='nav-link active' id='instructions-tab' data-bs-toggle='tab' 
                        href='#instructions' role='tab' aria-controls='instructions' aria-selected='true'>
                        Instructions
                    </a>
                </li>
                <li style='display: $display;' class='nav-item' >
                   $submission
                </li>
            </ul>
        ";
    }
}
?>