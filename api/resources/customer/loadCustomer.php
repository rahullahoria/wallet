<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 4:39 PM
 */

function loadCustomer($mobile){

    $sql = "SELECT
            `id`, `org_id`,
            `associate_id`,
            `first_name`,
            `last_name`,
            `mobile`,
            `email`,
            `aadhaar`,
            `dob`,
            `gender`,
            `address`,
            `creation`, `last_update` FROM `customers` WHERE mobile = :mobile";

    try {

        $db = getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam("mobile", $mobile);

        $stmt->execute();
        $resp['customer'] = $stmt->fetchAll(PDO::FETCH_OBJ);



        $options = str_getcsv($str, ',', "'");
        $db = null;

        echo '{"company_type": ' . json_encode($options) . '}';



    } catch (Exception $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":"' . $e->getMessage() . '"}}';
    }

}