<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 3/29/17
 * Time: 4:39 PM
 */

function loadCustomer($mobile){

    $sql = "SELECT
                    e.name,e.logo, a.type,sum(a.amount) as sum
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
                    group by a.customer_id,a.type ";

    try {

        $db = getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindParam("mobile", $mobile);

        $stmt->execute();
        $resp['customer'] = $stmt->fetchAll(PDO::FETCH_OBJ);


        $db = null;

        echo '{"company_type": ' . json_encode($resp) . '}';



    } catch (Exception $e) {
        //error_log($e->getMessage(), 3, '/var/tmp/php.log');
        echo '{"error":{"text":"' . $e->getMessage() . '"}}';
    }

}