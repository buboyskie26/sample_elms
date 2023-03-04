<?php

    class SubjectPeriodQuiz{

    private $con, $userLoggedInObj, $sqlData;

    public function __construct($con, $input, $userLoggedInObj)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
        $this->sqlData = $input;

        if(!is_array($input)){

            $query = $this->con->prepare("SELECT * FROM subject_period_quiz 
                WHERE subject_period_quiz_id = :subject_period_quiz_id");

            $query->bindParam(":subject_period_quiz_id", $input);
            $query->execute();

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }

    }
    public function GetSubjectPeriodQuizTeacherId(){
        return isset($this->sqlData['teacher_id']) ? $this->sqlData["teacher_id"] : 0; 
    }
    public function GetQuizId(){
        return isset($this->sqlData['subject_period_quiz_id']) ? $this->sqlData["subject_period_quiz_id"] :""; 
    }
    public function GetQuizTitle(){
        return isset($this->sqlData['quiz_title']) ? $this->sqlData["quiz_title"] :""; 
    }
    
    public function GetQuizDescription(){
        return isset($this->sqlData['quiz_description']) ? $this->sqlData["quiz_description"] : ""; 
    }

    public function GetQuizDueDate(){
        return isset($this->sqlData['due_date']) ? $this->sqlData["due_date"] : ""; 
    }

    public function create($subject_period_id, $teacher_id){
        $createForm = $this->createForm($subject_period_id, $teacher_id);
        $quiz_index = $this->GenerateQuizList($subject_period_id, $teacher_id);

        return "
            <div class='quiz_section_inner'>

                <div class='quiz_index'>
                    $quiz_index
                </div>
            </div>
            
        ";
    }
    
    private function GenerateQuizList($subject_period_id, $teacher_id){
        $table = "
            <section class='intro'>
                <div class='bg-image h-100' style='background-color: transparent;'>
                    <div class='container'>
                        
                        <div class='row justify-content-center'>
                            <div class='col-12'>
                                
                                <div class='card shadow-2-strong' style='background-color: #333;'>
                                    
                                    <div style='background-color: transparent; text-align: right' class='form-control  '>

                                        <button type='button' id='add_button' class='mr-2 btn btn-info btn-sm'>Add</button>

                                        <a  href='add_subject_period_quiz.php?teacher_id=$teacher_id&subject_period_id=$subject_period_id'>
                                            <button class='btn btn-primary btn-sm'>Add Quiz</button>
                                        </a>
                                        <a  href='add_subject_period_quiz_class.php?teacher_id=$teacher_id&subject_period_id=$subject_period_id'>
                                            <button class='btn btn-success btn-sm'>Set Quiz to class</button>
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
                                                        
                                                    </tr>
                                                </thead>
                                            
        ";
        
        $query = $this->con->prepare("SELECT * FROM subject_period_quiz
            WHERE subject_period_id=:subject_period_id
            AND teacher_id=:teacher_id");

        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":teacher_id", $teacher_id);
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

        $subject_period_quiz_id = $row['subject_period_quiz_id'];

        $quiz_title = $row['quiz_title'];
        $quiz_description = $row['quiz_description'];
        $date_creation = $row['date_creation'];

        $teacherCreationQuizId = $this->GetSubjectPeriodQuizTeacherId();

        $questions = "Invalid User";
        // echo $teacherCreationQuizId;

        if($row['teacher_id'] === $teacherCreationQuizId){
            $questions = "
            <a href='subject_period_quiz_question.php?subject_period_quiz_id=$subject_period_quiz_id'>
                Questions
            </a>";
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
                    <td>

                        <button spq_id='$subject_period_quiz_id' type='button' class='subjectPeriozQuizEdit btn btn-success btn-sm px-3'>
                            <i class='fas fa-pencil'></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        ";

        return $body;
    }
    public function createForm($subject_period_id, $teacher_id){

        $output = "";

        $output = "
            <form action='add_subject_period_quiz.php?subject_period_id=$subject_period_id&teacher_id=$teacher_id' method='POST' enctype='multipart/form-data'>
                    <div class='form-group row'>
                        
                        <input class='form-control mb-2' type='text' name='quiz_title' placeholder='Quiz Title'>
                        <textarea placeholder='Quiz Description'
                            class='form-control mb-2 summernote' name='quiz_description'></textarea>
                        
                        <input id='online_exam_datetime' class='form-control mb-2' type='text'  name='due_date' >


                        <input type='hidden' name='subject_period_id' value='$subject_period_id'>
                        <input type='hidden' name='teacher_id' value='$teacher_id'>

                    </div>

                    <button type='submit' class='btn btn-primary' name='create_subject_period_quiz'>Save</button>
            </form>
        ";

        return $output;
    }
    public function insertPeriodQuiz($quiz_title, $quiz_description,
        $due_date, $subject_period_id, $teacher_id){
 
        $timestamp = strtotime($due_date); 
        $due_date_final = date('Y-m-d H:i:s', $timestamp);

        $query = $this->con->prepare("INSERT INTO subject_period_quiz(quiz_title, quiz_description,
            due_date, subject_period_id, teacher_id)
            VALUES(:quiz_title, :quiz_description,
            :due_date, :subject_period_id, :teacher_id)");
        
        $query->bindValue(":quiz_title", $quiz_title);
        $query->bindValue(":quiz_description", $quiz_description);
        $query->bindValue(":due_date", $due_date_final);
        $query->bindValue(":subject_period_id", $subject_period_id);
        $query->bindValue(":teacher_id", $teacher_id);
       
        return $query->execute();
    }
    public function CreateTeacherQuizSection($teacherCourseId, $subjectPeriodTypeName){

        $subject_period_quiz_id = 0;

        // Somehing wrong here, check the subject_id.
        // Teacher overall student for its corresponding subject
        $query = $this->con->prepare("SELECT student_id FROM teacher_course_student
            WHERE teacher_course_id=:teacher_course_id");

        $query->bindValue(":teacher_course_id", $teacherCourseId);
        $query->execute();
        $overAllStudents = $query->rowCount();

        $type_name = "";
        $quiz_title = $this->GetQuizTitle();

        // $getUniquePassedStudent = $this->con->prepare("SELECT DISTINCT(student_id) FROM student_period_assignment
        //     WHERE subject_period_assignment_id= :subject_period_assignment_id");

        // $getUniquePassedStudent->bindValue(":subject_period_assignment_id", $subjectPeriodAssId);
        // $getUniquePassedStudent->execute();

        // Todo.
        $overStudentPassed = 0;

        $due_date = "";
        $due_date = $this->GetQuizDueDate();
        $due_date = date("M j", strtotime($due_date));

        return "
            <tbody>
                    <tr>
                        <td>
                            <a href=''>
                            0$subjectPeriodTypeName $quiz_title
                            </a>
                        </td>
                        <td class='text-center'>$overStudentPassed/$overAllStudents</td>
                        <td class='text-center'>$due_date</td>
                        <td></td>
                        <td class='text-center'>
                            <a>
                                <button>
                                    <i class='fas fa-edit'></i>
                                </button>
                            </a>
                        </td>
                    </tr>
            </tbody>
        ";
    }


    public function CreateStudentQuizSection($teacherCourseId,
        $subjectPeriodTypeName, $subject_period_quiz_class_id){
        
        $quizTitle = $this->GetQuizTitle();

        $due_date = $this->GetQuizDueDate();
        $due_date = date("M j", strtotime($due_date));
      

        $assignment_upload = "";
        $viewed = "";
        
        $subjectPeriodAssId = "";
       
        $gradedItemsScore =  "";


        $subjectPeriodQuizClass = new SubjectPeriodQuizClass($this->con,
            $subject_period_quiz_class_id, $this->userLoggedInObj);

        $overAllItems = $subjectPeriodQuizClass->GetMaxScore();

        $scoreResults = $subjectPeriodQuizClass->GetStudentQuizScore();;
        
        $submittedIcon = "~";
        
        $doesStudentAnswerTheQuiz = $this->CheckStudentAnsweredTheQuiz($subject_period_quiz_class_id);
        
        $status = "~";

        if($doesStudentAnswerTheQuiz == true){
            $status = "<i style='color: green;' class='fas fa-check'></i>";
            $submittedIcon = "<i style='color: green;' class='fas fa-check'></i>";

        }else if($doesStudentAnswerTheQuiz == false){
            $status = "<i style='color: red;' class='fas fa-times'></i>";
            // $submittedIcon = "<i style='color: orange;' class='fas fa-flag'></i>";
            

            $date_now = date("Y-m-d H:i:s");
            $deadline_date = $subjectPeriodQuizClass->GetDueDate();

            $allowLateSubmission = $subjectPeriodQuizClass->IsAllowedLateSubmission();

            // Did not passed on time (Deadline)
            if($date_now > $deadline_date && $allowLateSubmission != "yes")
                $submittedIcon = "<i style='color: orange;' class='fas fa-flag'></i>";
            
             
        }

        
        
        return "
             <tbody>
                    <tr>
                        <td>
                            <a 
                            href='student_quiz_view.php?subject_period_quiz_class_id=$subject_period_quiz_class_id&tc_id=$teacherCourseId'>
                            0$subjectPeriodTypeName $quizTitle
                            </a>
                        </td>
                        <td class='text-center'>
                            $submittedIcon
                        </td>
                        <td class='text-center'>$scoreResults/$overAllItems</td>
                        <td class='text-center'>$due_date</td>
                        <td class='text-center'>
                            $status
                        </td>
                    </tr>
            </tbody>
        ";
    }

    public function showQuizLayout($teacher_course_id){

        $generateStudentQuizAssignment = $this->GenerateStudentQuizAssignment($teacher_course_id);
        $tabsSection = $this->createTabs();

        $subject_title = "";

        $teacherCourse = "";
        $schoolYear = "";
        
        // <?php $class_query = mysqli_query($conn,"SELECT * from teacher_class
        //     LEFT JOIN class ON class.class_id = teacher_class.class_id
        //     LEFT JOIN subject ON subject.subject_id = teacher_class.subject_id
        //     where teacher_class_id = '$get_id'") or die(mysqli_error());
        //     $class_row = mysqli_fetch_array($class_query);
        // 

        // LEFT Join
        $query = $this->con->prepare("SELECT * FROM teacher_course
            LEFT JOIN course ON course.course_id  = teacher_course.course_id
            LEFT JOIN subject ON subject.subject_id = teacher_course.subject_id
            WHERE teacher_course_id=:teacher_course_id
            LIMIT 1
            ");

        $query->bindValue(":teacher_course_id", $teacher_course_id);
        // $query->bindValue(":teacher_course.course_id", $teacher_course_id);
        // $query->bindValue(":teacher_course.subject_id", $teacher_course_id);

        $query->execute();

        if($query->rowCount() > 0){
            $query = $query->fetch(PDO::FETCH_ASSOC);

            $schoolYear = $query['school_year_id'];
            $subject_title = $query['subject_title'];

            $queryv2 = $this->con->prepare("SELECT school_year_term FROM school_year
                WHERE school_year_id=:school_year_id
                LIMIT 1");


            $queryv2->bindValue(":school_year_id", $schoolYear);
            $queryv2->execute();

            $schoolYear = $queryv2->fetchColumn();
        }

        return "
            <div class='assignmentStudentSectionHeader'>
                <div class='left'>
                    $subject_title ~ School Year: $schoolYear
                </div>
            </div>

            <div class='content_outer'>
                $tabsSection
                <div class='tab-content channelContent' id='myTabContent'>
                    $generateStudentQuizAssignment
                </div>
            </div>
        ";
    }

    private function GenerateStudentQuizAssignment($teacher_course_id){


        $student_id = $this->userLoggedInObj->GetId();

        $subject_period_quiz_id = $this->GetQuizId();

        $query = $this->con->prepare("SELECT * FROM subject_period_quiz_question
            WHERE subject_period_quiz_id=:subject_period_quiz_id");

        $query->bindValue(":subject_period_quiz_id", $subject_period_quiz_id);
        $query->execute();
        
        $output = "";
        $subject_period_quiz_class_id = $_GET['subject_period_quiz_class_id'];

        $enableShowAnswer = $this->EnableShowAnswer($subject_period_quiz_class_id);

        if($query->rowCount() > 0){
            $questions = "";

            $i = 1;
            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $question_text = $row['question_text'];
                $question_answer = $row['question_answer'];
                $question_type_id = $row['question_type_id'];

                $subject_period_quiz_question_id = $row['subject_period_quiz_question_id'];

                $queryv2 = $this->con->prepare("SELECT * FROM student_period_quiz_question_answer
                    WHERE subject_period_quiz_question_id=:subject_period_quiz_question_id
                    AND student_id=:student_id
                    LIMIT 1");

                $queryv2->bindValue(":subject_period_quiz_question_id", $subject_period_quiz_question_id);
                $queryv2->bindValue(":student_id", $student_id);
                $queryv2->execute();

                $my_answer = "No Answer";
                $correct_answer = "";

                if($queryv2->rowCount() > 0){
                    $queryv2 = $queryv2->fetch(PDO::FETCH_ASSOC);

                    $my_answer = $queryv2['my_answer'];
                    // $correct_answer = $queryv2['question_answer'];
                }

                $queryMulti = $this->con->prepare("SELECT * FROM student_period_quiz_multi_question_answer
                    WHERE subject_period_quiz_question_id=:subject_period_quiz_question_id
                    AND student_id=:student_id
                    LIMIT 1");
                $queryMulti->bindValue(":subject_period_quiz_question_id", $subject_period_quiz_question_id);
                $queryMulti->bindValue(":student_id", $student_id);
                $queryMulti->execute();

                $my_answerMulti = "";
                if($queryMulti->rowCount() > 0){
                    $queryMulti = $queryMulti->fetch(PDO::FETCH_ASSOC);
                    $spqq_id = $queryMulti['subject_period_quiz_question_id'];

                    if($question_type_id == "1" && $spqq_id == $subject_period_quiz_question_id){
                        $my_answerMulti = $queryMulti['my_answer'];

                    }

                }

                $myAnswerOutput = "";

                if($question_type_id == "2"){
                    $myAnswerOutput="
                        <div style='display: flex'>
                            <b>Response:</b>
                            <span style='margin-left: 10px'>$my_answer</span>
                        </div>
                    ";
                }else if($question_type_id == "1"){
                    $myAnswerOutput="
                        <div style='display: flex'>
                            <b>Response:</b>
                            <span style='margin-left: 10px'>$my_answerMulti</span>
                        </div>
                    ";
                }

                $correctAnswer = "";

                if($question_type_id == "2"){
                    if($my_answer == $question_answer){
                        $correctAnswer = "
                                <b style='color: green;'>Score</b><span> 2 out of 2</span> <i style='color: green;' class='fas fa-check'></i>
                        ";
                    }else{
                        $correctAnswer = "
                            <b style='color: orange;'>Score</b><span> 0 out of 2</span> <i style='color: orange;' class='fas fa-times'></i>
                        ";
                    }
                }
                else if($question_type_id == "1"){
                    if($my_answerMulti == $question_answer){
                        $correctAnswer = "
                                <b style='color: green;'>Score</b><span> 2 out of 2</span> <i style='color: green;' class='fas fa-check'></i>
                        ";
                    }else{
                        $correctAnswer = "
                            <b style='color: orange;'>Score</b><span> 0 out of 2</span> <i style='color: orange;' class='fas fa-times'></i>
                        ";
                    }
                }

                

                $showingCorrectAnswer = "";
                if($enableShowAnswer == true){

                    $showingCorrectAnswer = "
                        <div style='display: flex'>
                            <b>Correct Answer:</b>
                            <span style='margin-left: 10px'>$question_answer</span>
                        </div>";
                }

                $questions .= "
                    <h3>Submissions</h3>
                    <span>Here are your latest answers</span>

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

            $subject_period_quiz_class_id = isset($_GET['subject_period_quiz_class_id']) ? $_GET['subject_period_quiz_class_id'] : 0;

            $takeQuizButton = "takeQuiz($subject_period_quiz_class_id, $student_id)";

            $doesStudentAnswerTheQuiz = $this->CheckStudentAnsweredTheQuiz($subject_period_quiz_class_id);
            $doesStudentDidntFinishTheQuiz = $this->CheckStudentDidntFinishTheQuiz($subject_period_quiz_class_id);

            $quizText = "Prepare Quiz";

            $style = "block";
            $styleSubmission = "block";
            
            $quizInstructionText = "This quiz is not timed. Feel free to answer before the deadline.";

            $href = "
                quiz_question.php?subject_period_quiz_class_id=$subject_period_quiz_class_id&tc_id=$teacher_course_id
            ";

            if($doesStudentAnswerTheQuiz == false){
                $styleSubmission = "none";
            }

            $subjectPeriodQuizClass = new SubjectPeriodQuizClass($this->con,
                $subject_period_quiz_class_id, $this->userLoggedInObj);
            
            $answerQuizCount = $this->StudentAnsweredTheQuizCount($subject_period_quiz_class_id);
            $answerQuizCountv2 = $this->StudentAnsweredTheQuizCountv2($subject_period_quiz_class_id);

            $btnColor = "btn-primary";

            // If deadline.

            $date_now = date("Y-m-d H:i:s");
            $deadline_date = $subjectPeriodQuizClass->GetDueDate();

            $allowLateSubmission = $subjectPeriodQuizClass->IsAllowedLateSubmission();
            
            if($date_now > $deadline_date && $allowLateSubmission != "yes"){
                $style = "none";
            }

            // else if($date_now > $deadline_date && $allowLateSubmission == "yes"){
            //     $style = "block";
            // }
            
            
            if($doesStudentAnswerTheQuiz == true){
            
                $max_submission = $subjectPeriodQuizClass->GetMaxAttempt();

                // The student only once taken the quiz
                if($answerQuizCountv2 == $max_submission && $max_submission == 1) {
                    $style = "none";
                    $quizInstructionText = "Successfully taken the Quiz..";
                    $href = "#";
                }

                else if($answerQuizCountv2 == $max_submission && $max_submission > 1) {
                    $style = "none";
                    $quizInstructionText = "You have reached the maximum submission.";
                    $href = "#";
                }
                else if($answerQuizCountv2 < $max_submission) {
                    $style = "block";
                    $quizInstructionText = "Take again the quiz. (Note) your previous score will be voided";
                    $quizText = "Retake Quiz";
                    // $href = "#";
                    $btnColor = "btn-outline-primary";
                }
                //
            }
            else if($doesStudentDidntFinishTheQuiz == true){
                $quizText = "Resume Quiz";
            }

            // TODO. If teacher sets the quiz without any time limit/
           
            if(true){
                $maxScore = "";
                $dateCreation = "";

                $due_date = "";
                $currentGrade = "";
                $percentageScore = "";
                $max_submission = "";

                $totalAttempts = "";
                $allow_late_submission = "";
                $passed_date = "";

                
                $rightColumn = $this->rightColumn($maxScore,$dateCreation,
                    $due_date, $currentGrade,$percentageScore,$max_submission,
                    $totalAttempts, $allow_late_submission, $passed_date);
            }


            $output = "
            
                <div class='tab-pane fade show active' id='instructions' role='tabpanel' aria-labelledby='home-tab'>
                    <div class='student_assignment_table_content'>
                        <div class='leftColumn_assignment'>
                            <h3>Instructions</h3>
                            <div class='description_container'>
                                <p class='description'>
                                    $quizInstructionText
                                </p>
                            </div>
                            <div>
                                <a style='width: 120px; display: $style' onclick='$takeQuizButton' style='display: $style' 
                                    href=$href>
                                    <button class='btn btn-sm $btnColor' title='Take the quiz now!'>
                                        <i class='fa-solid fa-plus-circle'></i> $quizText
                                    </button>
                                </a>
                            </div>

                        </div>  
                        $rightColumn


                    </div>  
                </div>  

                <div style='display: $styleSubmission;' class='tab-pane fade' id='submission' role='tabpanel' aria-labelledby='profile-tab'>
                    <div class='student_assignment_table_content' >
                        <div class='leftColumn_assignment'>
                            $questions
                        </div>

                    </div>
                </div>  

            ";
        }

        // $subjectPeriodQuizClass = new SubjectPeriodQuizClass($this->con, $this->userLoggedInObj,
        //     $subject_period_id);

        return $output;
    }

    private function rightColumn($maxScore, $dateCreation, $due_date,
        $currentGrade, $percentageScore, $max_submission, $totalAttempts,
        $allow_late_submission, $passed_date){

        $student_id = $this->userLoggedInObj->GetId();

        $subject_period_quiz_class_id =  isset($_GET['subject_period_quiz_class_id']) ? $_GET['subject_period_quiz_class_id'] : 0;
        $subjectPeriodQuizClass = new SubjectPeriodQuizClass($this->con,
            $_GET['subject_period_quiz_class_id'], $this->userLoggedInObj);
        

        // TODO
        $maxScore = $subjectPeriodQuizClass->GetMaxScore();

        $dateCreation = $subjectPeriodQuizClass->GetDateCreation();
        $dateCreation = date("M j", strtotime($dateCreation));
        
        $due_date = $subjectPeriodQuizClass->GetDueDate();
        $due_date = date("F j, g:i a", strtotime($due_date));

        $currentGrade = $subjectPeriodQuizClass->GetStudentQuizScore();

        $percentageScore = ($currentGrade/$maxScore) * 100;
        $percentageScore = round($percentageScore, 0);
        // TODO
        $max_submission = $subjectPeriodQuizClass->GetMaxAttempt();

        $totalAttempts = $subjectPeriodQuizClass->GetStudentAttemptOnQuiz();
        $allow_late_submission = $subjectPeriodQuizClass->IsAllowedLateSubmission();

        $passed_date = $this->GetTimeFinish($subject_period_quiz_class_id, $student_id);
        $passed_date = date("F j, g:i a", strtotime($passed_date));

        $doesAllow = "
            <p>Allow late submissions: <i style='color: red;' class='fas fa-times'></i></p>
        ";
        if($allow_late_submission === "yes"){
            $doesAllow = "
                <p>Allow late submissions: <i style='color: green;' class='fas fa-check'></i></p>
            ";
        }
            
        $isChecked = $this->DoesAssignmentBeenChecked();
        
        $waitingForGrade = "
            <h4>Score: <span>$currentGrade/$maxScore</span></h4> <h3>$percentageScore%</h3>
        ";

        // if($isChecked == false){
        //    $waitingForGrade = "
        //     <h4>Score</h4>
        //     <div>
        //         <i class='fas fa-hourglass'></i> <span>Waiting for grade</span>
        //     </div>
        // "; 
        // }

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
                            <p>Type: Quiz</p>
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

    private function GetTimeFinish($subject_period_quiz_class_id, $student_id){

        $query = $this->con->prepare("SELECT time_finish FROM student_period_quiz
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
            AND student_id=:student_id");
        
        $query->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        
        $query->execute();

        return $query->fetchColumn();
    }
    private function DoesAssignmentBeenChecked() : bool{

        $subject_period_assignment_id = 0;

        $student_id = $this->userLoggedInObj->GetId();

        $isChecked = false;

        $query = $this->con->prepare("SELECT * FROM student_period_assignment
                WHERE subject_period_assignment_id=:subject_period_assignment_id
                AND student_id=:student_id
                AND is_final=:is_final
                AND grade > 0
                LIMIT 1");
            
        $query->bindValue(":student_id", $student_id);
        $query->bindValue(":is_final", "yes");
        $query->bindValue(":subject_period_assignment_id", $subject_period_assignment_id);

        $query->execute();

        return $query->rowCount() > 0;
    }

    public function CheckStudentDidntFinishTheQuiz($subject_period_quiz_class_id){

        $student_id = $this->userLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT * FROM student_period_quiz
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
            AND student_id=:student_id
            AND total_score = 0
            AND time_finish IS NULL
            LIMIT 1");
        
        $query->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->rowCount() > 0;
    }

    public function CheckStudentAnsweredTheQuiz($subject_period_quiz_class_id){

        $student_id = $this->userLoggedInObj->GetId();

        // $query = $this->con->prepare("SELECT * FROM student_period_quiz_question_answer
        //     WHERE subject_period_quiz_id=:subject_period_quiz_id
        //     AND student_id=:student_id
        //     LIMIT 1");
        // $query->bindValue(":subject_period_quiz_id", $subject_period_quiz_id);
        // $query->bindValue(":student_id", $student_id);
        // $query->execute();

        $query = $this->con->prepare("SELECT * FROM student_period_quiz
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
            AND student_id=:student_id
            -- AND total_score > 0
            AND time_finish IS NOT NULL
            LIMIT 1");
        
        $query->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        // $query->bindValue(":time_finish", null);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->rowCount() > 0;
    }

    public function StudentAnsweredTheQuizCountv2($subject_period_quiz_class_id){

        $student_id = $this->userLoggedInObj->GetId();

        $query = $this->con->prepare("SELECT take_quiz_count FROM student_period_quiz
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
            AND student_id=:student_id");
        
        $query->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        // $query->bindValue(":time_finish", null);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->fetchColumn();
    }

    public function StudentAnsweredTheQuizCount($subject_period_quiz_class_id){

        $student_id = $this->userLoggedInObj->GetId();
        
        // $query = $this->con->prepare("SELECT * FROM student_period_quiz_question_answer
        //     WHERE subject_period_quiz_id=:subject_period_quiz_id
        //     AND student_id=:student_id
        //     LIMIT 1");
        // $query->bindValue(":subject_period_quiz_id", $subject_period_quiz_id);
        // $query->bindValue(":student_id", $student_id);
        // $query->execute();

        $query = $this->con->prepare("SELECT * FROM student_period_quiz
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id
            AND student_id=:student_id
            -- AND total_score > 0
            AND time_finish IS NOT NULL");
        
        $query->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        // $query->bindValue(":time_finish", null);
        $query->bindValue(":student_id", $student_id);
        $query->execute();

        return $query->rowCount();
    }

    private function EnableShowAnswer($subject_period_quiz_class_id){

        $query = $this->con->prepare("SELECT show_correct_answer FROM subject_period_quiz_class
            WHERE subject_period_quiz_class_id=:subject_period_quiz_class_id");

        $query->bindValue(":subject_period_quiz_class_id", $subject_period_quiz_class_id);
        $query->execute();

        $val = $query->fetchColumn();
        if($val == "yes"){
            return true;
        }

        return false;

    }
    private function createTabs(){

        $display = "block";

        // $subject_period_assignment_id = $this->subjectPeriodAssignment->GetSubjectPeriodAssignmentId();
        // $ifStudentSubmittedAss = $this->subjectPeriodAssignment
        //     ->CheckStudentSubmittedAssignment($subject_period_assignment_id);
        
        // if($ifStudentSubmittedAss == false)
        //     $display = "none";

        return "
            <ul class='nav nav-tabs' role='tablist'>
                <li class='nav-item'>
                    <a class='nav-link active' id='instructions-tab' data-toggle='tab' 
                        href='#instructions' role='tab' aria-controls='instructions' aria-selected='true'>
                        Instructions
                    </a>
                </li>
                <li style='display: $display;' class='nav-item' >
                    <a class='nav-link' id='submission-tab' data-toggle='tab' href='#submission' role='tab' 
                        aria-controls='submission' aria-selected='false'>
                        Submissions
                    </a>
                </li>
            </ul>
        ";
    }
}
?>