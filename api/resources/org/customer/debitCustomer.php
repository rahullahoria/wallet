<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 5:22 PM
 */

function debitCustomer($org){

    $request = \Slim\Slim::getInstance()->request();

    $requestJson = json_decode($request->getBody());

    $balanceSql = "
    select
    (SELECT
                    sum(a.amount)
                    FROM
                    `transactions` as a inner join
                    customers as b INNER JOIN
                    associates as c INNER JOIN
                    stores as d

                    WHERE

                    a.customer_id = b.id and
                    a.associate_id = c.id AND
                    c.store_id = d.id and
                    d.org_id = :org AND
                    b.mobile = :mobile AND
                    a.type != 'debit'
                    group by a.customer_id)
                    -


    ifnull((SELECT
                    sum(a.amount)
                    FROM
                    `transactions` as a inner join
                    customers as b INNER JOIN
                    associates as c INNER JOIN
                    stores as d

                    WHERE

                    a.customer_id = b.id and
                    a.associate_id = c.id AND
                    c.store_id = d.id and
                    d.org_id = :org AND
                    b.mobile = :mobile AND
                    a.type = 'debit'
                    group by a.customer_id)),0) as balance ";

    $creditSql = "INSERT INTO `transactions`(`amount`, `type`, `customer_id`,`associate_id`, sms_otp, status,`validity` )
                    VALUES (:amount,:type,:customer_id,:associate_id,:sms_otp, 'in-process', null)";

    $sqlSelectCustomer = "Select id from customers where mobile=:mobile and org_id=:org";

    try{
        $db = getDB();

        $stmt = $db->prepare($balanceSql);
        //var_dump($requestJson);

        $stmt->bindParam("mobile", $requestJson->mobile);
        $stmt->bindParam("org", $org);


        $stmt->execute();
        $balance = $stmt->fetchAll(PDO::FETCH_OBJ);

        if($balance[0]->balance < $requestJson->amount )
            echo '{"error":{"text":"Insufficient Balance"}}';
        else {

            $stmt = $db->prepare($sqlSelectCustomer);
            //var_dump($requestJson);

            $stmt->bindParam("mobile", $requestJson->mobile);
            $stmt->bindParam("org", $org);


            $stmt->execute();
            $cust = $stmt->fetchAll(PDO::FETCH_OBJ);

            if (count($cust) == 0) {
                echo '{"error":{"text":"This user is not wallet user"}}';

            } else {
                $requestJson->customer_id = $cust[0]->id;
                $requestJson->transaction_id[] = $db->lastInsertId();

                $stmt = $db->prepare($creditSql);
                $otp = getOTP();
                $message = "Debit of Rs.$requestJson->amount will happen from your account\nyour OTP\n " . $otp;
                sendSMS($requestJson->mobile, $message);
                //var_dump($requestJson);
                $type = 'debit';
                $stmt->bindParam("amount", $requestJson->amount);
                $stmt->bindParam("type", $type);
                $stmt->bindParam("customer_id", $requestJson->customer_id);
                $stmt->bindParam("associate_id", $requestJson->associate_id);
                $stmt->bindParam("sms_otp", $otp);
                //$stmt->bindParam("validity", null);


                $stmt->execute();


                $requestJson->transaction_id[] = $db->lastInsertId();
            }
        }

    }catch(Exception $e){

    }


}