<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 5:22 PM
 */

function debitCustomer($org, $mobile, $amount){

    $request = \Slim\Slim::getInstance()->request();

    $requestJson = json_decode($request->getBody());

    $creditSql = "INSERT INTO `transactions`(`amount`, `type`, `customer_id`,`associate_id`, sms_otp, status,`validity` )
                    VALUES (:amount,:type,:customer_id,:associate_id,:sms_otp, 'in-process', null)";

    $sqlSelectCustomer = "Select id from customers where mobile=:mobile and org_id=:org";

    try{
        $db = getDB();

        $stmt = $db->prepare($sqlSelectCustomer);
        //var_dump($requestJson);

        $stmt->bindParam("mobile", $requestJson->mobile);
        $stmt->bindParam("org", $org);


        $stmt->execute();
        $cust = $stmt->fetchAll(PDO::FETCH_OBJ);

        if(count($cust) == 0){
            echo '{"error":{"text":"This user is not wallet user"}}';

        }
        else {
            $requestJson->customer_id = $cust[0]->id;
            $requestJson->transaction_id[] = $db->lastInsertId();

            $stmt = $db->prepare($creditSql);
            $otp = getOTP();
            $message = "Debit of Rs.$amount will happen from your account\nyour OTP\n " . $otp;
            sendSMS($requestJson->mobile, $message);
            //var_dump($requestJson);
            $type = 'debit';
            $stmt->bindParam("amount", $amount);
            $stmt->bindParam("type", $type);
            $stmt->bindParam("customer_id", $requestJson->customer_id);
            $stmt->bindParam("associate_id", $requestJson->associate_id);
            $stmt->bindParam("sms_otp", $otp);
            //$stmt->bindParam("validity", null);


            $stmt->execute();


            $requestJson->transaction_id[] = $db->lastInsertId();
        }

    }catch(Exception $e){

    }


}