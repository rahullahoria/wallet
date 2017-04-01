<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 7:37 PM
 */

function addAssociate($org,$store){

    $request = \Slim\Slim::getInstance()->request();

    $requestJson = json_decode($request->getBody());



    $sql = "INSERT INTO `associates`
              (`name`, `mobile`, `password`, `store_id`, `creation`)
              VALUES (:name,:mobile,:password,:store,:creation)";



    $d =date("Y-m-d H:i:s");


    try {

        $db = getDB();

        $stmt = $db->prepare($sql);
        //var_dump($requestJson);

        $stmt->bindParam("name", $requestJson->name);
        $stmt->bindParam("mobile", $requestJson->mobile);
        $stmt->bindParam("password", $requestJson->mobile);
        $stmt->bindParam("store", $store);
        $stmt->bindParam("creation", $d);

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