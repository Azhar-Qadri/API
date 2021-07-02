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
//    $phno = $_POST['phno'];
    $email = mysqli_real_escape_string($db,$_POST['email']);
   
   
    $n = 6; 
    $gen_otp = $users->generateNumericOTP($n); 

    if ($gen_otp) {
    http_response_code(200);
             $response["status"] = true;
             $response["message"] =  "New Password is Sent in your Verified Email ID.";
             $forgotPassword = $users->forgotPassword($email,$gen_otp);
             

    echo json_encode($response);
} else{
      http_response_code(404);
      echo json_encode(["status" => false, "OTP" => "-", "message" => "OTP Failed", "data" => null]);

}}else {
    http_response_code(404);
    echo json_encode(["status" => false, "token" => "-", "message" => "Invalid Token", "data" => null]);
}
?>






