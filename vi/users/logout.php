<?php
   // header("Access-Control-Allow-Origin: *");
   // header("Content-Type: application/json; charset=UTF-8");
   // header("Access-Control-Allow-Methods: POST");
   // header("Access-Control-Max-Age: 3600");
   // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
   
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
          
           $logout = $users->logout($email,$token,$chk);
           

           $response["status"] = true;
           $response["message"] =  "User logout.";

            echo json_encode($response);

} else{
          http_response_code(404);
          echo json_encode(array("status" => false,"token" => "-", "message" => "Invalid Token","data" => null));
        }
}  
?>