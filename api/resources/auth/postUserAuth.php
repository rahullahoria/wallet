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
            $sql = "SELECT  `id`, `id` as org_id, `name`, `logo`, `address`, `city`, `poc_name`, `poc_mobile`, `password`, `creation`, `last_update`
                        FROM orgs where poc_mobile=:mobile and password=:password;";
            break;
        case 'store':
            $sql = "SELECT `id`, `id` as store_id , `name`, `password`, `address`, `location`, `poc_name`, `poc_mobile`, `creation`, `last_update`, `org_id`
                FROM stores where poc_mobile=:mobile and password=:password;";
            break;
        case 'till':
            $sql = "SELECT a.id, a.name, a.mobile, b.id as store_id, b.name as store_name, c.name as org_name, c.id as org_id
                FROM associates as a INNER JOIN stores as b INNER JOIN orgs as c where a.store_id = b.id and b.org_id = c.id
                and a.mobile=:mobile and a.password=:password;";
            break;
        default:
            $sql = "SELECT *
                FROM customers where mobile=:mobile and password=:password;";

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


