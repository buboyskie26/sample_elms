<?php
namespace MyApp;

use Message;
use MessageChat;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Teacher;

require dirname(__DIR__) . "/includes/classes/GroupChat.php";
require dirname(__DIR__) . "/database/GroupChatTeacher.php";
require dirname(__DIR__) . "/database/TeacherChatUser.php";
require dirname(__DIR__) . "/database/MessageChat.php";
// require dirname(__DIR__) . "/includes/classes/Message.php";

// require dirname(__DIR__) . "/includes/config.php";
// require_once("../includes/config.php");

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {

        // Store the new connection to send messages to later

        echo 'Server Started ';
        $this->clients->attach($conn);

        $querystring = $conn->httpRequest->getUri()->getQuery();

        parse_str($querystring, $queryarray);

        if(isset($queryarray['group_chat_id']) && isset($queryarray['username']))
        {

            $data['status_type'] = 'Online';
            $data['group_chat_id'] = intval($queryarray['group_chat_id']);

            $group_chat_id = intval($queryarray['group_chat_id']);
            $my_username = $queryarray['username'];


            $fa = $_SESSION['group_chat_session'][$my_username] = [
                'username'  =>  $my_username,
                'group_chat_id'   =>  $group_chat_id,
            ];

            // $username = "un";
            // $userType = "ut";

            // Todo: Only the same group chat id with the others
            // will be generated on the $client->send

            // first, you are sending to all existing users message of 'new'

            foreach ($this->clients as $client)
            {
                // here we are sending a status-message
                // $client->send(json_encode($data)); 
                $client->send(json_encode($fa)); 
            }


        }

        else if(isset($queryarray['private_chat_with']))
        {

            $group_chat = new \GroupChatTeacher;
 
            
            // Updating the Student And Teacher Receiver Connection. Be Careful.

            $teacherChatUser = new \TeacherChatUser;

            $data['status_type'] = 'Online';

            $data['my_username'] = $queryarray['private_chat_with'];

            // Update userConnection by the $data['my_username']
            // If Teacher was logged In
            $didTeacherUpdate = $teacherChatUser
                ->UpdateTeacherLatestConnectionId($data['my_username'], 
                    $conn->resourceId);

            if($didTeacherUpdate == false){
                // If Student was logged In
                $didStudentUpdate = $teacherChatUser
                    ->UpdateStudentLatestConnectionId($data['my_username'],
                        $conn->resourceId);

                if($didStudentUpdate == false){
                    echo "Connection Updateing to student error \n";
                }
            }

            $receiverConnectionIdViaUsername = $group_chat->
                GetStudentReceiverConnectionIdv2($data['my_username']);

            // if($didTeacherUpdate == true 
            //     || $didStudentUpdate == true && $didTeacherUpdate == false ){


            // }

            $data['student_latest_connection_id'] = $receiverConnectionIdViaUsername;
            // first, you are sending to all existing users message of 'new'
            foreach ($this->clients as $client)
            {
                // here we are sending a status-message
                $client->send(json_encode($data)); 
            }
        }

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $numRecv = count($this->clients) - 1;

        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');


        $data = json_decode($msg, true);

        // echo $data['clicked_username'];
        
        // $chat_data = json_decode($msg, true);
        // echo $chat_data;
        // $userLoggedInObj = new Teacher($con, $data['login_teacher_username']);


        // if($data['commando'] == 'privateMessage')
        if(isset($data['commando']) && $data['commando'] == 'privateMessage')
        {


            $group_chat = new \GroupChatTeacher;

            $timestamp = date('Y-m-d h:i:s');
            $data['created_at'] = $timestamp;

            $receiver_userid = $data['receiver_userid'];

            $receiverConnectionId = $group_chat->
                GetStudentReceiverConnectionId($receiver_userid);

            if($receiverConnectionId == -1){
                $receiverConnectionId = $group_chat->
                    GetTeacherReceiverConnectionId($receiver_userid);
            }
            
            $message = new \MessageChat;
            // if($data['userType'] == "student"){
            //     $message->setFromUsername($data['sender_username']);
            //     $message->setToUsername($data['receiver_username']);
            //     $message_id = $message->save_message();
            // }

            $message->setFromUsername($data['sender_username']);
            $message->setToUsername($data['receiver_username']);
            $message->setMessageBody($data['private_message']);

            // echo $data['sender_username'];
            // echo "br";
            // echo $data['receiver_username'];

            $message_id = $message->save_message();
            if($message_id != 0){
                echo "success";
            }else{
                echo "failed";
            }

            // echo $data['sender_id'];

            // In student_teacher_message side, the sender (teacher)
            $username = $group_chat->GetSenderTeacherName($data['sender_username']);

            // In teacher_student_message side, the sender (student)
            if($username == ""){
                $username = $group_chat->GetSenderStudentName($data['sender_username']);
                echo "  " . $username . "\n";
            }

            foreach($this->clients as $client)
            {
                if($from == $client)
                {
                    // Updating the data['from']
                    $data['from'] = 'Me';
                }
                else
                {
                    $data['from'] = $username;
                }

                // $data['userLoggeedInConnectionId'] = $client->resourceId;
                // $data['receiverConnectionId'] = $receiverConnectionId;
                // $client->send(json_encode($data));
 
                if($client->resourceId == $receiverConnectionId 
                    // && $connection_id ==  $receiverConnectionId 
                    || $from == $client
                    )
                {
                    
                    $data['sender_name_translate'] = $username;
                    $data['userLoggeedInConnectionId'] = $client->resourceId;
                    $data['receiverConnectionId'] = $receiverConnectionId;
                    // $data['clicked_connection_id'] = $connection_id;
                    $client->send(json_encode($data));
                }

            }
        }
        else if(isset($data['command']) &&  $data['command'] == 'privateGroupChat')
        {
            $querystring = $from->httpRequest->getUri()->getQuery();
            
            parse_str($querystring, $queryarray);

            if(isset($queryarray['group_chat_id']) && isset($queryarray['username']))
            {
                $my_group_chat_id = intval($queryarray['group_chat_id']);
                $my_username = $queryarray['username'];
                
                if($my_group_chat_id == 0 || $my_group_chat_id == null || $my_username == "" || $my_username==null){
                    echo "Something went wrong on getting  the websocket url parameter";
                }
            }

            // Private chat Saving Data to DB

            $group_chat = new \GroupChatTeacher;

            $group_chat->setGroupChatId($data['client_group_chat_id']);
            $group_chat->setUsername($data['login_username']);
            $group_chat->setMessageBody($data['msg']);

            $timestamp = date('Y-m-d h:i:s');

            $data['created_at'] = $timestamp;
            $groupMessageId = $group_chat->save_group_chat();

            $username = "";

            $username = $group_chat->
                get_username_for_teacher($data['login_username']);

            // echo $username;

            if($username == ""){
                // Used for student.
                $username = $group_chat->
                    get_username_for_student($data['login_username']);
            }

            // else{
            //     $username = "Couldnt get the username";
            // }

            $client_group_chat_id = $data['client_group_chat_id'];

            $receiverConnectionId = "";

            echo $data['msg'];

            // $asd = $_SESSION['teacher_groupchat_id'];
            // echo $asd;

            // $checkUserBelongToTheGroupChat = $group_chat
            //     ->CheckUserBelongToTheGroupChat($my_group_chat_id, $$data['login_username']);

            echo $my_group_chat_id;
            echo "br";
            echo $data['login_username'];

            foreach($this->clients as $client)
            {
                // if ($from !== $client) {
                //     // $client->send($msg);
                //     // $client->send(json_encode($data));
                //     $client->send($msg);
                // }

                if($from == $client)
                {
                    // Updating the data['from']
                    $data['from'] = 'Me';
                }
                else
                {
                    $data['from'] = $username;
                }
                // Group Chat Id in the front compare to the 
                // Group chat id that the student currently joined.
                // if($group_chat_id == $my_group_chat_id){

                if($my_group_chat_id == $client_group_chat_id){
                    

                    $data['resourceId'] = $client->resourceId;
                    // Client group chat_id
                    // $data['server_group_chat_id'] = $group_chat_id;
                    $data['ws_my_group_chat_id'] = $my_group_chat_id;

                    $client->send(json_encode($data));

                }
                // $data['userLoggeedInConnectionId'] = $client->resourceId;
                // $data['receiverConnectionId'] = $receiverConnectionId;

                    // $data['resourceId'] = $client->resourceId;
                    // $client->send(json_encode($data));

            }
        }

        // foreach ($this->clients as $client) {
        //     if ($from !== $client) {
        //         // The sender is not the receiver, send to each client connected
        //         $client->send($msg);
        //     }
        // }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}


