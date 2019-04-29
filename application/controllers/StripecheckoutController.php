<?php

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripecheckoutController extends Zend_Controller_Action
{
    public function init()
    {
       
    }
    
    public function indexAction()
    {  
 
    }
    
    public function chargeandcreateAction()
    {
        $formData = $this->getRequest()->getPost();
        
        Stripe::setApiKey(STRIPE_SECRET_KEY);
        
        // To use Session you must have the latest version of Stripe at least 6 and above
        // Also you must have an Account name set up in https://dashboard.stripe.com/account        Declan Ics Dev was the account name i gave
        $stripe_create_response = Session::create([
          'payment_method_types' => ['card'],
          'line_items' => [[
            'name' => 'T-shirt',
            'description' => 'Comfortable cotton t-shirt',
            'images' => [],
            'amount' => $formData['price'],
            'currency' => 'eur',
            'quantity' => 1,
          ]],
          'success_url' => 'http://zendcode.localhost/stripecheckout/success',
          'cancel_url' => 'http://zendcode.localhost/stripecheckout/cancel',
        ]);
        
        $this->_helper->json($stripe_create_response);
    }
    
    public function successAction()
    {
        die("On success page");
    }
    
    public function cancelAction()
    {
        die("On cancel page");
    }
}

