<?php

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeUpdateController extends Zend_Controller_Action
{
    public function init()
    {
        Stripe::setApiKey(Application_Service_Config::getStripePrivateKey('skills'));
    }
    
    // Works with new version of stripe api 2020-08-27
    public function checkoutWebhookAction() 
    {
        $response = file_get_contents('php://input');

        $response_array = json_decode($response, true);
        
        $intent = PaymentIntent::retrieve($response_array['data']['object']['payment_intent']);
        
        $this->_helper->json(array('metadata' => $intent['charges']['data'][0]['metadata'], 'payment_intent' => $intent['id']));
    }
    
    // Works with new version of stripe api 2020-08-27
    public function paymentIntentWebhookAction()
    {
        $response = file_get_contents('php://input');

        $response_array = json_decode($response, true);
        
        $payment_intent = PaymentIntent::retrieve('pi_1Gn6XOEosXjNQZCsTYcyYUIx');
        
        $this->_helper->json($payment_intent);
    }
}