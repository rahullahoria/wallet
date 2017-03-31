<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 7:36 PM
 */

function getAssociateCustomers($org,$store,$associate){

    $storeSql = "SELECT
                    b.id,b.first_name, b.last_name, b.email,b.mobile, a.type,sum(a.amount) as sum
                    FROM
                    `transactions` as a inner join
                    customers as b
                    WHERE

                    a.customer_id = b.id and
                    b.org_id = :org and
                    a.associate_id = :associate
                    group by a.customer_id,a.type";




    try {

        $db = getDB();
        $stmt = $db->prepare($storeSql);

        $stmt->bindParam("org", $org);
        $stmt->bindParam("associate", $associate);

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
                    $stores[$i]['trans']= array_merge($store['trans'],  array(array($tStore->type => $tStore->sum)));


                }
                $i++;
            }
            if($done == false){


                $stores[] = array(
                    'id' => $tStore->id,

                    'first_name' => $tStore->first_name,
                    'last_name' => $tStore->last_name,
                    'email' => $tStore->email,
                    'mobile' => $tStore->mobile,
                    'trans' => array(array($tStore->type => $tStore->sum)));

            }
        }


        $returnArr['customers'] = $stores;

        $db = null;

        echo '{"org_details": ' . json_encode($returnArr) . '}';



    } catch (Exception $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":"' . $e->getMessage() . '"}}';
    }
}