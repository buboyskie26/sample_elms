
<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjectPeriodAssignmentHandout.php');
    require_once('includes/classes/Student.php');
    require_once('includes/classes/GroupChat.php');
    require_once('includes/classes/Teacher.php');

    if(isset($_SESSION['teacher_groupchat_id'])){

        $group_chat_id=  $_SESSION['teacher_groupchat_id'];
        
        $group_chat = new GroupChat($con, $teacherUserLoggedInObj);

        $room = $group_chat->createChatForTeacher($group_chat_id);

        echo "
            $room
        ";
    }

?>

<script type="text/javascript">

	$(document).ready(function(){

        $(document).on('submit', '#chat_form_groupchat_teacher', function(e) {

            e.preventDefault();

            $('#chat_form_groupchat_teacher').parsley();
            
            if($('#chat_form_groupchat_teacher').parsley().isValid()){

                var group_chat_id = parseInt($("#group_chat_id").val());
                var login_teacher_username = $("#login_teacher_username").val();
                var chat_message_groupchat_teacher = $("#chat_message_groupchat_teacher").val();

                
                $.ajax({
                    url:"ajax/message/createTeacherMessageGroupChat.php",
                    method: "POST",
                    data:{
                        group_chat_id,
                        login_teacher_username,
                        chat_message_groupchat_teacher
                    },
                    dataType: "JSON",

                    success: function(data){
                        console.log(data);
                        
                        if(data != null)
                        {
                            var output = '';
                            
                            var row_class= ''; 
                            var background_class = '';
                            // var user_name = '';

                            if(data.user_username == login_teacher_username)
                            {
                                row_class = 'row justify-content-end';

                                background_class = 'alert-primary';

                                user_name = 'Me';
                            }
                            // else a.from_username != login_student_username)
                            // {
                            //     row_class = 'row justify-content-end';

                            //     background_class = 'alert-success';
                            //     // user_name = data[count].from_user_name;
                            //     user_name = studentName;
                            //     // console.log(user_name)
                            // }

                            output += `
                                <div class="${row_class}">
                                    <div class="col-sm-10">
                                        <div class="shadow alert `+background_class+`">
                                            <b>${user_name} - </b>
                                            ${data.body}<br />
                                            <div class="text-right">
                                                <small><i>`+data.created_at+`</i></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            $("#messages_area_for_groupchat_teacher").append(output);
                            $("#chat_message_groupchat_teacher").val('');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("An error occurred: " + error);
                    }
                });
            }

            


        });


    });

</script>


