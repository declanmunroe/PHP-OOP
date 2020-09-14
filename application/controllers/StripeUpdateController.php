<?php

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeUpdateController extends Zend_Controller_Action
{
    public function init()
    {
        Stripe::setApiKey(Application_Service_Config::getStripePrivateKey('skills'));
    }
    
    public function checkoutWebhookAction() 
    {
        $response = file_get_contents('php://input');

        $response_array = json_decode($response, true);
        
        $intent = PaymentIntent::retrieve($response_array['data']['object']['payment_intent']);
        
        $this->_helper->json($intent);
        
        $this->_helper->json(array('unique_id' => $intent['charges']['data'][0]['metadata']['uniqueid'], 'payment_intent' => $intent['id'], 'type' => $intent['charges']['data'][0]['metadata']['customtype']));
    }
}