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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = mysqli_real_escape_string($db,$_POST['password']);
    $email = mysqli_real_escape_string($db,$_POST['email']);

   

$resetForgotPassword = $users->resetForgotPassword($email,$password);
 if ($resetForgotPassword) {

             http_response_code(200);
             $response["status"] = true;
             $response["message"] =  "Your Password is Reset Successfulyy";
             echo json_encode($response);
} else{
      http_response_code(404);
      echo json_encode(["status" => false, "message" => "Reset password is failed."]);

}
}
?>






