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
            
            //print_r($charge);
            
            if ($charge->paid == 1)
            {
                $this->getRequest()->setParam('id', $charge->id);
                $this->forward('success');
            }
            else {
                die("Payment error");
            }
        }
    }
    
    public function successAction()
    {
        $this->view->charge_id = $charge_id = $this->getRequest()->getParam('id');
    }
    
    public function webhookAction()
    {
        $response = file_get_contents('php://input');
        
        $response_array = json_decode($response, true);
        
        $email = $response_array['data']['object']['email'];
        $charge_id = $response_array['data']['object']['id'];
        
        $this->_helper->json($response_array);
        //die("Webhook");
    }
}