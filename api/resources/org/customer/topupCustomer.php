<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 4:52 PM
 */

function topupCustomer($org){

    $request = \Slim\Slim::getInstance()->request();

    $requestJson = json_decode($request->getBody());



    $sql = "INSERT INTO `customers`
          ( `org_id`, `associate_id`, `mobile`,`creation`)
          VALUES (:org,:associate_id,:mobile,:creation)";

    $sqlSelectCustomer = "Select id from customers where mobile=:mobile and org_id=:org";

    $creditSql = "INSERT INTO `transactions`(`amount`, `type`, `customer_id`,`associate_id`, sms_otp, status,`validity` )
                    VALUES (:amount,:type,:customer_id,:associate_id,:sms_otp, 'in-process', null)";

    $d =date("Y-m-d H:i:s");


    try {

        $db = getDB();

        $stmt = $db->prepare($sqlSelectCustomer);
        //var_dump($requestJson);

        $stmt->bindParam("mobile", $requestJson->mobile);
        $stmt->bindParam("org", $org);


        $stmt->execute();
        $cust = $stmt->fetchAll(PDO::FETCH_OBJ);

        if(count($cust) == 0) {
            $stmt = $db->prepare($sql);
            //var_dump($requestJson);

            $stmt->bindParam("org", $org);
            $stmt->bindParam("associate_id", $requestJson->associate_id);
            $stmt->bindParam("mobile", $requestJson->mobile);

            $stmt->bindParam("creation", $d);

            $stmt->execute();


            $requestJson->customer_id = $db->lastInsertId();
        }else{
            $requestJson->customer_id = $cust[0]->id;
        }

        if($requestJson->customer_id) {
            $stmt = $db->prepare($creditSql);
            //var_dump($requestJson);
            $type = 'credit';
            $otp = getOTP();
            $message = "Credit of Rs.".($requestJson->amount+$requestJson->amount*0.1)." will happen to your account\nyour OTP\n " . $otp;
            sendSMS($requestJson->mobile, $message);
            $stmt->bindParam("amount", $requestJson->amount);
            $stmt->bindParam("type", $type);
            $stmt->bindParam("customer_id", $requestJson->customer_id);
            $stmt->bindParam("associate_id", $requestJson->associate_id);
            $stmt->bindParam("sms_otp", $otp);
            //$stmt->bindParam("validity", null);


            $stmt->execute();


            $requestJson->transaction_id[] = $db->lastInsertId();

            $stmt = $db->prepare($creditSql);
            //var_dump($requestJson);
            $type = 'gift_credit';
            $amount =$requestJson->amount*0.1;
            $stmt->bindParam("amount", $amount);
            $stmt->bindParam("type", $type);
            $stmt->bindParam("customer_id", $requestJson->customer_id);
            $stmt->bindParam("associate_id", $requestJson->associate_id);
            $stmt->bindParam("sms_otp", $otp);
            //$stmt->bindParam("validity", null);


            $stmt->execute();


            $requestJson->transaction_id[] = $db->lastInsertId();

        }

        /*if(count($requestJson->transaction_id) >=1){
            $message = "You topup of Rs.".($requestJson->amount+$requestJson->amount*0.1)." done successfully.\ncheck your balance @\nhttps://wallet.shatkonlabs.com/me";
            sendSMS($requestJson->mobile, $message);


        }*/
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