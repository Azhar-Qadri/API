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

        if ($chk) {
            
      
           http_response_code(200);
            $getActiveServices = $users->getActiveServices($email,$chk);
            $getExpireServices = $users->getExpireServices($email,$chk);
            $active=$users->subArraysToString($getActiveServices);
            $expire=$users->subArraysToString($getExpireServices);
          

           $response["status"] = true;
           $response["message"] =  "Services Data Fetched.";
           $response["data"]["active_service"] = $active;
           $response["data"]["expire_service"] = $expire;

            echo json_encode($response);

} else{
          http_response_code(404);
          echo json_encode(array("status" => false,"token" => "-", "message" => "Invalid Token","data" => null));
        }
  
}
?>