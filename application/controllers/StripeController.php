<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 03/05/2018
 * Time: 11:30
 */

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class StripeController extends Zend_Controller_Action
{
    public function init()
    {
        $stripe = array('secret_key' => STRIPE_SECRET_KEY, 'publishable_key' => STRIPE_PUBLIC_KEY);
        
        Stripe::setApiKey($stripe['secret_key']);
    }
    
    public function indexAction()
    {  
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            
            $customer = Customer::create(array('email' => $formData['stripeEmail'], 'source' => $formData['stripeToken']));
            
            $charge = Charge::create(array('amount' => 100, 'currency' => 'eur', 'description' => 'Testing out stripe api', 'customer' => $customer->id));
            
            print_r($charge);
        }
    }
}