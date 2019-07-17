<?php

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeelementsController extends Zend_Controller_Action
{
    public function init()
    {
       
    }
    
    public function indexAction()
    {  
 
    }
    
    public function createpaymentintentAction()
    {  
        $formData = $this->getRequest()->getPost();
        
        Stripe::setApiKey(STRIPE_SECRET_KEY);

        $intent_response = PaymentIntent::create([
          'amount' => $formData['price'],
          'currency' => 'eur',
        ]);
        
        $this->_helper->json($intent_response);
    }
    
}

