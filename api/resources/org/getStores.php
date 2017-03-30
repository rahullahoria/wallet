<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 7:36 PM
 */

function getStores($org){

    $storeSql = "SELECT
                    c.id,c.name,a.type,sum(a.amount) as sum
                    FROM `transactions` as a inner join associates as b inner join stores as c
                    WHERE DATE(a.creation) = CURDATE() and a.associate_id = b.id and b.store_id = c.id and c.org_id = :org group by c.id,c.name,a.type";
    $orgFloting = "

SELECT a.type, sum( a.amount )
FROM `transactions` AS a
INNER JOIN associates AS b
INNER JOIN stores AS c
WHERE DATE( a.creation ) = CURDATE( )
AND a.associate_id = b.id
AND b.store_id = c.id
AND c.org_id =1
AND b.store_id =1
GROUP BY a.type;
";

    $storeFloatingAmount = "SELECT (

SELECT sum( a.amount )
FROM `transactions` AS a
INNER JOIN associates AS b
INNER JOIN stores AS c
WHERE DATE( a.creation ) = CURDATE( )
AND a.associate_id = b.id
AND b.store_id = c.id
AND c.org_id =1
AND b.store_id =1
AND a.type != 'debit'
) - (
SELECT sum( a.amount )
FROM `transactions` AS a
INNER JOIN associates AS b
INNER JOIN stores AS c
WHERE DATE( a.creation ) = CURDATE( )
AND a.associate_id = b.id
AND b.store_id = c.id
AND c.org_id =:org
AND b.store_id =:store_id
AND a.type = 'debit' ) AS floating_amount";

    try {

        $db = getDB();
        $stmt = $db->prepare($storeSql);

        $stmt->bindParam("org", $org);

        $stmt->execute();
        $tStores = $stmt->fetchAll(PDO::FETCH_OBJ);

        $stores = array();
        foreach($tStores as $tStore){
            $done = false;
            foreach($stores as $store){
                if($tStore->id == $store['id']){
                    $done = true;
                    $store['trans'][] =  array($tStore->type => $tStore->sum);

                }
            }
            if($done == false){
                $stmt = $db->prepare($storeFloatingAmount);

                $stmt->bindParam("org", $org);
                $stmt->bindParam("store_id", $tStore->id);

                $stmt->execute();
                $FA = $stmt->fetchAll(PDO::FETCH_OBJ);

                $stores[] = array('id' => $tStore->id, 'floating_amount'=> $FA[0]->floating_amount,'name' => $tStore->name,'trans' => array(array($tStore->type => $tStore->sum)));

            }
        }

        $db = getDB();
        $stmt = $db->prepare($orgFloting);

        $stmt->bindParam("org", $org);

        $stmt->execute();
        $returnArr['amounts'] = $stmt->fetchAll(PDO::FETCH_OBJ);
        $returnArr['stores'] = $stores;






        $db = null;

        echo '{"company_type": ' . json_encode($returnArr) . '}';



    } catch (Exception $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":"' . $e->getMessage() . '"}}';
    }
}