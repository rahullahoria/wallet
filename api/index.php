<?php

require_once "header.php";

include 'db.php';
require 'Slim/Slim.php';

//sms lib
require_once "includes/sms.php";

//email lib
require_once "includes/error.php";

//Random string lib
require_once "includes/getRandonString.php";

//user
require_once "resources/org/createCampaign.php";
require_once "resources/org/customer/checkCustomerTransactionOtp.php";
require_once "resources/org/customer/debitCustomer.php";
require_once "resources/org/customer/topupCustomer.php";
require_once "resources/auth/postUserAuth.php";
require_once "resources/org/getStores.php";
require_once "resources/org/getOrgCustomers.php";
require_once "resources/org/store/getAssociates.php";
require_once "resources/org/store/associates/getAssciateCustomers.php";


//app
require_once "app.php";



?>