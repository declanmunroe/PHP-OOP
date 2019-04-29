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
        
        Stripe::setApiKey("sk_test_4eC39HqLyjWDarjtT1zdp7dc");

        $intent_response = PaymentIntent::create([
          'amount' => $formData['price'],
          'currency' => 'eur',
        ]);
        
        $this->_helper->json($intent_response);
    }
    
}

