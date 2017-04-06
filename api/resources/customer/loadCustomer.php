<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 4:39 PM
 */

function loadCustomer($mobile){

    $sql = "SELECT
                    e.id as org_id, e.name,e.logo, a.type,sum(a.amount) as sum
                    FROM
                    `transactions` as a inner join
                    customers as b INNER JOIN
                    associates as c INNER JOIN
                    stores as d INNER JOIN
                    orgs as e

                    WHERE

                    a.customer_id = b.id and
                    a.associate_id = c.id AND
                    c.store_id = d.id and
                    d.org_id = e.id and
                    b.mobile = :mobile
                    group by a.customer_id,a.type,e.id ";

    try {

        $db = getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam("mobile", $mobile);

        $stmt->execute();
        $tStores = $stmt->fetchAll(PDO::FETCH_OBJ);

        $stores = array();
        foreach($tStores as $tStore){
            $done = false;
            $i=0;
            foreach($stores as $store){

                if($tStore->id == $store['id']){
                    $done = true;
                    //var_dump($tStore,$store['trans']);
                    $stores[$i]['trans']= array_merge($store['trans'],  array(array('type'=>$tStore->type,'amount' => $tStore->sum)));


                }
                $i++;
            }
            if($done == false){


                $stores[] = array(
                    'org_id' => $tStore->org_id,

                    'org_name' => $tStore->name,
                    'logo' => $tStore->logo,
                    'trans' => array(array('type'=>$tStore->type,'amount' => $tStore->sum)));

            }
        }


        $db = null;

        echo '{"orgs": ' . json_encode($stores) . '}';



    } catch (Exception $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":"' . $e->getMessage() . '"}}';
    }

}