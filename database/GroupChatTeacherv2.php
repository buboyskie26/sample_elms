<?php

// require_once("../includes/config.php");

class GroupChatTeacherv2
{

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


}

?>