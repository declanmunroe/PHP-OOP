<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 03/05/2018
 * Time: 11:30
 */

use Stripe\Stripe;

class StripeController extends Zend_Controller_Action
{
    public function init()
    {
        $stripe = array('secret_key' => '1234', 'publishable_key' => '9999');
        
        Stripe::setApiKey($stripe['secret_key']);
    }
    
    public function indexAction()
    {  
    
    }
}