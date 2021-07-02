<?php

include_once '../config/Database.php';
include_once '../class/Users.php';

$database = new Database();
$db = $database->getConnection();
$users = new Users($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = mysqli_real_escape_string($db,$_POST['email']);
    $otp = mysqli_real_escape_string($db,$_POST['otp']);
        

$verifyOtp = $users->verifyOtp($email,$otp);
 if ($verifyOtp) {

             http_response_code(200);
             $response["status"] = true;
             $response["message"] =  "OTP is Verified";
             echo json_encode($response);
} else{
      http_response_code(404);
      echo json_encode(["status" => false, "message" => "OTP Verification Failed"]);

}
}
?>






