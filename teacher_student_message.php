<?php

    require_once('includes/teacherHeader.php');
    require_once('includes/classes/Message.php');
    require_once('includes/classes/TeacherCourse.php');
    require_once('includes/classes/Teacher.php');
    require_once('includes/classes/Student.php');
    
    // if(isset($_SESSION['teacher_course_id'])){

        // $teacher_course_id = $_SESSION['teacher_course_id'];

        // echo $teacher_course_id;

        // $teacherCourse = new TeacherCourse($con, $teacher_course_id,
        //     $studentLoggedIn);

        $message = new Message($con, null, $teacherUserLoggedInObj);

        // $form = $message->createMessageForm();
        $createLayout = $message->createLayoutForTeacher();

        $username = $teacherLoggedIn;

        echo "
            $createLayout
        ";
        // echo "
        //     <div style='width: 100%; height:100vh;' class='message_section'>
        //         $form
        //     </div>
        // ";
    // }
    // else{
    //     echo "Invalid Teacher Chat";
    //     exit();
    // }
?>

<style>
    #messages_area_for_teacher
	{
        height: 75vh;
        overflow-y: auto;
        /*background-color:#e6e6e6;*/
        /*background-color: #EDE6DE;*/
	}
</style>


<script type="text/javascript">

	$(document).ready(function(){

        

        var receiver_userid = '';
        var sender_student_username = '';
        var login_teacher_username = $("#login_teacher_username").val();

        var connection_id = 0;
        var user_name = '';

        $(document).on('click', '.select_user', function(){
 
            receiver_userid = $(this).data('student_id');
            sender_student_username = $(this).data('student_username');
            connection_id = $(this).data('connection_id');

            // console.log(connection_id)
            var login_user_id = $("#login_teacher_id").val();

            login_user_id = parseInt(login_user_id);
            receiver_userid = parseInt(receiver_userid);

            // Combo in order to work properly.
			$('.select_user.active').removeClass('active');
			$(this).addClass('active');

			$('#is_active_chat_teacher').val('Yes');

            user_name = $(`#list_student_name_${receiver_userid}`).text();

            // console.log(user_name)
            createMessageForm(user_name, login_user_id, receiver_userid);

            // Remove sign of messaged.
            $(`#user_username_${sender_student_username}`).text('');

            // Teacher username

            // login_teacher_username = $("#login_teacher_username").val();

            // console.log(login_teacher_username)

            $.ajax({
                url:"ajax/message/populateStudentMessage.php",
				method: "POST",
				data:{
                    action:'fetch_chat', 
                    to_user_id: receiver_userid,
                    from_user_id: login_user_id
				},
				dataType: "JSON",

             	success: function(data){
                    // console.log(data)

                    // console.log(data.length)
                    if(data != null)
					{
						var output = '';

                        for(var count = 0; count < data.length; count++)
						{
							var row_class= ''; 
							var background_class = '';
							var userNameReflect = '';

							if(data[count].from_username == login_teacher_username)
							{
								row_class = 'row justify-content-start';

								background_class = 'alert-primary';

								userNameReflect = 'Me';
							}
							else
							{
								row_class = 'row justify-content-end';

								background_class = 'alert-success';
								// user_name = data[count].from_user_name;

                                userNameReflect = user_name;
							}

                            output += `
								<div class="${row_class}">
									<div class="col-sm-10">
										<div class="shadow alert `+background_class+`">
											<b>${userNameReflect} - </b>
											`+data[count].body+`<br />
											<div class="text-right">
												<small><i>`+data[count].message_creation+`</i></small>
											</div>
										</div>
									</div>
								</div>
							`;
                        }

                        $('#messages_area_for_teacher').html(output);
                    }

                },
                error: function(xhr, status, error) {
                    console.log("An error occurred: " + error);
                }
            });
        });

        var conn = new WebSocket('ws://localhost:8080?private_chat_with=<?php echo $username;?>');

        conn.onopen = function(event)
        {
            console.log('Connection Established in teacher message with');
        };

        conn.onmessage = function(event)
        {

            var login_user_id = $("#login_teacher_id").val();

            var data = JSON.parse(event.data);
            console.log(data);

            var row_class = '';
            var background_class = '';
            
            var senderName = '';
            
            // console.log(data.sender_name_translate + " second");
            if(data.sender_name_translate == user_name){
                console.log('corr')
                console.log(data.sender_name_translate);
                console.log(user_name);
            }else{
                console.log("not");
                console.log(data.sender_name_translate);
                console.log(user_name);
            }

            if(data.private_message != null
                && data.receiver_userid == login_user_id 
                && data.sender_name_translate == user_name
                || data.from == 'Me'
            ){
                
                if(data.from == 'Me')
                {
                    row_class = 'row justify-content-end';
                    background_class = 'alert-primary';
                    senderName = 'Me';
                }
                else
                {
                    row_class = 'row justify-content-start';
                    background_class = 'alert-success';
                    senderName = data.from;
                }
                
                // if(data.privateMessage == "privateMessage"){}

                var html_data = `
                    <div class="`+row_class+` card-body" id="messages_area_for_groupchat_student">
                        <div class="col-sm-10">
                            <div class="shadow-sm alert `+background_class+`">
                                <b>`+data.from+` - </b>`+data.private_message+`<br />
                                <div class="text-right">
                                    <small>${data.created_at}</i></small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#messages_area_for_teacher').append(html_data);
				$('#messages_area_for_teacher')
                    .scrollTop($('#messages_area_for_teacher')[0].scrollHeight);

                $('#chat_message_for_teacher').val('');

            }else if(data.private_message != null
                && data.receiver_userid == login_user_id 
                && !data.sender_name_translate == user_name){

                // console.log(receiver_userid);
                // var senderName = parseInt(data.sender_name);

				$('#user_username_'+data.sender_username).html(
                    '<span style="background-color: red;" class="badge badge-danger badge-pill">1</span>'
                );

            }
        }
       

        // $('#chat_form').parsley();
		$(document).on('submit', '#chat_form_for_teacher', function(event){

			event.preventDefault();

            if($('#chat_form_for_teacher').parsley().isValid()){
                
				var user_id = parseInt($('#login_teacher_id').val());
				var message = $('#chat_message_for_teacher').val();
                
                // console.log(message);
                // console.log(user_id);

                // console.log(login_teacher_username);

                var chat_data = {
					// sender_id: user_id,
                    sender_username: login_teacher_username,
                    receiver_userid: receiver_userid,
                    receiver_username: sender_student_username, //sd
					commando: 'privateMessage',
					private_message: message,
                    userType: "teacher"

                    // connection_id: connection_id
				};

				conn.send(JSON.stringify(chat_data));

                // $.post({
                //     url: "ajax/message/createMessageFromTeacher.php",
                //     data: {user_id, receiver_userid, message},
                //     dataType: "JSON",
                //     success: function(data) {

                //     console.log(data)
                //     if(data != null)
				// 	{
				// 		var output = '';

                //         var row_class= ''; 
                //         var background_class = '';
                //         var user_name = '';
                        
                //         if(data.from_username == login_teacher_username)
                //         {
                //             row_class = 'row justify-content-start';

                //             background_class = 'alert-primary';

                //             user_name = 'Me';
                //         }

                //         output += `
                //             <div class="${row_class}">
                //                 <div class="col-sm-10">
                //                     <div class="shadow alert `+background_class+`">
                //                         <b>${user_name} - </b>
                //                         `+data.body+`<br />
                //                         <div class="text-right">
                //                             <small><i>`+data.message_creation+`</i></small>
                //                         </div>
                //                     </div>
                //                 </div>
                //             </div>
                //         `;
                //         }

                //         $('#messages_area_for_teacher').append(output);

                //         $('#chat_message_for_teacher').val('');

                //     },
                //     error: function(error) {
                //         // Handle the error
                //     }
                // });
                
                // $.post("ajax/message/createMessageFromTeacher.php",
                //     {user_id, receiver_userid, message}).done(function(data) {

                //     console.log(data)

                //     $('#chat_message_for_teacher').val('');
                // });

            }else{
            }
        });

        function createMessageForm(user_name,
            login_user_id, receiver_userid){

            // We`re not in the php file
            // createPost = "createMessage($login_user_id, $receiver_userid)";

            var output = `
                <div class="card">
                    <div class="card-header">
                        <div class="row">
						<div class="col col-sm-6">
							<b>Chat with <span class="text-danger"
							id="chat_student_name">${user_name}</span></b>
						</div>
						<div class="col col-sm-6 text-right">
							<a href="chatroom.php" class="btn btn-success btn-sm">Group Chat</a>&nbsp;&nbsp;&nbsp;
							<button type="button" class="close" id="close_chat_area" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					</div>
                    </div>

                    <div class="card-body" id="messages_area_for_teacher">
                        
                    </div>
                </div>

                <form id="chat_form_for_teacher" method="POST" data-parsley-errors-container="#validation_error">
                    <div class="input-group mb-3" style="height:7vh">

                        <textarea class="form-control" id="chat_message_for_teacher" name="chat_message" placeholder="Type Message Here" data-parsley-maxlength="1000" data-parsley-pattern="/^[a-zA-Z0-9 ]+$/" required></textarea>

                        <input type="hidden" id="user_from_id" name="user_from_id" value="">
                        <input type="hidden" id="user_to_id" name="user_to_id" value="">

                        <div class="input-group-append">
                            <button  type="submit" name="send" id="send" class="btn btn-primary"><i class="fa fa-paper-plane"></i></button>
                        </div>
                    </div>

                    <br />
                    <div id="validation_error"></div>
                    <br />

                </form>
            `;

            $('#chat_area').html(output);
			$('#chat_form').parsley();
        }
    });
</script>
