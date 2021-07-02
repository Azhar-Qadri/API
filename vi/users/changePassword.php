<?php


include_once '../config/Database.php';
include_once '../class/Users.php';

$database = new Database();
$db = $database->getConnection();
$users = new Users($db);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($db,$_POST['email']);
    $token = mysqli_real_escape_string($db,$_POST['token']);
    $password = mysqli_real_escape_string($db,$_POST['password']);
    $new = mysqli_real_escape_string($db,$_POST['new']);
   
    $chk = $users->tokenCheck($email, $token);

    if ($chk) {
    if ($password) {
        http_response_code(200);
        $changePassword = $users->changePassword($email, $token, $password, $chk, $new);
           $response["status"] = true;
           $response["message"] =  "Password Changed Successfully.";
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Password Not Changed."]);
    }
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode(["status" => false, "token" => "-", "message" => "Invalid Token", "data" => null]);
}
}
?>






