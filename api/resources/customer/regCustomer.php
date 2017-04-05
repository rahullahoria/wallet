<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 7:37 PM
 */

function regCustomer($mobile){

    $request = \Slim\Slim::getInstance()->request();

    $requestJson = json_decode($request->getBody());



    $sql = "UPDATE `customers`
              SET `first_name`=:first_name,
              `last_name`=:last_name,
              `password`=:password,
              `email`=:email,
              `dob`=:dob,
              `gender`=:gender,
              `address`=:address
              WHERE mobile=:mobile and sms_otp=:otp";



    try {

        $db = getDB();

        $stmt = $db->prepare($sql);
        //var_dump($requestJson);

        $stmt->bindParam("first_name", $requestJson->first_name);
        $stmt->bindParam("last_name", $requestJson->last_name);

        $stmt->bindParam("password", $requestJson->password);
        $stmt->bindParam("email", $requestJson->email);
        $stmt->bindParam("dob", $requestJson->dob);
        $stmt->bindParam("gender", $requestJson->gender);
        $stmt->bindParam("address", $requestJson->address);

        $stmt->bindParam("mobile", $requestJson->mobile);
        $stmt->bindParam("otp", $requestJson->otp);

        $stmt->execute();

        $requestJson->rows = $stmt->rowCount();


        $db = null;


        echo '{"results": ' . json_encode($requestJson) . '}';



    } catch (Exception $e) {
        $errorMessage = " Already Exists";
        $errors = array('username','mobile','email');
        $flag = false;
        foreach($errors as $error){
            if (strpos($e->getMessage(), $error) !== false) {
                echo '{"error":{"text":"' . $error.$errorMessage.'\nDetails'.$e->getMessage() . '"}}';
                $flag = true;
            }

        }
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        if(!$flag)
            echo '{"error":{"text":"' . $e->getMessage() . '"}}';
    }

}