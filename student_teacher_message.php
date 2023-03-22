<?php

    require_once('includes/studentHeader.php');
    require_once('includes/classes/Message.php');
    require_once('includes/classes/TeacherCourse.php');
    require_once('includes/classes/Student.php');
    require_once('includes/classes/Teacher.php');
    
    if(isset($_SESSION['teacher_course_id'])){

        $teacher_course_id = $_SESSION['teacher_course_id'];

        // echo $teacher_course_id;

        $teacherCourse = new TeacherCourse($con, $teacher_course_id,
            $studentLoggedIn);

        $message = new Message($con, null, $studentUserLoggedInObj);

        $form = $message->createMessageForm();
        $createLayout = $message->createLayoutForStudent();

        $username = $studentLoggedIn;
        echo "
            $createLayout
        ";
        // echo "
        //     <div style='width: 100%; height:100vh;' class='message_section'>
        //         $form
        //     </div>
        // ";
    }else{
        echo "Invalid Teacher Chat";
        exit();
    }
?>
<style type="text/css">
		html,
		body {
		  height: 100%;
		  width: 100%;
		  margin: 0;
		}
		#wrapper
		{
			display: flex;
		  	flex-flow: column;
		  	height: 100%;
		}
		#remaining
		{
			flex-grow : 1;
		}
		#messages {
			height: 200px;
			background: whitesmoke;
			overflow: auto;
		}
		#chat-room-frm {
			margin-top: 10px;
		}
		#user_list
		{
			height:450px;
			overflow-y: auto;
		}

		#messages_area
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
        var receiver_username = '';
        var login_student_username = $("#student_username").val();

        var sender_teacher_username = "";

        var user_name = '';

        $(document).on('click', '.select_user', function(){

            // Each Teacher id 
            receiver_userid = $(this).data('userid');
            sender_teacher_username = $(this).data('teacher_username').toString();

            var login_user_id = $("#login_user_id").val();
            
            // Combo in order to work properly.
			$('.select_user.active').removeClass('active');
			$(this).addClass('active');

			$('#is_active_chat').val('Yes');

            user_name = $(`#list_user_name_${receiver_userid}`).text();

            $(`#userid_${receiver_userid}`).text('');


            // Remove sign of messaged.
            $(`#user_username_${sender_teacher_username}`).text('');

            login_user_id = parseInt(login_user_id);
            receiver_userid = parseInt(receiver_userid);

            createMessageForm(user_name, login_user_id, receiver_userid);

            // login_student_username = $("#student_username").val();
 
            var chat_data = {
                clicked_username: user_name
            };

			// conn.send(JSON.stringify(chat_data));

            $.ajax({
                url:"ajax/message/populateTeacherMessage.php",
				method: "POST",
				data:{
                    action:'fetch_chat', 
                    to_user_id: receiver_userid,
                    from_user_id: login_user_id
				},
				dataType: "JSON",

             	success: function(data){
                    // console.log(data)

                    var teacherName = user_name;
                    if(data != null)
					{
						var output = '';

                        for(var count = 0; count < data.length; count++)
						{
							var row_class= ''; 
							var background_class = '';
							// var user_name = '';

							if(data[count].from_username == login_student_username)
							{
								row_class = 'row justify-content-start';

								background_class = 'alert-primary';

								user_name = 'Me';
							}
							else if(data[count].from_username != login_student_username)
							{
								row_class = 'row justify-content-end';

								background_class = 'alert-success';
								// user_name = data[count].from_user_name;
                                user_name = teacherName;
                                // console.log(user_name)
							}

                            output += `
								<div class="${row_class}">
									<div class="col-sm-10">
										<div class="shadow alert `+background_class+`">
											<b>${user_name} - </b>
											`+data[count].body+`<br />
											<div class="text-right">
												<small><i>`+data[count].message_creation+`</i></small>
											</div>
										</div>
									</div>
								</div>
							`;
                        }

                        $('#messages_area').html(output);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("An error occurred: " + error);
                }
            });

        });

        var conn = new WebSocket('ws://localhost:8080?private_chat_with=<?php echo $username; ?>');

        conn.onopen = function(event)
        {
            console.log('Connection Established in teacher message with');
        };

        conn.onmessage = function(event)
        {

            var login_user_id = $("#login_user_id").val();

            var data = JSON.parse(event.data);
            console.log(data);
            
            var row_class = '';
            var background_class = '';

            var senderName = '';

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
                    senderName = user_name;
                }

                var html_data = `
                    <div class="`+row_class+` card-body" id="messages_area_for_groupchat_student">
                        <div class="col-sm-10">
                            <div class="shadow-sm alert `+background_class+`">
                                <b>`+senderName+` - </b>`+data.private_message+`<br />
                                <div class="text-right">
                                    <small>${data.created_at}</i></small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#messages_area').append(html_data);
                $('#messages_area')
                    .scrollTop($('#messages_area')[0].scrollHeight);  
                
                $("#chat_message").val('');

            }else if(data.private_message != null
                && data.receiver_userid == login_user_id 
                && !data.sender_name_translate == user_name){
                
                $('#user_username_'+data.sender_username).html(
                    '<span style="background-color: red;" class="badge badge-danger badge-pill">1</span>'
                );
            }
        }

        // $('#chat_form').parsley();
		$(document).on('submit', '#chat_form', function(event){

			event.preventDefault();

            if($('#chat_form').parsley().isValid()){
                
				var user_id = parseInt($('#login_user_id').val());
				var message = $('#chat_message').val();

                
                var chat_data = {
                    // sender_id: user_id,
                    sender_username: login_student_username,
                    receiver_userid: receiver_userid,
                    receiver_username: sender_teacher_username, //sd
					commando: 'privateMessage',
					private_message: message,
                    userType: "student"
					// login_id: user_id,
                    // sender_name: login_student_username,
				};
                
				conn.send(JSON.stringify(chat_data));
                 
                // console.log(message);
                // console.log(receiver_userid);

                // $.post({
                //     url: "ajax/message/createMessage.php",
                //     data: {user_id, receiver_userid, message},
                //     dataType: "JSON",
                //     success: function(data) {

                //         console.log(data)

                //         if(data != null) {

                //             var output = '';
                //             var row_class= ''; 
                //             var background_class = '';
                //             var user_name = '';
                            
                //             if(data.from_username == login_student_username)
                //             {
                //                 row_class = 'row justify-content-start';

                //                 background_class = 'alert-primary';

                //                 user_name = 'Me';
                //             }

                //             output += `
                //                 <div class="${row_class}">
                //                     <div class="col-sm-10">
                //                         <div class="shadow alert `+background_class+`">
                //                             <b>${user_name} - </b>
                //                             `+data.body+`<br />
                //                             <div class="text-right">
                //                                 <small><i>`+data.message_creation+`</i></small>
                //                             </div>
                //                         </div>
                //                     </div>
                //                 </div>
                //             `;

                //             $('#messages_area').append(output);

                //             $('#chat_message').val('');

                //         }

                //         $('#chat_message').val('');

                //     }

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
							id="chat_user_name">${user_name}</span></b>
						</div>
						<div class="col col-sm-6 text-right">
							<a href="chatroom.php" class="btn btn-success btn-sm">Group Chat</a>&nbsp;&nbsp;&nbsp;
							<button type="button" class="close" id="close_chat_area" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
					</div>
                    </div>

                    <div class="card-body" id="messages_area">
                        
                    </div>
                </div>

                <form id="chat_form" method="POST" data-parsley-errors-container="#validation_error">
                    <div class="input-group mb-3" style="height:7vh">

                        <textarea class="form-control" id="chat_message" name="chat_message" placeholder="Type Message Here" data-parsley-maxlength="1000" data-parsley-pattern="/^[a-zA-Z0-9 ]+$/" required></textarea>

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
