<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/4/17
 * Time: 10:43 AM
 */

function checkCustomerTransactionOtp($org,$mobile, $otp){


    $sql = "SELECT a.id,a.amount,a.type
                FROM transactions as a INNER JOIN customers as b
                 WHERE a.customer_id = b.id AND
                  b.org_id = :org AND
                  b.mobile =:mobile AND
                  a.status = 'in-process'
                 and a.sms_otp =:otp ;";


    $updateOTP = "update transactions set status = 'done' where id=:id";


    try {
        $db = getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam("org", $org);
        $stmt->bindParam("mobile", $mobile);
        $stmt->bindParam("otp", $otp);

        // var_dump($user);die();

        $stmt->execute();
        $trans = $stmt->fetchAll(PDO::FETCH_OBJ);




        if(count($trans) >= 1) {
            foreach($trans as $tran) {
                $stmt = $db->prepare($updateOTP);
                $stmt->bindParam("id", $tran->id);

                $stmt->execute();
            }
            if($trans[0]->type == 'debit'){
                $at = $trans[0]->amount;
            }
            else
                $at = $trans[0]->amount+$trans[0]->amount*0.1;
            $message = $trans[0]->type." of Rs.".$at." done successfully.\ncheck your balance @\nhttps://wallet.shatkonlabs.com/me";
            sendSMS($mobile, $message);


            echo '{"transactions": ' . json_encode($trans) . ',"auth": "true"}';
        }
        else
            echo '{"auth": "false"}';
        $db = null;


    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }

}