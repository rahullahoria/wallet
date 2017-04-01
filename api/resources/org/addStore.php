<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 4/1/17
 * Time: 12:46 PM
 */

function addStore($org){

    $request = \Slim\Slim::getInstance()->request();
    $requestJson = json_decode($request->getBody());

    $sql = "INSERT INTO `stores`(`name`, `password`, `address`, `poc_name`, `poc_mobile`, `creation`, `org_id`)
              VALUES (:name,:password,:address,:poc_name,:poc_mobile,:creation,:org)";

    $d =date("Y-m-d H:i:s");


    try {

        $db = getDB();

        $stmt = $db->prepare($sql);
        //var_dump($requestJson);

        $stmt->bindParam("name", $requestJson->name);
        $stmt->bindParam("password", $requestJson->poc_mobile);
        $stmt->bindParam("address", $requestJson->address);
        $stmt->bindParam("poc_name", $requestJson->poc_name);
        $stmt->bindParam("poc_mobile", $requestJson->poc_mobile);
        $stmt->bindParam("creation", $d);
        $stmt->bindParam("org", $org);


        $stmt->execute();

        $requestJson->id = $db->lastInsertId();

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