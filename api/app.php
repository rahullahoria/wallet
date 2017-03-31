<?php
/**
 * Created by PhpStorm.
 * User: spider-ninja
 * Date: 6/4/16
 * Time: 1:12 PM
 */

\Slim\Slim::registerAutoloader();

global $app;

if(!isset($app))
    $app = new \Slim\Slim();

$app->response->headers->set('Access-Control-Allow-Credentials',  'true');

$app->response->headers->set('Content-Type', 'application/json');

/* Starting routes */


$app->post('/org/:org/credit', 'topupCustomer');
$app->post('/org/:org/debit', 'debitCustomer');
$app->post('/org/:org/sms', 'smsOrgCamp');
$app->get('/org/:org/stores', 'getStores');
$app->get('/org/:org/customers', 'getOrgCustomers');
$app->get('/org/:org/store/:store', 'getAssociates');
$app->get('/org/:org/store/:store/associate/:associate', 'getAssociateCustomers');
$app->get('/org/:org/mobile/:mobile/otp/:otp', 'checkCustomerTransactionOtp');


$app->post('/auth', 'userAuth');

/* Ending Routes */

$app->run();