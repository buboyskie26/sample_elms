<?php


class MessageChat
{

	protected $connect;
	private $body;
	private $to_username;
	private $from_username;

	public function __construct()
	{
		require_once('DatabaseConnection.php');
		$db = new DatabaseConnection();
		$this->connect = $db->connect();
	}

    function setMessageBody($body)
	{
		$this->body = $body;
	}

    function setToUsername($to_username)
	{
		$this->to_username = $to_username;
	}

    function setFromUsername($from_username)
	{
		$this->from_username = $from_username;
	}
 
    public function save_message()
	{

		$statement = $this->connect->prepare("INSERT INTO message_teacher 
			(to_username, from_username, body) 
			VALUES (:to_username, :from_username, :body)
		");

		$statement->bindValue(':to_username', $this->to_username);
		$statement->bindValue(':from_username', $this->from_username);
		$statement->bindValue(':body', $this->body);
		$statement->execute();
        
		return $this->connect->lastInsertId();
    }


}

?>