<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 6/22/16
 * Time: 2:13 PM
 */


function checkUser($mobile){


    $sql = "SELECT *
                FROM customers where mobile=:mobile ;";

    $sqlUpdateOtp = "UPDATE customers set sms_otp =:otp WHERE id=:id";




    try {
        $db = getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam("mobile", $mobile);


       // var_dump($user);die();

        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);


        $db = null;

        if(count($users) == 1)
            if($users[0]->password)
                echo '{"password": "true","auth": "true"}';
            else {
                $otp = getOTP();
                $message = "Your Account Registration OTP\n " . $otp;
                sendSMS($mobile, $message);

                $stmt = $db->prepare($sqlUpdateOtp);

                $stmt->bindParam("otp", $otp);
                $stmt->bindParam("id", $users[0]->id);


                // var_dump($user);die();

                $stmt->execute();

                echo '{"password": "false","auth": "true"}';
            }
        else
            echo '{"auth": "false"}';


    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}


