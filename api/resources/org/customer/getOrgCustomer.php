<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 7:36 PM
 */

function getOrgCustomer($org,$mobile){

    $storeSql = "SELECT
                    b.id,b.first_name, b.last_name, b.email,b.mobile, a.type,sum(a.amount) as sum
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
                    b.mobile = :mobile
                    group by a.customer_id,a.type";
    $orgFloting = "

SELECT a.type, sum( a.amount ) as amount
FROM `transactions` AS a
INNER JOIN associates AS b
INNER JOIN stores AS c
WHERE a.associate_id = b.id
AND b.store_id = c.id
AND c.org_id =:org
GROUP BY a.type;
";



    try {

        $db = getDB();
        $stmt = $db->prepare($storeSql);

        $stmt->bindParam("org", $org);
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
                    'id' => $tStore->id,

                    'first_name' => $tStore->first_name,
                    'last_name' => $tStore->last_name,
                    'email' => $tStore->email,
                    'mobile' => $tStore->mobile,
                    'trans' => array(array('type'=>$tStore->type,'amount' => $tStore->sum)));

            }
        }

        $stmt = $db->prepare($orgFloting);

        $stmt->bindParam("org", $org);

        $stmt->execute();
        $returnArr['amounts'] = $stmt->fetchAll(PDO::FETCH_OBJ);
        $returnArr['customers'] = $stores;






        $db = null;

        echo '{"org_details": ' . json_encode($returnArr) . '}';



    } catch (Exception $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":"' . $e->getMessage() . '"}}';
    }
}