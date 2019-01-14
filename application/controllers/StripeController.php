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
        $stripe = array('secret_key' => STRIPE_SECRET_KEY, 'publishable_key' => STRIPE_PUBLIC_KEY);
        
        Stripe::setApiKey($stripe['secret_key']);
    }
    
    public function indexAction()
    {  
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            die(print_r($formData));
        }
    }
}