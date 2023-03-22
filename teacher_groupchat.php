
<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/SubjectPeriodAssignmentHandout.php');
    require_once('includes/classes/Student.php');
    require_once('includes/classes/GroupChat.php');
    require_once('includes/classes/Teacher.php');

    if(isset($_SESSION['teacher_groupchat_id'])){

        $group_chat_id=  $_SESSION['teacher_groupchat_id'];
        
        $_SESSION['user_data'][$teacherLoggedIn] = [
            'username'  =>  $teacherLoggedIn,
            'userType'   =>  'teacher',
        ];

        
        $username = "un";
        $userType = "ut";
        foreach ($_SESSION['user_data'] as $key => $value) {
            $username = $value['username'];
            echo "<br>";
            $userType = $value['userType'];

        }
        echo $username;
        echo $userType;

        $group_chat = new GroupChat($con, $group_chat_id, $teacherUserLoggedInObj);

        $room = $group_chat->createChatForTeacher($group_chat_id);

        echo "
            $room
        ";
    }

?>
<!-- <style>
    #messages_area_for_groupchat_teacher
	{
        height: 75vh;
        overflow-y: auto;
	}

</style> -->
<script type="text/javascript">

	$(document).ready(function(){

        var group_chat_id = $("#group_chat_id").val();

        var grp_id = <?php echo $group_chat_id;?>

  

        var receiver_userid = '';

		var conn = new WebSocket('ws://localhost:8080?group_chat_id=<?php echo $group_chat_id; ?>&username=<?php echo $teacherLoggedIn?>');

		conn.onopen = function(event)
		{
			console.log('Connection Established in teacher gc');
		};

       	conn.onmessage = function(event)
		{

            var data = JSON.parse(event.data);
			console.log(data);

            var row_class = '';
            var background_class = '';
 

            if(data.msg != null
             && data.client_group_chat_id == grp_id
            
            ){
                
                if(data.from == 'Me')
                {
                    row_class = 'row justify-content-end';
                    background_class = 'alert-primary';
                }
                else
                {
                    row_class = 'row justify-content-start';
                    background_class = 'alert-success';
                }

                var html_data = `
                    <div class="`+row_class+` card-body" id="messages_area_for_groupchat_student">
                        <div class="col-sm-10">
                            <div class="shadow-sm alert `+background_class+`">
                                <b>`+data.from+` - </b>`+data.msg+`<br />
                                <div class="text-right">
                                    <small>${data.created_at}</i></small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#messages_area_for_groupchat_teacher').append(html_data);
				$('#messages_area_for_groupchat_teacher')
                    .scrollTop($('#messages_area_for_groupchat_teacher')[0].scrollHeight);

                $('#chat_message_groupchat_teacher').val('');
            }
            else if (data.client_group_chat_id 
                && data.client_group_chat_id != grp_id){

                var error = `
                    <div class="col-sm-10">
                        <p>Has message, not yours</p>
                    </div>
                `;
                $('#messages_area_for_groupchat_teacher').append(error);
            }
            
        }

        $(document).on('submit', '#chat_form_groupchat_teacher', function(e) {

            e.preventDefault();

            $('#chat_form_groupchat_teacher').parsley();
            
            if($('#chat_form_groupchat_teacher').parsley().isValid()){

                var group_chat_id = parseInt($("#group_chat_id").val());
                var login_teacher_username = $("#login_teacher_username").val();
                var chat_message_groupchat_teacher = $("#chat_message_groupchat_teacher").val();

                var data = {

					login_username: login_teacher_username,
					msg: chat_message_groupchat_teacher,
                    client_group_chat_id: group_chat_id,
					command: 'privateGroupChat'

				};

                // Will go on the onMessage Function
				// conn.send(JSON.stringify(data));
				conn.send(JSON.stringify(data));

                // $.ajax({
                //     url:"ajax/message/createTeacherMessageGroupChat.php",
                //     method: "POST",
                //     data:{
                //         group_chat_id,
                //         login_teacher_username,
                //         chat_message_groupchat_teacher
                //     },
                //     dataType: "JSON",

                //     success: function(data){
                //         console.log(data);
                        
                //         if(data != null)
                //         {
                //             var output = '';
                            
                //             var row_class= ''; 
                //             var background_class = '';
                //             // var user_name = '';

                //             if(data.user_username == login_teacher_username)
                //             {
                //                 row_class = 'row justify-content-end';

                //                 background_class = 'alert-primary';

                //                 user_name = 'Me';
                //             }
                //             // else a.from_username != login_student_username)
                //             // {
                //             //     row_class = 'row justify-content-end';

                //             //     background_class = 'alert-success';
                //             //     // user_name = data[count].from_user_name;
                //             //     user_name = studentName;
                //             //     // console.log(user_name)
                //             // }

                //             output += `
                //                 <div class="${row_class}">
                //                     <div class="col-sm-10">
                //                         <div class="shadow alert `+background_class+`">
                //                             <b>${user_name} - </b>
                //                             ${data.body}<br />
                //                             <div class="text-right">
                //                                 <small><i>`+data.created_at+`</i></small>
                //                             </div>
                //                         </div>
                //                     </div>
                //                 </div>
                //             `;

                //             $("#messages_area_for_groupchat_teacher").append(output);
                //             $("#chat_message_groupchat_teacher").val('');
                //         }
                //     },
                //     error: function(xhr, status, error) {
                //         console.log("An error occurred: " + error);
                //     }
                // });
            }

            


        });


    });

</script>


