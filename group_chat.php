<?php 

    require_once('includes/studentHeader.php');
    require_once('includes/classes/GroupChat.php');
    require_once('includes/classes/Student.php');
    require_once('includes/classes/Teacher.php');

    if(isset($_SESSION['group_chat_id'])){

        $group_chat_id = $_SESSION['group_chat_id'];


        $_SESSION['user_data'][$studentLoggedIn] = [
            'username'  =>  $studentLoggedIn,
            'userType'   =>  'student',
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

        // if student is not belong to the group chat of teacher course.
        $checkStudent = $con->prepare("SELECT * FROM group_chat_member
                WHERE group_chat_id=:group_chat_id
                AND user_username=:user_username
                LIMIT 1");

        $checkStudent->bindValue(":group_chat_id", $group_chat_id); 
        $checkStudent->bindValue(":user_username", $studentLoggedIn); 
        $checkStudent->execute(); 

        if($checkStudent->rowCount() == 0){
            header("Location: dashboard_student.php");
            exit();
        }

        // echo "gc $group_chat_id";

        $group_chat = new GroupChat($con, $group_chat_id, $studentUserLoggedInObj);

        $room = $group_chat->createChatForStudent($group_chat_id);

        echo "
            $room
        ";


    }
?>
<style>
    /* #messages_area_for_groupchat_student
	{
        height: 75vh;
        overflow-y: auto;
	} */
</style>
<script type="text/javascript">

	$(document).ready(function(){

        // var group_chat_id = $("#group_chat_id").val();

        var grp_id = <?php echo $group_chat_id;?>

        var receiver_userid = '';

		var conn = new WebSocket('ws://localhost:8080?group_chat_id=<?php echo $group_chat_id; ?>&username=<?php echo $studentLoggedIn?>');

		conn.onopen = function(event)
		{
			console.log('Connection Established in student gc');
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
                    <div class="`+row_class+` card-body" 
                        id="messages_area_for_groupchat_student">
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
                $('#messages_area_for_groupchat_student').append(html_data);
				$('#messages_area_for_groupchat_student')
                    .scrollTop($('#messages_area_for_groupchat_student')[0].scrollHeight);
                $('#chat_message_groupchat').val('');

            }
            else if (data.client_group_chat_id 
                && data.client_group_chat_id != grp_id){

                var error = `
                    <div class="col-sm-10">
                        <p>Has message, not yours</p>
                    </div>
                `;
                $('#messages_area_for_groupchat_student').append(error);
            }

        }

        $(document).on('submit', '#chat_form_groupchat_student', function(e) {

            e.preventDefault();

            var user_name = '';

            $('#chat_form_groupchat_student').parsley();

            if($('#chat_form_groupchat_student').parsley().isValid()){

                var group_chat_id = parseInt($("#group_chat_id").val());
                var login_student_username = $("#login_username").val();
                var chat_message_groupchat_student = $("#chat_message_groupchat").val();

                var data = {
					login_username: login_student_username,
					msg: chat_message_groupchat_student,
                    client_group_chat_id: group_chat_id,
					// receiver_userid: receiver_userid,
					command: 'privateGroupChat'
				};

                // Will go on the onMessage Function
				conn.send(JSON.stringify(data));


                // $.ajax({
                //     url:"ajax/message/createStudentMessageGroupChat.php",
                //     method: "POST",
                //     data:{
                //         group_chat_id,
                //         login_username,
                //         chat_message_groupchat
                //     },
                //     dataType: "JSON",

                //     success: function(data){
                //         console.log(data)
                        
                //         if(data != null)
                //         {
                //             var output = '';
                            
                //             var row_class= ''; 
                //             var background_class = '';
                //             // var user_name = '';

                //             if(data.user_username == login_username)
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

                //             $("#messages_area_for_groupchat_student").append(output);
                //             $("#chat_message_groupchat").val('');
                //         }
                //     },
                //     error: function(xhr, status, error) {
                //         console.log("An error occurred: " + error);
                //     }
                // });
            }

        })
    });

</script>
