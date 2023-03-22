<?php

// require_once("../includes/config.php");

class GroupChatTeacher
{
	private $chat_message_id;
	private $to_user_id;
	private $from_user_id;
	private $chat_message;
	private $timestamp;
	private $status;


	protected $connect;
	private $group_chat_id;
	private $user_username;
	private $body;

	public function __construct()
	{
		require_once('DatabaseConnection.php');
		$db = new DatabaseConnection();
		$this->connect = $db->connect();
	}


    function setGroupChatId($group_chat_id)
	{
		$this->group_chat_id = $group_chat_id;
	}

    function setUsername($user_username)
	{
		$this->user_username = $user_username;
	}

    function setMessageBody($body)
	{
		$this->body = $body;
	}

    function save_group_chat()
	{
		$query = " INSERT INTO group_message 
			(group_chat_id, user_username, body) 
			VALUES (:group_chat_id, :user_username, :body)
		";

		$statement = $this->connect->prepare($query);

		$statement->bindParam(':group_chat_id', $this->group_chat_id);
		$statement->bindParam(':user_username', $this->user_username);
		$statement->bindParam(':body', $this->body);
		$statement->execute();

		return $this->connect->lastInsertId();
    }

	public function get_username_for_teacher($username)
	{
		$query = " SELECT * FROM Teacher 
			WHERE username = :username
		";

		$statement = $this->connect->prepare($query);
		$statement->bindValue(':username', $username);
		$statement->execute();

		$user_data = "";

		if($statement->rowCount() > 0){
			$user_data = $statement->fetch(PDO::FETCH_ASSOC);

			$user_data = $user_data['firstname'] . " " . $user_data['lastname'];

		}

		return $user_data;
		// return $username;
		
	}

	function get_username_for_student($username)
	{
		$query = " SELECT * FROM Student 
			WHERE username = :username
		";

		$statement = $this->connect->prepare($query);

		$statement->bindValue(':username', $username);

		$statement->execute();

		$user_data = "";

		if($statement->rowCount() > 0){
			$user_data = $statement->fetch(PDO::FETCH_ASSOC);

			$user_data = $user_data['firstname'] . " " . $user_data['lastname'];
			
		}

		return $user_data;
		
	}

	public function GetStudentReceiverConnectionIdv2($username) : int
	{
		$query = " SELECT user_connection_id FROM Student 
			WHERE username = :username
		";

		$statement = $this->connect->prepare($query);

		$statement->bindValue(':username', $username);

		$statement->execute();

		$user_data = -1;

		if($statement->rowCount() > 0){
			$user_data = $statement->fetchColumn();
		}

		return $user_data;
	}
	
	public function GetSenderTeacherName($receiver_id) : string
	{
		$query = " SELECT firstname FROM Teacher 
			WHERE username = :username
		";

		$statement = $this->connect->prepare($query);

		$statement->bindValue(':username', $receiver_id);

		$statement->execute();

		$user_data = "";

		if($statement->rowCount() > 0){
			$user_data = $statement->fetchColumn();
		}

		return $user_data;
	}

	public function GetSenderStudentName($receiver_id) : string
	{
		$query = " SELECT firstname FROM Student 
			WHERE username = :username
		";

		$statement = $this->connect->prepare($query);

		$statement->bindValue(':username', $receiver_id);

		$statement->execute();

		$user_data = "";

		if($statement->rowCount() > 0){
			$user_data = $statement->fetchColumn();
		}

		return $user_data;
	}
	public function GetStudentReceiverConnectionId($student_id) : int
	{
		$query = " SELECT user_connection_id FROM Student 
			WHERE student_id = :student_id
		";

		$statement = $this->connect->prepare($query);

		$statement->bindValue(':student_id', $student_id);

		$statement->execute();

		$user_data = -1;

		if($statement->rowCount() > 0){
			$user_data = $statement->fetchColumn();
		}

		return $user_data;
	}

	public function GetTeacherReceiverConnectionId($teacher_id)
	{
		$query = " SELECT user_connection_id FROM Teacher 
			WHERE teacher_id = :teacher_id
		";

		$statement = $this->connect->prepare($query);

		$statement->bindValue(':teacher_id', $teacher_id);

		$statement->execute();

		$user_data = "";

		if($statement->rowCount() > 0){
			$user_data = $statement->fetchColumn();
		}

		return $user_data;
	}

	public function CheckUserBelongToTheGroupChat($my_group_chat_id, $username)
	{
		$query = " SELECT group_chat_member_id FROM group_chat_member 
			WHERE group_chat_id = :group_chat_id
			AND user_username=:user_username
		";

		$statement = $this->connect->prepare($query);

		$statement->bindValue(':group_chat_id', $my_group_chat_id);
		$statement->bindValue(':user_username', $username);

		$statement->execute();

		if($statement->rowCount() > 0){
			return true;
		}

		return false;

	}
}

?>