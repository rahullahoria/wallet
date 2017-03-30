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
$app->post('/org/:org/mobile/:mobile/amount/:amount', 'debitCustomer');
$app->get('/org/:org/mobile/:mobile/otp/:otp', 'checkCustomerTransactionOtp');

$app->get('/company_type','getCompanyTypes');
$app->get('/industry','getIndustryTypes');
$app->post('/auth', 'userAuth');

/* Ending Routes */

$app->run();