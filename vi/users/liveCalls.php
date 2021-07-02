<?php

include_once '../config/Database.php';
include_once '../class/Users.php';

$database = new Database();
$db = $database->getConnection();
$users = new Users($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($db,$_POST['email']);
    $token = mysqli_real_escape_string($db,$_POST['token']);
    $active_service = mysqli_real_escape_string($db,$_POST['active_service']);
   
    $chk = $users->tokenCheck($email,$token);
    if ($active_service) {
        http_response_code(200);
        $liveCalls = $users->liveCalls($email, $token, $active_service, $chk);
        $response["status"] = true;
        $response["message"] =  "liveCalls Data Fetched.";
        $response["data"] = $liveCalls;
         
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "liveCalls Data Not Fetch..."));
    }
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode(array("status" => false, "token" => "-", "message" => "Invalid Token", "data" => null));
}
?>






