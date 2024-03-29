<?php 

    class Message{

    private $con, $teacherCourse, $userLoggedInObj;

    public function __construct($con, $teacherCourse, $userLoggedInObj)
    {
        $this->con = $con;
        $this->teacherCourse = $teacherCourse;
        $this->userLoggedInObj = $userLoggedInObj;
        
    }

    public function createLayoutForTeacher(){

        $createListOfStudent = $this->listOfStudenr();

        $login_teacher_id = $this->userLoggedInObj->GetId();
        $login_teacher_username = $this->userLoggedInObj->GetUsername();
        return "
            <div class='container-fluid'>
		        <div class='row'>
                    <div class='col-lg-3 col-md-4 col-sm-5' 
                        style='background-color: #f1f1f1;
                        height: 55vh; border-right:1px solid #ccc;'>

                        <input type='hidden' name='login_teacher_id' id='login_teacher_id'
                            value='$login_teacher_id' />

                        <input type='hidden' name='login_teacher_username' id='login_teacher_username'
                            value='$login_teacher_username' />

                        <input type='hidden' name='is_active_chat_teacher' id='is_active_chat_teacher'
                            value='No' />

                        <div class='list-group' style=' max-height: 100vh; 
                            margin-bottom: 10px;overflow-y:scroll;-webkit-overflow-scrolling: touch;'>

                         $createListOfStudent
                        </div>

                    </div>

                    <div class='col-lg-9 col-md-8 col-sm-7'  id='body_container'>
                        <br />
                        <h3 class='text-center'>One to One Real time Chat</h3>
                        <hr />
                        <br />
                        
                        <div id='chat_area'>
                        </div>
                    </div>

                </div>
            </div>
        ";
    }
    // For Student
    public function createLayoutForStudent(){

        $createListOfTeacher = $this->listOfTeachers();

        $login_user_id = $this->userLoggedInObj->GetId();
        $student_username = $this->userLoggedInObj->GetUsername();

        return "
            <div class='container-fluid'>
		        <div class='row'>
                    <div class='col-lg-3 col-md-4 col-sm-5' 
                        style='background-color: #f1f1f1;
                        height: 100vh; border-right:1px solid #ccc;'>

                        <input type='hidden' name='login_user_id' id='login_user_id'
                            value='$login_user_id' />

                        
                        <input type='hidden' name='student_username' id='student_username'
                            value='$student_username' />

                        <input type='hidden' name='is_active_chat' id='is_active_chat'
                            value='No' />

                        <div class='list-group' style=' max-height: 100vh; 
                            margin-bottom: 10px;overflow-y:scroll;-webkit-overflow-scrolling: touch;'>

                         $createListOfTeacher
                        </div>

                    </div>

                    <div class='col-lg-9 col-md-8 col-sm-7'>
                        <br />
                        <h3 class='text-center'>Realtime One to One Chat</h3>
                        <br />
                        
                        <div id='chat_area'>
                        </div>
                    </div>

                </div>
            </div>
        ";
    }
    private function listOfStudenr(){

        $userLoggedInId = $this->userLoggedInObj->GetId();
        $userLoggedInUsername = $this->userLoggedInObj->GetUsername();

        $output = "";

        $query =  $this->con->prepare("SELECT * FROM student");

        $query->execute();



        if($query->rowCount() > 0){

	        $icon = '<i class="fa fa-circle text-success"></i>';

            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $username = $row['username'];

                $student = new Student($this->con, $username);

                // $student_name = $student->GetName();
                $student_name = $row['firstname'];
                $student_id = $student->GetId();
                $userConnectionId = $row['user_connection_id'];
                $profilePic = $student->GetProfilePic();

                // echo $student_name;

                $didStudMessaged =  $this->UserHadMessagedByStudent($row['username'],
                    $userLoggedInUsername);

                $total_unread_message = "
                    <span class='badge badge-danger badge-pill'
                        id='user_username_$username'>
                    </span>
                ";
                // jj

                if($didStudMessaged > 0){

                    // $total_unread_message = "
                    //         <span style='background-color: red;' 
                    //             id='userid_$student_id' class='badge badge-danger'>$didStudMessaged</span>
                    //     ";
                    $total_unread_message = "
                            <span style='background-color: red;'
                                class='badge badge-danger badge-pill' id='user_username_$username'>$didStudMessaged
                            </span>
                        ";
                }

                // 
                $output .= "
                    <a class='list-group-item list-group-item-action select_user' 
                        style='cursor:pointer' data-student_id='$student_id'
                        data-student_username='$username' data-connection_id='$userConnectionId' >
                        <img src='$profilePic' class='img-fluid rounded-circle img-thumbnail' width='50' />
                        <span class='ml-1'>
                            <strong>
                                <span id='list_student_name_$student_id'>$student_name</span>
                                $total_unread_message
                            </strong>
                        </span>
                        <span class='mt-2 float-right' id='userstatus_1'>$icon</span>
					</a>
                ";
            }
        }
        return $output;
    }
    private function listOfTeachers(){

        $output = "";
        
        $userLoggedInUsername = $this->userLoggedInObj->GetUsername();

        $query =  $this->con->prepare("SELECT * FROM teacher");

        $query->execute();

        if($query->rowCount() > 0){

	        $icon = '<i class="fa fa-circle text-success"></i>';
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){

                $username = $row['username'];

                $teacher = new Teacher($this->con, $username);

                // $teacher_name = $teacher->GetName();
                $teacher_name = $row['firstname'];
                $teacher_id = $teacher->GetId();
                $profilePic = $teacher->GetProfilePic();

				// $total_unread_message = "";

                $didStudMessaged = $this->UserHadMessagedByTeacher($row['username'],
                    $userLoggedInUsername);

                

                // if($didStudMessaged > 0){
                //     $total_unread_message = "
                //         <span id='userid_$teacher_id'
                //             class='badge badge-danger badge-pill'>
                //             $didStudMessaged
                //         </span>"
                //     ;
                // }
               

                $total_unread_message = "
                    <span class='badge badge-danger badge-pill'
                        id='user_username_$username'>
                    </span>
                ";
                // jj

                if($didStudMessaged > 0){
 
                    $total_unread_message = "
                            <span style='background-color: red;' class='badge badge-danger badge-pill'
                                id='user_username_$username'>$didStudMessaged
                            </span>
                        ";
                }
                

                // echo $teacher_name;
                $output .= "
                    <a class='list-group-item list-group-item-action select_user' 
                        style='cursor:pointer' data-userid='$teacher_id'
                         data-teacher_username='$username'
                        >
                        <img src='$profilePic' class='img-fluid rounded-circle img-thumbnail' width='50' />
                        <span class='ml-1'>
                            <strong>
                                <span id='list_user_name_$teacher_id'>$teacher_name</span>
                                $total_unread_message
                            </strong>
                        </span>
                        <span class='mt-2 float-right' id='userstatus_1'>$icon</span>
					</a>
                ";
            }
        }
        return $output;
    }

    private function UserHadMessagedByTeacher($teacher_username, $userLoggedInUsername){

        $statement = $this->con->prepare("SELECT message_teacher_id FROM message_teacher
            WHERE from_username=:from_username
            AND to_username=:to_username
            AND opened='no'
            " );

        $statement->bindValue(":from_username", $teacher_username);
        $statement->bindValue(":to_username", $userLoggedInUsername);
        $statement->execute();

        if($statement->rowCount() > 0){
        // If we want to see only one unread messages.
            return 1;
        }
        return 0;
        // If we want to see all unread messages.
        // return $statement->rowCount();
        // return $statement->rowCount();

    }

    private function UserHadMessagedByStudent($student_username, $userLoggedInUsername){

        $statement = $this->con->prepare("SELECT message_teacher_id FROM message_teacher
            WHERE from_username=:from_username
            AND to_username=:to_username
            AND opened='no'
            " );

        $statement->bindValue(":from_username", $student_username);
        $statement->bindValue(":to_username", $userLoggedInUsername);
        $statement->execute();
        if($statement->rowCount() > 0){
        // If we want to see only one unread messages.
            return 1;
        }
        return 0;
        // If we want to see all unread messages.
        // return $statement->rowCount();
    }
    
    public function createMessageForm(){


        // $user_from_id = $this->teacherCourse->GetTeacherCourseTeacherId();

        // Student will msg teacher on teachercourse table.
        // $user_to_id = $this->teacherCourse->GetTeacherCourseTeacherId();

        $form = "
                <div class='card'>
                    <div class='card-header'>
                        <div class='row'>
						<div class='col col-sm-6'>
							<b>Chat with <span class='text-danger'
							id='chat_user_name'>Lexter</span></b>
						</div>
						<div class='col col-sm-6 text-right'>
							<a href='chatroom.php' class='btn btn-success btn-sm'>Group Chat</a>&nbsp;&nbsp;&nbsp;
							<button type='button' class='close' id='close_chat_area' data-dismiss='alert' aria-label='Close'>
								<span aria-hidden='true'>&times;</span>
							</button>
						</div>
					</div>
                    </div>

                    <div class='card-body' id='messages_area'>
                        
                    </div>
                </div>

                <form id='chat_form' method='POST' data-parsley-errors-container='#validation_error'>
                    <div class='input-group mb-3' style='height:7vh'>

                        <textarea class='form-control' id='chat_message' name='chat_message' placeholder='Type Message Here' data-parsley-maxlength='1000' data-parsley-pattern='/^[a-zA-Z0-9 ]+$/' required></textarea>

                        <input type='hidden' id='user_from_id' name='user_from_id' value=''>
                        <input type='hidden' id='user_to_id' name='user_to_id' value=''>

                        <div class='input-group-append'>
                            <button type='submit' name='send' id='send' class='btn btn-primary'><i class='fa fa-paper-plane'></i></button>
                        </div>

                    </div>
                    <br />
                    <div id='validation_error'></div>
                    <br />
                </form>
			";
        
        return "
            <div class='col-lg-9 col-md-8 col-sm-7'>
				<br />
		        <h3 class='text-center'>Realtime One to One Chat</h3>
		        <br />
		        <div id='chat_areax'></div>
			</div>
        ";

    }
    public function GetOneToOneMessage($teacher_username, $student_username, $userView){
        

        $sql = $this->con->prepare("SELECT * FROM message_teacher
            WHERE to_username=:to_username
            AND from_username=:from_username

            -- OR from_username=:teacher_username
            -- AND to_username=:student_username

            OR from_username=:to_username
            AND to_username=:from_username
            ");

        $sql->bindValue(":to_username", $teacher_username);
        $sql->bindValue(":from_username", $student_username);

        // $sql->bindValue(":teacher_username", $teacher_username);
        // $sql->bindValue(":student_username", $student_username);

        $sql->execute();

        if($sql->rowCount() > 0){


            if($userView == "teacher"){

                $checkIfOpened = $this->con->prepare("SELECT message_teacher_id FROM message_teacher
                    WHERE to_username=:to_username
                    AND from_username=:from_username
                    AND opened= 'no'
                    ");
                $checkIfOpened->bindValue(":to_username", $teacher_username);
                $checkIfOpened->bindValue(":from_username", $student_username);
                $checkIfOpened->execute();

                if($checkIfOpened->rowCount() > 0){
                    // Update in the opened column as 'yes' in the student side.
                    // Needs to be in real time.
                    $sqlUpdate = $this->con->prepare("UPDATE message_teacher
                        SET opened= 'yes'
                        WHERE from_username=:from_username
                        AND to_username=:to_username");

                    $sqlUpdate->bindValue(":from_username", $student_username);
                    $sqlUpdate->bindValue(":to_username", $teacher_username);
                    $sqlUpdate->execute();
                }

                
            }

            if($userView == "student"){

                $checkIfOpened = $this->con->prepare("SELECT message_teacher_id FROM message_teacher
                    WHERE to_username=:to_username
                    AND from_username=:from_username
                    AND opened= 'no'
                    ");

                $checkIfOpened->bindValue(":to_username", $student_username);
                $checkIfOpened->bindValue(":from_username", $teacher_username);
                $checkIfOpened->execute();

                if($checkIfOpened->rowCount() > 0){
                    // Update in the opened column as 'yes' in the student side.
                    // Needs to be in real time.
                    $sqlUpdate = $this->con->prepare("UPDATE message_teacher
                        SET opened= 'yes'
                        WHERE from_username=:from_username
                        AND to_username=:to_username");

                    $sqlUpdate->bindValue(":from_username", $teacher_username);
                    $sqlUpdate->bindValue(":to_username", $student_username);
                    $sqlUpdate->execute();
                }
                

            }

            // echo "qweqweqwe";
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }
    
    }


    public function AddMessage(){

    }
}
?>