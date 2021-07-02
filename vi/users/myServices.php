<?php
   include_once '../config/Database.php';
   include_once '../class/Users.php';
   
   $database = new Database();
   $db = $database->getConnection();
   $users = new Users($db);



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $data = array();
    $response = array();
    parse_str(file_get_contents('php://input'), $data);

    $email = mysqli_real_escape_string($db,$_POST['email']);
    $token = mysqli_real_escape_string($db,$_POST['token']);
    
    $chk = $users->tokenCheck($email,$token);
    $res=$token. ' ' .$token;
 
    if ($res) {
        
    	  http_response_code(200);
          $myServices = $users->myServices($email,$token,$chk);
          $response["status"] = true;
          $response["message"] =  "Myservices Data Fetched.";
          $response["data"] = $myServices;
 
    } else {
            http_response_code(404);
            echo json_encode(array("status" => false, "message" => "Myservices Data Not Fetch..."));     	
    }
         echo json_encode($response);
} else{
          http_response_code(404);
          echo json_encode(array("status" => false,"token" => "-", "message" => "Invalid Token","data" => null));
        }
  
?>