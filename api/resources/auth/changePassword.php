<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 4/1/17
 * Time: 12:49 PM
 */

function changePassword(){

    $request = \Slim\Slim::getInstance()->request();

    $user = json_decode($request->getBody());

    switch($user->type){
        case 'org':
            $sql = "update orgs set password=:password where id=:org";
            break;
        case 'store':
            $sql = "update stores set password=:password where id=:store";
            break;
        case 'till':
            $sql = "update associates set password=:password where id=:associate";
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