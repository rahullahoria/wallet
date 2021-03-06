<?php


// Merchant key here as provided by Payu
$MERCHANT_KEY = "itLYa9GO";

// Merchant Salt as provided by Payu
$SALT = "3Xr7ygpTpo";

// End point - change to https://secure.payu.in for LIVE mode
$action = $PAYU_BASE_URL = "https://secure.payu.in/_payment";

//$action = '';

$posted = array();

$posted['service_provider'] = "payu_paisa";
$posted['key'] = $MERCHANT_KEY;
$posted['txnid'] = substr(hash('sha256', mt_rand() . microtime()), 0, 20);

/*
 *
 * if($input) {



    $posted['amount'] = $input->amount;
    $posted['firstname'] = $input->name;
    $posted['email'] = $input->email;
    $posted['phone'] = $input->mobile;
    $posted['productinfo'] = $input->product;

    $posted['surl'] = $input->surl;
    $posted['furl'] = $input0>furl;

    $sql = "INSERT INTO `payments`(`txnid`, `name`, `mobile`, `email`, `product`)
              VALUES
              ('".$posted['txnid']."','".$posted['firstname']."','".$posted['phone']."','".$posted['email']."','".$posted['productinfo']."')";
}*/



if(!empty($_GET)) {
    //print_r($_POST);
    foreach($_GET as $key => $value) {
        $posted[$key] = $value;

    }
}

$dbHandle = mysqli_connect("localhost","root","redhat@11111p","rocket_payments");
$sql = "INSERT INTO `payments`(`txnid`, `name`, `mobile`, `email`, `product`)
              VALUES
              ('".$posted['txnid']."','".$posted['firstname']."','".$posted['phone']."','".$posted['email']."','".$posted['productinfo']."')";mysqli_query($dbHandle,$sql);
mysqli_close($dbHandle);



$formError = 0;

if(empty($posted['txnid'])) {
    // Generate random transaction id
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
    $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
    if(
        empty($posted['key'])
        || empty($posted['txnid'])
        || empty($posted['amount'])
        || empty($posted['firstname'])
        || empty($posted['email'])
        || empty($posted['phone'])
        || empty($posted['productinfo'])
        || empty($posted['surl'])
        || empty($posted['furl'])
        || empty($posted['service_provider'])
    ) {
        $formError = 1;
    } else {
        //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';
        foreach($hashVarsSeq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }

        $hash_string .= $SALT;


        $hash = strtolower(hash('sha512', $hash_string));
       // $action = $PAYU_BASE_URL . '';
    }
} elseif(!empty($posted['hash'])) {
    $hash = $posted['hash'];
    //$action = $PAYU_BASE_URL . '/_payment';
}
?>
<html>
<head>
    <script>
        var hash = '<?php echo $hash ?>';
        function submitPayuForm() {
            if(hash == '') {
                return;
            }
            var payuForm = document.forms.payuForm;
            payuForm.submit();
        }
    </script>
</head>
<body onload="submitPayuForm()">

<br/>
<?php if($formError) { ?>

    <br/>
    <br/>
<?php } ?>
<form action="<?php echo $action; ?>" method="post" name="payuForm">
    <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
    <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
    <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
    <table>
        <tr>
            <td><b>Wait Processing .......</b></td>
        </tr>
        <tr>

            <td><input name="amount" type="hidden" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" /></td>
            <td><input name="firstname" type="hidden" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" /></td>
        </tr>
        <tr>
            <td><input name="email" type="hidden" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" /></td>
            <td><input name="phone" type="hidden" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" /></td>
        </tr>
        <tr>
            <td colspan="3"><textarea type="hidden" name="productinfo"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea></td>
        </tr>
        <tr>
            <td colspan="3"><input type="hidden" name="surl" value="<?php echo (empty($posted['surl'])) ? '' : $posted['surl'] ?>" size="64" /></td>
        </tr>
        <tr>
            <td colspan="3"><input type="hidden" name="furl" value="<?php echo (empty($posted['furl'])) ? '' : $posted['furl'] ?>" size="64" /></td>
        </tr>

        <tr>
            <td colspan="3"><input type="hidden" name="service_provider" value="payu_paisa" size="64" /></td>
        </tr>

        <tr>
            <?php if(!$hash) { ?>
                <td colspan="4"><input type="submit" value="Submit" /></td>
            <?php } ?>
        </tr>
    </table>
</form>
</body>
</html>
