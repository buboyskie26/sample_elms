<?php 

    class GroupChat{

    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function createChatForTeacher($group_chat_id){

        
        $login_teacher_id = $this->userLoggedInObj->GetId();
        $login_teacher_username = $this->userLoggedInObj->GetUsername();

        $groupChatBody = $this->GenerateGroupChatBodyForTeacher($group_chat_id, $login_teacher_username);
        
        return "
            <div class='container-fluid'>
		        <div class='row'>

                    <div class='col-lg-10 col-md-8 col-sm-7'>
                        <br />
                        <h3 class='text-center'>Realtime Group Chat</h3>
                        <br />
 
                        <div class='card-body' id='messages_area_for_groupchat_teacher'>
                            $groupChatBody
                        </div>
                            
                        <div id='chat_area'>
                            <form id='chat_form_groupchat_teacher' method='POST' 
                                data-parsley-errors-container='#validation_error'>
                                <div class='input-group mb-3' style='height:7vh'>

                                    <textarea class='form-control' id='chat_message_groupchat_teacher' name='chat_message_groupchat_teacher' placeholder='Type Message Here' data-parsley-maxlength='1000' data-parsley-pattern='/^[a-zA-Z0-9 ]+$/' required></textarea>

                                    <div class='input-group-append'>
                                        <button type='submit' name='send' id='send' class='btn btn-primary'><i class='fa fa-paper-plane'></i></button>
                                    </div>

                                    <input type='hidden' id='login_teacher_username' name='login_teacher_username' value='$login_teacher_username' >
                                    <input type='hidden' id='login_teacher_id' name='login_teacher_id' value='$login_teacher_id' >
                                    <input type='hidden' id='user_from_gc_username' name='user_from_gc_username' >
                                    <input type='hidden' id='group_chat_id' name='group_chat_id' value='$group_chat_id'>
                                </div>
                                <br />
                                <div id='validation_error'></div>
                                <br />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }
    private function GenerateGroupChatBodyForTeacher($group_chat_id,
        $login_teacher_username){

        $output = "";

        $statement = $this->con->prepare("SELECT * FROM group_message
            WHERE group_chat_id=:group_chat_id");

        $statement->bindValue(":group_chat_id", $group_chat_id);
        $statement->execute();
        $output = "";
    
        if($statement->rowCount() > 0){

            while($row = $statement->fetch(PDO::FETCH_ASSOC)){

                $user_username = $row['user_username'];

                $un = "";

                $student = new Student($this->con, $user_username);

                $un = $student->GetName();

                // If username is the teacher
                if($un == ""){
                    $teacher = new Teacher($this->con, $user_username);
                    $un = $teacher->GetName();
                }

                $created_at = $row['created_at'];
                $body = $row['body'];

                // if(data.user_username == login_username)

                if($user_username == $login_teacher_username){
                    $row_class = 'row justify-content-end';

                    $background_class = 'alert-primary';

                    $user_name = 'Me' ;

                }else{
                    $row_class = 'row justify-content-start';

                    $background_class = 'alert-success';

                    $user_name = $un;
                } 


                $output .= "
                    <div class='card-body' id='messages_area_for_groupchat_teacher'>
                        <div class='$row_class'>
                            <div class='col-sm-10'>
                                <div class='shadow alert $background_class'>
                                    <b>$user_name </b>
                                    $body<br />
                                    <div class='text-right'>
                                        <small><i>$created_at</i></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                ";
            }
        }

        

        

        return $output;
    }

    public function createChatForStudent($group_chat_id){


        $login_id = $this->userLoggedInObj->GetId();
        $login_username = $this->userLoggedInObj->GetUsername();

        $groupChatBody = $this->GenerateGroupChatBodyForStudent($group_chat_id, $login_username);

        return "
            <div class='container-fluid'>
		        <div class='row'>

                    <div class='col-lg-10 col-md-8 col-sm-7'>
                        <br />
                        <h3 class='text-center'>Realtime Group Chat</h3>
                        <br />
 
                        <div class='card-body' id='messages_area_for_groupchat_student'>
                            $groupChatBody
                        </div>
                            
                        <div id='chat_area'>
                            <form id='chat_form_groupchat_student' method='POST' data-parsley-errors-container='#validation_error'>
                                <div class='input-group mb-3' style='height:7vh'>

                                    <textarea class='form-control' id='chat_message_groupchat' name='chat_message_groupchat' placeholder='Type Message Here' data-parsley-maxlength='1000' data-parsley-pattern='/^[a-zA-Z0-9 ]+$/' required></textarea>


                                    <div class='input-group-append'>
                                        <button type='submit' name='send' id='send' class='btn btn-primary'><i class='fa fa-paper-plane'></i></button>
                                    </div>

                                    
                                    <input type='hidden' id='login_username' name='login_username' value='$login_username' >
                                    <input type='hidden' id='user_from_gc_username' name='user_from_gc_username' >
                                    <input type='hidden' id='group_chat_id' name='group_chat_id' value='$group_chat_id'>
                                </div>
                                <br />
                                <div id='validation_error'></div>
                                <br />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }
    private function GenerateGroupChatBodyForStudent($group_chat_id,
        $login_username){

        $statement = $this->con->prepare("SELECT * FROM group_message
            WHERE group_chat_id=:group_chat_id");

        $statement->bindValue(":group_chat_id", $group_chat_id);
        $statement->execute();
        $output = "";
    
        if($statement->rowCount() > 0){

            while($row = $statement->fetch(PDO::FETCH_ASSOC)){

                $user_username = $row['user_username'];

                $un = "";

                $student = new Student($this->con, $user_username);
                $un = $student->GetName();

                // If username is the teacher
                if($un == ""){
                    $teacher = new Teacher($this->con, $user_username);
                    $un = $teacher->GetName();
                }

                $created_at = $row['created_at'];
                $body = $row['body'];

                // if(data.user_username == login_username)

                if($user_username == $login_username){
                    $row_class = 'row justify-content-end';

                    $background_class = 'alert-primary';

                    $user_name = 'Me' ;

                }else{
                    $row_class = 'row justify-content-start';

                    $background_class = 'alert-success';

                    $user_name = $un;
                }

                $output .= "
                    <div class='card-body' id='messages_area_for_groupchat_student'>
                        <div class='$row_class'>
                            <div class='col-sm-10'>
                                <div class='shadow alert $background_class'>
                                    <b>$user_name </b>
                                    $body<br />
                                    <div class='text-right'>
                                        <small><i>$created_at</i></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                ";
            }
        }

        return $output;
    }
}


?>