<?php

//Database_connection.php
    date_default_timezone_set("Asia/Manila");

class DatabaseConnection
{
	function connect()
	{
		// $connect = new PDO("mysql:host=localhost; dbname=chat_rachet",
		// 	"root", "");
		// return $connect;

		try {

		    $connect = new PDO('mysql:host=localhost;port=3307;dbname=elms', 'root', '');
			// $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			
			return $connect;
    	}
		catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}

	}
}

?>