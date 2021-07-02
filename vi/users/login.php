<?php

include_once '../config/Database.php';
include_once '../class/Users.php';

$database = new Database();
$db = $database->getConnection();
$users = new Users($db);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = [];
    $response = [];

    parse_str(file_get_contents('php://input'), $data);

        // Get Varibales

            $phno = mysqli_real_escape_string($db,$_POST['phno']);
            $password = mysqli_real_escape_string($db,$_POST['password']);
            $fcm_id = mysqli_real_escape_string($db,$_POST['fcm_id']);
            $device_type = mysqli_real_escape_string($db,$_POST['device_type']);

        // GenerateToken

            date_default_timezone_set('Asia/Kolkata');
            $date = date('y-m-d h:i:s');
            $token = sha1($phno . $password . $date);
            $token_in = $users->addToken($token, $phno);
      
        // FCM
      
            $fetch = $users->fetchEmail($phno);
            $email = $fetch['email'];
            $deviceType = $users->deviceType($email, $fcm_id, $device_type);
            $login = $users->login($phno, $password);

        // Call Functions
           
            $chk = $users->tokenCheck($phno,$token);
            $getActiveServices = $users->getActiveServices($email,$chk);
            $getExpireServices = $users->getExpireServices($email,$chk);
            $active=$users->subArraysToString($getActiveServices);
            $expire=$users->subArraysToString($getExpireServices);
        
        if ($login) {
            http_response_code(200);
            $response["status"] = true;
            $response["message"] = "Login Successfully Done and Data Fetched.";
            $response["data"] = $login;
            $response["data"]["acitive_service"] = $active;
            $response["data"]["expire_service"] = $expire;
        } else {
            http_response_code(404);
            echo json_encode(["status" => false, "message" => "Login credentials are wrong."]);
        }

    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode(["status" => false, "token" => "-", "message" => "Invalid Token", "data" => null]);
}
?>
   
         