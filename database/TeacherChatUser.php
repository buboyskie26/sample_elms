<?php

// require_once("../includes/config.php");

class TeacherChatUser
{

	protected $connect;

	public function __construct()
	{
		require_once('DatabaseConnection.php');
		$db = new DatabaseConnection();
		$this->connect = $db->connect();
	}
 

	public function UpdateStudentLatestConnectionId($username, $connectionId) : bool
	{
		$query = " SELECT student_id FROM Student 
			WHERE username = :username
		";

        $didUpdate = false;

		$statement = $this->connect->prepare($query);
		$statement->bindValue(':username', $username);
		$statement->execute();

        if($statement->rowCount() > 0){
            // Update
            $update = $this->connect->prepare("UPDATE Student
                SET user_connection_id=:user_connection_id
                WHERE username=:username");

            $update->bindParam(':username', $username);
            $update->bindParam(':user_connection_id', $connectionId);
            $didUpdate = $update->execute();
        }

		return $didUpdate;
		
	}
    public function UpdateTeacherLatestConnectionId($username,
    $connection) : bool
	{
		$query = " SELECT teacher_id FROM Teacher 
			WHERE username = :username
		";

        $didUpdate = false;

		$statement = $this->connect->prepare($query);
		$statement->bindValue(':username', $username);
		$statement->execute();

        if($statement->rowCount() > 0){
            // Update
            $update = $this->connect->prepare("UPDATE Teacher
                SET user_connection_id=:user_connection_id
                WHERE username=:username");

            $update->bindParam(':user_connection_id', $connection);
            $update->bindParam(':username', $username);
            $didUpdate = $update->execute();
        }

		return $didUpdate;
		
	}
}

?>