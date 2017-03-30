<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 6/22/16
 * Time: 2:13 PM
 */


function userAuth(){

    $request = \Slim\Slim::getInstance()->request();

    $user = json_decode($request->getBody());

    switch($user->type){
       case 'org':
            $sql = "SELECT *
                        FROM orgs where poc_mobile=:mobile and password=:password;";
            break;
        case 'store':
            $sql = "SELECT *
                FROM stores where poc_mobile=:mobile and password=:password;";
            break;
        case 'till':
            $sql = "SELECT *
                FROM associates where mobile=:mobile and password=:password;";
            break;
        default:
            $sql = "SELECT *
                FROM users where mobile=:mobile and password=:password;";

    }




    try {
        $db = getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam("mobile", $user->mobile);
        $stmt->bindParam("password", $user->password);

       // var_dump($user);die();

        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);


        $db = null;

        if(count($users) == 1)
            echo '{"user": ' . json_encode($users[0]) . ',"auth": "true"}';
        else
            echo '{"auth": "false"}';


    } catch (PDOException $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":' . $e->getMessage() . '}}';
    }
}


