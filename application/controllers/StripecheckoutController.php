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
            'name' => $formData['description'],
            'description' => $formData['description'],
            'images' => [],
            'amount' => $formData['price'],
            'currency' => 'eur',
            'quantity' => 1,
          ]],
          'success_url' => "http://zendcode.localhost/stripecheckout/success/reg_id/1234/status/paid/price/{$formData['price']}",
          'cancel_url' => 'http://zendcode.localhost/stripecheckout/cancel',
        ]);
        
        $this->_helper->json($stripe_create_response);
        
        // Docs say to validate a propper successfull charge transaction that you should not fully rely on the success url, you should use a webhook as well.
        // But to use a webhook it is best practice if not nessacary to have seperate stripe accounts for every stripe payment option in each website
    }
    
    public function successAction()
    {
        $params = $this->getAllParams();
        die(print_r($params));
    }
    
    public function cancelAction()
    {
        die("On cancel page");
    }
}

