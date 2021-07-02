<?php
   class Users
   {
       public function __construct($db)
       {
           $this->conn = $db;
       }
   
       function tokenCheck($phno, $token)
       {
           $stmt_check_token = $this->conn->prepare("SELECT * FROM ep_user_details WHERE phno = ? and token = ?");
           $stmt_check_token->bind_param("is", $phno,$token);
           $stmt_check_token->execute();
           $stmt_check_token = $stmt_check_token->get_result();      
           if ($stmt_check_token) {
               return $stmt_check_token;
           } else {    
               return false;
           }
    }
   
       function fetchEmail($phno){
           
               $stmt_select_email = $this->conn->prepare("SELECT email FROM ep_user_details WHERE phno = ?");
               $stmt_select_email->bind_param("i", $phno);
               $stmt_select_email->execute();
               $execute = $stmt_select_email->get_result();      
               if ($execute->num_rows > 0) {
               while ($row = mysqli_fetch_array($execute, MYSQLI_ASSOC)) {
                       $show = $row;
                   }
                   $result = $show;
                   return $result;
           } else {
               return "Mobile number not matched";
           }
       }
       function subArraysToString($myarr, $sep = ',')
       {
           $mystr = '';
           foreach ($myarr as $val) {
               $mystr .= implode($sep, $val);
               $mystr .= $sep; // add separator between sub-arrays
           }
           $mystr = rtrim($mystr, $sep); // remove last separator
           return $mystr;
       }
   
       function login($phno, $password)
       {
           $stmt_login = $this->conn->prepare("SELECT * FROM ep_user_details WHERE phno = ? and password = ?");
           $stmt_login->bind_param("is", $phno,$password);
           $stmt_login->execute();
           $login = $stmt_login->get_result();      
           $result = $login->fetch_assoc();
                   
           if ($result) {
               return $result;
           } else {    
               return false;
               
               }
       }
   
       function addToken($token, $phno)
       {
           $stmt_add_token = $this->conn->prepare("UPDATE `ep_user_details` SET token = ? ,login_status = 1 WHERE phno = ?");
           $stmt_add_token->bind_param("si",$token, $phno);
           if ($stmt_add_token->execute()) {
               return true;
           } else {    
               return false;
           }
       }
   
       function deviceType($email, $fcm_id, $device_type)
       {
           $response = [];
          
   
               $stmt_sql = $this->conn->prepare("SELECT * FROM `ep_fcm_ids` WHERE email = ?");
               $stmt_sql->bind_param("s", $email);
               $stmt_sql->execute();
               $run = $stmt_sql->get_result();      
               if ($run->num_rows > 0) {
               $stmt_type_update_sql = $this->conn->prepare("UPDATE `ep_fcm_ids` SET fcm_id = ?,device_type= ? WHERE email = ?");
               $stmt_type_update_sql->bind_param("iss", $fcm_id,$device_type,$email);
               $update=$stmt_type_update_sql->execute();
               $response = $update;
           } else {
             $stmt_type_insert_run = $this->conn->prepare("INSERT INTO ep_fcm_ids (email, fcm_id, app, deviceType) VALUES ('?, ?,'-', ?)");
               $stmt_type_insert_run->bind_param("sss", $fcm_id,$device_type,$email);
               $insert=$stmt_type_insert_run->execute();
               $response = $insert;
           }
           return $response;
       }
   
       function getActiveServices($email, $chk)
       {
           $response = [];
           $message = [];
           $act = [];
           date_default_timezone_set('Asia/Kolkata');
           $todayDate = date('Y-m-d');
   
           if ($chk) {
               $stmt_active = $this->conn->prepare("SELECT sname FROM `ep_userservices` WHERE email = ? and expire_on > '$todayDate'");
               $stmt_active->bind_param("s", $email);
               $stmt_active->execute();
               $active = $stmt_active->get_result();      
               if ($active->num_rows > 0) {
                   while ($row = mysqli_fetch_array($active, MYSQLI_ASSOC)) {
                       $act[] = $row;
                   }
                   $active_service = $act;
                   return $active_service;
               }
           }
       }
   
       function getExpireServices($email, $chk)
       {
           $response = [];
           $message = [];
           $exp = [];
           date_default_timezone_set('Asia/Kolkata');
           $todayDate = date('Y-m-d');
           if ($chk) {
             
               $stmt_expire = $this->conn->prepare("SELECT sname FROM `ep_userservices` WHERE email = ? and expire_on < '$todayDate'");
               $stmt_expire->bind_param("s", $email);
               $stmt_expire->execute();
               $expire = $stmt_expire->get_result();      
          
               if ($expire->num_rows > 0) {
   
                   while ($row = mysqli_fetch_array($expire, MYSQLI_ASSOC)) {
                       $exp[] = $row;
                   }
                   $expire_service = $exp;
                   return $expire_service;
               }
           }
       }
   
       function logout($email, $token, $chk)
       {
           if ($chk) {
              $stmt_logout = $this->conn->prepare("UPDATE `ep_user_details` SET token='-',login_status = 0 WHERE email= ?");
               $stmt_logout->bind_param("s", $email);
             
                   if ($stmt_logout->execute()) {
                       return true;
                   } else {
                       return false;
                       
                       }
           }
       }
   
       function myServices($email, $token, $chk)
       {
           $response = [];
           $message = [];
   
           if ($chk) {
              
         $stmt = $this->conn->prepare("SELECT * FROM ep_userservices WHERE email = ? and expire_on > '2021-06-11'");
               $stmt->bind_param("s", $email);
               $stmt->execute();
               $result = $stmt->get_result();      
          
               if ($result->num_rows > 0) {
                   $myServices = [];
   
                   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                       $myServices[] = $row;
                   }
                   $response = $myServices;
               } else {
                   $response["status"] = false;
                   $response["message"] = "No Services Found!";
                   $response["data"] = null;
               }
           } else {
               $response["status"] = false;
               $response["message"] = "Invalid Token";
               $response["data"] = null;
           }
           return $response;
            $stmt->close();
            $this->conn->close();
       }
   
       function liveCalls($email, $token, $active_service, $chk)
       {
           $response = [];
           $message = [];
           $msg = [];
   
           $stmt = $this->conn->prepare("SELECT msg_id, sname,call_main_msg_id, call_type, message, sent_on FROM ep_message where sname in ('EP_BASIC') and sent_on like '%2021-01-11%' order by msg_id desc");
           $stmt->execute();
           $result = $stmt->get_result();      
           if ($result->num_rows > 0) {
               $message = [];
               while ($todays_calls = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                   $msg['msg_id'] = $todays_calls['msg_id'];
                   $msg['sname'] = $todays_calls['sname'];
                   $msg['call_type'] = $todays_calls['call_type'];
                   $msg['sent_on'] = $todays_calls['sent_on'];
   
                   if ($todays_calls['call_type'] == "FOLLOWUP") {
                       $msg['follow_up'] = $todays_calls['message'];
   
                       $stmt_get_main_call = $this->conn->prepare("SELECT * FROM ep_message where msg_id = ?");
                       $stmt_get_main_call->bind_param("s", $todays_calls['call_main_msg_id']);
                       $stmt_get_main_call->execute();
                       $main_call = $stmt_get_main_call->get_result();   
                       
                       while ($main_call_details = mysqli_fetch_array($main_call, MYSQLI_ASSOC)) {
                           $msg['main_msg'] = $main_call_details['message'];
                       }
                       $message[] = $msg;
                   } else {
                       $msg['follow_up'] = "-";
                       $msg['main_msg'] = $todays_calls['message'];
                   }
               }
               $response = $message;
           } else {
               $response["status"] = false;
               $response["message"] = "Invalid Token";
               $response["data"] = null;
           }
           return $response;
       }
   
       function changePassword($email, $token, $password, $chk, $new)
       {
           $response = [];
           $message = [];
   
           if ($chk) {
               if ($password) {
               $stmt = $this->conn->prepare("UPDATE `ep_user_details` SET password = ? WHERE token = ?");
               $stmt->bind_param("ss", $new, $token);
             
                   if ($stmt->execute()) {
                       return true;
                   } else {
                       return false;
                       
                       }
               }
           }
       }
   
   
   function generateNumericOTP($n) { 
         
       $generator = "1357902468"; 
     
       $result = ""; 
     
       for ($i = 1; $i <= $n; $i++) { 
           $result .= substr($generator, (rand()%(strlen($generator))), 1); 
       } 
     
       return $result; 
   } 
   
   
      function forgotpassword($email,$gen_otp)
      {
           $response = [];
           $message = [];
          
                    
           $stmt_foget_sql = $this->conn->prepare("SELECT * FROM `ep_user_details` WHERE email = ?");
           $stmt_foget_sql->bind_param("s", $email);
           $stmt_foget_sql->execute();
           $forget_result = $stmt_foget_sql->get_result();      
           if ($forget_result->num_rows > 0) { 
                while ($row = mysqli_fetch_array($forget_result, MYSQLI_ASSOC)) {
                       $available = $row;
                   }
                       if ($gen_otp) {
                          $otp=$gen_otp; 
   
                       $stmt_update_otp = $this->conn->prepare("UPDATE `ep_user_details` SET otp = ? WHERE email = ?");
                       $stmt_update_otp->bind_param("is", $otp, $email);
                       if ($stmt_update_otp->execute()) {
                               $fetch_otp = $this->conn->prepare("SELECT otp FROM ep_user_details WHERE email = ?");
                                       $fetch_otp->bind_param("s", $email);
                                       $fetch_otp->execute();
                                       $fetch_otp = $fetch_otp->get_result();      
                                       if ($fetch_otp->num_rows > 0) { 
                                     while ($show_otp = mysqli_fetch_array($fetch_otp, MYSQLI_ASSOC)) {
                                       $get_otp[] = $show_otp;
                                       // $text="Don't worry it happen with everyone, Please use OTP for Verification.";
                                       $msg=$get_otp;
                                       
                                           // if ($get_otp) {
                                           //     $to = "$email";
                                           //     $subject = "Reset your Equity Pandit Password.";
                                           //     $message = "$msg";
                                           //     $from = "azhar.coderr@gmail.com";
                                           //     $header = "From: $from";
   
                                           //     if (mail($to, $subject, $message,$header)) {
                                           //         return "Otp sent on mail";
                                           //     }else{
                                           //         return "Otp sending process failed";
                                           //     }
                                           // }  
                                     } 
                                   }
                           }
                       }
               }
       }
   
   
       function verifyOtp($email,$otp){
   
          
           $stmt_verify = $this->conn->prepare("SELECT otp FROM `ep_user_details` WHERE otp = ? and email = ?");
           $stmt_verify->bind_param("is", $otp,$email);
           $stmt_verify->execute();
           $result_verify = $stmt_verify->get_result();      
           if ($result_verify->num_rows > 0) {
   
                   while ($check = mysqli_fetch_array($result_verify, MYSQLI_ASSOC)) {
                       $verify = $check;
                   }
                       return $verify;
                   }
       
       }
       
       function resetForgotPassword($email,$password){
           $stmt_update_pass_sql = $this->conn->prepare("UPDATE `ep_user_details` SET password = ? WHERE email= ?");
           $stmt_update_pass_sql->bind_param("ss", $password, $email);
             
                   if ($stmt_update_pass_sql->execute()) {
                       return $stmt_update_pass_sql;
                   } else {
                       return false;
                       
                       }  
       }
   
       
   } ?>