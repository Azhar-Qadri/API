<?php 
class Database{

// LOCAL
	
		private $host = 'localhost';
		private $user = 'root';
		private $password = '';
		private $database = 'equitypandit';

// LIVE

		// private $host = 'localhost';
		// private $user = 'aparakh_equity_user';
		// private $password = 'Capi@2013';
		// private $database = 'aparakh_equity';

		public function getConnection()
		{
			$conn = new mysqli($this->host,$this->user,$this->password,$this->database);

			if ($conn->connect_error) {
				die("Error failed to connect Mysqli:".$conn->connect_error);
			}else{
				return $conn;
			}
		}

}





 ?>