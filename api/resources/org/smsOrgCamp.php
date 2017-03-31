<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/31/17
 * Time: 4:23 PM
 */

function smsOrgCamp($org,$sms){
    $storeSql = "SELECT
                    DISTINCT
                    b.mobile
                    FROM
                    `transactions` as a inner join
                    customers as b
                    WHERE

                    a.customer_id = b.id and
                    b.org_id = :org
                    ";

    try {

        $db = getDB();
        $stmt = $db->prepare($storeSql);

        $stmt->bindParam("org", $org);

        $stmt->execute();
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        foreach($customers as $customer){
            sendSMS($customer->mobile,$sms);
        }
        $db = null;

        echo '{"org_details": ' . json_encode(array('sms_sent'=>count($customers))) . '}';



    } catch (Exception $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":"' . $e->getMessage() . '"}}';
    }
}